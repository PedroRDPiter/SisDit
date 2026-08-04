<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
$alerta = null;
$mostrarRegistro = false;

if (isset($_GET['ok']) && in_array($_GET['ok'], ['correo_enviado', 'correo_procesado', 'correo_dev'], true)) {
    $alerta = [
        'tipo' => 'exito',
        'titulo' => 'Revisa tu correo',
        'mensaje' => $_SESSION['recuperar_msg'] ?? 'Enviamos las instrucciones de recuperación a tu correo electrónico.'
    ];
    unset($_SESSION['recuperar_msg']);
} elseif (isset($_GET['ok']) && in_array($_GET['ok'], ['usuario_creado', 'solicitud_enviada'], true)) {
    $alerta = [
        'tipo' => 'exito',
        'titulo' => 'Solicitud recibida',
        'mensaje' => 'El administrador revisará tu información y te notificará cuando tu cuenta sea aprobada.'
    ];
}

if (isset($_GET['error'])) {
    $errorOriginal = trim(urldecode((string) $_GET['error']));
    $errorClave = strtolower($errorOriginal);
    $titulo = 'No fue posible continuar';
    $mensaje = $errorOriginal;
    $tipo = 'error';

    $errores = [
        'csrf' => ['Sesión vencida', 'Recarga la página e intenta nuevamente.'],
        'token de seguridad invalido' => ['Sesión vencida', 'Recarga la página e intenta nuevamente.'],
        'campos_obligatorios' => ['Campos incompletos', 'Completa todos los campos obligatorios.'],
        'completa todos los campos obligatorios' => ['Campos incompletos', 'Completa todos los campos obligatorios.'],
        'email_invalido' => ['Correo inválido', 'Ingresa una dirección de correo electrónico válida.'],
        'usuario_no_encontrado' => ['Cuenta no encontrada', 'No existe una cuenta con ese correo. Verifica tus datos o solicita una cuenta.'],
        'password_incorrecto' => ['Contraseña incorrecta', 'La contraseña ingresada no coincide con la cuenta.'],
        'password_invalida' => ['Contraseña inválida', 'La contraseña no cumple los requisitos de seguridad.'],
        'usuario_inactivo' => ['Cuenta inactiva', 'Tu cuenta aún no está activa. Contacta al administrador.'],
        'cuenta_inactiva' => ['Cuenta pendiente', 'Tu cuenta está pendiente de activación por el administrador.'],
        'telefono_invalido' => ['Teléfono inválido', 'El teléfono debe contener 10 dígitos.'],
        'rol_invalido' => ['Rol inválido', 'El rol asociado a la cuenta no es válido.'],
        'error_sistema' => ['Error del sistema', 'Ocurrió un problema. Intenta nuevamente más tarde.'],
        'error_envio' => ['No se pudo enviar el correo', 'Intenta nuevamente más tarde.']
    ];

    if (isset($errores[$errorClave])) {
        [$titulo, $mensaje] = $errores[$errorClave];
    } elseif (strpos($errorClave, 'pendiente') !== false) {
        $titulo = 'Solicitud pendiente';
        $mensaje = 'Ya existe una solicitud pendiente con este correo. Espera la revisión del administrador.';
        $tipo = 'aviso';
        $mostrarRegistro = true;
    } elseif (strpos($errorClave, 'rechazada') !== false) {
        $titulo = 'Solicitud rechazada';
        $mensaje = 'La solicitud fue rechazada. Contacta al administrador para recibir más información.';
        $mostrarRegistro = true;
    } elseif (strpos($errorClave, 'registrado') !== false) {
        $titulo = 'Correo registrado';
        $mensaje = 'Ese correo ya está registrado en el sistema.';
        $tipo = 'aviso';
        $mostrarRegistro = true;
    } elseif (strpos($errorClave, 'contraseña') !== false || strpos($errorClave, 'password') !== false) {
        $titulo = 'Contraseña inválida';
        $mostrarRegistro = true;
    } elseif (strpos($errorClave, 'nombre') !== false || strpos($errorClave, 'rol') !== false) {
        $titulo = 'Revisa los datos';
        $mostrarRegistro = true;
    }

    if ($errorClave === 'usuario_no_encontrado') {
        $mostrarRegistro = false;
    }

    $alerta = ['tipo' => $tipo, 'titulo' => $titulo, 'mensaje' => $mensaje];
}

$enlaceRecuperacion = $_SESSION['recuperar_enlace_mostrar'] ?? null;
unset($_SESSION['recuperar_enlace_mostrar']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Acceso al Sistema Único de Simplificación y Digitalización de Trámites del Municipio de Rincón de Romos.">
    <meta name="theme-color" content="#721832">
    <title>Acceso | SisDiT</title>

    <style>
        :root {
            --vino: #721832;
            --vino-oscuro: #4b0e22;
            --vino-claro: #9c3150;
            --verde: #287468;
            --verde-claro: #dcebe7;
            --tinta: #20262d;
            --gris: #66717c;
            --linea: #e5e2dd;
            --crema: #f7f5f1;
            --blanco: #ffffff;
            --sombra: 0 30px 90px rgba(53, 35, 40, .16);
            --radio: 26px;
            --contenedor: 1180px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--tinta);
            background:
                radial-gradient(circle at 8% 20%, rgba(114, 24, 50, .07), transparent 25%),
                var(--crema);
            font-family: "Segoe UI", Inter, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.5;
            overflow-x: hidden;
        }

        a {
            color: inherit;
        }

        button,
        input,
        select {
            font: inherit;
        }

        .contenedor {
            width: min(calc(100% - 40px), var(--contenedor));
            margin-inline: auto;
        }

        .encabezado {
            position: relative;
            z-index: 10;
            color: var(--blanco);
            background: var(--vino-oscuro);
            border-bottom: 1px solid rgba(255, 255, 255, .13);
        }

        .navegacion {
            min-height: 84px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
        }

        .marca {
            display: flex;
            align-items: center;
            gap: 13px;
            color: var(--blanco);
            text-decoration: none;
        }

        .marca-sello {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .34);
            border-radius: 15px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .marca-texto strong,
        .marca-texto span {
            display: block;
        }

        .marca-texto strong {
            font-size: 15px;
        }

        .marca-texto span {
            color: rgba(255, 255, 255, .65);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .nav-enlaces {
            display: flex;
            align-items: center;
            gap: 26px;
            list-style: none;
        }

        .nav-enlaces a {
            color: rgba(255, 255, 255, .78);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: color .25s ease;
        }

        .nav-enlaces a:hover,
        .nav-enlaces a:focus-visible {
            color: var(--blanco);
        }

        .pagina-acceso {
            flex: 1;
            display: grid;
            place-items: center;
            padding: clamp(42px, 7vw, 86px) 0;
        }

        .acceso-panel {
            display: grid;
            grid-template-columns: minmax(360px, .9fr) minmax(470px, 1.1fr);
            width: 100%;
            min-height: 700px;
            border: 1px solid rgba(114, 24, 50, .09);
            border-radius: 32px;
            background: var(--blanco);
            box-shadow: var(--sombra);
            overflow: hidden;
        }

        .acceso-panel,
        .panel-institucional,
        .panel-formulario,
        .formulario,
        .campo {
            min-width: 0;
        }

        .panel-institucional {
            position: relative;
            min-height: 700px;
            display: flex;
            align-items: flex-end;
            padding: clamp(36px, 5vw, 62px);
            color: var(--blanco);
            background:
                linear-gradient(180deg, rgba(46, 6, 20, .16), rgba(57, 8, 25, .94)),
                url("assets/img/index/principal.jpg") center / cover no-repeat;
            overflow: hidden;
        }

        .panel-institucional::before,
        .panel-institucional::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 50%;
        }

        .panel-institucional::before {
            width: 330px;
            height: 330px;
            top: -150px;
            right: -150px;
        }

        .panel-institucional::after {
            width: 190px;
            height: 190px;
            left: -90px;
            bottom: 28%;
        }

        .institucional-contenido {
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 34px;
            height: 2px;
            background: currentColor;
        }

        .panel-institucional h1 {
            max-width: 470px;
            margin-top: 20px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(42px, 5vw, 66px);
            font-weight: 400;
            line-height: .98;
            letter-spacing: -.035em;
            overflow-wrap: break-word;
        }

        .panel-institucional h1 em {
            color: #edc6d1;
            font-weight: 400;
        }

        .panel-institucional p {
            max-width: 430px;
            margin-top: 23px;
            color: rgba(255, 255, 255, .76);
        }

        .beneficios {
            display: grid;
            gap: 11px;
            margin-top: 30px;
            list-style: none;
        }

        .beneficios li {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, .83);
            font-size: 13px;
        }

        .beneficios li::before {
            content: "✓";
            width: 23px;
            height: 23px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, .14);
            font-size: 11px;
            font-weight: 900;
        }

        .panel-formulario {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: clamp(34px, 6vw, 72px);
        }

        .logo-sisdit {
            width: min(100%, 260px);
            height: auto;
            display: block;
            margin: 0 auto 26px;
        }

        .pestanas {
            position: relative;
            display: grid;
            grid-template-columns: 1fr 1fr;
            padding: 5px;
            border-radius: 15px;
            background: #f0ede9;
        }

        .pestana {
            position: relative;
            z-index: 1;
            min-height: 45px;
            border: 0;
            border-radius: 11px;
            color: var(--gris);
            background: transparent;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            min-width: 0;
            transition: color .25s ease, background .25s ease, box-shadow .25s ease;
        }

        .pestana.activa {
            color: var(--vino);
            background: var(--blanco);
            box-shadow: 0 5px 18px rgba(53, 35, 40, .09);
        }

        .alerta {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 12px;
            margin-bottom: 22px;
            padding: 14px 16px;
            border: 1px solid;
            border-radius: 14px;
            font-size: 13px;
        }

        .alerta-icono {
            width: 25px;
            height: 25px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            font-weight: 900;
        }

        .alerta strong {
            display: block;
            margin-bottom: 2px;
        }

        .alerta-error {
            color: #8a2634;
            background: #fcecef;
            border-color: #efc8d0;
        }

        .alerta-aviso {
            color: #74520e;
            background: #fff5d8;
            border-color: #efd99b;
        }

        .alerta-exito {
            color: #23594f;
            background: var(--verde-claro);
            border-color: #bad8d1;
        }

        .enlace-desarrollo {
            display: block;
            margin-top: 6px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .formulario {
            margin-top: 28px;
            animation: aparecer .42s ease both;
        }

        .formulario[hidden] {
            display: none;
        }

        .formulario-cabecera {
            margin-bottom: 26px;
        }

        .formulario-cabecera h2 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(31px, 4vw, 42px);
            font-weight: 500;
            line-height: 1.05;
        }

        .formulario-cabecera p {
            margin-top: 9px;
            color: var(--gris);
            font-size: 14px;
        }

        .campos-dobles {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .campo {
            margin-bottom: 17px;
        }

        .campo label {
            display: block;
            margin-bottom: 7px;
            color: #4b535c;
            font-size: 12px;
            font-weight: 800;
        }

        .campo-control {
            position: relative;
        }

        .campo input,
        .campo select {
            width: 100%;
            min-height: 52px;
            padding: 0 15px;
            border: 1px solid #d8d4ce;
            border-radius: 13px;
            color: var(--tinta);
            background: var(--blanco);
            outline: none;
            transition: border-color .25s ease, box-shadow .25s ease, background .25s ease;
        }

        .campo input::placeholder {
            color: #9a9fa4;
        }

        .campo input:focus,
        .campo select:focus {
            border-color: var(--vino);
            box-shadow: 0 0 0 4px rgba(114, 24, 50, .1);
            background: #fffdfd;
        }

        .campo-password input {
            padding-right: 82px;
        }

        .mostrar-password {
            position: absolute;
            right: 8px;
            top: 50%;
            min-width: 64px;
            height: 36px;
            border: 0;
            border-radius: 9px;
            color: var(--vino);
            background: rgba(114, 24, 50, .07);
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .mostrar-password:hover,
        .mostrar-password:focus-visible {
            background: rgba(114, 24, 50, .13);
            outline: none;
        }

        .boton-principal {
            width: 100%;
            min-height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 7px;
            border: 0;
            border-radius: 14px;
            color: var(--blanco);
            background: var(--vino);
            box-shadow: 0 13px 28px rgba(114, 24, 50, .22);
            font-weight: 800;
            cursor: pointer;
            transition: background .25s ease, box-shadow .25s ease, transform .25s ease;
        }

        .boton-principal:hover {
            background: var(--vino-claro);
            box-shadow: 0 16px 34px rgba(114, 24, 50, .28);
            transform: translateY(-2px);
        }

        .boton-principal span {
            transition: transform .25s ease;
        }

        .boton-principal:hover span {
            transform: translateX(4px);
        }

        .recuperar {
            margin-top: 18px;
            text-align: center;
        }

        .recuperar a {
            color: var(--vino);
            font-size: 13px;
            font-weight: 700;
            text-underline-offset: 4px;
        }

        .reglas-password {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 12px;
            margin-top: 9px;
            list-style: none;
        }

        .reglas-password li {
            position: relative;
            padding-left: 18px;
            color: #8a8f94;
            font-size: 11px;
        }

        .reglas-password li::before {
            content: "○";
            position: absolute;
            left: 0;
            color: #a7aaad;
        }

        .reglas-password li.valida {
            color: var(--verde);
        }

        .reglas-password li.valida::before {
            content: "✓";
            color: var(--verde);
            font-weight: 900;
        }

        .nota-rol {
            margin: -3px 0 17px;
            padding: 12px 14px;
            border: 1px solid #efd99b;
            border-radius: 12px;
            color: #74520e;
            background: #fff5d8;
            font-size: 12px;
        }

        .nota-rol[hidden] {
            display: none;
        }

        .pie {
            padding: 30px 0;
            color: rgba(255, 255, 255, .67);
            background: #260812;
        }

        .pie-contenido {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .pie strong {
            color: var(--blanco);
        }

        .pie p {
            font-size: 11px;
            text-align: right;
        }

        .dialogo {
            width: min(calc(100% - 30px), 450px);
            padding: 0;
            border: 0;
            border-radius: 22px;
            color: var(--tinta);
            box-shadow: var(--sombra);
        }

        .dialogo::backdrop {
            background: rgba(31, 8, 16, .68);
            backdrop-filter: blur(5px);
        }

        .dialogo-contenido {
            padding: 34px;
        }

        .dialogo h2 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 30px;
            font-weight: 500;
        }

        .dialogo p {
            margin-top: 11px;
            color: var(--gris);
        }

        .dialogo-correo {
            margin-top: 17px;
            padding: 13px;
            border-radius: 12px;
            color: var(--vino);
            background: #f6ebee;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .dialogo-acciones {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }

        .dialogo-acciones button {
            min-height: 43px;
            padding: 0 17px;
            border: 1px solid var(--linea);
            border-radius: 11px;
            background: var(--blanco);
            font-weight: 800;
            cursor: pointer;
        }

        .dialogo-acciones .confirmar {
            border-color: var(--verde);
            color: var(--blanco);
            background: var(--verde);
        }

        @keyframes aparecer {
            from { opacity: 0; transform: translateY(13px); }
            to { opacity: 1; transform: none; }
        }

        @media (max-width: 940px) {
            .acceso-panel {
                grid-template-columns: 1fr;
                max-width: 680px;
            }

            .panel-institucional {
                min-height: 400px;
            }
        }

        @media (max-width: 650px) {
            .contenedor {
                width: min(calc(100% - 28px), var(--contenedor));
            }

            .navegacion {
                min-height: 74px;
            }

            .marca-texto span,
            .nav-enlaces li:last-child {
                display: none;
            }

            .nav-enlaces {
                gap: 14px;
            }

            .pagina-acceso {
                padding: 25px 0 45px;
            }

            .acceso-panel {
                width: 100%;
                border-radius: 23px;
            }

            .panel-institucional {
                min-height: 330px;
                padding: 32px 26px;
            }

            .panel-institucional h1 {
                font-size: 43px;
            }

            .beneficios {
                display: none;
            }

            .panel-formulario {
                padding: 32px 22px 38px;
            }

            .pestana {
                padding-inline: 6px;
                font-size: 12px;
            }

            .campos-dobles,
            .reglas-password {
                grid-template-columns: 1fr;
            }

            .pie-contenido {
                flex-direction: column;
                align-items: flex-start;
            }

            .pie p {
                text-align: left;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <header class="encabezado">
        <div class="contenedor navegacion">
            <a class="marca" href="index.php" aria-label="Volver al portal principal">
                <span class="marca-sello" aria-hidden="true">Logo</span>
                <span class="marca-texto">
                    <strong>Portal del Personal</strong>
                    <span>Mejora Regulatoria Municipal</span>
                </span>
            </a>

            <nav aria-label="Navegación principal">
                <ul class="nav-enlaces">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="requisitos.php">Requisitos</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="pagina-acceso">
        <div class="contenedor">
            <section class="acceso-panel" aria-labelledby="tituloAcceso">
                <div class="panel-institucional">
                    <div class="institucional-contenido">
                        <span class="eyebrow">Acceso institucional</span>
                        <h1 id="tituloAcceso">Tu trabajo, <em>en un solo lugar.</em></h1>
                        <p>Ingresa a SisDiT para gestionar, consultar y dar seguimiento a los trámites municipales.</p>
                        <ul class="beneficios">
                            <li>Seguimiento centralizado de solicitudes</li>
                            <li>Información disponible según tu rol</li>
                            <li>Procesos protegidos y trazables</li>
                        </ul>
                    </div>
                </div>

                <div class="panel-formulario">
                    <img class="logo-sisdit" src="logos/SisDiT LOGO.png"
                         alt="SisDiT, Sistema Único de Simplificación y Digitalización de Trámites"
                         width="1640" height="870">

                    <?php if ($alerta): ?>
                        <div class="alerta alerta-<?= htmlspecialchars($alerta['tipo'], ENT_QUOTES, 'UTF-8') ?>" role="alert">
                            <span class="alerta-icono" aria-hidden="true"><?= $alerta['tipo'] === 'exito' ? '✓' : '!' ?></span>
                            <div>
                                <strong><?= htmlspecialchars($alerta['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <span><?= htmlspecialchars($alerta['mensaje'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($enlaceRecuperacion): ?>
                                    <a class="enlace-desarrollo" href="<?= htmlspecialchars($enlaceRecuperacion, ENT_QUOTES, 'UTF-8') ?>">Abrir enlace de recuperación</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="pestanas" role="tablist" aria-label="Opciones de acceso">
                        <button class="pestana<?= !$mostrarRegistro ? ' activa' : '' ?>" id="btnLogin" type="button"
                                role="tab" aria-controls="loginForm" aria-selected="<?= !$mostrarRegistro ? 'true' : 'false' ?>">
                            Iniciar sesión
                        </button>
                        <button class="pestana<?= $mostrarRegistro ? ' activa' : '' ?>" id="btnRegistro" type="button"
                                role="tab" aria-controls="registroForm" aria-selected="<?= $mostrarRegistro ? 'true' : 'false' ?>">
                            Solicitar cuenta
                        </button>
                    </div>

                    <form class="formulario" id="loginForm" action="php/login.php" method="POST"<?= $mostrarRegistro ? ' hidden' : '' ?>>
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <div class="formulario-cabecera">
                            <h2>Bienvenido</h2>
                            <p>Escribe los datos asociados a tu cuenta institucional.</p>
                        </div>

                        <div class="campo">
                            <label for="login_correo">Correo electrónico</label>
                            <div class="campo-control">
                                <input type="email" name="correo" id="login_correo" placeholder="nombre@municipio.gob.mx"
                                       autocomplete="username" required>
                            </div>
                        </div>

                        <div class="campo">
                            <label for="login_password">Contraseña</label>
                            <div class="campo-control campo-password">
                                <input type="password" name="password" id="login_password" placeholder="Ingresa tu contraseña"
                                       autocomplete="current-password" required>
                                <button class="mostrar-password" type="button" data-password="login_password"
                                        aria-label="Mostrar contraseña">Mostrar</button>
                            </div>
                        </div>

                        <button class="boton-principal" type="submit">Ingresar al sistema <span aria-hidden="true">→</span></button>
                        <p class="recuperar"><a href="#" id="forgotPassword">¿Olvidaste tu contraseña?</a></p>
                    </form>

                    <form class="formulario" id="registroForm" action="php/registro.php" method="POST"<?= !$mostrarRegistro ? ' hidden' : '' ?>>
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <div class="formulario-cabecera">
                            <h2>Solicitar una cuenta</h2>
                            <p>Todos los registros requieren revisión y aprobación del administrador.</p>
                        </div>

                        <div class="campos-dobles">
                            <div class="campo">
                                <label for="reg_nombre">Nombre(s)</label>
                                <input type="text" name="nombre" id="reg_nombre" placeholder="NOMBRE(S)" required
                                       pattern="[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+" autocomplete="given-name"
                                       title="Solo se permiten letras y espacios">
                            </div>
                            <div class="campo">
                                <label for="reg_apellidos">Apellidos</label>
                                <input type="text" name="apellidos" id="reg_apellidos" placeholder="APELLIDOS" required
                                       pattern="[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+" autocomplete="family-name"
                                       title="Solo se permiten letras y espacios">
                            </div>
                        </div>

                        <div class="campo">
                            <label for="reg_correo">Correo electrónico</label>
                            <input type="email" name="correo" id="reg_correo" placeholder="nombre@municipio.gob.mx"
                                   autocomplete="email" required>
                        </div>

                        <div class="campo">
                            <label for="reg_telefono">Teléfono para notificaciones <span>(opcional)</span></label>
                            <input type="tel" name="telefono" id="reg_telefono" placeholder="10 dígitos" maxlength="10"
                                   pattern="[0-9]{10}" inputmode="numeric" autocomplete="tel">
                        </div>

                        <div class="campo">
                            <label for="reg_password">Contraseña</label>
                            <div class="campo-control campo-password">
                                <input type="password" name="password" id="reg_password" placeholder="8 caracteres exactos"
                                       minlength="8" maxlength="8" autocomplete="new-password" required>
                                <button class="mostrar-password" type="button" data-password="reg_password"
                                        aria-label="Mostrar contraseña">Mostrar</button>
                            </div>
                            <ul class="reglas-password" id="reglasPassword" aria-live="polite">
                                <li data-regla="longitud">8 caracteres exactos</li>
                                <li data-regla="mayuscula">Una mayúscula</li>
                                <li data-regla="minuscula">Una minúscula</li>
                                <li data-regla="numero">Un número</li>
                                <li data-regla="simbolo">Un símbolo</li>
                            </ul>
                        </div>

                        <div class="campo">
                            <label for="selectRol">Rol solicitado</label>
                            <select name="rol" id="selectRol" required>
                                <option value="">Selecciona tu rol…</option>
                                <option value="Usuario">Usuario</option>
                                <option value="Ventanilla">Ventanilla</option>
                                <option value="Verificador">Verificador</option>
                            </select>
                        </div>

                        <p class="nota-rol" id="notaRol" hidden></p>
                        <button class="boton-principal" type="submit">Enviar solicitud <span aria-hidden="true">→</span></button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <footer class="pie">
        <div class="contenedor pie-contenido">
            <strong>Dirección de Planeación y Desarrollo Urbano</strong>
            <p>© <?= date('Y') ?> Municipio de Rincón de Romos, Aguascalientes.<br>Sistema de Simplificación y Digitalización de Trámites.</p>
        </div>
    </footer>

    <dialog class="dialogo" id="dialogoRecuperacion" aria-labelledby="tituloRecuperacion">
        <div class="dialogo-contenido">
            <h2 id="tituloRecuperacion">Recuperar contraseña</h2>
            <p>Enviaremos un enlace de recuperación al siguiente correo:</p>
            <div class="dialogo-correo" id="correoRecuperacion"></div>
            <div class="dialogo-acciones">
                <button type="button" id="cancelarRecuperacion">Cancelar</button>
                <button class="confirmar" type="button" id="confirmarRecuperacion">Enviar enlace</button>
            </div>
        </div>
    </dialog>

    <script>
        const loginForm = document.getElementById('loginForm');
        const registroForm = document.getElementById('registroForm');
        const btnLogin = document.getElementById('btnLogin');
        const btnRegistro = document.getElementById('btnRegistro');

        function cambiarPestana(mostrarRegistro) {
            loginForm.hidden = mostrarRegistro;
            registroForm.hidden = !mostrarRegistro;
            btnLogin.classList.toggle('activa', !mostrarRegistro);
            btnRegistro.classList.toggle('activa', mostrarRegistro);
            btnLogin.setAttribute('aria-selected', String(!mostrarRegistro));
            btnRegistro.setAttribute('aria-selected', String(mostrarRegistro));
        }

        btnLogin.addEventListener('click', () => cambiarPestana(false));
        btnRegistro.addEventListener('click', () => cambiarPestana(true));

        document.querySelectorAll('.mostrar-password').forEach(boton => {
            boton.addEventListener('click', () => {
                const input = document.getElementById(boton.dataset.password);
                const mostrar = input.type === 'password';
                input.type = mostrar ? 'text' : 'password';
                boton.textContent = mostrar ? 'Ocultar' : 'Mostrar';
                boton.setAttribute('aria-label', `${mostrar ? 'Ocultar' : 'Mostrar'} contraseña`);
            });
        });

        ['reg_nombre', 'reg_apellidos'].forEach(id => {
            document.getElementById(id).addEventListener('input', event => {
                event.target.value = event.target.value
                    .toLocaleUpperCase('es-MX')
                    .replace(/[^A-ZÁÉÍÓÚÜÑ\s]/g, '');
            });
        });

        ['login_correo', 'reg_correo'].forEach(id => {
            document.getElementById(id).addEventListener('input', event => {
                event.target.value = event.target.value.toLocaleLowerCase('es-MX').trimStart();
            });
        });

        document.getElementById('reg_telefono').addEventListener('input', event => {
            event.target.value = event.target.value.replace(/\D/g, '').slice(0, 10);
        });

        const passwordRegistro = document.getElementById('reg_password');
        const reglas = {
            longitud: valor => valor.length === 8,
            mayuscula: valor => /[A-Z]/.test(valor),
            minuscula: valor => /[a-z]/.test(valor),
            numero: valor => /[0-9]/.test(valor),
            simbolo: valor => /[!@#$%^&*(),.?":{}|<>]/.test(valor)
        };

        function validarPassword() {
            const valor = passwordRegistro.value;
            let valida = true;

            Object.entries(reglas).forEach(([nombre, prueba]) => {
                const cumple = prueba(valor);
                document.querySelector(`[data-regla="${nombre}"]`).classList.toggle('valida', cumple);
                valida = valida && cumple;
            });

            return valida;
        }

        passwordRegistro.addEventListener('input', validarPassword);

        registroForm.addEventListener('submit', event => {
            if (!validarPassword()) {
                event.preventDefault();
                passwordRegistro.setCustomValidity('La contraseña debe cumplir todos los requisitos indicados.');
                passwordRegistro.reportValidity();
            } else {
                passwordRegistro.setCustomValidity('');
            }
        });

        passwordRegistro.addEventListener('input', () => passwordRegistro.setCustomValidity(''));

        const selectRol = document.getElementById('selectRol');
        const notaRol = document.getElementById('notaRol');
        selectRol.addEventListener('change', () => {
            if (!selectRol.value) {
                notaRol.hidden = true;
                return;
            }

            notaRol.textContent = selectRol.value === 'Usuario'
                ? 'La solicitud será revisada por el administrador. Recibirás una notificación cuando tu cuenta sea activada.'
                : `El rol ${selectRol.value} requiere aprobación del administrador. Recibirás una notificación por WhatsApp o correo.`;
            notaRol.hidden = false;
        });

        const dialogo = document.getElementById('dialogoRecuperacion');
        const correoRecuperacion = document.getElementById('correoRecuperacion');
        const forgotPassword = document.getElementById('forgotPassword');

        forgotPassword.addEventListener('click', event => {
            event.preventDefault();
            const inputCorreo = document.getElementById('login_correo');

            if (!inputCorreo.checkValidity()) {
                inputCorreo.reportValidity();
                inputCorreo.focus();
                return;
            }

            correoRecuperacion.textContent = inputCorreo.value;
            dialogo.showModal();
        });

        document.getElementById('cancelarRecuperacion').addEventListener('click', () => dialogo.close());
        document.getElementById('confirmarRecuperacion').addEventListener('click', () => {
            window.location.href = `php/recuperar.php?correo=${encodeURIComponent(document.getElementById('login_correo').value)}`;
        });

        dialogo.addEventListener('click', event => {
            if (event.target === dialogo) dialogo.close();
        });
    </script>
</body>
</html>
