<?php
// Configura una respuesta silenciosa en formato JSON y garantiza una sesión activa.
error_reporting(0);
ini_set('display_errors', 0);
if (ob_get_length()) ob_clean();
if (session_status() === PHP_SESSION_NONE) session_start();

require_once "db.php";
require_once "funciones_seguridad.php";
require_once "documento_escaneado.php";

header('Content-Type: application/json; charset=utf-8');

// Valida la sesión y los permisos necesarios para consultar la información.
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesion expirada']);
    exit;
}

if (!esVerificador() && !esAdministrador() && !esVentanilla()) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

$cuenta = isset($_GET['cuenta']) ? trim($_GET['cuenta']) : '';
// Limita la entrada para evitar búsquedas vacías o excesivamente largas.
if ($cuenta === '' || strlen($cuenta) > 50) {
    echo json_encode(['success' => false, 'message' => 'Numero de poligono invalido']);
    exit;
}

// Una cuenta catastral puede acumular varios trámites, incluso con folios distintos.
// Se recopilan todos los trámites relacionados para mostrarlos en el resultado.
$tramitesCuenta = [];
$stmtTramites = $conn->prepare("
    SELECT DISTINCT t.id AS tramite_id, t.folio_numero, t.folio_anio,
           t.estatus, t.numero_asignado, t.tipo_tramite_id,
           t.formato_constancia, t.otros_archivos, t.updated_at,
           tt.nombre AS tipo_tramite
    FROM tramites t
    LEFT JOIN tipos_tramite tt ON tt.id = t.tipo_tramite_id
    LEFT JOIN croquis_poligono_detalles dc ON dc.tramite_id = t.id AND dc.activo = 1
    WHERE t.cuenta_catastral = ?
       OR dc.cuenta_catastral_origen = ?
       OR dc.numero_poligono = ?
    ORDER BY t.updated_at DESC, t.id DESC
");
if ($stmtTramites) {
    $stmtTramites->bind_param('sss', $cuenta, $cuenta, $cuenta);
    $stmtTramites->execute();
    $resultadoTramites = $stmtTramites->get_result();
    while ($tramiteCuenta = $resultadoTramites->fetch_assoc()) {
        $tramiteCuenta['tramite_id'] = (int) $tramiteCuenta['tramite_id'];
        $tramiteCuenta['tipo_tramite_id'] = (int) $tramiteCuenta['tipo_tramite_id'];
        $tramiteCuenta['folio'] = str_pad((string) $tramiteCuenta['folio_numero'], 3, '0', STR_PAD_LEFT) . '/' . $tramiteCuenta['folio_anio'];
        $tramiteCuenta['documento_escaneado'] = obtenerDocumentoEscaneadoTramite($tramiteCuenta);
        unset($tramiteCuenta['formato_constancia'], $tramiteCuenta['otros_archivos']);
        $tramitesCuenta[] = $tramiteCuenta;
    }
    $stmtTramites->close();
}

$stmt = $conn->prepare("
    /* Obtiene el polígono activo más relevante asociado a la cuenta consultada. */
    SELECT
        d.id,
        d.tramite_id,
        d.feature_uid,
        d.numero_poligono,
        d.origen,
        d.cuenta_catastral_origen,
        d.texto_poligono,
        d.geojson,
        d.utm_vertices_json,
        d.utm_centro_x,
        d.utm_centro_y,
        d.label_lng,
        d.label_lat,
        d.croquis_archivo,
        d.updated_at,
        t.estatus,
        t.tipo_tramite_id,
        t.formato_constancia,
        t.otros_archivos,
        tt.nombre AS tipo_tramite
    FROM croquis_poligono_detalles d
    INNER JOIN tramites t ON t.id = d.tramite_id
    LEFT JOIN tipos_tramite tt ON tt.id = t.tipo_tramite_id
    WHERE (d.cuenta_catastral_origen = ? OR d.numero_poligono = ?) AND d.activo = 1
    ORDER BY d.seleccionado DESC, d.updated_at DESC, d.id DESC
    LIMIT 1
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Tabla croquis_poligono_detalles no disponible']);
    exit;
}

$stmt->bind_param("ss", $cuenta, $cuenta);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    // Si no existe un detalle de polígono, intenta devolver los datos básicos del trámite.
    $fallback = $conn->prepare("
        SELECT t.id AS tramite_id, t.estatus, t.numero_asignado, t.tipo_tramite_id,
               t.formato_constancia, t.otros_archivos,
               t.folio_numero, t.folio_anio, t.updated_at,
               tt.nombre AS tipo_tramite
        FROM tramites t
        LEFT JOIN tipos_tramite tt ON tt.id = t.tipo_tramite_id
        WHERE t.cuenta_catastral = ?
        ORDER BY t.updated_at DESC, t.id DESC
        LIMIT 1
    ");
    $fallback->bind_param("s", $cuenta);
    $fallback->execute();
    $tramite = $fallback->get_result()->fetch_assoc();
    $fallback->close();
    if (!$tramite) {
        echo json_encode(['success' => true, 'poligono' => null, 'tramites' => $tramitesCuenta]);
        exit;
    }
    $texto = trim((string)($tramite['numero_asignado'] ?? ''));
    // Usa el número asignado o, en su defecto, el folio formateado como texto identificador.
    if ($texto === '') {
        $texto = str_pad((string)$tramite['folio_numero'], 3, '0', STR_PAD_LEFT) . '/' . $tramite['folio_anio'];
    }
    $documento = obtenerDocumentoEscaneadoTramite($tramite);
    echo json_encode([
        'success' => true,
        'poligono' => [
            'tramite_id' => (int)$tramite['tramite_id'],
            'texto' => $texto,
            'utm_centro_x' => null,
            'utm_centro_y' => null,
            'estatus' => $tramite['estatus'],
            'tipo_tramite_id' => (int)$tramite['tipo_tramite_id'],
            'tipo_tramite' => $tramite['tipo_tramite'],
            'documento_escaneado' => $documento,
            'updated_at' => $tramite['updated_at']
        ],
        'tramites' => $tramitesCuenta
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$documento = obtenerDocumentoEscaneadoTramite($row);
// Devuelve el detalle del polígono junto con los trámites relacionados.
echo json_encode([
    'success' => true,
    'poligono' => [
        'id' => (int)$row['id'],
        'tramite_id' => (int)$row['tramite_id'],
        'feature_uid' => $row['feature_uid'],
        'numero_poligono' => $row['numero_poligono'],
        'origen' => $row['origen'],
        'cuenta_catastral_origen' => $row['cuenta_catastral_origen'],
        'texto' => $row['texto_poligono'],
        'geojson' => $row['geojson'],
        'utm_vertices' => $row['utm_vertices_json'],
        'utm_centro_x' => $row['utm_centro_x'],
        'utm_centro_y' => $row['utm_centro_y'],
        'label_lng' => $row['label_lng'],
        'label_lat' => $row['label_lat'],
        'croquis_archivo' => $row['croquis_archivo'],
        'estatus' => $row['estatus'],
        'tipo_tramite_id' => (int)$row['tipo_tramite_id'],
        'tipo_tramite' => $row['tipo_tramite'],
        'documento_escaneado' => $documento,
        'updated_at' => $row['updated_at']
    ],
    'tramites' => $tramitesCuenta
]);
