<?php
declare(strict_types=1);
require_once __DIR__ . '/funciones_seguridad.php';
require_once __DIR__ . '/OficiosAdmin.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
function responderOficio(int $codigo, array $datos): never
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
if (!isset($_SESSION['id']) || (isset($_SESSION['last_activity']) && time() - (int)$_SESSION['last_activity'] > 1800)) {
    responderOficio(401, ['success' => false, 'message' => 'La sesión expiró. Inicia sesión de nuevo.']);
}
if (!esAdministrador()) responderOficio(403, ['success' => false, 'message' => 'Acceso exclusivo del administrador.']);
if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== ($_SERVER['REMOTE_ADDR'] ?? '')) {
    responderOficio(401, ['success' => false, 'message' => 'La sesión no es válida. Inicia sesión de nuevo.']);
}
$metodo = $_SERVER['REQUEST_METHOD'] ?? '';
if (!in_array($metodo, ['GET', 'POST'], true)) {
    header('Allow: GET, POST');
    responderOficio(405, ['success' => false, 'message' => 'Método no permitido.']);
}
if ($metodo === 'POST' && !validarCSRF()) responderOficio(403, ['success' => false, 'message' => 'La sesión de seguridad cambió. Recarga la página.']);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/FolioSalida.php';
$guardados = [];
$transaccion = false;
try {
    $uid = (int)$_SESSION['id'];
    $stmt = $conn->prepare('SELECT nombre, apellidos, rol, activo FROM usuarios WHERE id = ?');
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    if (!$usuario || !$usuario['activo'] || $usuario['rol'] !== 'Administrador') {
        responderOficio(403, ['success' => false, 'message' => 'Tu cuenta ya no tiene permiso para administrar oficios.']);
    }
    $_SESSION['last_activity'] = time();
    $id = filter_var(($metodo === 'POST' ? $_POST['id'] ?? null : $_GET['id'] ?? null), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$id) throw new DomainException('Selecciona un oficio válido.', 422);
    if ($metodo === 'POST') {
        $conn->begin_transaction();
        $transaccion = true;
    }
    $stmt = $conn->prepare('SELECT * FROM tramites WHERE id = ?' . ($metodo === 'POST' ? ' FOR UPDATE' : ''));
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $tramite = $stmt->get_result()->fetch_assoc();
    if (!$tramite) throw new DomainException('El oficio no existe.', 404);
    if ($metodo === 'GET') {
        $stmt = $conn->prepare('SELECT h.accion, h.estatus_nuevo, h.comentario, h.fecha, CONCAT_WS(" ", u.nombre, u.apellidos) AS responsable
            FROM historial_tramites h LEFT JOIN usuarios u ON u.id = h.usuario_id WHERE h.tramite_id = ? ORDER BY h.id DESC LIMIT 20');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $historial = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $plantilla = null;
        if ((int)$tramite['tipo_tramite_id'] === 1) $plantilla = 'constancia_numero.php?id=' . $id;
        if (in_array((int)$tramite['tipo_tramite_id'], [2, 7], true)) {
            $stmt = $conn->prepare('SELECT id FROM tramites_salida WHERE tramite_id = ? LIMIT 1');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $salida = $stmt->get_result()->fetch_assoc();
            if ($salida) $plantilla = ((int)$tramite['tipo_tramite_id'] === 7 ? 'licencia_construccion.php' : 'constancia_compatibilidad.php') . '?id=' . (int)$salida['id'];
        }
        $campos = array_flip(['id', 'estatus', 'propietario', 'solicitante', 'direccion', 'cuenta_catastral', 'observaciones',
            'folio_numero', 'folio_anio', 'folio_salida_numero', 'folio_salida_anio', 'fecha_aprobacion_director', 'tiempo_salida']);
        responderOficio(200, ['success' => true, 'tramite' => array_intersect_key($tramite, $campos),
            'documentos' => documentosOficio($tramite), 'historial' => $historial, 'plantilla' => $plantilla,
            'firma_digital_disponible' => documentoFirmadoDigital($tramite) !== null]);
    }

    $accion = (string)($_POST['accion'] ?? '');
    $nuevo = validarAccionOficio($usuario['rol'], $accion, $tramite['estatus'], (string)($_POST['estado_esperado'] ?? ''), ($_POST['confirmar'] ?? '') === '1');
    $nota = trim((string)($_POST['observaciones'] ?? ''));
    if (mb_strlen($nota) > 2000) throw new DomainException('Las observaciones deben tener como máximo 2000 caracteres.', 422);
    $nombre = trim($usuario['nombre'] . ' ' . $usuario['apellidos']);
    $comentario = ($accion === 'firmar' ? 'Firma visible incorporada al PDF y autorizada por ' : 'Entrega y cierre digital registrados por ') . $nombre . '.';
    if ($nota !== '') $comentario .= "\n" . $nota;
    $folioNumero = (int)($tramite['folio_salida_numero'] ?? 0);
    $folioAnio = (int)($tramite['folio_salida_anio'] ?? 0);
    $hash = null;

    $otros = json_decode((string)($tramite['otros_archivos'] ?? ''), true);
    if (!is_array($otros) && !empty($tramite['otros_archivos'])) throw new RuntimeException('El índice documental requiere revisión.');
    $otros = is_array($otros) ? $otros : [];
    $firmaExistente = $accion === 'cerrar' ? documentoFirmadoDigital($tramite) : null;
    if ($firmaExistente !== null) {
        $hash = $firmaExistente['sha256'];
    } else {
        $archivo = $_FILES['documento_firmado'] ?? [];
        $valido = Utilidades::validarArchivo($archivo, $accion === 'firmar' ? ['pdf'] : ['pdf', 'jpg', 'jpeg', 'png']);
        $carpeta = dirname(__DIR__) . '/.private/oficios';
        if (!is_dir($carpeta) && !mkdir($carpeta, 0750, true) && !is_dir($carpeta)) throw new RuntimeException('No se pudo crear el archivo digital.');
        if (file_put_contents($carpeta . '/.htaccess', "Require all denied\n", LOCK_EX) === false) throw new RuntimeException('No se pudo proteger el archivo digital.');
        $nombreArchivo = Utilidades::generarNombreArchivo('oficio_' . $id, $valido['extension']);
        $destino = $carpeta . '/' . $nombreArchivo;
        if (!move_uploaded_file($archivo['tmp_name'], $destino)) throw new RuntimeException('No se pudo guardar el documento final.');
        $guardados[] = $destino;
        $hash = hash_file('sha256', $destino);
        if ($hash === false) throw new RuntimeException('No se pudo verificar el archivo digital.');
        $documento = ['tipo' => (int)$tramite['tipo_tramite_id'] === 7 ? 'documento_firmado_licencia' : 'documento_firmado_constancia',
            'label' => 'Oficio firmado', 'archivo' => '.private/oficios/' . $nombreArchivo,
            'fecha' => date('Y-m-d H:i:s'), 'usuario_id' => $uid, 'responsable' => $nombre, 'sha256' => $hash];
        if ($accion === 'firmar') {
            $original = $_FILES['documento_original'] ?? [];
            Utilidades::validarArchivo($original, ['pdf']);
            $nombreOriginal = Utilidades::generarNombreArchivo('original_' . $id, 'pdf');
            $destinoOriginal = $carpeta . '/' . $nombreOriginal;
            if (!move_uploaded_file($original['tmp_name'], $destinoOriginal)) throw new RuntimeException('No se pudo conservar el oficio original.');
            $guardados[] = $destinoOriginal;
            $hashOriginal = hash_file('sha256', $destinoOriginal);
            if ($hashOriginal === false || hash_equals($hashOriginal, $hash)) throw new DomainException('El PDF firmado debe incorporar la firma al documento original.', 422);
            $documento['origen_firma'] = 'visible_pdf';
            $documento['original_sha256'] = $hashOriginal;
            $documento['pagina_firma'] = max(1, (int)($_POST['pagina_firma'] ?? 1));
            $otros[] = ['tipo' => 'oficio_original', 'label' => 'Oficio original (antes de firmar)',
                'archivo' => '.private/oficios/' . $nombreOriginal, 'sha256' => $hashOriginal, 'usuario_id' => $uid, 'fecha' => date('Y-m-d H:i:s')];
        }
        $otros[] = $documento;
    }
    $json = json_encode($otros, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

    if ($accion === 'firmar') {
        if ($folioNumero <= 0 || $folioAnio <= 0) {
            $folioAnio = (int)date('Y');
            $folioNumero = reservarFolioSalida($conn, (int)$tramite['tipo_tramite_id'], $folioAnio);
        }
        $stmt = $conn->prepare('UPDATE tramites SET estatus = ?, aprobado_director = 1, fecha_aprobacion_director = NOW(),
            folio_salida_numero = ?, folio_salida_anio = ?, otros_archivos = ? WHERE id = ?');
        $stmt->bind_param('siisi', $nuevo, $folioNumero, $folioAnio, $json, $id);
    } else {
        $stmt = $conn->prepare('UPDATE tramites SET estatus = ?, otros_archivos = ?, tiempo_salida = NOW(), fecha_entrega = CURDATE() WHERE id = ?');
        $stmt->bind_param('ssi', $nuevo, $json, $id);
    }
    $stmt->execute();
    $stmt = $conn->prepare('SELECT estatus FROM tramites WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()['estatus'] !== $nuevo) throw new RuntimeException('El esquema no admite el estado solicitado.');
    $stmt = $conn->prepare('INSERT INTO historial_tramites (tramite_id, usuario_id, accion, estatus_anterior, estatus_nuevo, comentario) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('iissss', $id, $uid, $nuevo, $tramite['estatus'], $nuevo, $comentario);
    $stmt->execute();
    $detalle = json_encode(['anterior' => $tramite['estatus'], 'nuevo' => $nuevo, 'responsable' => $nombre, 'sha256' => $hash], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $evento = $accion === 'firmar' ? 'Autorizó firma de oficio' : 'Cerró oficio digital';
    $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
    $stmt = $conn->prepare("INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent) VALUES (?, ?, 'tramites', ?, ?, ?, ?)");
    $stmt->bind_param('isisss', $uid, $evento, $id, $detalle, $ip, $ua);
    $stmt->execute();
    $conn->commit();
    $transaccion = false;
    responderOficio(200, ['success' => true, 'message' => $accion === 'firmar' ? 'Firma autorizada y registrada correctamente.' : 'Oficio entregado y cerrado con su documento final.', 'estatus' => $nuevo]);
} catch (Throwable $error) {
    if ($transaccion) $conn->rollback();
    foreach ($guardados as $guardado) if (is_file($guardado)) unlink($guardado);
    $esperado = $error instanceof DomainException || $error instanceof ArchivoException;
    if (!$esperado) error_log('admin_oficios: ' . $error->getMessage());
    responderOficio($esperado ? ($error->getCode() ?: 422) : 500, ['success' => false,
        'message' => $esperado ? $error->getMessage() : 'No se pudo guardar el oficio. No se aplicaron cambios; revisa la configuración o inténtalo de nuevo.']);
}
