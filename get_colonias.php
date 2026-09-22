<?php
// Carga la conexión con la base de datos.
require_once "php/db.php";

// Indica que la respuesta se devolverá en formato JSON y con codificación UTF-8.
header('Content-Type: application/json; charset=utf-8');

// Obtiene únicamente los dígitos enviados como código postal.
$cp = isset($_GET['cp']) ? preg_replace('/\D/', '', $_GET['cp']) : '';

// El código postal debe contener exactamente cinco dígitos.
if (strlen($cp) !== 5) {
    echo json_encode([]);
    exit;
}

try {
    // Consulta las colonias asociadas al código postal, sin duplicados.
    $stmt = $conn->prepare("
        SELECT DISTINCT asentamiento
        FROM codigos_postales
        WHERE codigo_postal = ?
        ORDER BY asentamiento
    ");
    $stmt->bind_param("s", $cp);
    $stmt->execute();
    $result = $stmt->get_result();

    // Construye la lista de colonias para enviarla como JSON.
    $colonias = [];
    while ($row = $result->fetch_assoc()) {
        $colonias[] = $row['asentamiento'];
    }

    echo json_encode($colonias, JSON_UNESCAPED_UNICODE);
    $stmt->close();
} catch (Throwable $error) {
    // Registra el error y devuelve una respuesta vacía al cliente.
    error_log('Error al consultar colonias: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode([]);
}
