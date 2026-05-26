<?php
require 'C:/xampp/htdocs/desarrollo/php/db.php';

try {
    // First drop the unique
    $conn->query('ALTER TABLE tramites DROP INDEX uk_folio');
    echo "Dropped UNIQUE uk_folio successfully.\n";
    
    // Add back as regular (non-unique) index for performance
    $conn->query('ALTER TABLE tramites ADD INDEX uk_folio (folio_numero, folio_anio)');
    echo "Added regular INDEX uk_folio (folio_numero, folio_anio).\n";
    
    echo "\n=== VERIFICATION ===\n";
    $r = $conn->query("SHOW INDEX FROM tramites WHERE Key_name = 'uk_folio'");
    while ($row = $r->fetch_assoc()) {
        echo "Key: {$row['Key_name']} | Non_unique: {$row['Non_unique']} | Column: {$row['Column_name']}\n";
    }
    echo "\nSUCCESS: uk_folio is now a regular (non-unique) index. Multiple tramites can share the same ingreso folio.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>