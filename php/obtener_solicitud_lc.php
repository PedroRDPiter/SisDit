<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Cargar la conexión a la base de datos y las funciones de seguridad.
require_once "db.php";
require_once "funciones_seguridad.php";

// Todas las respuestas de este endpoint se envían en formato JSON.
header('Content-Type: application/json; charset=utf-8');

// Iniciar la sesión para validar el usuario autenticado.
if (session_status() === PHP_SESSION_NONE) session_start();

// Rechazar solicitudes de usuarios que no hayan iniciado sesión.
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesión expirada.']);
    exit;
}

// Obtener y validar el identificador del trámite recibido por GET.
$tramite_id = isset($_GET['tramite_id']) ? (int)$_GET['tramite_id'] : 0;

if ($tramite_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

// Consultar la solicitud y los datos necesarios para verificar permisos.
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

// Informar que no existe una solicitud asociada al trámite.
if (!$row) {
    echo json_encode(['success' => true, 'data' => null]);
    exit;
}

// Verificar que el usuario tenga acceso al trámite solicitado.
if (!puedeAccederTramite($row)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sin permisos para consultar esta solicitud.']);
    exit;
}

// No exponer información interna usada únicamente para la autorización.
unset($row['usuario_creador_id']);

// Decodificar los campos JSON almacenados en la base de datos.
$row['urbanizacion'] = json_decode($row['urbanizacion'] ?? '[]', true) ?? [];
$row['superficies']  = json_decode($row['superficies']  ?? '{}', true) ?? [];
$row['peritos']      = json_decode($row['peritos']      ?? '{}', true) ?? [];

// Formatear el folio de solicitud para mostrarlo como número/año.
$row['folio_solicitud'] = $row['folio_numero'] !== null
    ? str_pad($row['folio_numero'], 3, '0', STR_PAD_LEFT) . '/' . $row['folio_anio']
    : null;

echo json_encode(['success' => true, 'data' => $row]);
