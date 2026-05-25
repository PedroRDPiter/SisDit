<?php
require 'C:/xampp/htdocs/desarrollo/php/db.php';

echo "=== SHOW INDEX FOR folio columns ===\n";
$r = $conn->query("SHOW INDEX FROM tramites WHERE Key_name LIKE '%folio%'");
while ($row = $r->fetch_assoc()) {
    print_r($row);
}

echo "\n=== Exact unique constraint ===\n";
$r2 = $conn->query("SHOW CREATE TABLE tramites");
$row2 = $r2->fetch_assoc();
$create = $row2['Create Table'];
if (preg_match('/UNIQUE KEY `[^`]+` \(`folio_numero`, `folio_anio`\)/', $create, $m)) {
    echo "Found: " . $m[0] . "\n";
} else {
    echo "Could not extract exact UNIQUE clause. Full create snippet:\n";
    echo substr($create, strpos($create, 'UNIQUE') ?: 0, 300);
}
?>