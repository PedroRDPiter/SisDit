<?php
error_reporting(0);
ini_set('display_errors', 0);
if (ob_get_length()) ob_clean();
if (session_status() === PHP_SESSION_NONE) session_start();

require_once "db.php";
require_once "funciones_seguridad.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesion expirada']);
    exit;
}

if (!esVerificador() && !esAdministrador() && !esVentanilla()) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

$stmt = $conn->prepare("
    SELECT
        d.cuenta_catastral_origen,
        d.numero_poligono,
        d.tramite_id,
        d.texto_poligono,
        d.seleccionado,
        d.updated_at,
        t.estatus
    FROM croquis_poligono_detalles d
    INNER JOIN tramites t ON t.id = d.tramite_id
    WHERE d.activo = 1
      AND (d.cuenta_catastral_origen IS NOT NULL OR d.numero_poligono IS NOT NULL)
    ORDER BY
        d.cuenta_catastral_origen ASC,
        d.numero_poligono ASC,
        d.seleccionado DESC,
        d.updated_at DESC,
        d.id DESC
");

if (!$stmt || !$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'No se pudieron consultar los estatus de los predios']);
    exit;
}

$predios = [];
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $clave = trim((string)($row['cuenta_catastral_origen'] ?: $row['numero_poligono']));
    if ($clave === '' || isset($predios[$clave])) continue;

    $predios[$clave] = [
        'estatus' => $row['estatus'],
        'tramite_id' => (int)$row['tramite_id'],
        'texto' => $row['texto_poligono'],
        'updated_at' => $row['updated_at']
    ];
}
$stmt->close();

$resumen_tramites = [
    'En revisión' => 0,
    'Aprobado por Verificador' => 0,
    'Aprobado' => 0
];
$resConteos = $conn->query("
    SELECT estatus, COUNT(*) AS total
    FROM tramites
    GROUP BY estatus
");
if ($resConteos) {
    while ($conteo = $resConteos->fetch_assoc()) {
        $resumen_tramites[$conteo['estatus']] = (int)$conteo['total'];
    }
}

echo json_encode([
    'success' => true,
    'predios' => $predios,
    'resumen_tramites' => $resumen_tramites
], JSON_UNESCAPED_UNICODE);
