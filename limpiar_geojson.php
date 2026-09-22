<?php
// Permitir la ejecución únicamente desde la línea de comandos.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

// Definir la ruta del archivo GeoJSON que se limpiará.
$geojsonPath = __DIR__ . '/Geojson/TRAMITES_reprojected.geojson';
if (file_exists($geojsonPath)) {
    // Leer y convertir el contenido del archivo a un arreglo asociativo.
    $geojson = json_decode(file_get_contents($geojsonPath), true);
    if ($geojson && isset($geojson['features'])) {
        // Recorrer las entidades y eliminar la propiedad "tramites".
        foreach ($geojson['features'] as &$feature) {
            if (isset($feature['properties']['tramites'])) {
                unset($feature['properties']['tramites']);
            }
        }
    }

    // Guardar el GeoJSON actualizado con formato legible y caracteres Unicode.
    file_put_contents($geojsonPath, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "Limpieza completada.";
} else {
    // Informar si el archivo de origen no existe.
    echo "Archivo no encontrado.";
}
?>
