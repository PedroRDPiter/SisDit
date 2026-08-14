<?php
declare(strict_types=1);

require_once __DIR__ . '/sesion.php';
iniciarSesionSegura();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/funciones_seguridad.php';

if (!isset($_SESSION['id'], $_SESSION['rol'])) {
    http_response_code(401);
    exit('No autenticado');
}

$scope = $_GET['scope'] ?? '';
$requested = rawurldecode((string) ($_GET['path'] ?? ''));
if (!in_array($scope, ['uploads', 'private'], true) || $requested === '' || str_contains($requested, "\0")) {
    http_response_code(400);
    exit('Ruta invalida');
}

$base = $scope === 'uploads'
    ? realpath(__DIR__ . '/../uploads')
    : realpath(__DIR__ . '/../.private');
$candidate = $base !== false ? realpath($base . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $requested)) : false;

if ($base === false || $candidate === false || !is_file($candidate)) {
    http_response_code(404);
    exit('Archivo no encontrado');
}

$prefix = rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
if (!str_starts_with($candidate, $prefix)) {
    http_response_code(403);
    exit('Ruta no permitida');
}

$relative = str_replace(DIRECTORY_SEPARATOR, '/', substr($candidate, strlen($prefix)));
$storedPath = $scope === 'private' ? '.private/' . $relative : $relative;
$basename = basename($relative);

$columns = [
    'ine_archivo', 'escrituras_archivo', 'titulo_archivo', 'predial_archivo',
    'formato_constancia', 'oficio_vobo', 'foto_predio_archivo', 'carta_poder',
    'foto1_archivo', 'foto2_archivo', 'croquis_archivo'
];
$columnList = implode(', ', $columns);
$matches = "? IN ($columnList) OR ? IN ($columnList) OR ? IN ($columnList)";
$sql = "SELECT id, usuario_creador_id FROM tramites WHERE ($matches OR otros_archivos LIKE ?) LIMIT 1";
$stmt = $conn->prepare($sql);
$prefixedPath = $scope === 'uploads' ? 'uploads/' . $relative : $storedPath;
$jsonPath = '%"archivo"%' . $relative . '%';
$stmt->bind_param('ssss', $storedPath, $prefixedPath, $basename, $jsonPath);
$stmt->execute();
$tramite = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tramite) {
    http_response_code(404);
    exit('Archivo no asociado');
}

$rol = (string) $_SESSION['rol'];
$esPersonal = in_array($rol, ['Administrador', 'Ventanilla', 'Verificador'], true);
$esPropietario = (int) $tramite['usuario_creador_id'] === (int) $_SESSION['id'];
if (!$esPersonal && !$esPropietario) {
    http_response_code(403);
    exit('Acceso denegado');
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($candidate) ?: 'application/octet-stream';
$inline = str_starts_with($mime, 'image/') || $mime === 'application/pdf';

header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($candidate));
header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . rawurlencode($basename) . '"');
readfile($candidate);
exit;
