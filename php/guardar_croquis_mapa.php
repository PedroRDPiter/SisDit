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

$id_post = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$folio   = isset($_POST['folio']) ? trim($_POST['folio']) : '';

if ($id_post > 0) {
    $stmt = $conn->prepare("SELECT id FROM tramites WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id_post);
} else {
    if (!preg_match('/^(\d{1,4})\/(\d{4})$/', $folio, $m)) {
        echo json_encode(['success' => false, 'message' => 'Folio o id invalido']);
        exit;
    }
    $folio_numero = (int)$m[1];
    $folio_anio   = (int)$m[2];
    $stmt = $conn->prepare("SELECT id FROM tramites WHERE folio_numero = ? AND folio_anio = ? ORDER BY (tramite_principal_id IS NULL) DESC, id ASC LIMIT 1");
    $stmt->bind_param("ii", $folio_numero, $folio_anio);
}

$stmt->execute();
$res = $stmt->get_result();
$tramite = $res->fetch_assoc();
$stmt->close();

if (!$tramite) {
    echo json_encode(['success' => false, 'message' => 'Tramite no encontrado']);
    exit;
}

$tramite_id = (int)$tramite['id'];

if (!isset($_FILES['croquis']) || $_FILES['croquis']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No se recibio la imagen del mapa']);
    exit;
}

$geojson = isset($_POST['geojson']) ? trim($_POST['geojson']) : '';
$poligonos_detalle = isset($_POST['poligonos_detalle']) ? trim($_POST['poligonos_detalle']) : '';
$utm_vertices = isset($_POST['utm_vertices']) ? trim($_POST['utm_vertices']) : '';
$georeferencia = isset($_POST['georeferencia']) ? trim($_POST['georeferencia']) : '';

$geojson_decoded = json_decode($geojson, true);
if ($geojson === '' || $geojson_decoded === null) {
    echo json_encode(['success' => false, 'message' => 'GeoJSON invalido']);
    exit;
}
$detalles_decoded = [];
if ($poligonos_detalle !== '') {
    $detalles_decoded = json_decode($poligonos_detalle, true);
    if (!is_array($detalles_decoded)) {
        echo json_encode(['success' => false, 'message' => 'Detalle de poligonos invalido']);
        exit;
    }
}

$texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';
$origen = isset($_POST['origen']) ? trim($_POST['origen']) : 'seleccionado';
$cuenta = isset($_POST['cuenta_catastral_origen']) ? trim($_POST['cuenta_catastral_origen']) : '';
$utm_x = isset($_POST['utm_centro_x']) && $_POST['utm_centro_x'] !== '' ? (float)$_POST['utm_centro_x'] : null;
$utm_y = isset($_POST['utm_centro_y']) && $_POST['utm_centro_y'] !== '' ? (float)$_POST['utm_centro_y'] : null;

// La identidad del predio debe salir del poligono que el usuario dejo
// seleccionado. Los campos generales del POST se conservan como respaldo para
// croquis antiguos, pero no deben asociar por error los datos a otro predio.
$detalle_seleccionado = null;
foreach ($detalles_decoded as $detalle_candidato) {
    if (is_array($detalle_candidato) && !empty($detalle_candidato['seleccionado'])) {
        $detalle_seleccionado = $detalle_candidato;
        break;
    }
}

if (!empty($detalles_decoded) && $detalle_seleccionado === null) {
    echo json_encode(['success' => false, 'message' => 'No se identifico el predio seleccionado']);
    exit;
}

if ($detalle_seleccionado !== null) {
    $cuenta_seleccionada = isset($detalle_seleccionado['cuenta_catastral_origen'])
        ? trim((string)$detalle_seleccionado['cuenta_catastral_origen'])
        : '';
    if ($cuenta_seleccionada === '' && isset($detalle_seleccionado['numero_poligono'])) {
        $cuenta_seleccionada = trim((string)$detalle_seleccionado['numero_poligono']);
    }
    if ($cuenta_seleccionada !== '') {
        $cuenta = substr($cuenta_seleccionada, 0, 50);
    }
    if (isset($detalle_seleccionado['origen'])) {
        $origen = substr(trim((string)$detalle_seleccionado['origen']), 0, 40);
    }
    if (array_key_exists('texto_poligono', $detalle_seleccionado)) {
        $texto = trim((string)$detalle_seleccionado['texto_poligono']);
    }
    if (isset($detalle_seleccionado['utm_vertices'])) {
        $utm_vertices = json_encode($detalle_seleccionado['utm_vertices'], JSON_UNESCAPED_UNICODE);
    }
    if (isset($detalle_seleccionado['utm_centro_x']) && $detalle_seleccionado['utm_centro_x'] !== '') {
        $utm_x = (float)$detalle_seleccionado['utm_centro_x'];
    }
    if (isset($detalle_seleccionado['utm_centro_y']) && $detalle_seleccionado['utm_centro_y'] !== '') {
        $utm_y = (float)$detalle_seleccionado['utm_centro_y'];
    }
}

$cuenta = substr($cuenta, 0, 50);
$origen = substr($origen !== '' ? $origen : 'seleccionado', 0, 40);

if (in_array($origen, ['catastro', 'catastro-copia'], true) && $cuenta === '') {
    echo json_encode(['success' => false, 'message' => 'El predio seleccionado no tiene una clave catastral valida']);
    exit;
}
if ($utm_vertices !== '' && json_decode($utm_vertices, true) === null) {
    echo json_encode(['success' => false, 'message' => 'Coordenadas UTM invalidas']);
    exit;
}
if ($georeferencia !== '' && json_decode($georeferencia, true) === null) {
    echo json_encode(['success' => false, 'message' => 'Georeferencia invalida']);
    exit;
}

$tmp = $_FILES['croquis']['tmp_name'];
$mime = function_exists('mime_content_type') ? mime_content_type($tmp) : $_FILES['croquis']['type'];
if (!in_array($mime, ['image/png', 'image/jpeg', 'image/webp'])) {
    echo json_encode(['success' => false, 'message' => 'La captura del croquis debe ser imagen PNG, JPG o WEBP']);
    exit;
}

$carpeta = "../.private/{$tramite_id}/croquis/";
if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

$max_num = 0;
if ($handle = opendir($carpeta)) {
    while (false !== ($entry = readdir($handle))) {
        if (preg_match('/^croquis_mapa_(\d+)\.png$/i', $entry, $matches)) {
            $max_num = max($max_num, (int)$matches[1]);
        }
    }
    closedir($handle);
}

$nombre = 'croquis_mapa_' . ($max_num + 1) . '.png';
$relative_path = ".private/{$tramite_id}/croquis/{$nombre}";

if (!move_uploaded_file($tmp, "../" . $relative_path)) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen del mapa']);
    exit;
}

$usuario_id = (int)$_SESSION['id'];

try {
    $conn->begin_transaction();

    $stmtUp = $conn->prepare("UPDATE tramites SET croquis_archivo = ? WHERE id = ?");
    $stmtUp->bind_param("si", $relative_path, $tramite_id);
    if (!$stmtUp->execute()) throw new Exception('No se pudo actualizar el croquis del tramite');
    $stmtUp->close();

    $stmtOff = $conn->prepare("UPDATE croquis_poligonos SET activo = 0 WHERE tramite_id = ?");
    if (!$stmtOff) throw new Exception('Tabla croquis_poligonos no disponible');
    $stmtOff->bind_param("i", $tramite_id);
    $stmtOff->execute();
    $stmtOff->close();

    $stmtIns = $conn->prepare("
        INSERT INTO croquis_poligonos (
            tramite_id,
            origen,
            cuenta_catastral_origen,
            texto_poligono,
            geojson,
            utm_vertices_json,
            utm_centro_x,
            utm_centro_y,
            georeferencia_json,
            croquis_archivo,
            creado_por,
            activo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
    ");
    if (!$stmtIns) throw new Exception('No se pudo preparar el guardado del poligono');
    $stmtIns->bind_param(
        "isssssddssi",
        $tramite_id,
        $origen,
        $cuenta,
        $texto,
        $geojson,
        $utm_vertices,
        $utm_x,
        $utm_y,
        $georeferencia,
        $relative_path,
        $usuario_id
    );
    if (!$stmtIns->execute()) throw new Exception('No se pudo guardar la informacion del poligono');
    $croquis_poligono_id = $stmtIns->insert_id;
    $stmtIns->close();

    $stmtOffDetalle = $conn->prepare("UPDATE croquis_poligono_detalles SET activo = 0 WHERE tramite_id = ?");
    if (!$stmtOffDetalle) throw new Exception('Tabla croquis_poligono_detalles no disponible');
    $stmtOffDetalle->bind_param("i", $tramite_id);
    $stmtOffDetalle->execute();
    $stmtOffDetalle->close();

    if (empty($detalles_decoded) && isset($geojson_decoded['features']) && is_array($geojson_decoded['features'])) {
        foreach ($geojson_decoded['features'] as $feature) {
            $detalles_decoded[] = [
                'feature_uid' => $feature['properties']['croquis_uid'] ?? '',
                'numero_poligono' => $feature['properties']['numero_poligono'] ?? ($feature['properties']['CVE_CAT_OR'] ?? $cuenta),
                'origen' => $feature['properties']['croquis_source'] ?? $origen,
                'cuenta_catastral_origen' => $feature['properties']['CVE_CAT_OR'] ?? $cuenta,
                'texto_poligono' => $feature['properties']['croquis_text'] ?? $texto,
                'geojson' => $feature,
                'utm_vertices' => [],
                'utm_centro_x' => null,
                'utm_centro_y' => null,
                'label_lng' => $feature['properties']['croquis_label_lng'] ?? null,
                'label_lat' => $feature['properties']['croquis_label_lat'] ?? null,
                'seleccionado' => !empty($feature['properties']['croquis_selected'])
            ];
        }
    }

    $stmtDetalle = $conn->prepare("
        INSERT INTO croquis_poligono_detalles (
            croquis_poligono_id,
            tramite_id,
            feature_uid,
            numero_poligono,
            origen,
            cuenta_catastral_origen,
            texto_poligono,
            geojson,
            utm_vertices_json,
            utm_centro_x,
            utm_centro_y,
            label_lng,
            label_lat,
            seleccionado,
            croquis_archivo,
            creado_por,
            activo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
    ");
    if (!$stmtDetalle) throw new Exception('No se pudo preparar el guardado de detalles de poligonos');

    foreach ($detalles_decoded as $detalle) {
        if (!is_array($detalle)) continue;
        $feature_uid = isset($detalle['feature_uid']) ? substr(trim((string)$detalle['feature_uid']), 0, 80) : '';
        $numero_poligono = isset($detalle['numero_poligono']) ? substr(trim((string)$detalle['numero_poligono']), 0, 80) : '';
        $detalle_origen = isset($detalle['origen']) ? substr(trim((string)$detalle['origen']), 0, 40) : $origen;
        $detalle_cuenta = isset($detalle['cuenta_catastral_origen']) ? substr(trim((string)$detalle['cuenta_catastral_origen']), 0, 50) : '';
        if ($detalle_cuenta === '' && isset($detalle['numero_poligono'])) {
            $detalle_cuenta = substr(trim((string)$detalle['numero_poligono']), 0, 50);
        }
        $detalle_texto = isset($detalle['texto_poligono']) ? trim((string)$detalle['texto_poligono']) : '';
        $detalle_geojson = isset($detalle['geojson']) ? json_encode($detalle['geojson'], JSON_UNESCAPED_UNICODE) : '';
        $detalle_utm = isset($detalle['utm_vertices']) ? json_encode($detalle['utm_vertices'], JSON_UNESCAPED_UNICODE) : null;
        if ($detalle_geojson === '' || json_decode($detalle_geojson, true) === null) continue;
        if ($detalle_utm !== null && json_decode($detalle_utm, true) === null) $detalle_utm = null;
        $detalle_utm_x = (isset($detalle['utm_centro_x']) && $detalle['utm_centro_x'] !== '' && $detalle['utm_centro_x'] !== null) ? (float)$detalle['utm_centro_x'] : null;
        $detalle_utm_y = (isset($detalle['utm_centro_y']) && $detalle['utm_centro_y'] !== '' && $detalle['utm_centro_y'] !== null) ? (float)$detalle['utm_centro_y'] : null;
        $detalle_label_lng = (isset($detalle['label_lng']) && $detalle['label_lng'] !== '' && $detalle['label_lng'] !== null) ? (float)$detalle['label_lng'] : null;
        $detalle_label_lat = (isset($detalle['label_lat']) && $detalle['label_lat'] !== '' && $detalle['label_lat'] !== null) ? (float)$detalle['label_lat'] : null;
        $detalle_seleccionado = !empty($detalle['seleccionado']) ? 1 : 0;

        $stmtDetalle->bind_param(
            "iisssssssddddisi",
            $croquis_poligono_id,
            $tramite_id,
            $feature_uid,
            $numero_poligono,
            $detalle_origen,
            $detalle_cuenta,
            $detalle_texto,
            $detalle_geojson,
            $detalle_utm,
            $detalle_utm_x,
            $detalle_utm_y,
            $detalle_label_lng,
            $detalle_label_lat,
            $detalle_seleccionado,
            $relative_path,
            $usuario_id
        );
        if (!$stmtDetalle->execute()) throw new Exception('No se pudo guardar el detalle de un poligono');
    }
    $stmtDetalle->close();

    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $predio_log = $cuenta !== '' ? $cuenta : 'sin-clave (dibujado)';
    $det = "Croquis de mapa guardado para tramite ID: {$tramite_id} | Predio: {$predio_log} | Archivo: {$relative_path}";
    $stmtLog = $conn->prepare("INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, detalles, ip_address) VALUES (?, 'Croquis mapa', 'croquis_poligonos', ?, ?)");
    if ($stmtLog) {
        $stmtLog->bind_param("iss", $usuario_id, $det, $ip);
        $stmtLog->execute();
        $stmtLog->close();
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Croquis guardado correctamente',
        'predio' => $cuenta,
        'archivo' => $relative_path,
        'url' => $relative_path
    ]);
} catch (Exception $e) {
    $conn->rollback();
    if (file_exists("../" . $relative_path)) unlink("../" . $relative_path);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
