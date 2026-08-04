<?php
require_once "php/db.php";

header('Content-Type: application/json; charset=utf-8');

$cp = isset($_GET['cp']) ? preg_replace('/\D/', '', $_GET['cp']) : '';

if (strlen($cp) !== 5) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT DISTINCT asentamiento
        FROM codigos_postales
        WHERE codigo_postal = ?
        ORDER BY asentamiento
    ");
    $stmt->bind_param("s", $cp);
    $stmt->execute();
    $result = $stmt->get_result();

    $colonias = [];
    while ($row = $result->fetch_assoc()) {
        $colonias[] = $row['asentamiento'];
    }

    echo json_encode($colonias, JSON_UNESCAPED_UNICODE);
    $stmt->close();
} catch (Throwable $error) {
    error_log('Error al consultar colonias: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode([]);
}
