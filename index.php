<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal interno del Departamento de Mejora Regulatoria para el personal del Municipio de Rincón de Romos.">
    <meta name="theme-color" content="#6f1731">
    <title>Portal del Personal | Mejora Regulatoria</title>

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
            --sombra: 0 24px 70px rgba(53, 35, 40, .12);
            --radio: 24px;
            --contenedor: 1180px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--crema);
            color: var(--tinta);
            font-family: "Segoe UI", Inter, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        body.menu-abierto {
            overflow: hidden;
        }

        a {
            color: inherit;
        }

        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        .contenedor {
            width: min(calc(100% - 40px), var(--contenedor));
            margin-inline: auto;
        }

        .encabezado {
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            color: var(--blanco);
            transition: background .35s ease, box-shadow .35s ease, padding .35s ease;
        }

        .encabezado::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            height: 1px;
            background: rgba(255, 255, 255, .18);
        }

        .encabezado.scrolled {
            background: rgba(75, 14, 34, .96);
            box-shadow: 0 12px 35px rgba(33, 8, 17, .22);
            backdrop-filter: blur(16px);
        }

        .navegacion {
            min-height: 88px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .marca {
            display: flex;
            align-items: center;
            gap: 13px;
            color: var(--blanco);
            text-decoration: none;
        }

        .marca-imagen {
            width: 54px;
            height: 54px;
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            border: 1px dashed rgba(255, 255, 255, .55);
            border-radius: 14px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .08em;
            text-align: center;
            text-transform: uppercase;
        }

        .marca-texto strong,
        .marca-texto span {
            display: block;
        }

        .marca-texto strong {
            font-size: 15px;
            line-height: 1.25;
        }

        .marca-texto span {
            margin-top: 2px;
            font-size: 11px;
            letter-spacing: .11em;
            opacity: .74;
            text-transform: uppercase;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .menu a {
            position: relative;
            color: var(--blanco);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .menu a:not(.boton-nav)::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: -8px;
            left: 0;
            height: 2px;
            background: var(--blanco);
            transform: scaleX(0);
            transition: transform .25s ease;
        }

        .menu a:hover::after,
        .menu a:focus-visible::after {
            transform: scaleX(1);
        }

        .boton-nav {
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            padding: 0 20px;
            border: 1px solid rgba(255, 255, 255, .42);
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            transition: background .25s ease, transform .25s ease;
        }

        .boton-nav:hover {
            background: var(--blanco);
            color: var(--vino-oscuro);
            transform: translateY(-2px);
        }

        .menu-toggle {
            display: none;
            width: 46px;
            height: 46px;
            border: 1px solid rgba(255, 255, 255, .4);
            border-radius: 50%;
            background: rgba(255, 255, 255, .1);
            cursor: pointer;
        }

        .menu-toggle span {
            display: block;
            width: 20px;
            height: 2px;
            margin: 5px auto;
            border-radius: 2px;
            background: var(--blanco);
            transition: transform .25s ease, opacity .25s ease;
        }

        .hero {
            position: relative;
            min-height: 760px;
            display: flex;
            align-items: center;
            padding: 150px 0 100px;
            overflow: hidden;
            color: var(--blanco);
            background:
                radial-gradient(circle at 82% 20%, rgba(255, 255, 255, .12), transparent 25%),
                linear-gradient(125deg, var(--vino-oscuro) 0%, var(--vino) 58%, #8f2948 100%);
        }

        .hero::before,
        .hero::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 50%;
        }

        .hero::before {
            width: 520px;
            height: 520px;
            right: -190px;
            top: 100px;
        }

        .hero::after {
            width: 300px;
            height: 300px;
            right: 40px;
            bottom: -160px;
        }

        .hero-contenido {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            align-items: center;
            gap: clamp(50px, 8vw, 100px);
        }

        .hero-texto,
        .contenido-nosotros,
        .accesos-grid > * {
            min-width: 0;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
            color: currentColor;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 36px;
            height: 2px;
            background: currentColor;
        }

        .hero h1 {
            max-width: 720px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(44px, 6.2vw, 82px);
            font-weight: 500;
            letter-spacing: -.045em;
            line-height: .99;
        }

        .hero h1 em {
            color: #efd3dc;
            font-weight: 400;
        }

        .hero-descripcion {
            max-width: 620px;
            margin-top: 28px;
            color: rgba(255, 255, 255, .82);
            font-size: clamp(16px, 1.7vw, 19px);
        }

        .acciones {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 36px;
        }

        .boton {
            min-height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0 24px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
        }

        .boton:hover {
            transform: translateY(-3px);
        }

        .boton-claro {
            background: var(--blanco);
            color: var(--vino-oscuro);
            box-shadow: 0 12px 30px rgba(34, 7, 15, .2);
        }

        .boton-transparente {
            border-color: rgba(255, 255, 255, .45);
            color: var(--blanco);
            background: rgba(255, 255, 255, .06);
        }

        .boton-vino {
            background: var(--vino);
            color: var(--blanco);
            box-shadow: 0 12px 28px rgba(114, 24, 50, .2);
        }

        .flecha {
            transition: transform .25s ease;
        }

        .boton:hover .flecha {
            transform: translateX(4px);
        }

        .marco-imagen {
            position: relative;
            min-height: 480px;
            display: grid;
            place-items: center;
            border: 1px dashed rgba(255, 255, 255, .45);
            border-radius: 220px 220px 28px 28px;
            background: rgba(255, 255, 255, .075);
            box-shadow: 0 35px 80px rgba(37, 6, 17, .24);
            backdrop-filter: blur(5px);
            overflow: hidden;
        }

        .marco-imagen::before {
            content: "";
            position: absolute;
            inset: 16px;
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: inherit;
        }

        .placeholder {
            position: relative;
            z-index: 1;
            max-width: 170px;
            color: rgba(255, 255, 255, .62);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .13em;
            text-align: center;
            text-transform: uppercase;
        }

        .placeholder-icono {
            width: 58px;
            height: 46px;
            display: block;
            margin: 0 auto 14px;
            border: 1px solid currentColor;
            border-radius: 10px;
            opacity: .8;
        }

        .placeholder-icono::before {
            content: "";
            width: 9px;
            height: 9px;
            display: block;
            margin: 9px 0 0 10px;
            border: 1px solid currentColor;
            border-radius: 50%;
        }

        .hero-indicador {
            position: absolute;
            z-index: 3;
            bottom: 30px;
            left: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, .62);
            font-size: 9px;
            letter-spacing: .2em;
            text-transform: uppercase;
            transform: translateX(-50%);
        }

        .hero-indicador::after {
            content: "";
            width: 1px;
            height: 30px;
            background: linear-gradient(var(--blanco), transparent);
            animation: pulsoLinea 1.8s ease-in-out infinite;
        }

        .franja {
            position: relative;
            z-index: 4;
            margin-top: -1px;
            background: var(--blanco);
            border-bottom: 1px solid var(--linea);
        }

        .franja-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .franja-item {
            min-height: 118px;
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 25px clamp(20px, 3vw, 42px);
            border-right: 1px solid var(--linea);
        }

        .franja-item:last-child {
            border-right: 0;
        }

        .franja-numero {
            color: var(--vino);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 34px;
            line-height: 1;
        }

        .franja-item p {
            color: var(--gris);
            font-size: 13px;
            line-height: 1.4;
        }

        .seccion {
            padding: clamp(80px, 10vw, 140px) 0;
        }

        .seccion-cabecera {
            max-width: 750px;
            margin-bottom: 54px;
        }

        .seccion-cabecera.centrada {
            margin-inline: auto;
            text-align: center;
        }

        .seccion-cabecera.centrada .eyebrow {
            justify-content: center;
        }

        .seccion h2 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(38px, 5vw, 60px);
            font-weight: 500;
            letter-spacing: -.035em;
            line-height: 1.06;
        }

        .seccion-cabecera p {
            max-width: 650px;
            margin-top: 20px;
            color: var(--gris);
            font-size: 17px;
        }

        .seccion-cabecera.centrada p {
            margin-inline: auto;
        }

        .nosotros-grid {
            display: grid;
            grid-template-columns: .9fr 1.1fr;
            align-items: center;
            gap: clamp(50px, 8vw, 100px);
        }

        .imagen-secundaria {
            min-height: 570px;
            display: grid;
            place-items: center;
            border: 1px dashed #bbb6ae;
            border-radius: 28px;
            background:
                linear-gradient(145deg, rgba(114, 24, 50, .055), transparent 60%),
                #eeece7;
            color: #969088;
            overflow: hidden;
        }

        .contenido-nosotros .eyebrow {
            color: var(--vino);
        }

        .contenido-nosotros > p {
            margin-top: 24px;
            color: var(--gris);
            font-size: 17px;
        }

        .lista-valores {
            margin-top: 38px;
            border-top: 1px solid var(--linea);
        }

        .valor {
            display: grid;
            grid-template-columns: 52px 1fr;
            gap: 18px;
            padding: 24px 0;
            border-bottom: 1px solid var(--linea);
        }

        .valor-numero {
            color: var(--vino);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .12em;
        }

        .valor h3 {
            margin-bottom: 5px;
            font-size: 17px;
        }

        .valor p {
            color: var(--gris);
            font-size: 14px;
        }

        .servicios {
            background: var(--blanco);
        }

        .servicios-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .servicio {
            --acento: var(--vino);
            --acento-rgb: 114, 24, 50;
            position: relative;
            isolation: isolate;
            display: flex;
            flex-direction: column;
            min-height: 320px;
            padding: 34px;
            border: 1px solid var(--linea);
            border-radius: var(--radio);
            color: inherit;
            background:
                radial-gradient(circle at 100% 100%, rgba(var(--acento-rgb), .06), transparent 42%),
                var(--blanco);
            text-decoration: none;
            overflow: hidden;
            transition:
                transform .42s cubic-bezier(.2, .8, .2, 1),
                box-shadow .42s ease,
                border-color .42s ease;
        }

        .servicio:nth-child(2) {
            --acento: var(--verde);
            --acento-rgb: 40, 116, 104;
        }

        .servicio:nth-child(3) {
            --acento: #a35f18;
            --acento-rgb: 163, 95, 24;
        }

        .servicio > * {
            z-index: 1;
        }

        .servicio::before {
            content: "";
            position: absolute;
            inset: auto -70px -100px auto;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(var(--acento-rgb), .12);
            transition: transform .55s cubic-bezier(.2, .8, .2, 1), opacity .4s ease;
        }

        .servicio::after {
            content: "";
            position: absolute;
            z-index: 0;
            inset: -55% -35%;
            background: linear-gradient(
                105deg,
                transparent 42%,
                rgba(255, 255, 255, .72) 49%,
                rgba(var(--acento-rgb), .08) 52%,
                transparent 59%
            );
            pointer-events: none;
            transform: translateX(-65%) rotate(8deg);
            transition: transform .8s cubic-bezier(.2, .75, .25, 1);
        }

        .servicio:hover {
            border-color: rgba(var(--acento-rgb), .34);
            box-shadow: 0 26px 58px rgba(var(--acento-rgb), .17);
            transform: translateY(-12px) scale(1.015);
        }

        .servicio:hover::before {
            opacity: .9;
            transform: scale(1.48) translate(-8px, -8px);
        }

        .servicio:hover::after {
            transform: translateX(65%) rotate(8deg);
        }

        .servicio:focus-visible {
            border-color: var(--vino);
            box-shadow: 0 0 0 4px rgba(114, 24, 50, .14), var(--sombra);
            outline: none;
            transform: translateY(-10px) scale(1.01);
        }

        .servicio:focus-visible::before {
            transform: scale(1.25);
        }

        .servicio:focus-visible::after {
            transform: translateX(65%) rotate(8deg);
        }

        .servicio-icono {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            margin-bottom: 58px;
            border-radius: 16px;
            background: rgba(var(--acento-rgb), .1);
            color: var(--acento);
            font-size: 20px;
            font-weight: 700;
            box-shadow: 0 0 0 0 rgba(var(--acento-rgb), 0);
            transition:
                color .35s ease,
                background .35s ease,
                box-shadow .35s ease,
                transform .45s cubic-bezier(.2, .8, .2, 1);
        }

        .servicio h3 {
            position: relative;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 25px;
            font-weight: 500;
            line-height: 1.18;
            transition: color .3s ease, transform .35s ease;
        }

        .servicio p {
            position: relative;
            margin-top: 13px;
            color: var(--gris);
            font-size: 14px;
            transition: color .3s ease, transform .35s ease;
        }

        .servicio:hover .servicio-icono,
        .servicio:focus-visible .servicio-icono {
            color: var(--blanco);
            background: var(--acento);
            box-shadow: 0 12px 24px rgba(var(--acento-rgb), .24);
            transform: rotate(-7deg) scale(1.1);
        }

        .servicio:hover h3,
        .servicio:focus-visible h3 {
            color: var(--acento);
            transform: translateY(-3px);
        }

        .servicio:hover p,
        .servicio:focus-visible p {
            color: var(--tinta);
            transform: translateY(-2px);
        }

        .servicio-enlace {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: auto;
            padding-top: 24px;
            color: var(--acento);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .servicio-enlace b {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: var(--acento);
            background: rgba(var(--acento-rgb), .1);
            font-size: 18px;
            transition: color .3s ease, background .3s ease, transform .3s ease;
        }

        .servicio:hover .servicio-enlace b,
        .servicio:focus-visible .servicio-enlace b {
            color: var(--blanco);
            background: var(--acento);
            transform: translate(3px, -3px) rotate(8deg);
        }

        .accesos {
            color: var(--blanco);
            background: var(--vino-oscuro);
        }

        .accesos .eyebrow {
            color: #e7bdca;
        }

        .accesos-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: end;
            gap: 60px;
        }

        .accesos h2 {
            max-width: 700px;
        }

        .accesos p {
            max-width: 570px;
            margin-top: 22px;
            color: rgba(255, 255, 255, .7);
        }

        .accesos-tarjetas {
            display: grid;
            gap: 14px;
        }

        .acceso-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 24px 26px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 18px;
            color: var(--blanco);
            background: rgba(255, 255, 255, .055);
            text-decoration: none;
            transition: transform .25s ease, background .25s ease;
        }

        .acceso-card:hover {
            background: rgba(255, 255, 255, .12);
            transform: translateX(6px);
        }

        .acceso-card span {
            display: block;
            color: rgba(255, 255, 255, .55);
            font-size: 11px;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .acceso-card strong {
            display: block;
            margin-top: 3px;
            font-size: 17px;
        }

        .contacto-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 26px;
        }

        .contacto-info,
        .mapa-placeholder {
            min-height: 410px;
            border-radius: var(--radio);
        }

        .contacto-info {
            padding: clamp(34px, 5vw, 60px);
            color: var(--blanco);
            background: var(--verde);
        }

        .contacto-info h2 {
            max-width: 480px;
            font-size: clamp(34px, 4vw, 50px);
        }

        .contacto-info > p {
            max-width: 500px;
            margin-top: 18px;
            color: rgba(255, 255, 255, .77);
        }

        .datos-contacto {
            display: grid;
            gap: 16px;
            margin-top: 42px;
        }

        .dato-contacto {
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, .2);
        }

        .dato-contacto small {
            display: block;
            margin-bottom: 3px;
            color: rgba(255, 255, 255, .58);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .15em;
            text-transform: uppercase;
        }

        .mapa-placeholder {
            display: grid;
            place-items: center;
            border: 1px dashed #bbb6ae;
            color: #8b857d;
            background:
                linear-gradient(rgba(255, 255, 255, .28) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .28) 1px, transparent 1px),
                #e9e6df;
            background-size: 34px 34px;
        }

        .marco-imagen,
        .imagen-secundaria,
        .mapa-placeholder {
            position: relative;
            overflow: hidden;
            border-style: solid;
        }

        .imagen-portal {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .imagen-planeacion {
            object-position: center 66%;
        }

        .imagen-ventanilla {
            object-position: center 36%;
        }

        .pie {
            padding: 36px 0;
            color: rgba(255, 255, 255, .72);
            background: #260812;
        }

        .pie-contenido {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .pie-marca {
            color: var(--blanco);
            font-weight: 700;
        }

        .pie p {
            font-size: 12px;
            text-align: right;
        }

        .volver-arriba {
            position: fixed;
            z-index: 900;
            right: clamp(18px, 3vw, 38px);
            bottom: clamp(18px, 3vw, 38px);
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 50%;
            color: var(--blanco);
            background: var(--vino);
            box-shadow: 0 14px 34px rgba(38, 8, 18, .28);
            font-size: 24px;
            line-height: 1;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            cursor: pointer;
            transform: translateY(18px) scale(.88);
            transition:
                opacity .3s ease,
                visibility .3s ease,
                transform .3s ease,
                background .25s ease;
        }

        .volver-arriba.visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: none;
        }

        .volver-arriba:hover {
            background: var(--vino-claro);
            transform: translateY(-4px);
        }

        .volver-arriba:focus-visible {
            outline: 3px solid rgba(114, 24, 50, .3);
            outline-offset: 4px;
        }

        .revelar {
            opacity: 0;
            filter: blur(7px);
            transform: translateY(52px) scale(.975);
            transition:
                opacity .85s cubic-bezier(.2, .75, .25, 1),
                filter .85s cubic-bezier(.2, .75, .25, 1),
                transform .85s cubic-bezier(.2, .75, .25, 1);
            will-change: opacity, filter, transform;
        }

        .revelar.visible {
            opacity: 1;
            filter: none;
            transform: none;
        }

        .imagen-secundaria.revelar,
        .contacto-info.revelar {
            transform: translateX(-56px) scale(.975);
        }

        .contenido-nosotros.revelar,
        .mapa-placeholder.revelar,
        .accesos-tarjetas.revelar {
            transform: translateX(56px) scale(.975);
        }

        .servicio.revelar {
            transform: translateY(58px) scale(.94);
        }

        .imagen-secundaria.revelar.visible,
        .contacto-info.revelar.visible,
        .contenido-nosotros.revelar.visible,
        .mapa-placeholder.revelar.visible,
        .accesos-tarjetas.revelar.visible,
        .servicio.revelar.visible {
            transform: none;
        }

        .contenido-nosotros .valor,
        .accesos-tarjetas .acceso-card {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity .55s ease, transform .55s ease;
        }

        .contenido-nosotros.visible .valor,
        .accesos-tarjetas.visible .acceso-card {
            opacity: 1;
            transform: none;
        }

        .contenido-nosotros.visible .valor:nth-child(2),
        .accesos-tarjetas.visible .acceso-card:nth-child(2) {
            transition-delay: .12s;
        }

        .contenido-nosotros.visible .valor:nth-child(3),
        .accesos-tarjetas.visible .acceso-card:nth-child(3) {
            transition-delay: .24s;
        }

        .retraso-1 { transition-delay: .12s; }
        .retraso-2 { transition-delay: .24s; }

        @keyframes entradaHero {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: none; }
        }

        @keyframes pulsoLinea {
            0%, 100% { opacity: .3; transform: scaleY(.7); transform-origin: top; }
            50% { opacity: 1; transform: scaleY(1); transform-origin: top; }
        }

        .hero-texto > * {
            animation: entradaHero .75s both;
        }

        .hero-texto h1 { animation-delay: .08s; }
        .hero-descripcion { animation-delay: .16s; }
        .acciones { animation-delay: .24s; }
        .hero .marco-imagen { animation: entradaHero .9s .22s both; }

        @media (max-width: 980px) {
            .menu-toggle {
                display: block;
                position: relative;
                z-index: 1002;
            }

            .menu-toggle.activo span:nth-child(1) {
                transform: translateY(7px) rotate(45deg);
            }

            .menu-toggle.activo span:nth-child(2) {
                opacity: 0;
            }

            .menu-toggle.activo span:nth-child(3) {
                transform: translateY(-7px) rotate(-45deg);
            }

            .menu {
                position: fixed;
                inset: 0;
                z-index: 1001;
                flex-direction: column;
                justify-content: center;
                gap: 30px;
                background: var(--vino-oscuro);
                opacity: 0;
                pointer-events: none;
                transform: translateY(-15px);
                transition: opacity .3s ease, transform .3s ease;
            }

            .menu.abierto {
                opacity: 1;
                pointer-events: auto;
                transform: none;
            }

            .menu a {
                font-size: 20px;
            }

            .hero-contenido,
            .nosotros-grid,
            .accesos-grid {
                grid-template-columns: 1fr;
            }

            .hero {
                min-height: auto;
            }

            .hero-contenido {
                gap: 60px;
            }

            .hero-texto {
                text-align: center;
            }

            .hero-descripcion {
                margin-inline: auto;
            }

            .hero .eyebrow,
            .acciones {
                justify-content: center;
            }

            .marco-imagen {
                width: min(100%, 590px);
                min-height: 430px;
                margin-inline: auto;
            }

            .franja-grid {
                grid-template-columns: 1fr;
            }

            .franja-item {
                min-height: 92px;
                border-right: 0;
                border-bottom: 1px solid var(--linea);
            }

            .servicios-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 720px) {
            .contenedor {
                width: min(calc(100% - 30px), var(--contenedor));
            }

            .navegacion {
                min-height: 76px;
            }

            .marca-imagen {
                width: 46px;
                height: 46px;
            }

            .marca-texto strong {
                font-size: 13px;
            }

            .marca-texto span {
                font-size: 9px;
            }

            .hero {
                padding: 125px 0 80px;
            }

            .hero h1 {
                max-width: 100%;
                font-size: clamp(40px, 11vw, 54px);
                overflow-wrap: break-word;
            }

            .hero-indicador {
                display: none;
            }

            .acciones {
                width: 100%;
                max-width: 410px;
                flex-direction: column;
                margin-inline: auto;
            }

            .boton {
                width: 100%;
            }

            .marco-imagen {
                min-height: 350px;
                border-radius: 160px 160px 24px 24px;
            }

            .imagen-secundaria {
                min-height: 400px;
            }

            .servicios-grid,
            .contacto-grid {
                grid-template-columns: 1fr;
            }

            .servicio {
                min-height: 270px;
            }

            .servicio-icono {
                margin-bottom: 40px;
            }

            .pie-contenido {
                flex-direction: column;
                align-items: flex-start;
            }

            .pie p {
                text-align: left;
            }
        }

        @media (max-width: 480px) {
            .marca-texto span {
                display: none;
            }

            .franja-item {
                padding-inline: 15px;
            }

            .contacto-info,
            .mapa-placeholder {
                min-height: 360px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }

            .revelar {
                opacity: 1;
                filter: none;
                transform: none;
            }

            .contenido-nosotros .valor,
            .accesos-tarjetas .acceso-card {
                opacity: 1;
                transform: none;
            }

            .servicio:hover,
            .servicio:focus-visible,
            .servicio:hover .servicio-icono,
            .servicio:focus-visible .servicio-icono,
            .servicio:hover h3,
            .servicio:focus-visible h3,
            .servicio:hover p,
            .servicio:focus-visible p,
            .servicio:hover .servicio-enlace b,
            .servicio:focus-visible .servicio-enlace b {
                transform: none;
            }
        }
    </style>
</head>

<body>
    <header class="encabezado" id="encabezado">
        <div class="contenedor navegacion">
            <a class="marca" href="#inicio" aria-label="Ir al inicio">
                <span class="marca-imagen" aria-hidden="true">Logo</span>
                <span class="marca-texto">
                    <strong>Portal del Personal</strong>
                    <span>Mejora Regulatoria Municipal</span>
                </span>
            </a>

            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="menuPrincipal">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav aria-label="Navegación principal">
                <ul class="menu" id="menuPrincipal">
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#objetivo">Objetivo</a></li>
                    <li><a href="#herramientas">Herramientas</a></li>
                    <li><a href="#soporte">Soporte</a></li>
                    <li><a class="boton-nav" href="acceso.php">Iniciar sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero" id="inicio">
            <div class="contenedor hero-contenido">
                <div class="hero-texto">
                    <span class="eyebrow">Uso exclusivo del personal municipal</span>
                    <h1>Herramientas claras para un municipio <em>en transformación.</em></h1>
                    <p class="hero-descripcion">
                        Un espacio interno para consultar recursos, gestionar procesos y acceder a los sistemas que apoyan las actividades diarias del Municipio de Rincón de Romos.
                    </p>
                    <div class="acciones">
                        <a class="boton boton-claro" href="acceso.php">
                            Iniciar sesión <span class="flecha" aria-hidden="true">→</span>
                        </a>
                        <a class="boton boton-transparente" href="#herramientas">Ver herramientas</a>
                    </div>
                </div>

                <div class="marco-imagen">
                    <img class="imagen-portal imagen-principal"
                         src="assets/img/index/principal.jpg"
                         alt="Fachada del Palacio Municipal de Rincón de Romos iluminada por la noche"
                         width="1420" height="800" fetchpriority="high">
                </div>
            </div>

            <span class="hero-indicador" aria-hidden="true">Descubre</span>
        </section>

        <section class="franja" aria-label="Principios del departamento">
            <div class="contenedor franja-grid">
                <div class="franja-item revelar">
                    <span class="franja-numero">01</span>
                    <p>Procesos internos más claros, ordenados y fáciles de consultar.</p>
                </div>
                <div class="franja-item revelar retraso-1">
                    <span class="franja-numero">02</span>
                    <p>Menos tareas repetitivas y mejor coordinación entre áreas.</p>
                </div>
                <div class="franja-item revelar retraso-2">
                    <span class="franja-numero">03</span>
                    <p>Información institucional disponible en un mismo lugar.</p>
                </div>
            </div>
        </section>

        <section class="seccion" id="objetivo">
            <div class="contenedor nosotros-grid">
                <div class="imagen-secundaria revelar">
                    <img class="imagen-portal imagen-planeacion"
                         src="assets/img/index/plan.jpg"
                         alt="Personal municipal reunido para revisar un plano de trabajo"
                         width="1200" height="1600">
                </div>

                <div class="contenido-nosotros revelar retraso-1">
                    <span class="eyebrow">Objetivo del portal</span>
                    <h2>Facilitamos el trabajo de las áreas municipales.</h2>
                    <p>
                        El Departamento de Mejora Regulatoria pone a disposición del personal municipal herramientas y recursos para estandarizar procesos, reducir cargas administrativas y mejorar la gestión diaria de trámites y servicios.
                    </p>

                    <div class="lista-valores">
                        <article class="valor">
                            <span class="valor-numero">01</span>
                            <div>
                                <h3>Organización</h3>
                                <p>Información y procesos internos concentrados para facilitar su consulta.</p>
                            </div>
                        </article>
                        <article class="valor">
                            <span class="valor-numero">02</span>
                            <div>
                                <h3>Digitalización</h3>
                                <p>Herramientas tecnológicas que reducen tiempos y tareas manuales.</p>
                            </div>
                        </article>
                        <article class="valor">
                            <span class="valor-numero">03</span>
                            <div>
                                <h3>Coordinación</h3>
                                <p>Criterios compartidos para fortalecer el trabajo entre dependencias.</p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion servicios" id="herramientas">
            <div class="contenedor">
                <div class="seccion-cabecera centrada revelar">
                    <span class="eyebrow" style="color: var(--vino);">Recursos internos</span>
                    <h2>Herramientas de Mejora Regulatoria</h2>
                    <p>Accede rápidamente a las plataformas e información que apoyan la operación de las áreas municipales.</p>
                </div>

                <div class="servicios-grid">
                    <a class="servicio revelar" href="http://10.1.85.9/sisdit/acceso.php"
                       target="_blank" rel="noopener noreferrer">
                        <span class="servicio-icono" aria-hidden="true">01</span>
                        <h3>Gestión de trámites de Planeación y Desarrollo Urbano (SISDIT)</h3>
                        <p>Registra, consulta y da seguimiento a los procesos asignados desde SisDiT.</p>
                        <span class="servicio-enlace">Abrir sistema <b aria-hidden="true">↗</b></span>
                    </a>

                    <a class="servicio revelar retraso-1" href="http://10.1.85.9:3344/"
                       target="_blank" rel="noopener noreferrer">
                        <span class="servicio-icono" aria-hidden="true">02</span>
                        <h3>Control de Oficios</h3>
                        <p>Organiza el seguimiento de oficios y mantén disponible la información de cada gestión.</p>
                        <span class="servicio-enlace">Abrir sistema <b aria-hidden="true">↗</b></span>
                    </a>

                    <a class="servicio revelar retraso-2" href="http://10.1.85.9/poa/"
                       target="_blank" rel="noopener noreferrer">
                        <span class="servicio-icono" aria-hidden="true">03</span>
                        <h3>Programa Operativo Anual</h3>
                        <p>Programa para presupuestos y operaciones anuales.</p>
                        <span class="servicio-enlace">Abrir sistema <b aria-hidden="true">↗</b></span>
                    </a>
                </div>
            </div>
        </section>

        <section class="seccion" id="soporte">
            <div class="contenedor">
                <div class="seccion-cabecera revelar">
                    <span class="eyebrow" style="color: var(--vino);">Apoyo al personal</span>
                    <h2>¿Necesitas orientación?</h2>
                </div>

                <div class="contacto-grid">
                    <div class="contacto-info revelar">
                        <h2>Cuenta con el apoyo de Mejora Regulatoria.</h2>
                        <p>Solicita orientación para el uso de las plataformas, la revisión de procesos o la actualización de información institucional.</p>

                        <div class="datos-contacto">
                            <div class="dato-contacto">
                                <small>Área responsable</small>
                                <span>Departamento de Mejora Regulatoria</span>
                            </div>
                            <div class="dato-contacto">
                                <small>Atención interna</small>
                                <span>Lunes a viernes · 9:00 A.M. - 3:00 P.M.</span>
                            </div>
                            <div class="dato-contacto">
                                <small>Canal de soporte</small>
                                <span>Dir.Planeacionydu@gmail.com</span>
                            </div>
                        </div>
                    </div>

                    <div class="mapa-placeholder revelar retraso-1">
                        <img class="imagen-portal imagen-ventanilla"
                             src="assets/img/index/ventanilla.jpg"
                             alt="Atención a un ciudadano en una ventanilla municipal"
                             width="960" height="1280">
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="pie">
        <div class="contenedor pie-contenido">
            <div class="pie-marca">Portal interno · Mejora Regulatoria</div>
            <p>© <?= date('Y') ?> Municipio de Rincón de Romos, Aguascalientes.<br>Sitio de uso exclusivo para el personal municipal.</p>
        </div>
    </footer>

    <button class="volver-arriba" id="volverArriba" type="button"
            aria-label="Volver al inicio de la página" aria-hidden="true" title="Volver arriba">
        <span aria-hidden="true">↑</span>
    </button>

    <script>
        const encabezado = document.getElementById('encabezado');
        const menuToggle = document.getElementById('menuToggle');
        const menuPrincipal = document.getElementById('menuPrincipal');
        const volverArriba = document.getElementById('volverArriba');
        const actualizarInterfazScroll = () => {
            encabezado.classList.toggle('scrolled', window.scrollY > 24);

            const mostrarBoton = window.scrollY > 320;
            volverArriba.classList.toggle('visible', mostrarBoton);
            volverArriba.setAttribute('aria-hidden', String(!mostrarBoton));
        };

        const cerrarMenu = () => {
            menuToggle.classList.remove('activo');
            menuPrincipal.classList.remove('abierto');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Abrir menú');
            document.body.classList.remove('menu-abierto');
        };

        menuToggle.addEventListener('click', () => {
            const estaAbierto = menuPrincipal.classList.toggle('abierto');
            menuToggle.classList.toggle('activo', estaAbierto);
            menuToggle.setAttribute('aria-expanded', String(estaAbierto));
            menuToggle.setAttribute('aria-label', estaAbierto ? 'Cerrar menú' : 'Abrir menú');
            document.body.classList.toggle('menu-abierto', estaAbierto);
        });

        menuPrincipal.querySelectorAll('a').forEach(enlace => {
            enlace.addEventListener('click', cerrarMenu);
        });

        volverArriba.addEventListener('click', () => {
            const reducirMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({
                top: 0,
                behavior: reducirMovimiento ? 'auto' : 'smooth'
            });
        });

        window.addEventListener('scroll', actualizarInterfazScroll, { passive: true });
        window.addEventListener('resize', () => {
            if (window.innerWidth > 980) cerrarMenu();
        });
        actualizarInterfazScroll();

        const elementos = document.querySelectorAll('.revelar');

        if ('IntersectionObserver' in window) {
            const observador = new IntersectionObserver((entradas, observer) => {
                entradas.forEach(entrada => {
                    if (entrada.isIntersecting) {
                        entrada.target.classList.add('visible');
                        observer.unobserve(entrada.target);
                    }
                });
            }, {
                threshold: .12,
                rootMargin: '0px 0px -8% 0px'
            });

            elementos.forEach(elemento => observador.observe(elemento));
        } else {
            elementos.forEach(elemento => elemento.classList.add('visible'));
        }
    </script>
</body>
</html>
