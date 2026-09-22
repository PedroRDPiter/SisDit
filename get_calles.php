<?php
require_once "php/db.php";

// Consulta las calles activas y las ordena por nombre.
$result = $conn->query("SELECT nombre FROM calles WHERE activo = 1 ORDER BY nombre");
$calles = [];
while ($row = $result->fetch_assoc()) {
    // Guarda el nombre de cada calle en el listado.
    $calles[] = $row['nombre'];
}

// Devuelve el listado de calles en formato JSON.
echo json_encode($calles);
?>