<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Cargar la conexión a la base de datos del proyecto.
require __DIR__ . '/php/db.php';

try {
    // Eliminar la restricción UNIQUE para permitir folios compartidos.
    $conn->query('ALTER TABLE tramites DROP INDEX uk_folio');
    echo "Dropped UNIQUE uk_folio successfully.\n";
    
    // Recrear el índice como no único para conservar el rendimiento de las búsquedas.
    $conn->query('ALTER TABLE tramites ADD INDEX uk_folio (folio_numero, folio_anio)');
    echo "Added regular INDEX uk_folio (folio_numero, folio_anio).\n";
    
    // Verificar que el índice exista y tenga la configuración esperada.
    echo "\n=== VERIFICATION ===\n";
    $r = $conn->query("SHOW INDEX FROM tramites WHERE Key_name = 'uk_folio'");
    while ($row = $r->fetch_assoc()) {
        // Mostrar la información del índice para facilitar la comprobación.
        echo "Key: {$row['Key_name']} | Non_unique: {$row['Non_unique']} | Column: {$row['Column_name']}\n";
    }
    // Confirmar que la migración finalizó correctamente.
    echo "\nSUCCESS: uk_folio is now a regular (non-unique) index. Multiple tramites can share the same ingreso folio.\n";
} catch (Exception $e) {
    // Informar cualquier error ocurrido durante la migración.
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
