<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/php/db.php';
$r = $conn->query('SELECT COUNT(*) as total, SUM(tramite_principal_id IS NOT NULL) as con_principal FROM tramites');
$row = $r->fetch_assoc();
echo 'Total tramites: ' . $row['total'] . ' | Con tramite_principal_id (hijos): ' . ($row['con_principal'] ?? 0) . PHP_EOL;

$r2 = $conn->query("SELECT DISTINCT folio_numero, folio_anio FROM tramites WHERE folio_numero IS NOT NULL GROUP BY folio_numero, folio_anio HAVING COUNT(*) > 1 LIMIT 5");
echo "Folios duplicados (mismo numero/año en múltiples filas): " . $r2->num_rows . PHP_EOL;
while($d = $r2->fetch_assoc()) {
    echo "  - " . str_pad($d['folio_numero'],3,'0',STR_PAD_LEFT) . "/" . $d['folio_anio'] . PHP_EOL;
}
?>
