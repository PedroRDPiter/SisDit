<?php
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/Utilidades.php';
require_once __DIR__ . '/AppLogger.php';
iniciarSesionSegura();
// =====================================================
// FUNCIONES DE SEGURIDAD Y VALIDACIÓN
// Aquí están todas las funciones que se usan en
// múltiples archivos para validar, limpiar y
// sanitizar datos antes de meterlos a la BD
// =====================================================

// =====================================================
// LIMPIAR INPUTS
// Siempre pasar los datos del usuario por aquí antes
// de usarlos — quita espacios, barras y escapa HTML
// =====================================================
function limpiarInput(mixed $data): mixed {
    if (is_array($data)) {
        return array_map('limpiarInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// =====================================================
// VALIDACIONES DE FORMATO
// =====================================================

// Email con el filtro nativo de PHP
function validarEmail(string $email): string|false {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// =====================================================
// VALIDACIÓN DE ARCHIVOS SUBIDOS
// Verifica extensión, tamaño Y tipo MIME real del archivo
// (no solo el nombre — alguien podría renombrar un .php a .jpg)
// =====================================================
function validarArchivo(mixed $archivo, array $tiposPermitidos = ['pdf', 'jpg', 'jpeg', 'png'], int $tamanoMaximo = Utilidades::TAMANO_MAXIMO_ARCHIVO): array {
    try {
        return ['valido' => true] + Utilidades::validarArchivo((array) $archivo, $tiposPermitidos, (int) $tamanoMaximo);
    } catch (ArchivoException $error) {
        return ['valido' => false, 'mensaje' => $error->getMessage()];
    }
}

// =====================================================
// REGISTRAR ACTIVIDAD EN LOGS
// Guarda un registro de cada acción importante.
// usuario_id = 0 se convierte a NULL para no romper
// la clave foránea cuando es un usuario no autenticado
// =====================================================
function registrarLog(mixed $conn, mixed $usuario_id, string $accion, ?string $tabla = null, ?int $registro_id = null, mixed $detalles = null): void {
    $uid = ($usuario_id == 0 || $usuario_id === '0') ? null : (int) $usuario_id;
    AppLogger::evento($conn instanceof mysqli ? $conn : null, (string) $accion, $tabla, $registro_id === null ? null : (int) $registro_id, $detalles, $uid);
}

// =====================================================
// CSRF — Protección contra ataques de formularios externos
// =====================================================

// Validar que el token del formulario coincida con el de la sesión
function validarCSRF($token = null) {
    $token = $token ?? ($_POST['csrf_token'] ?? null);
    if (!is_string($token) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals((string) $_SESSION['csrf_token'], $token);
}

function consumirLimite(string $accion, string $identificador, int $maximo, int $ventana): int {
    $clave = hash('sha256', $accion . '|' . $identificador);
    $ruta = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'sisdit_rate_' . $clave;
    $ahora = time();
    $fp = @fopen($ruta, 'c+');

    if (!$fp || !flock($fp, LOCK_EX)) {
        return 0;
    }

    $datos = json_decode(stream_get_contents($fp) ?: '[]', true);
    if (!is_array($datos) || ($datos['inicio'] ?? 0) + $ventana <= $ahora) {
        $datos = ['inicio' => $ahora, 'intentos' => 0];
    }

    if (($datos['intentos'] ?? 0) >= $maximo) {
        $restante = max(1, ($datos['inicio'] + $ventana) - $ahora);
        flock($fp, LOCK_UN); fclose($fp);
        return $restante;
    }

    $datos['intentos']++;
    ftruncate($fp, 0); rewind($fp);
    fwrite($fp, json_encode($datos)); fflush($fp);
    flock($fp, LOCK_UN); fclose($fp);
    return 0;
}

function limpiarLimite(string $accion, string $identificador): void {
    $clave = hash('sha256', $accion . '|' . $identificador);
    $ruta = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'sisdit_rate_' . $clave;
    if (is_file($ruta)) @unlink($ruta);
}

// Generar token CSRF (se crea si no existe)
function generarCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validar fecha en el formato esperado
function validarFecha(string $fecha, string $formato = 'Y-m-d'): bool {
    $d = DateTime::createFromFormat($formato, $fecha);
    return $d && $d->format($formato) === $fecha;
}

// Shortcuts para los roles más usados
function esAdministrador() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador';
}

// Verificador incluye también al Administrador
function esVerificador() {
    return isset($_SESSION['rol']) &&
           ($_SESSION['rol'] === 'Verificador' || $_SESSION['rol'] === 'Administrador');
}

// Ventanilla incluye también al Administrador
function esVentanilla() {
    return isset($_SESSION['rol']) &&
           ($_SESSION['rol'] === 'Ventanilla' || $_SESSION['rol'] === 'Administrador');
}

// Calificador incluye también al Administrador
function esCalificador() {
    return isset($_SESSION['rol']) &&
           ($_SESSION['rol'] === 'Calificador' || $_SESSION['rol'] === 'Administrador');
}

// =====================================================
// HELPERS DE TEXTO
// =====================================================

// Escape para evitar XSS en la salida HTML
function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Solo letras y espacios (para nombres propios)
function soloLetras(string $string): int|false {
    return preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $string);
}

// Convertir a mayúsculas sin eliminar acentos, comas ni otros símbolos.
// La seguridad de estos valores se aplica al consultar con sentencias preparadas
// y al mostrarlos con escape HTML, no destruyendo el texto ingresado.
function limpiarMayusculas(string $texto): string {
    return mb_strtoupper(trim($texto), 'UTF-8');
}

function esPersonalAutorizado(): bool {
    return isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['Administrador', 'Ventanilla', 'Verificador', 'Calificador'], true);
}

function puedeAccederTramite(array $tramite): bool {
    if (esPersonalAutorizado()) {
        return true;
    }

    return isset($_SESSION['id'], $tramite['usuario_creador_id'])
        && (int) $_SESSION['id'] === (int) $tramite['usuario_creador_id'];
}
