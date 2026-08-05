<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/funciones_seguridad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validarCSRF()) {
    header('Location: ../acceso.php?error=csrf');
    exit;
}

$correo = strtolower(trim((string) ($_POST['correo'] ?? '')));
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../acceso.php?error=email_invalido');
    exit;
}

$limitKey = ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '|' . $correo;
if (consumirLimite('recuperar', $limitKey, 3, 3600) > 0) {
    // La respuesta sigue siendo generica para no revelar cuentas.
    header('Location: ../acceso.php?ok=correo_procesado');
    exit;
}

$stmt = $conn->prepare('SELECT id, nombre FROM usuarios WHERE correo = ? AND activo = 1 LIMIT 1');
$stmt->bind_param('s', $correo);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($usuario) {
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expira = date('Y-m-d H:i:s', time() + 3600);
    $stmt = $conn->prepare('UPDATE usuarios SET token_recuperacion = ?, token_expira = ? WHERE id = ?');
    $stmt->bind_param('ssi', $tokenHash, $expira, $usuario['id']);
    $stmt->execute();
    $stmt->close();

    $baseUrl = rtrim(getenv('APP_URL') ?: 'http://localhost/SisDiTdesarrollo', '/');
    $enlace = $baseUrl . '/php/reset_password.php?token=' . rawurlencode($token);
    $asunto = 'Recuperación de contraseña - SisDiT';
    $mensaje = "Hola {$usuario['nombre']},\n\nAbre este enlace para restablecer tu contraseña:\n{$enlace}\n\nEl enlace expira en una hora.";
    $cabeceras = "From: noreply@sistema-geo.com\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    if (!@mail($correo, $asunto, $mensaje, $cabeceras)) {
        error_log('No se pudo enviar correo de recuperacion para usuario id ' . (int) $usuario['id']);
    }
}

$_SESSION['recuperar_msg'] = 'Si el correo existe en el sistema, recibirás un enlace de recuperación.';
header('Location: ../acceso.php?ok=correo_procesado');
exit;
