<?php
require_once __DIR__ . '/funciones_seguridad.php';
header('Content-Type: application/json; charset=utf-8');

function responderShp(int $codigo, array $datos): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_SESSION['id']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
    responderShp(403, ['success' => false, 'message' => 'Acceso exclusivo para administradores.']);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderShp(405, ['success' => false, 'message' => 'Método no permitido.']);
}
if (!validarCSRF()) {
    responderShp(403, ['success' => false, 'message' => 'La sesión venció. Recarga la página e intenta nuevamente.']);
}
$archivo = $_FILES['shp'] ?? null;
if (!$archivo || is_array($archivo['name']) || $archivo['error'] !== UPLOAD_ERR_OK) {
    responderShp(400, ['success' => false, 'message' => 'No se recibió el archivo completo. Revisa el tamaño e intenta nuevamente.']);
}
if (strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION)) !== 'shp'
    || $archivo['size'] < 100 || $archivo['size'] > 30 * 1024 * 1024
    || !is_uploaded_file($archivo['tmp_name'])) {
    responderShp(400, ['success' => false, 'message' => 'Selecciona un archivo .shp de polígonos de hasta 30 MB.']);
}

$raiz = dirname(__DIR__);
$destino = $raiz . '/Geojson/TRAMITES_reprojected.geojson';
$bloqueo = null;
$temporal = null;
$errores = null;
$codigo = 500;
$respuesta = ['success' => false, 'message' => 'No fue posible actualizar la capa. La versión anterior se conserva.'];
try {
    set_time_limit(180);
    $bloqueo = fopen(sys_get_temp_dir() . '/sisdit-shp-' . sha1($raiz) . '.lock', 'c');
    if (!$bloqueo || !flock($bloqueo, LOCK_EX | LOCK_NB)) {
        throw new RuntimeException('Otra actualización está en curso. Intenta nuevamente en unos minutos.');
    }
    $temporal = tempnam(dirname($destino), 'shp_conversion_');
    $errores = tempnam(sys_get_temp_dir(), 'sisdit_shp_');
    if (!$temporal || !$errores) {
        throw new RuntimeException('No se pudo preparar la conversión.');
    }
    $python = getenv('SISDIT_PYTHON') ?: (is_file('C:/ProgramData/anaconda3/python.exe') ? 'C:/ProgramData/anaconda3/python.exe' : 'python');
    $proceso = proc_open([
        $python, $raiz . '/scripts/actualizar_poligonos.py',
        '--entrada', $archivo['tmp_name'], '--anterior', $destino, '--salida', $temporal
    ], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['file', $errores, 'w']], $pipes, $raiz, null, ['bypass_shell' => true]);
    if (!is_resource($proceso)) {
        throw new RuntimeException('No se pudo iniciar el convertidor de polígonos.');
    }
    fclose($pipes[0]);
    $salida = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $estado = proc_close($proceso);
    $resultado = json_decode($salida, true);
    if ($estado !== 0 || empty($resultado['success']) || filesize($temporal) < 100) {
        AppLogger::error(new RuntimeException($salida . ' ' . file_get_contents($errores)), ['operacion' => 'convertir_shp']);
        $codigo = 422;
        throw new RuntimeException('No se pudo convertir el archivo. Debe ser un SHP completo de polígonos del municipio en UTM zona 13 norte.');
    }
    // Preparar el respaldo antes de reemplazar la capa compartida por los mapas.
    if (!copy($destino, $destino . '.bak')) {
        throw new RuntimeException('No fue posible respaldar la capa anterior.');
    }
    if (!rename($temporal, $destino)) {
        throw new RuntimeException('No fue posible publicar la nueva capa.');
    }
    $temporal = null;
    AppLogger::evento(null, 'ACTUALIZAR_SHP', null, null, json_encode($resultado), (int) $_SESSION['id']);
    $codigo = 200;
    $respuesta = $resultado + ['message' => 'Polígonos actualizados. Abre nuevamente o recarga los mapas para ver la nueva capa.'];
} catch (Throwable $error) {
    AppLogger::error($error, ['operacion' => 'actualizar_shp']);
    $respuesta['message'] = $error instanceof RuntimeException ? $error->getMessage() : $respuesta['message'];
} finally {
    if ($temporal && is_file($temporal)) unlink($temporal);
    if ($errores && is_file($errores)) unlink($errores);
    if (is_resource($bloqueo)) {
        flock($bloqueo, LOCK_UN);
        fclose($bloqueo);
    }
}
responderShp($codigo, $respuesta);
