<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once "php/db.php";

// Verificar columnas en tramites
$result = $conn->query("DESCRIBE tramites");
echo "Columnas en tramites:<br>";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}
?>
