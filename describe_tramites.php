<?php
// Este script solo puede ejecutarse desde la línea de comandos.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

// Cargar la conexión a la base de datos.
require_once "php/db.php";

// Consultar la estructura de la tabla tramites.
$result = $conn->query("DESCRIBE tramites");

// Mostrar el nombre y el tipo de cada columna.
echo "Columnas en tramites:<br>";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}
?>
