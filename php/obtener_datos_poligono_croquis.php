<?php
error_reporting(0);
ini_set('display_errors', 0);
if (ob_get_length()) ob_clean();
if (session_status() === PHP_SESSION_NONE) session_start();

require_once "db.php";
require_once "funciones_seguridad.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesion expirada']);
    exit;
}

if (!esVerificador() && !esAdministrador() && !esVentanilla()) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

$cuenta = isset($_GET['cuenta']) ? trim($_GET['cuenta']) : '';
if ($cuenta === '' || strlen($cuenta) > 50) {
    echo json_encode(['success' => false, 'message' => 'Numero de poligono invalido']);
    exit;
}

$stmt = $conn->prepare("
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
    $fallback = $conn->prepare("
        SELECT t.id AS tramite_id, t.estatus, t.numero_asignado, t.tipo_tramite_id,
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
        echo json_encode(['success' => true, 'poligono' => null]);
        exit;
    }
    $texto = trim((string)($tramite['numero_asignado'] ?? ''));
    if ($texto === '') {
        $texto = str_pad((string)$tramite['folio_numero'], 3, '0', STR_PAD_LEFT) . '/' . $tramite['folio_anio'];
    }
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
            'updated_at' => $tramite['updated_at']
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

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
        'updated_at' => $row['updated_at']
    ]
]);
