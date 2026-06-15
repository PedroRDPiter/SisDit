<?php
require_once "db.php";

header('Content-Type: application/json');

$q = $_POST['q'] ?? '';
$q = trim($q);

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT DISTINCT asentamiento, codigo_postal FROM codigos_postales WHERE asentamiento LIKE ? ORDER BY asentamiento LIMIT 20");
$buscar = "%{$q}%";
$stmt->bind_param("s", $buscar);
$stmt->execute();
$result = $stmt->get_result();

$respuesta = [];
while ($row = $result->fetch_assoc()) {
    $respuesta[] = ['asentamiento' => $row['asentamiento'], 'codigo_postal' => $row['codigo_postal']];
}

echo json_encode($respuesta);
$stmt->close();