<?php
/*
-----------------------------------------------------------
 OBTENER TRAMITE_SALIDA
 Devuelve el registro de tramites_salida asociado a un
 tramite_id, o data:null si aún no existe.
-------------------------------------------------------------
*/

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "db.php";
require_once "funciones_seguridad.php";

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesión expirada.']);
    exit;
}

if (!esCalificador() && !esAdministrador()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sin permisos para consultar esta calificación.']);
    exit;
}

$tramite_id = isset($_GET['tramite_id']) ? (int)$_GET['tramite_id'] : 0;

if ($tramite_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM tramites_salida WHERE tramite_id = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error interno.']);
    exit;
}
$stmt->bind_param("i", $tramite_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => true, 'data' => null]);
    exit;
}

echo json_encode(['success' => true, 'data' => $row]);
