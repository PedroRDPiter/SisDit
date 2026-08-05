<?php
// =====================================================
// ASIGNAR CROQUIS A TRÁMITE
// Asocia un archivo de croquis ya subido al trámite indicado
// =====================================================
/**
 * ASIGNAR CROQUIS DE TRÁMITE ANTERIOR AL ACTUAL
 * Reutiliza el archivo de croquis ya existente en uploads/
 * sin duplicarlo físicamente.
 */
error_reporting(0);
ini_set('display_errors', 0);
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
if (!validarCSRF()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de seguridad invalido']);
    exit;
}

$id_destino     = isset($_POST['id_destino'])     ? (int)$_POST['id_destino']      : 0;
$folio_destino  = isset($_POST['folio_destino'])  ? trim($_POST['folio_destino'])  : '';
$croquis_archivo = isset($_POST['croquis_archivo']) ? trim($_POST['croquis_archivo']) : '';

// Validar destino: preferir id del subtrámite
if ($id_destino <= 0 && !preg_match('/^(\d{1,4})\/(\d{4})$/', $folio_destino, $m)) {
    echo json_encode(['success' => false, 'message' => 'Destino invalido (id o folio)']);
    exit;
}

// Validar que el archivo existe
if (empty($croquis_archivo)) {
    echo json_encode(['success' => false, 'message' => 'Archivo de croquis no especificado']);
    exit;
}

$appRoot = realpath(__DIR__ . '/..');
$ruta = $appRoot !== false ? realpath($appRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $croquis_archivo)) : false;
$privateRoot = realpath(__DIR__ . '/../.private');
$uploadsRoot = realpath(__DIR__ . '/../uploads');
$enRaizPermitida = $ruta !== false && (
    ($privateRoot !== false && str_starts_with($ruta, rtrim($privateRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)) ||
    ($uploadsRoot !== false && str_starts_with($ruta, rtrim($uploadsRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR))
);
if (!$enRaizPermitida || !is_file($ruta)) {
    echo json_encode(['success' => false, 'message' => 'El archivo del croquis no existe']);
    exit;
}
$mimeCroquis = (new finfo(FILEINFO_MIME_TYPE))->file($ruta);
if (!in_array($mimeCroquis, ['image/jpeg', 'image/png', 'image/webp'], true)) {
    echo json_encode(['success' => false, 'message' => 'El archivo no es una imagen valida']);
    exit;
}
$croquis_archivo = str_replace(DIRECTORY_SEPARATOR, '/', substr($ruta, strlen($appRoot) + 1));

// Actualizar el trámite destino con el mismo nombre de archivo
if ($id_destino > 0) {
    $stmt = $conn->prepare("UPDATE tramites SET croquis_archivo = ? WHERE id = ?");
    $stmt->bind_param("si", $croquis_archivo, $id_destino);
} else {
    $folio_numero = (int)$m[1];
    $folio_anio   = (int)$m[2];
    // Sin id: asignar a la fila principal del grupo (compatibilidad)
    $stmt = $conn->prepare("UPDATE tramites SET croquis_archivo = ? WHERE id = (SELECT id FROM (SELECT id FROM tramites WHERE folio_numero = ? AND folio_anio = ? ORDER BY (tramite_principal_id IS NULL) DESC, id ASC LIMIT 1) x)");
    $stmt->bind_param("sii", $croquis_archivo, $folio_numero, $folio_anio);
}
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar en BD']);
    exit;
}
$stmt->close();

// Log
$uid = (int)$_SESSION['id'];
$ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$destino_log = $id_destino > 0 ? "id $id_destino" : $folio_destino;
$det = "Croquis anterior asignado al destino: $destino_log | Archivo: $croquis_archivo";
$log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, detalles, ip_address) VALUES (?, 'Asignar croquis anterior', 'tramites', ?, ?)");
$log->bind_param("iss", $uid, $det, $ip);
$log->execute();
$log->close();

echo json_encode([
    'success'  => true,
    'message'  => 'Croquis asignado correctamente',
    'archivo'  => $croquis_archivo
]);
