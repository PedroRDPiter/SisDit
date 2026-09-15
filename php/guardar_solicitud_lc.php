<?php
/*
-----------------------------------------------------------
 GUARDAR / ACTUALIZAR SOLICITUD LICENCIA DE CONSTRUCCIÓN
 Notas: Recibe los datos del modal de Solicitud de cosntrucción mediante AJAX
 y actualiza el registro en la tabla solicitud_LC en la base
-------------------------------------------------------------
*/

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "db.php";
require_once "funciones_seguridad.php";

header('Content-Type: application/json; charset=utf-8');

/*  Sesión  */
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

/*  Datos del POST  */
$tramite_id = isset($_POST['tramite_id']) ? (int)$_POST['tramite_id'] : 0;

if ($tramite_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de trámite inválido.']);
    exit;
}

/*  Datos del POST  */
/*  Verificar que el trámite existe y es Licencia de Construcción }*/
$stmtCheck = $conn->prepare(
    "SELECT id FROM tramites WHERE id = ? AND tipo_tramite_id = 7"
);
if (!$stmtCheck) {
    echo json_encode(['success' => false, 'message' => 'Error interno: ' . $conn->error]);
    exit;
}
$stmtCheck->bind_param("i", $tramite_id);
$stmtCheck->execute();
if ($stmtCheck->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Trámite no encontrado o no es Licencia de Construcción.']);
    exit;
}
$stmtCheck->close();

/*  Leer y sanitizar campos de texto */
$descripcion_obra = trim($_POST['descripcion_obra'] ?? '');
$tipo_obra        = trim($_POST['tipo_obra']        ?? '');

/*  Validar campos obligatorios */
if ($descripcion_obra === '') {
    echo json_encode(['success' => false, 'message' => 'La descripción de la obra es obligatoria.']);
    exit;
}
if (!in_array($tipo_obra, ['Construcción','Demolición','Otro'], true)) {
    echo json_encode(['success' => false, 'message' => 'Debes seleccionar un tipo de obra válido.']);
    exit;
}

/*   Peritos como JSON */
$peritos_raw = $_POST['peritos'] ?? '{}';
$peritos_decoded = json_decode($peritos_raw, true) ?? [];
$peritos_limpios = [];
foreach (['dro','estructural','especialista'] as $p) {
    $obj = $peritos_decoded[$p] ?? [];
    $peritos_limpios[$p] = [
        'nombre'   => trim($obj['nombre']   ?? ''),
        'registro' => trim($obj['registro'] ?? ''),
        'cedula'   => trim($obj['cedula']   ?? ''),
    ];
}
$peritos_json = json_encode($peritos_limpios, JSON_UNESCAPED_UNICODE);

// Superficies como JSON
// El JS ahora envía superficies como un JSON serializado con {valor, unidad} por campo
$superficies_raw = $_POST['superficies'] ?? '{}';
$superficies_decoded = json_decode($superficies_raw, true);

$claves_numericas = ['sotano','planta_baja','primer_nivel','segundo_nivel','tercer_nivel'];
$superficies_limpias = [];
foreach ($claves_numericas as $clave) {
    $obj = $superficies_decoded[$clave] ?? [];
    $valor  = isset($obj['valor'])  && $obj['valor'] !== '' ? (float)$obj['valor'] : null;
    $unidad = in_array($obj['unidad'] ?? '', ['m2','mlin']) ? $obj['unidad'] : 'm2';
    $superficies_limpias[$clave] = ['valor' => $valor, 'unidad' => $unidad];
}

// otra_area: texto libre (descripción), no numérico ni con unidad
$otra_area_raw = $superficies_decoded['otra_area'] ?? '';
// Compatibilidad: si llegara en formato antiguo {valor,unidad}, se extrae el valor
$otra_area_txt = is_array($otra_area_raw) ? trim((string)($otra_area_raw['valor'] ?? '')) : trim((string)$otra_area_raw);
$superficies_limpias['otra_area'] = $otra_area_txt;

$superficies_json = json_encode($superficies_limpias, JSON_UNESCAPED_UNICODE);

//  Urbanización como JSON
$urbanizacion_arr = $_POST['urbanizacion'] ?? [];
$validos = ['Agua potable','Electricidad','Drenaje','Pavimento','Banqueta','Guarnición'];
$urbanizacion_arr = array_values(array_intersect($urbanizacion_arr, $validos));
$urbanizacion_json = json_encode($urbanizacion_arr, JSON_UNESCAPED_UNICODE);

//  Verificar si ya existe un registro en solicitud_LC
$stmtExiste = $conn->prepare("SELECT id, estatus FROM solicitud_lc WHERE tramite_id = ?");
if (!$stmtExiste) {
    echo json_encode(['success' => false, 'message' => 'Error interno: ' . $conn->error]);
    exit;
}
$stmtExiste->bind_param("i", $tramite_id);
$stmtExiste->execute();
$solicitudExistente = $stmtExiste->get_result()->fetch_assoc();
$existe = $solicitudExistente !== null;
$stmtExiste->close();

if ($existe && $solicitudExistente['estatus'] === 'Aprobada') {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'La solicitud ya fue aprobada y no puede modificarse.']);
    exit;
}

try {
    if ($existe) {
        $sql = "UPDATE solicitud_lc SET
                    descripcion_obra = ?,
                    tipo_obra        = ?,
                    superficies      = ?,
                    urbanizacion     = ?,
                    peritos          = ?
                WHERE tramite_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Error preparar UPDATE: " . $conn->error);
        $stmt->bind_param("sssssi",
            $descripcion_obra, $tipo_obra,
            $superficies_json, $urbanizacion_json,
            $peritos_json, $tramite_id
        );
    } else {
        $sql = "INSERT INTO solicitud_lc
                    (tramite_id, descripcion_obra, tipo_obra, superficies, urbanizacion, peritos)
                VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Error preparar INSERT: " . $conn->error);
        $stmt->bind_param("isssss",
            $tramite_id, $descripcion_obra, $tipo_obra,
            $superficies_json, $urbanizacion_json, $peritos_json
        );
    }

    if (!$stmt->execute()) throw new Exception("Error al guardar: " . $stmt->error);
    $stmt->close();

    //  Log de actividad
    $uid = (int)$_SESSION['id'];
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
    $ua  = $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido';
    $accion = $existe ? 'Actualizó solicitud LC' : 'Creó solicitud LC';
    $det = "tramite_id: $tramite_id";
    $log = $conn->prepare(
        "INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent)
         VALUES (?, ?, 'solicitud_lc', ?, ?, ?, ?)"
    );
    if ($log) {
        $log->bind_param("isisss", $uid, $accion, $tramite_id, $det, $ip, $ua);
        $log->execute();
        $log->close();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Solicitud guardada correctamente.'
    ]);

} catch (Exception $e) {
    error_log("[guardar_solicitud_lc] " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
}
