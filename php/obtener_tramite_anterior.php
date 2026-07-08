<?php
// =====================================================
// OBTENER TRAMITE ANTERIOR DEL MISMO PREDIO (AJAX)
// Busca si el predio ya tuvo tramites antes para prellenar datos.
// =====================================================

ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

function responder_json($payload, $status_code = 200) {
    if (ob_get_length()) {
        ob_clean();
    }
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

session_start();
require_once "db.php";

if (!isset($_SESSION['id']) || !isset($_SESSION['usuario'])) {
    responder_json(['error' => 'No autorizado'], 401);
}

$folio = isset($_GET['folio']) ? trim($_GET['folio']) : '';
$propietario = isset($_GET['propietario']) ? trim($_GET['propietario']) : '';
$tipo_tramite_id = isset($_GET['tipo_tramite_id']) ? intval($_GET['tipo_tramite_id']) : 0;
$incluir_constancia = isset($_GET['incluir_constancia']) ? $_GET['incluir_constancia'] === 'true' : false;
$buscar_por_folio_salida = isset($_GET['buscar_por_folio_salida']) ? $_GET['buscar_por_folio_salida'] === 'true' : false;

if (empty($folio) && empty($propietario)) {
    responder_json(['error' => 'Se requiere folio o propietario'], 400);
}

$sql = "SELECT
            t.*,
            CONCAT(LPAD(t.folio_numero, 3, '0'), '/', t.folio_anio) as folio_formateado,
            CASE
                WHEN t.folio_salida_numero IS NULL OR t.folio_salida_anio IS NULL THEN ''
                ELSE CONCAT(LPAD(t.folio_salida_numero, 3, '0'), '/', t.folio_salida_anio)
            END as folio_salida_formateado,
            tt.nombre as tipo_tramite_nombre
        FROM tramites t
        LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($folio)) {
    $partes = explode('/', $folio);
    if (count($partes) !== 2) {
        responder_json(['error' => 'Formato de folio invalido'], 400);
    }

    if ($buscar_por_folio_salida) {
        $sql .= " AND t.folio_salida_numero = ? AND t.folio_salida_anio = ?";
    } else {
        $sql .= " AND t.folio_numero = ? AND t.folio_anio = ?";
    }
    $params[] = intval($partes[0]);
    $params[] = intval($partes[1]);
    $types .= "ii";
} elseif (!empty($propietario) && $tipo_tramite_id > 0) {
    $sql .= " AND t.propietario LIKE ? AND t.tipo_tramite_id = ?
              AND t.estatus IN ('Aprobado', 'Aprobado por Verificador')";
    $params[] = '%' . $propietario . '%';
    $params[] = $tipo_tramite_id;
    $types .= "si";
} else {
    responder_json(['error' => 'Parametros insuficientes'], 400);
}

$sql .= " ORDER BY t.fecha_ingreso DESC, t.id DESC LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Error en prepare obtener_tramite_anterior: " . $conn->error);
    responder_json(['error' => 'No se pudo preparar la busqueda del tramite'], 500);
}

if (!empty($params) && !$stmt->bind_param($types, ...$params)) {
    error_log("Error en bind_param obtener_tramite_anterior: " . $stmt->error);
    responder_json(['error' => 'No se pudieron aplicar los parametros de busqueda'], 500);
}

if (!$stmt->execute()) {
    error_log("Error en execute obtener_tramite_anterior: " . $stmt->error);
    responder_json(['error' => 'No se pudo consultar el tramite'], 500);
}

$result = $stmt->get_result();
$tramite = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$tramite) {
    responder_json(['error' => 'No se encontro el tramite'], 404);
}

$datos = [
    'success' => true,
    'tramite' => [
        'folio' => $tramite['folio_formateado'],
        'folio_salida' => $tramite['folio_salida_formateado'] ?? '',
        'propietario' => $tramite['propietario'],
        'direccion' => $tramite['direccion'],
        'localidad' => $tramite['localidad'],
        'colonia' => $tramite['colonia'],
        'cp' => $tramite['cp'],
        'calle' => $tramite['calle'],
        'entre_calle1' => $tramite['entre_calle1'],
        'entre_calle2' => $tramite['entre_calle2'],
        'cuenta_catastral' => $tramite['cuenta_catastral'],
        'superficie' => $tramite['superficie'],
        'lat' => $tramite['lat'],
        'lng' => $tramite['lng'],
        'solicitante' => $tramite['solicitante'],
        'telefono' => $tramite['telefono'],
        'correo' => $tramite['correo'],
        'observaciones' => $tramite['observaciones'],
        'archivos' => [
            'ine_archivo' => $tramite['ine_archivo'],
            'escrituras_archivo' => $tramite['escrituras_archivo'] ?: $tramite['titulo_archivo'],
            'predial_archivo' => $tramite['predial_archivo'],
            'formato_constancia' => $tramite['formato_constancia'],
            'foto1_archivo' => $tramite['foto1_archivo'],
            'foto2_archivo' => $tramite['foto2_archivo'],
            'croquis_archivo' => $tramite['croquis_archivo']
        ],
        'constancia' => $incluir_constancia ? [
            'numero_asignado' => $tramite['numero_asignado'],
            'tipo_asignacion' => $tramite['tipo_asignacion'],
            'referencia_anterior' => $tramite['referencia_anterior'],
            'entre_calle1' => $tramite['entre_calle1'],
            'entre_calle2' => $tramite['entre_calle2'],
            'manzana' => $tramite['manzana'],
            'lote' => $tramite['lote'],
            'fecha_constancia' => $tramite['fecha_constancia'],
            'cuenta_catastral_constancia' => $tramite['cuenta_catastral']
        ] : null
    ]
];

$conn->close();
responder_json($datos);
