<?php
header('Content-Type: application/json; charset=utf-8');

ini_set('session.cookie_httponly', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'php/db.php';

if (!isset($_SESSION['id']) || !isset($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$folio_raw = trim($_GET['folio'] ?? '');
$partes = explode('/', $folio_raw);
if (count($partes) !== 2 || !ctype_digit($partes[0]) || !ctype_digit($partes[1])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Folio inválido']);
    exit;
}

$folio_numero = (int) $partes[0];
$folio_anio = (int) $partes[1];

$stmt = $conn->prepare("
    SELECT t.*, tt.nombre AS tipo_tramite_nombre
    FROM tramites t
    LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
    WHERE t.folio_numero = ? AND t.folio_anio = ?
    LIMIT 1
");
$stmt->bind_param('ii', $folio_numero, $folio_anio);
$stmt->execute();
$tramite = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tramite) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Trámite no encontrado']);
    exit;
}

$docMap = [
    'ine' => ['label' => 'INE / Identificación', 'campo' => 'ine_archivo'],
    'escritura' => ['label' => 'Escritura / Título', 'campo' => 'escrituras_archivo'],
    'predial' => ['label' => 'Boleta Predial', 'campo' => 'predial_archivo'],
    'formato' => ['label' => 'Formato de Constancia', 'campo' => 'formato_constancia'],
    'oficio_vobo' => ['label' => 'Oficio Visto Bueno', 'campo' => 'oficio_vobo'],
    'contrato_arrendamiento' => ['label' => 'Contrato de Arrendamiento o Escritura', 'campo' => 'contrato_arrendamiento_archivo'],
    'memoria_descriptiva' => ['label' => 'Memoria Descriptiva / Cálculo de Superficie', 'campo' => 'memoria_descriptiva_archivo'],
    'poder_notariado' => ['label' => 'Poder Notariado', 'campo' => 'poder_notariado_archivo'],
    'acta_constitutiva' => ['label' => 'Acta Constitutiva', 'campo' => 'acta_constitutiva_archivo'],
    'solicitud_por_escrito' => ['label' => 'Solicitud por Escrito', 'campo' => 'solicitud_por_escrito_archivo'],
    'licencia_de_construccion' => ['label' => 'Licencia de Construcción', 'campo' => 'licencia_de_construccion_archivo'],
    'bitacora_de_obra' => ['label' => 'Bitácora de Obra', 'campo' => 'bitacora_de_obra_archivo'],
    'foto1' => ['label' => 'Fotografía 1 del Inmueble', 'campo' => 'foto1_archivo'],
    'foto2' => ['label' => 'Fotografía 2 del Inmueble', 'campo' => 'foto2_archivo'],
];

function uploadPath($path) {
    return 'uploads/' . implode('/', array_map('rawurlencode', explode('/', $path)));
}

$documents = [];
foreach ($docMap as $type => $info) {
    $archivo = isset($tramite[$info['campo']]) ? $tramite[$info['campo']] : '';
    if (empty($archivo)) {
        continue;
    }

    $documents[] = [
        'type' => $type,
        'label' => $info['label'],
        'fileName' => basename($archivo),
        'filePath' => uploadPath($archivo),
    ];
}

$otros = isset($tramite['otros_archivos']) && trim($tramite['otros_archivos']) !== ''
    ? json_decode($tramite['otros_archivos'], true)
    : [];

if (is_array($otros)) {
    foreach ($otros as $index => $doc) {
        if (!is_array($doc) || empty($doc['archivo'])) {
            continue;
        }

        $documents[] = [
            'type' => $doc['tipo'] ?? 'otro',
            'label' => $doc['label'] ?? $doc['tipo'] ?? 'Documento adicional',
            'fileName' => basename($doc['archivo']),
            'filePath' => uploadPath($doc['archivo']),
            'sort' => $index,
        ];
    }
}

usort($documents, static function ($a, $b) {
    return ($a['sort'] ?? 1000) <=> ($b['sort'] ?? 1000);
});

echo json_encode([
    'success' => true,
    'documents' => $documents,
    'folio' => $folio_numero . '/' . $folio_anio,
    'tramite' => $tramite['tipo_tramite_nombre'] ?? '',
]);
