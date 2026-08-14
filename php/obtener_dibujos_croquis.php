<?php
error_reporting(0);
ini_set('display_errors', 0);
if (ob_get_length()) ob_clean();
if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'db.php';
require_once 'funciones_seguridad.php';
require_once 'documento_escaneado.php';

header('Content-Type: application/json; charset=utf-8');

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

$sql = "
    SELECT
        d.id,
        d.tramite_id,
        d.feature_uid,
        d.numero_poligono,
        d.cuenta_catastral_origen,
        d.origen,
        d.texto_poligono,
        d.geojson,
        d.label_lng,
        d.label_lat,
        d.seleccionado,
        d.updated_at,
        t.estatus,
        t.tipo_tramite_id,
        t.formato_constancia,
        t.otros_archivos,
        tt.nombre AS tipo_tramite
    FROM croquis_poligono_detalles d
    INNER JOIN tramites t ON t.id = d.tramite_id
    LEFT JOIN tipos_tramite tt ON tt.id = t.tipo_tramite_id
    WHERE d.activo = 1
      AND d.geojson IS NOT NULL
      AND TRIM(d.geojson) <> ''
      AND LOWER(COALESCE(d.origen, '')) <> 'catastro'
    ORDER BY d.updated_at ASC, d.id ASC
";

$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudieron consultar los dibujos de verificacion']);
    exit;
}

$features = [];
while ($row = $result->fetch_assoc()) {
    $feature = json_decode((string)$row['geojson'], true);
    if (!is_array($feature) || ($feature['type'] ?? '') !== 'Feature' || empty($feature['geometry'])) continue;

    $geometryType = $feature['geometry']['type'] ?? '';
    if (!in_array($geometryType, ['Polygon', 'MultiPolygon', 'LineString', 'MultiLineString'], true)) continue;

    $documento = obtenerDocumentoEscaneadoTramite($row);
    $feature['properties'] = [
        'detalle_id' => (int)$row['id'],
        'tramite_id' => (int)$row['tramite_id'],
        'tipo_tramite_id' => (int)$row['tipo_tramite_id'],
        'tipo_tramite' => $row['tipo_tramite'],
        'feature_uid' => $row['feature_uid'],
        'numero_poligono' => $row['numero_poligono'],
        'cuenta_catastral' => $row['cuenta_catastral_origen'],
        'origen' => $row['origen'],
        'texto' => $row['texto_poligono'],
        'label_lng' => $row['label_lng'] !== null ? (float)$row['label_lng'] : null,
        'label_lat' => $row['label_lat'] !== null ? (float)$row['label_lat'] : null,
        'seleccionado' => (bool)$row['seleccionado'],
        'estatus' => $row['estatus'],
        'documento_escaneado' => $documento,
        'updated_at' => $row['updated_at']
    ];
    $features[] = $feature;
}

echo json_encode([
    'success' => true,
    'type' => 'FeatureCollection',
    'features' => $features
], JSON_UNESCAPED_UNICODE);
