<?php
error_reporting(0);
ini_set('display_errors', 0);
if (ob_get_length()) ob_clean();
if (session_status() === PHP_SESSION_NONE) session_start();

require_once "funciones_seguridad.php";

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesion expirada']);
    exit;
}

if (!esVerificador() && !esAdministrador() && !esVentanilla()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

$path = __DIR__ . '/../Geojson/calles.shp';
if (!is_file($path)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'No se encontro calles.shp']);
    exit;
}

$binary = file_get_contents($path);
if ($binary === false || strlen($binary) < 100) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Shapefile de calles invalido']);
    exit;
}

function shpInt32LE($data, $offset) {
    return unpack('V', substr($data, $offset, 4))[1];
}

function shpInt32BE($data, $offset) {
    return unpack('N', substr($data, $offset, 4))[1];
}

function shpDoubleLE($data, $offset) {
    return unpack('e', substr($data, $offset, 8))[1];
}

$features = [];
$offset = 100;
$length = strlen($binary);
$xmin = shpDoubleLE($binary, 36);
$ymin = shpDoubleLE($binary, 44);
$xmax = shpDoubleLE($binary, 52);
$ymax = shpDoubleLE($binary, 60);
$isGeographic = $xmin >= -180 && $xmax <= 180 && $ymin >= -90 && $ymax <= 90;
$dataProjection = $isGeographic ? 'EPSG:4326' : 'EPSG:32613';

while ($offset + 8 <= $length) {
    $recordNumber = shpInt32BE($binary, $offset);
    $contentBytes = shpInt32BE($binary, $offset + 4) * 2;
    $contentOffset = $offset + 8;
    $recordEnd = $contentOffset + $contentBytes;

    if ($contentBytes < 4 || $recordEnd > $length) break;

    $shapeType = shpInt32LE($binary, $contentOffset);
    $isPolyline = in_array($shapeType, [3, 13, 23], true);
    $isPolygon = in_array($shapeType, [5, 15, 25], true);

    if (($isPolyline || $isPolygon) && $contentBytes >= 44) {
        $numParts = shpInt32LE($binary, $contentOffset + 36);
        $numPoints = shpInt32LE($binary, $contentOffset + 40);
        $partsOffset = $contentOffset + 44;
        $pointsOffset = $partsOffset + ($numParts * 4);

        if ($numParts > 0 && $numPoints > 0 && $pointsOffset + ($numPoints * 16) <= $recordEnd) {
            $partStarts = [];
            for ($i = 0; $i < $numParts; $i++) {
                $partStarts[] = shpInt32LE($binary, $partsOffset + ($i * 4));
            }
            $partStarts[] = $numPoints;

            $parts = [];
            for ($part = 0; $part < $numParts; $part++) {
                $coordinates = [];
                for ($point = $partStarts[$part]; $point < $partStarts[$part + 1]; $point++) {
                    $pointOffset = $pointsOffset + ($point * 16);
                    $coordinates[] = [
                        shpDoubleLE($binary, $pointOffset),
                        shpDoubleLE($binary, $pointOffset + 8)
                    ];
                }
                $minimumPoints = $isPolygon ? 4 : 2;
                if (count($coordinates) >= $minimumPoints) $parts[] = $coordinates;
            }

            if ($parts) {
                if ($isPolygon) {
                    $geometryType = 'Polygon';
                    $geometryCoordinates = $parts;
                } else {
                    $geometryType = count($parts) === 1 ? 'LineString' : 'MultiLineString';
                    $geometryCoordinates = count($parts) === 1 ? $parts[0] : $parts;
                }

                $features[] = [
                    'type' => 'Feature',
                    'properties' => [
                        'shp_record' => $recordNumber,
                        'capa' => 'calles'
                    ],
                    'geometry' => [
                        'type' => $geometryType,
                        'coordinates' => $geometryCoordinates
                    ]
                ];
            }
        }
    }

    $offset = $recordEnd;
}

echo json_encode([
    'type' => 'FeatureCollection',
    'name' => 'calles',
    'crs' => [
        'type' => 'name',
        'properties' => ['name' => $dataProjection]
    ],
    'dataProjection' => $dataProjection,
    'features' => $features
], JSON_UNESCAPED_UNICODE);
