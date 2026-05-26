<?php
require "db.php";
require "funciones_seguridad.php";

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$tramite_id = null;
if (isset($_GET['tramite_id']) && is_numeric($_GET['tramite_id'])) {
    $tramite_id = (int)$_GET['tramite_id'];
} elseif (isset($_GET['folio']) && !empty($_GET['folio'])) {
    $folio = $_GET['folio'];
    $partes = explode('/', $folio);
    if (count($partes) === 2) {
        $folio_numero = (int)$partes[0];
        $folio_anio = (int)$partes[1];
        $stmt = $conn->prepare("SELECT id FROM tramites WHERE folio_numero = ? AND folio_anio = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $folio_numero, $folio_anio);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 1) {
                $row = $res->fetch_assoc();
                $tramite_id = (int)$row['id'];
            }
            $stmt->close();
        }
    }
}

if (!$tramite_id) {
    echo json_encode(['error' => 'ID de trámite no proporcionado o no válido']);
    exit;
}

$stmt = $conn->prepare("
    SELECT ta.*, tt.nombre AS tipo_tramite_nombre, tt.codigo AS tipo_tramite_codigo
    FROM tramites_adicionales ta
    LEFT JOIN tipos_tramite tt ON ta.tipo_tramite_id = tt.id
    WHERE ta.tramite_principal_id = ?
    ORDER BY ta.folio_numero_adicional ASC
");
if (!$stmt) {
    echo json_encode(['error' => 'Error de preparación: ' . $conn->error]);
    exit;
}
$stmt->bind_param("i", $tramite_id);
$stmt->execute();
$result = $stmt->get_result();

$adicionales = [];
while ($row = $result->fetch_assoc()) {
    $adicionales[] = $row;
}
$stmt->close();

echo json_encode(['adicionales' => $adicionales]);
?>