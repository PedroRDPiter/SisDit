<?php
require_once "db.php";
require_once "funciones_seguridad.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id']) || !esPersonalAutorizado()) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

// Función para convertir lat/lng a UTM (zona 13)
function latLngToUtm($lat, $lon, $zone = 13) {
    $a = 6378137.0;
    $f = 1/298.257223563;
    $k0 = 0.9996;
    $e = sqrt(2*$f - $f*$f);
    $latRad = deg2rad($lat);
    $lonRad = deg2rad($lon);
    $lon0 = deg2rad($zone * 6 - 183);

    $N = $a / sqrt(1 - $e*$e * sin($latRad)*sin($latRad));
    $T = tan($latRad)*tan($latRad);
    $C = $e*$e * cos($latRad)*cos($latRad) / (1 - $e*$e);
    $A = ($lonRad - $lon0) * cos($latRad);

    $M = $a * ((1 - $e*$e/4 - 3*$e*$e*$e*$e/64 - 5*$e*$e*$e*$e*$e*$e/256) * $latRad - (3*$e*$e/8 + 3*$e*$e*$e*$e/32 + 45*$e*$e*$e*$e*$e*$e/1024) * sin(2*$latRad) + (15*$e*$e*$e*$e/256 + 45*$e*$e*$e*$e*$e*$e/1024) * sin(4*$latRad) - (35*$e*$e*$e*$e*$e*$e/3072) * sin(6*$latRad));

    $utmE = $k0 * $N * ($A + (1 - $T + $C) * $A*$A*$A/6 + (5 - 18*$T + $T*$T + 72*$C - 58*$e*$e) * $A*$A*$A*$A*$A/120) + 500000;
    $utmN = $k0 * ($M + $N * tan($latRad) * ($A*$A/2 + (5 - $T + 9*$C + 4*$C*$C) * $A*$A*$A*$A/24 + (61 - 58*$T + $T*$T + 600*$C - 330*$e*$e) * $A*$A*$A*$A*$A*$A/720));

    if ($lat < 0) $utmN += 10000000;

    return [$utmE, $utmN];
}

function utmToLatLng($easting, $northing, $zone = 13) {
    $a = 6378137.0;
    $f = 1 / 298.257223563;
    $k0 = 0.9996;
    $e = sqrt(2 * $f - $f * $f);
    $e1sq = $e * $e / (1 - $e * $e);
    $x = $easting - 500000;
    $m = $northing / $k0;
    $mu = $m / ($a * (1 - $e*$e/4 - 3*pow($e, 4)/64 - 5*pow($e, 6)/256));
    $e1 = (1 - sqrt(1 - $e*$e)) / (1 + sqrt(1 - $e*$e));
    $fp = $mu
        + (3*$e1/2 - 27*pow($e1, 3)/32) * sin(2*$mu)
        + (21*$e1*$e1/16 - 55*pow($e1, 4)/32) * sin(4*$mu)
        + (151*pow($e1, 3)/96) * sin(6*$mu)
        + (1097*pow($e1, 4)/512) * sin(8*$mu);
    $c1 = $e1sq * pow(cos($fp), 2);
    $t1 = pow(tan($fp), 2);
    $n1 = $a / sqrt(1 - $e*$e*pow(sin($fp), 2));
    $r1 = $a * (1 - $e*$e) / pow(1 - $e*$e*pow(sin($fp), 2), 1.5);
    $d = $x / ($n1 * $k0);
    $lat = $fp - ($n1*tan($fp)/$r1) * ($d*$d/2 - (5+3*$t1+10*$c1-4*$c1*$c1-9*$e1sq)*pow($d,4)/24 + (61+90*$t1+298*$c1+45*$t1*$t1-252*$e1sq-3*$c1*$c1)*pow($d,6)/720);
    $lon0 = deg2rad($zone * 6 - 183);
    $lon = $lon0 + ($d - (1+2*$t1+$c1)*pow($d,3)/6 + (5-2*$c1+28*$t1-3*$c1*$c1+8*$e1sq+24*$t1*$t1)*pow($d,5)/120) / cos($fp);
    return [rad2deg($lat), rad2deg($lon)];
}

try {
    $sql = "
        SELECT
            CONCAT(LPAD(t.folio_numero, 3, '0'), '/', t.folio_anio) AS FOLIO_INGR,
            t.solicitante AS NOM_SOLI,
            tt.nombre AS TIP_TRAMIT,
            t.direccion AS UBICACION,
            DATE_FORMAT(t.fecha_ingreso, '%Y-%m-%d') AS FECH_INGRE,
            DATE_FORMAT(t.fecha_entrega, '%Y-%m-%d') AS FECH_ENTRE,
            t.estatus AS ESTATUS,
            t.telefono AS CONTACTO,
            t.numero_asignado AS NUMERO,
            t.lat,
            t.lng
        FROM tramites t
        LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
        WHERE t.lat IS NOT NULL AND t.lng IS NOT NULL
        ORDER BY t.created_at DESC
    ";

    $result = $conn->query($sql);
    $features = [];
    $omitidos = 0;

    while ($row = $result->fetch_assoc()) {
        $coordA = (float) $row['lat'];
        $coordB = (float) $row['lng'];
        if ($coordA >= 100000 && $coordA <= 900000 && $coordB >= 0 && $coordB <= 10000000) {
            [$latitud, $longitud] = utmToLatLng($coordA, $coordB);
            $utm = [$coordA, $coordB];
        } elseif ($coordA != 0.0 && $coordB != 0.0 && $coordA >= -90 && $coordA <= 90 && $coordB >= -180 && $coordB <= 180) {
            $latitud = $coordA;
            $longitud = $coordB;
            $utm = latLngToUtm($latitud, $longitud);
        } else {
            $omitidos++;
            continue;
        }
        // Limites amplios alrededor de Aguascalientes para descartar ceros y
        // coordenadas capturadas en otra zona sin ocultar predios limitrofes.
        if (!is_finite($latitud) || !is_finite($longitud)
            || $latitud < 21 || $latitud > 23
            || $longitud < -103 || $longitud > -101) {
            $omitidos++;
            continue;
        }
        $features[] = [
            'type' => 'Feature',
            'properties' => [
                'FOLIO_INGR' => $row['FOLIO_INGR'],
                'NOM_SOLI' => $row['NOM_SOLI'] ?? 'N/A',
                'TIP_TRAMIT' => $row['TIP_TRAMIT'] ?? 'N/A',
                'UBICACION' => $row['UBICACION'] ?? 'N/A',
                'FECH_INGRE' => $row['FECH_INGRE'] ?? 'N/A',
                'FECH_ENTRE' => $row['FECH_ENTRE'] ?? 'N/A',
                'ESTATUS' => $row['ESTATUS'] ?? 'N/A',
                'X' => round($utm[0], 2),
                'Y' => round($utm[1], 2),
                'CONTACTO' => $row['CONTACTO'] ?? 'N/A',
                'NUMERO' => $row['NUMERO'] ?? 'N/A'
            ],
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$longitud, $latitud]
            ]
        ];
    }

    $geojson = [
        'type' => 'FeatureCollection',
        'name' => 'TRAMITES_' . date('Y'),
        'features' => $features,
        'metadata' => [
            'total' => count($features),
            'omitidos_por_coordenadas' => $omitidos,
            'generado' => date(DATE_ATOM)
        ]

    ];

    echo json_encode($geojson, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>
