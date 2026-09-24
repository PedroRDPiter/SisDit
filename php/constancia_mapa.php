<?php
declare(strict_types=1);

require_once __DIR__ . '/sesion.php';
iniciarSesionSegura();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/funciones_seguridad.php';
require_once __DIR__ . '/OficiosAdmin.php';

if (
    !isset($_SESSION['id'], $_SESSION['rol'])
    || !esPersonalAutorizado()
) {
    http_response_code(403);
    exit('Acceso denegado');
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    http_response_code(400);
    exit('Trámite inválido');
}

$stmt = $conn->prepare("SELECT id, estatus, otros_archivos FROM tramites WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$tramite = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tramite || !in_array($tramite['estatus'], ['Aprobado', 'Entregado y archivado'], true)) {
    http_response_code(404);
    exit('No hay constancia aprobada disponible');
}

$documento = documentoFirmadoDigital($tramite);
if (!$documento) {
    http_response_code(404);
    exit('La constancia PDF firmada todavía no está disponible');
}

$ruta = (string)($documento['archivo'] ?? '');
$base = realpath(dirname(__DIR__) . '/.private/oficios');
$archivo = realpath(dirname(__DIR__) . '/' . $ruta);
if (
    $base === false
    || $archivo === false
    || !str_starts_with($archivo, $base . DIRECTORY_SEPARATOR)
    || !is_file($archivo)
) {
    http_response_code(404);
    exit('Archivo PDF no encontrado');
}

header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');
header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($archivo));
header('Content-Disposition: inline; filename="constancia_tramite_' . (int)$id . '.pdf"');
readfile($archivo);
exit;
