<?php
// =====================================================
// LOGOUT — Cerrar sesión
// Limpia la sesión completa y redirige al login.
// También registra el logout en los logs de actividad.
// =====================================================

require_once "php/db.php";
require_once "php/funciones_seguridad.php";

// Obtener y validar el token CSRF para evitar solicitudes de cierre de sesión no autorizadas.
$csrf = (string) ($_GET['csrf_token'] ?? '');
if ($csrf === '' || !isset($_SESSION['csrf_token']) || !hash_equals((string) $_SESSION['csrf_token'], $csrf)) {
    http_response_code(403);
    exit('Solicitud invalida');
}

// Registrar el cierre de sesión antes de eliminar los datos de la sesión.
if (isset($_SESSION['id'])) {
    registrarLog($conn, $_SESSION['id'], 'Logout', 'usuarios', $_SESSION['id']);
}

// Vaciar todas las variables almacenadas en la sesión actual.
$_SESSION = array();

// Expirar la cookie de sesión para invalidarla también en el navegador.
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir la sesión en el servidor.
session_destroy();

// Volver al formulario de acceso indicando que la sesión se cerró correctamente.
header("Location: acceso.php?logout=1");
exit();
