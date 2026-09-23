<?php
// Instalación de datos ficticios, exclusivamente en una base aislada de pruebas.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$destino = getenv('SISDIT_DB_NAME') ?: '';
if (!preg_match('/^sisdit_test_oficios_[a-z0-9_]+$/D', $destino)) exit("Usa SISDIT_DB_NAME=sisdit_test_oficios_<identificador>.\n");
$conn = new mysqli(getenv('SISDIT_DB_HOST') ?: 'localhost', getenv('SISDIT_DB_USER') ?: 'root', getenv('SISDIT_DB_PASS') ?: '');
$conn->set_charset('utf8mb4');
if (($argv[1] ?? '') === 'limpiar') {
    $conn->select_db($destino);
    $result = $conn->query('SELECT otros_archivos FROM tramites');
    while ($row = $result->fetch_assoc()) {
        foreach (json_decode($row['otros_archivos'] ?: '[]', true) ?: [] as $doc) {
            $ruta = $doc['archivo'] ?? '';
            if (preg_match('#^\.private/oficios/(oficio|original)_[a-zA-Z0-9_-]+\.pdf$#D', $ruta)) {
                $base = realpath(dirname(__DIR__) . '/.private/oficios');
                $real = realpath(dirname(__DIR__) . '/' . $ruta);
                if ($base && $real && str_starts_with($real, $base . DIRECTORY_SEPARATOR) && is_file($real)) unlink($real);
            }
        }
    }
    $conn->query("DROP DATABASE `$destino`");
    echo "Base aislada y documentos de prueba eliminados.\n";
    exit;
}
// CREATE DATABASE sin IF NOT EXISTS evita reutilizar o borrar una base ajena.
$conn->query("CREATE DATABASE `$destino` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$conn->select_db($destino);
$conn->query('SET FOREIGN_KEY_CHECKS=0');
$tablas = $conn->query("SHOW FULL TABLES FROM sistema WHERE Table_type = 'BASE TABLE'")->fetch_all();
foreach ($tablas as [$tabla]) {
    $ddl = $conn->query("SHOW CREATE TABLE sistema.`$tabla`")->fetch_row()[1];
    $ddl = preg_replace('/AUTO_INCREMENT=\d+/', 'AUTO_INCREMENT=1', $ddl);
    $conn->query($ddl);
}
$conn->query('SET FOREIGN_KEY_CHECKS=1');
$hash = password_hash('Prueba-Oficios-2026!', PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO usuarios (id,nombre,apellidos,correo,password,rol,activo) VALUES (?,?,\'Pruebas\',?,?,?,1)');
foreach ([1 => 'Administrador', 2 => 'Usuario', 3 => 'Ventanilla'] as $id => $rol) {
    $correo = strtolower($rol) . '@pruebas.example';
    $stmt->bind_param('issss', $id, $rol, $correo, $hash, $rol); $stmt->execute();
}
$conn->query("INSERT INTO tipos_tramite (id,codigo,nombre,activo) VALUES (1,'NUM_OFICIAL','Oficio de prueba',1),(2,'CMCU','Compatibilidad de prueba',1),(7,'LIC_CONST','Licencia de prueba',1)");
foreach ([1 => 'Pendiente por firmar', 2 => 'Pendiente por firmar', 3 => 'Firmado', 4 => 'En revisión', 5 => 'Pendiente por firmar'] as $id => $estado) {
    $stmt = $conn->prepare("INSERT INTO tramites (id,folio_numero,folio_anio,tipo_tramite_id,propietario,direccion,numero,entre_calle2,localidad,fecha_ingreso,fecha_entrega,solicitante,telefono,oficio_vobo,Resolucion,usuario_creador_id,estatus,observaciones,verificador_nombre,aprobado_por)
        VALUES (?,100,2026,1,'PERSONA FICTICIA','CALLE DE PRUEBA','','','LOCALIDAD',CURDATE(),CURDATE(),'SOLICITANTE FICTICIO','0000000000','','',1,?,'Observaciones previas conservadas','VERIFICADOR DE PRUEBA',3)");
    $stmt->bind_param('is', $id, $estado); $stmt->execute();
}
echo "Base aislada preparada: $destino\n";
