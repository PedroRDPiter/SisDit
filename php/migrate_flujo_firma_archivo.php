<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/db.php';

$ruta = dirname(__DIR__) . '/migrate_flujo_firma_archivo.sql';
$sql = file_get_contents($ruta);
if ($sql === false) {
    fwrite(STDERR, "No se pudo leer la migración.\n");
    exit(1);
}

if (!$conn->multi_query($sql)) {
    fwrite(STDERR, "Error al aplicar la migración: {$conn->error}\n");
    exit(1);
}

do {
    if ($resultado = $conn->store_result()) {
        $resultado->free();
    }
} while ($conn->more_results() && $conn->next_result());

if ($conn->errno) {
    fwrite(STDERR, "Error al finalizar la migración: {$conn->error}\n");
    exit(1);
}

echo "Migración de firma y archivo aplicada correctamente.\n";
