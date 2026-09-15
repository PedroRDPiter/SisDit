<?php
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

$tramite_id = isset($_GET['tramite_id']) ? (int)$_GET['tramite_id'] : 0;

if ($tramite_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

$stmt = $conn->prepare(
    "SELECT s.*,
            CONCAT(u.nombre, ' ', u.apellidos) AS aprobado_por_nombre,
            t.usuario_creador_id
     FROM solicitud_lc s
     INNER JOIN tramites t ON t.id = s.tramite_id
     LEFT JOIN usuarios u ON u.id = s.aprobado_por
     WHERE s.tramite_id = ? LIMIT 1"
);
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

if (!puedeAccederTramite($row)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sin permisos para consultar esta solicitud.']);
    exit;
}
unset($row['usuario_creador_id']);

// Decodificar los campos JSON
$row['urbanizacion'] = json_decode($row['urbanizacion'] ?? '[]', true) ?? [];
$row['superficies']  = json_decode($row['superficies']  ?? '{}', true) ?? [];
$row['peritos']      = json_decode($row['peritos']      ?? '{}', true) ?? [];

// Formatear folio de solicitud
$row['folio_solicitud'] = $row['folio_numero'] !== null
    ? str_pad($row['folio_numero'], 3, '0', STR_PAD_LEFT) . '/' . $row['folio_anio']
    : null;

echo json_encode(['success' => true, 'data' => $row]);
