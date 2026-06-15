<?php
header('Content-Type: application/json; charset=utf-8');

ini_set('session.cookie_httponly', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'php/db.php';
require_once 'php/funciones_seguridad.php';

if (!isset($_SESSION['id']) || !isset($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!validarCSRF()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
    exit;
}

$allowedTypes = [
    'ine',
    'escritura',
    'predial',
    'formato',
    'oficio_vobo',
    'contrato_arrendamiento',
    'memoria_descriptiva',
    'poder_notariado',
    'acta_constitutiva',
    'solicitud_por_escrito',
    'licencia_de_construccion',
    'bitacora_de_obra',
];

$allowedMimesByExtension = [
    'pdf' => ['application/pdf'],
    'jpg' => ['image/jpeg', 'image/pjpeg'],
    'jpeg' => ['image/jpeg', 'image/pjpeg'],
    'png' => ['image/png'],
    'doc' => ['application/msword', 'application/octet-stream'],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
];

$labelMap = [
    'ine' => 'INE / Identificación',
    'escritura' => 'Escritura / Título',
    'predial' => 'Boleta Predial',
    'formato' => 'Formato de Constancia',
    'oficio_vobo' => 'Oficio Visto Bueno',
    'contrato_arrendamiento' => 'Contrato de Arrendamiento o Escritura',
    'memoria_descriptiva' => 'Memoria Descriptiva / Cálculo de Superficie',
    'poder_notariado' => 'Poder Notariado',
    'acta_constitutiva' => 'Acta Constitutiva',
    'solicitud_por_escrito' => 'Solicitud por Escrito',
    'licencia_de_construccion' => 'Licencia de Construcción',
    'bitacora_de_obra' => 'Bitácora de Obra',
];

$folio_raw = trim($_POST['folio'] ?? '');
$documentType = trim($_POST['documentType'] ?? '');
$partes = explode('/', $folio_raw);

if (count($partes) !== 2 || !ctype_digit($partes[0]) || !ctype_digit($partes[1])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Folio inválido']);
    exit;
}

if (!in_array($documentType, $allowedTypes, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de documento inválido']);
    exit;
}

$folio_numero = (int) $partes[0];
$folio_anio = (int) $partes[1];

function uploadPath($path) {
    return 'uploads/' . implode('/', array_map('rawurlencode', explode('/', $path)));
}

if (!isset($_FILES['documentFile'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No se recibió ningún archivo']);
    exit;
}

$archivo = $_FILES['documentFile'];
if ($archivo['error'] !== UPLOAD_ERR_OK) {
    $mensajesError = [
        UPLOAD_ERR_INI_SIZE => 'El archivo excede el límite configurado en el servidor.',
        UPLOAD_ERR_FORM_SIZE => 'El archivo excede el límite permitido por el formulario.',
        UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente.',
        UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo.',
        UPLOAD_ERR_NO_TMP_DIR => 'No hay carpeta temporal disponible.',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en disco.',
        UPLOAD_ERR_EXTENSION => 'Una extensión de PHP bloqueó la subida.',
    ];
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $mensajesError[$archivo['error']] ?? 'Error al recibir el archivo']);
    exit;
}

$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if (!array_key_exists($extension, $allowedMimesByExtension)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
    exit;
}

$maxSize = 10485760;
if ($archivo['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande. Máximo 10MB']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = $finfo ? finfo_file($finfo, $archivo['tmp_name']) : '';
if ($finfo) {
    finfo_close($finfo);
}

if (!in_array($mimeType, $allowedMimesByExtension[$extension], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El contenido del archivo no corresponde al formato indicado']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM tramites WHERE folio_numero = ? AND folio_anio = ? LIMIT 1");
$stmt->bind_param('ii', $folio_numero, $folio_anio);
$stmt->execute();
$tramite = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tramite) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Trámite no encontrado']);
    exit;
}

$uploadsDir = __DIR__ . '/uploads';
if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo crear la carpeta de archivos']);
    exit;
}

$htaccess = $uploadsDir . '/.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Options -Indexes\nAddType application/octet-stream .php .phtml .php3 .php4 .php5\nphp_flag engine off\n");
}

$folderName = '.private+' . $folio_numero . '+' . $folio_anio . '+' . $documentType;
$folder = $uploadsDir . '/' . $folderName;

if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo crear la carpeta del documento']);
    exit;
}

$folderHtaccess = $folder . '/.htaccess';
if (!file_exists($folderHtaccess)) {
    file_put_contents($folderHtaccess, "Options -Indexes\nAddType application/octet-stream .php .phtml .php3 .php4 .php5\nphp_flag engine off\n");
}

$fileName = $documentType . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
$destination = $folder . '/' . $fileName;

if (!move_uploaded_file($archivo['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo guardar el archivo']);
    exit;
}

$storedPath = $folderName . '/' . $fileName;
$otros = isset($tramite['otros_archivos']) && trim($tramite['otros_archivos']) !== ''
    ? json_decode($tramite['otros_archivos'], true)
    : [];

if (!is_array($otros)) {
    $otros = [];
}

foreach ($otros as $index => $doc) {
    if (is_array($doc) && ($doc['tipo'] ?? '') === $documentType) {
        unset($otros[$index]);
    }
}

$otros[] = [
    'tipo' => $documentType,
    'label' => $labelMap[$documentType] ?? $documentType,
    'archivo' => $storedPath,
    'fecha' => date('Y-m-d H:i:s'),
    'usuario_id' => (int) $_SESSION['id'],
];

$json = json_encode(array_values($otros), JSON_UNESCAPED_UNICODE);
if ($json === false) {
    unlink($destination);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo preparar la información del documento']);
    exit;
}

$stmt = $conn->prepare("UPDATE tramites SET otros_archivos = ? WHERE id = ?");
$stmt->bind_param('si', $json, $tramite['id']);

if (!$stmt->execute()) {
    unlink($destination);
    $stmt->close();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo guardar el documento en la base de datos']);
    exit;
}

$stmt->close();

echo json_encode([
    'success' => true,
    'message' => 'Documento guardado correctamente',
    'filePath' => uploadPath($storedPath),
    'fileName' => basename($storedPath),
]);
