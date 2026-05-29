<?php
// =====================================================
// ELIMINAR TRÁMITE ADICIONAL
// Elimina un registro de tramites_adicionales y actualiza el campo cantidad del registro principal
// =====================================================

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "db.php";
require_once "funciones_seguridad.php";

header('Content-Type: application/json; charset=utf-8');

// ── Iniciar sesión ──
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Autenticación ──────────────────────────────────────────
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesión expirada. Recarga la página.']);
    exit;
}

if (!esVerificador() && !esAdministrador() && !esVentanilla()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sin permisos para esta acción.']);
    exit;
}

// ── CSRF ──────────────────────────────────────────────────
$csrfEnviado  = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$csrfSesion   = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
if (empty($csrfEnviado) || empty($csrfSesion) || !hash_equals($csrfSesion, $csrfEnviado)) {
    echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido. Recarga la página e intenta de nuevo.']);
    exit;
}

// ── Datos del POST ─────────────────────────────────────────
$adicionalId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($adicionalId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de adicional no válido.']);
    exit;
}

try {
    $conn->begin_transaction();

    // ── Obtener el adicional a eliminar ─────────────────────
    $stmtGet = $conn->prepare("
        SELECT ta.*, t.folio_numero, t.folio_anio
        FROM tramites_adicionales ta
        INNER JOIN tramites t ON ta.tramite_principal_id = t.id
        WHERE ta.id = ?
    ");
    if (!$stmtGet) {
        throw new Exception("Error BD: " . $conn->error);
    }
    $stmtGet->bind_param("i", $adicionalId);
    $stmtGet->execute();
    $res = $stmtGet->get_result();

    if ($res->num_rows === 0) {
        throw new Exception("Adicional no encontrado: $adicionalId");
    }
    $adicional = $res->fetch_assoc();
    $stmtGet->close();

    $tramiteId = $adicional['tramite_principal_id'];
    $folioNumero = $adicional['folio_numero'];
    $folioAnio = $adicional['folio_anio'];

    // ── Eliminar el adicional ───────────────────────────────
    $stmtDelete = $conn->prepare("DELETE FROM tramites_adicionales WHERE id = ?");
    if (!$stmtDelete) {
        throw new Exception("Error prepare DELETE: " . $conn->error);
    }
    $stmtDelete->bind_param("i", $adicionalId);
    if (!$stmtDelete->execute()) {
        throw new Exception("Error DELETE: " . $stmtDelete->error);
    }
    $stmtDelete->close();

    // ── Actualizar el campo cantidad del registro principal ─────
    // Obtener la cantidad actual
    $stmtCurrent = $conn->prepare("SELECT cantidad FROM tramites WHERE folio_numero = ? AND folio_anio = ?");
    $stmtCurrent->bind_param("ii", $folioNumero, $folioAnio);
    $stmtCurrent->execute();
    $resultCurrent = $stmtCurrent->get_result();
    $rowCurrent = $resultCurrent->fetch_assoc();
    $stmtCurrent->close();

    $currentCantidad = $rowCurrent['cantidad'] ?? 1;
    $newCantidad = max(1, $currentCantidad - 1); // Nunca menos de 1

    $stmtUpdate = $conn->prepare("UPDATE tramites SET cantidad = ? WHERE folio_numero = ? AND folio_anio = ?");
    if (!$stmtUpdate) {
        throw new Exception("Error prepare UPDATE: " . $conn->error);
    }
    $stmtUpdate->bind_param("iii", $newCantidad, $folioNumero, $folioAnio);
    if (!$stmtUpdate->execute()) {
        throw new Exception("Error UPDATE: " . $stmtUpdate->error);
    }
    $stmtUpdate->close();

    // ── Historial ───────────────────────────────────────────
    $uid = (int) $_SESSION['id'];
    $stmtH = $conn->prepare("
        INSERT INTO historial_tramites
          (tramite_id, usuario_id, accion, estatus_nuevo, comentario)
        VALUES (?, ?, ?, ?, ?)
    ");
    if ($stmtH) {
        $accionHist = 'Eliminado adicional';
        $estatusNuevo = 'En revisión'; // Mantener el estatus actual
        $comentario = "Se eliminó un registro adicional. Nueva cantidad: $newCantidad";
        $stmtH->bind_param("issss", $tramiteId, $uid, $accionHist, $estatusNuevo, $comentario);
        $stmtH->execute();
        $stmtH->close();
    }

    // ── Log ───────────────────────────────────────────────
    $ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'desconocida';
    $ua  = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'desconocido';
    $det = "Folio: $folioNumero/$folioAnio | Eliminado adicional ID: $adicionalId | Cantidad anterior: $currentCantidad | Nueva cantidad: $newCantidad";
    $stmtL = $conn->prepare("
        INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent)
        VALUES (?, 'Eliminó adicional', 'tramites_adicionales', ?, ?, ?, ?)
    ");
    if ($stmtL) {
        $stmtL->bind_param("issss", $uid, $adicionalId, $det, $ip, $ua);
        $stmtL->execute();
        $stmtL->close();
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Adicional eliminado correctamente',
        'nueva_cantidad' => $newCantidad
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("[eliminar_adicional] " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>