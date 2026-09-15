<?php
/*
-----------------------------------------------------------
 APROBAR SOLICITUD LICENCIA DE CONSTRUCCIÓN
 Cambia solicitud_lc.estatus de 'Pendiente' a 'Aprobada',
 registra fecha_aprobacion y aprobado_por.
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
    echo json_encode(['success' => false, 'message' => 'Sesión expirada. Recarga la página.']);
    exit;
}

if (!esVentanilla() && !esAdministrador()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sin permisos para esta acción.']);
    exit;
}

if (!validarCSRF()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido.']);
    exit;
}

$solicitud_id = isset($_POST['solicitud_id']) ? (int)$_POST['solicitud_id'] : 0;

if ($solicitud_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de solicitud inválido.']);
    exit;
}

/* Verificar que la solicitud existe y está Pendiente */
$stmtCheck = $conn->prepare("SELECT id, tramite_id, estatus, descripcion_obra, tipo_obra FROM solicitud_lc WHERE id = ?");
if (!$stmtCheck) {
    echo json_encode(['success' => false, 'message' => 'Error interno: ' . $conn->error]);
    exit;
}
$stmtCheck->bind_param("i", $solicitud_id);
$stmtCheck->execute();
$sol = $stmtCheck->get_result()->fetch_assoc();
$stmtCheck->close();

if (!$sol) {
    echo json_encode(['success' => false, 'message' => 'Solicitud no encontrada.']);
    exit;
}

if ($sol['estatus'] === 'Aprobada') {
    echo json_encode(['success' => false, 'message' => 'La solicitud ya se encuentra aprobada.']);
    exit;
}

/* No se puede aprobar una solicitud incompleta */
if (trim((string)$sol['descripcion_obra']) === '' || trim((string)$sol['tipo_obra']) === '') {
    echo json_encode(['success' => false, 'message' => 'No se puede aprobar: faltan la descripción o el tipo de obra.']);
    exit;
}

$uid = (int)$_SESSION['id'];

try {
    $stmt = $conn->prepare(
        "UPDATE solicitud_lc
            SET estatus = 'Aprobada',
                fecha_aprobacion = NOW(),
                aprobado_por = ?
          WHERE id = ? AND estatus = 'Pendiente'"
    );
    if (!$stmt) throw new Exception("Error preparar UPDATE: " . $conn->error);
    $stmt->bind_param("ii", $uid, $solicitud_id);
    if (!$stmt->execute()) throw new Exception("Error al aprobar: " . $stmt->error);
    if ($stmt->affected_rows !== 1) throw new Exception("La solicitud ya no está pendiente.");
    $stmt->close();

    //  Log de actividad
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
    $ua  = $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido';
    $accion = 'Aprobó solicitud LC';
    $det = "solicitud_id: $solicitud_id, tramite_id: " . $sol['tramite_id'];
    $log = $conn->prepare(
        "INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent)
         VALUES (?, ?, 'solicitud_lc', ?, ?, ?, ?)"
    );
    if ($log) {
        $log->bind_param("isisss", $uid, $accion, $solicitud_id, $det, $ip, $ua);
        $log->execute();
        $log->close();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Solicitud aprobada correctamente.',
        'fecha_aprobacion' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    error_log("[aprobar_solicitud_lc] " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al aprobar: ' . $e->getMessage()]);
}
