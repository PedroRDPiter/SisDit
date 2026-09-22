<?php
require_once "db.php";

// Indica que la respuesta del endpoint tendrá formato JSON.
header('Content-Type: application/json');

// Obtiene y limpia el texto de búsqueda enviado por POST.
$q = $_POST['q'] ?? '';
$q = trim($q);

// Evita consultas innecesarias para búsquedas demasiado cortas.
if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Busca hasta 20 asentamientos que coincidan parcialmente con el texto.
$stmt = $conn->prepare("SELECT DISTINCT asentamiento, codigo_postal FROM codigos_postales WHERE asentamiento LIKE ? ORDER BY asentamiento LIMIT 20");
$buscar = "%{$q}%";
$stmt->bind_param("s", $buscar);
$stmt->execute();
$result = $stmt->get_result();

// Convierte los resultados de la consulta en una respuesta JSON.
$respuesta = [];
while ($row = $result->fetch_assoc()) {
    $respuesta[] = ['asentamiento' => $row['asentamiento'], 'codigo_postal' => $row['codigo_postal']];
}

echo json_encode($respuesta);
// Libera los recursos asociados a la consulta.
$stmt->close();