<?php
// =====================================================
// OBTENER DATOS DE UN TRÁMITE (AJAX)
// Devuelve JSON con los datos de un trámite por su folio.
// Usado por los modales de detalle en los dashboards
// =====================================================
/**
 * Obtener datos actualizados de un trámite
 * Ruta: php/obtener_datos_tramite.php
 */

require_once "db.php";
require_once "funciones_seguridad.php";

// Todas las respuestas de este endpoint se envían en formato JSON.
header('Content-Type: application/json; charset=utf-8');

// Verificar que exista una sesión activa antes de consultar información.
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
    exit;
}

if (!isset($_GET['folio'])) {
    echo json_encode(['success' => false, 'message' => 'Folio no proporcionado']);
    exit;
}

// El folio debe tener el formato número/año, por ejemplo: 123/2024.
$folio = $_GET['folio'];

// Validar formato folio
if (!preg_match('/^(\d+)\/(\d{4})$/', $folio, $matches)) {
    echo json_encode(['success' => false, 'message' => 'Formato de folio inválido']);
    exit;
}

$folio_numero = (int) $matches[1];
$folio_anio = (int) $matches[2];

// Obtener TODOS los trámites (principal + subtramites) que comparten el mismo folio
// (después del refactor, los adicionales comparten el folio_numero/folio_anio exacto)
// Se utiliza una consulta preparada para evitar inyección SQL.
$sql = "SELECT 
            t.id, t.usuario_creador_id, t.folio_numero, t.folio_anio, t.estatus,
            t.propietario, t.direccion, t.localidad, t.telefono, t.correo,
            t.ine_archivo, t.escrituras_archivo, t.titulo_archivo,
            t.predial_archivo, t.formato_constancia,
            t.foto1_archivo, t.foto2_archivo, t.croquis_archivo,
            t.comentario_sin_doc, t.numero_asignado, t.tipo_asignacion,
            t.referencia_anterior, t.entre_calle1, t.entre_calle2, t.cuenta_catastral,
            t.manzana, t.lote, t.fecha_constancia,
            t.tipo_tramite_id, tt.nombre AS tipo_tramite_nombre,
            t.tramite_principal_id
        FROM tramites t
        LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
        WHERE t.folio_numero = ? AND t.folio_anio = ?
        ORDER BY 
            CASE WHEN t.tramite_principal_id IS NULL THEN 0 ELSE 1 END,
            t.id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $folio_numero, $folio_anio);
$stmt->execute();
$result = $stmt->get_result();

// Informar si no existe ningún trámite con el folio solicitado.
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Trámite no encontrado']);
    exit;
}

$tramites = [];
while ($row = $result->fetch_assoc()) {
    // Comprobar los permisos de cada trámite, incluidos los subtrámites.
    if (!puedeAccederTramite($row)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
        exit;
    }
    $tramites[] = $row;
}

// Devolver el listado y la cantidad total de registros encontrados.
echo json_encode([
    'success' => true,
    'tramites' => $tramites,
    'count' => count($tramites)
]);
