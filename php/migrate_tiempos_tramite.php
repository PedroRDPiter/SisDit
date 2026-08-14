<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/db.php';
$cols=[];
$res=$conn->query('SHOW COLUMNS FROM tramites');
while($r=$res->fetch_assoc())$cols[]=$r['Field'];
if(!in_array('tiempo_ingreso',$cols)){
  $conn->query("ALTER TABLE tramites ADD COLUMN tiempo_ingreso DATETIME NULL AFTER created_at");
  $conn->query("ALTER TABLE tramites ALTER COLUMN tiempo_ingreso SET DEFAULT CURRENT_TIMESTAMP");
  echo "Added: tiempo_ingreso\n";
}
if(!in_array('tiempo_salida',$cols)){
  $conn->query("ALTER TABLE tramites ADD COLUMN tiempo_salida DATETIME NULL AFTER tiempo_ingreso");
  echo "Added: tiempo_salida\n";
}
$conn->query("UPDATE tramites SET tiempo_ingreso=created_at WHERE created_at IS NOT NULL AND (tiempo_ingreso IS NULL OR tiempo_ingreso>created_at)");
echo "Backfill ingreso: ".$conn->affected_rows." rows\n";
$conn->query("UPDATE tramites SET tiempo_salida=fecha_aprobacion WHERE fecha_aprobacion IS NOT NULL AND estatus IN('Aprobado','Aprobado por Verificador','Rechazado') AND (tiempo_salida IS NULL OR tiempo_salida<fecha_aprobacion)");
echo "Backfill salida: ".$conn->affected_rows." rows\n";
echo "OK\n";
