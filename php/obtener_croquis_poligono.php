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

$tramite_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($tramite_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Id de tramite invalido']);
    exit;
}

$stmt = $conn->prepare("
    SELECT
        id,
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
        updated_at
    FROM croquis_poligonos
    WHERE tramite_id = ? AND activo = 1
    ORDER BY updated_at DESC, id DESC
    LIMIT 1
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Tabla croquis_poligonos no disponible']);
    exit;
}

$stmt->bind_param("i", $tramite_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => true, 'poligono' => null]);
    exit;
}

$detalles = [];
$stmtDet = $conn->prepare("
    SELECT
        id,
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
        updated_at
    FROM croquis_poligono_detalles
    WHERE tramite_id = ? AND activo = 1
    ORDER BY id ASC
");
if ($stmtDet) {
    $stmtDet->bind_param("i", $tramite_id);
    $stmtDet->execute();
    $resDet = $stmtDet->get_result();
    while ($det = $resDet->fetch_assoc()) {
        $detalles[] = [
            'id' => (int)$det['id'],
            'feature_uid' => $det['feature_uid'],
            'numero_poligono' => $det['numero_poligono'],
            'origen' => $det['origen'],
            'cuenta_catastral_origen' => $det['cuenta_catastral_origen'],
            'texto' => $det['texto_poligono'],
            'geojson' => $det['geojson'],
            'utm_vertices' => $det['utm_vertices_json'],
            'utm_centro_x' => $det['utm_centro_x'],
            'utm_centro_y' => $det['utm_centro_y'],
            'label_lng' => $det['label_lng'],
            'label_lat' => $det['label_lat'],
            'seleccionado' => (int)$det['seleccionado'],
            'updated_at' => $det['updated_at']
        ];
    }
    $stmtDet->close();
}

echo json_encode([
    'success' => true,
    'poligono' => [
        'id' => (int)$row['id'],
        'tramite_id' => (int)$row['tramite_id'],
        'origen' => $row['origen'],
        'cuenta_catastral_origen' => $row['cuenta_catastral_origen'],
        'texto' => $row['texto_poligono'],
        'geojson' => $row['geojson'],
        'utm_vertices' => $row['utm_vertices_json'],
        'utm_centro_x' => $row['utm_centro_x'],
        'utm_centro_y' => $row['utm_centro_y'],
        'georeferencia' => $row['georeferencia_json'],
        'croquis_archivo' => $row['croquis_archivo'],
        'updated_at' => $row['updated_at'],
        'detalles' => $detalles
    ]
]);
