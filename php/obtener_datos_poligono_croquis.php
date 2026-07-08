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
        id,
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
        croquis_archivo,
        updated_at
    FROM croquis_poligono_detalles
    WHERE (cuenta_catastral_origen = ? OR numero_poligono = ?) AND activo = 1
    ORDER BY updated_at DESC, id DESC
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
    echo json_encode(['success' => true, 'poligono' => null]);
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
        'updated_at' => $row['updated_at']
    ]
]);
