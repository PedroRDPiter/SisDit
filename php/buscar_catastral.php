<?php
// =====================================================
// BUSCAR CUENTA CATASTRAL (AJAX)
// Busca predios por su número de cuenta catastral para autocompletar
// =====================================================
require_once "db.php";
require_once "funciones_seguridad.php";

// Todas las respuestas de este endpoint se envían en formato JSON.
header('Content-Type: application/json');

// Verifica que la solicitud provenga de un usuario autenticado.
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Procesa la búsqueda únicamente cuando se recibe una cuenta catastral.
if(isset($_GET['cuenta'])){

    // Limpia los espacios innecesarios antes de consultar la base de datos.
    $cuenta = trim($_GET['cuenta']);

    // Utiliza una consulta preparada para evitar inyección SQL.
    $stmt = $conn->prepare("SELECT utm_x, utm_y FROM tramites WHERE cuenta_catastral = ?");
    $stmt->bind_param("s", $cuenta);
    $stmt->execute();
    $result = $stmt->get_result();

    // Devuelve las coordenadas encontradas o null si no existe la cuenta.
    if($result->num_rows > 0){
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(null);
    }

    // Libera los recursos utilizados por la consulta y la conexión.
    $stmt->close();
    $conn->close();
}
?>
