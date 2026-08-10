<?php
$tramites = [
    [
        'codigo' => 'NUM-01',
        'nombre' => 'Constancia de Número Oficial',
        'descripcion' => 'Asignación del número oficial que identifica un inmueble dentro del municipio.',
        'requisitos' => [
            'INE o pasaporte vigente.',
            'Boleta predial vigente.',
            'Título de propiedad o escritura pública.',
            'Datos de ubicación del predio.'
        ],
        'nota' => 'Si el trámite lo realiza una tercera persona, deberá presentar carta poder.',
        'fundamento' => 'Capítulo III, artículo 30 del Reglamento de Ordenamiento Territorial.',
        'acento' => '#721832'
    ],
    [
        'codigo' => 'CMCU-02',
        'nombre' => 'Constancia de Compatibilidad Urbanística',
        'descripcion' => 'Consulta la compatibilidad del uso solicitado con la ubicación y características del predio.',
        'requisitos' => [
            'INE o pasaporte vigente.',
            'Boleta predial vigente.',
            'Título de propiedad o escritura pública.',
            'Formato de constancia.'
        ],
        'nota' => 'Para uso comercial se requiere contrato de arrendamiento y medidas de superficie. En predios menores a 10,000 m² se solicita plano catastral; para superficies mayores, levantamiento topográfico catastral.',
        'fundamento' => 'Artículo 576 del Código Urbano aplicable.',
        'acento' => '#287468'
    ],
    [
        'codigo' => 'FUS-03',
        'nombre' => 'Fusión de Predios',
        'descripcion' => 'Integración de dos o más predios para conformar una sola unidad territorial.',
        'requisitos' => [
            'INE o pasaporte vigente.',
            'Boleta predial vigente.',
            'Título de propiedad o escritura pública.'
        ],
        'nota' => 'Cuando la superficie sea mayor a 10,000 m² se requiere levantamiento topográfico.',
        'fundamento' => '',
        'acento' => '#b46a22'
    ],
    [
        'codigo' => 'SUB-04',
        'nombre' => 'Subdivisión de Predio',
        'descripcion' => 'División de un predio en dos o más fracciones conforme a la normativa aplicable.',
        'requisitos' => [
            'INE o pasaporte vigente.',
            'Boleta predial vigente.',
            'Título de propiedad o escritura pública.'
        ],
        'nota' => '',
        'fundamento' => '',
        'acento' => '#7650a8'
    ],
    [
        'codigo' => 'ICU-05',
        'nombre' => 'Informe de Compatibilidad Urbanística',
        'descripcion' => 'Informe técnico sobre la compatibilidad urbanística correspondiente al predio consultado.',
        'requisitos' => [
            'INE o pasaporte vigente.',
            'Cuenta catastral del predio.'
        ],
        'nota' => '',
        'fundamento' => 'Capítulo III, artículo 30 del Reglamento de Ordenamiento Territorial.',
        'acento' => '#27739a'
    ],
    [
        'codigo' => 'TO-06',
        'nombre' => 'Terminación de Obra',
        'descripcion' => 'Constancia correspondiente a la conclusión de una obra registrada ante el municipio.',
        'requisitos' => [
            'INE o pasaporte vigente.',
            'Boleta predial vigente.',
            'Título de propiedad o escritura pública.'
        ],
        'nota' => '',
        'fundamento' => '',
        'acento' => '#6f42a6'
    ],
    [
        'codigo' => 'LC-07',
        'nombre' => 'Licencia de Construcción',
        'descripcion' => 'Autorización municipal para trabajos de construcción, ampliación o remodelación.',
        'requisitos' => [
            'INE o pasaporte vigente.',
            'Boleta predial vigente.',
            'Título de propiedad o escritura pública.'
        ],
        'nota' => '',
        'fundamento' => '',
        'acento' => '#b83a46'
    ],
    [
        'codigo' => 'AP-08',
        'nombre' => 'Anuncios Publicitarios',
        'descripcion' => 'Trámite para la colocación o regularización de anuncios publicitarios.',
        'requisitos' => [
            'INE o pasaporte vigente.',
            'Boleta predial vigente.',
            'Contrato de arrendamiento o escritura pública.',
            'Memoria descriptiva o cálculo de superficie.'
        ],
        'nota' => 'Cuando el solicitante sea una empresa, deberá presentar poder notariado y acta constitutiva.',
        'fundamento' => '',
        'acento' => '#a87913'
    ],
    [
        'codigo' => 'VOBO-09',
        'nombre' => 'Visto Bueno',
        'descripcion' => 'Recepción y seguimiento de una solicitud de visto bueno emitida mediante oficio.',
        'requisitos' => [
            'Oficio de solicitud de visto bueno.'
        ],
        'nota' => 'Se deberá anexar el oficio solicitado en formato legible.',
        'fundamento' => '',
        'acento' => '#5f6872'
    ]
];

$totalTramites = count($tramites);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Consulta los requisitos de los trámites de Planeación y Desarrollo Urbano del Municipio de Rincón de Romos.">
    <meta name="theme-color" content="#721832">
    <title>Requisitos para Trámites | Planeación y Desarrollo Urbano</title>

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
        }

        a {
            color: inherit;
        }

        button,
        input {
            font: inherit;
        }

        .contenedor {
            width: min(calc(100% - 40px), var(--contenedor));
            margin-inline: auto;
        }

        .encabezado {
            position: sticky;
            z-index: 1000;
            top: 0;
            color: var(--blanco);
            background: rgba(75, 14, 34, .96);
            border-bottom: 1px solid rgba(255, 255, 255, .14);
            backdrop-filter: blur(14px);
        }

        .navegacion {
            min-height: 86px;
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

        .marca-sello {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .36);
            border-radius: 16px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .06em;
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
            color: rgba(255, 255, 255, .68);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .nav-enlaces {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .nav-enlaces a {
            color: rgba(255, 255, 255, .82);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: color .25s ease;
        }

        .nav-enlaces a:hover,
        .nav-enlaces a:focus-visible {
            color: var(--blanco);
        }

        .nav-enlaces .boton-nav {
            padding: 11px 19px;
            border: 1px solid rgba(255, 255, 255, .34);
            border-radius: 999px;
        }

        .hero {
            position: relative;
            padding: clamp(76px, 10vw, 130px) 0 clamp(92px, 11vw, 145px);
            color: var(--blanco);
            background:
                radial-gradient(circle at 82% 18%, rgba(156, 49, 80, .58), transparent 34%),
                linear-gradient(120deg, #590f29, var(--vino));
            overflow: hidden;
        }

        .hero::before,
        .hero::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 50%;
        }

        .hero::before {
            width: 430px;
            height: 430px;
            right: -90px;
            top: -180px;
        }

        .hero::after {
            width: 260px;
            height: 260px;
            right: 15%;
            bottom: -180px;
        }

        .hero-contenido {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: end;
            gap: 50px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .17em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 36px;
            height: 2px;
            background: currentColor;
        }

        h1,
        h2,
        h3 {
            font-family: Georgia, "Times New Roman", serif;
            line-height: 1.05;
        }

        .hero h1 {
            max-width: 820px;
            margin-top: 22px;
            font-size: clamp(46px, 7vw, 82px);
            font-weight: 500;
            letter-spacing: -.04em;
        }

        .hero h1 em {
            color: #edc6d1;
            font-weight: 400;
        }

        .hero p {
            max-width: 700px;
            margin-top: 24px;
            color: rgba(255, 255, 255, .76);
            font-size: 17px;
        }

        .contador {
            min-width: 190px;
            padding: 26px;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: var(--radio);
            background: rgba(255, 255, 255, .08);
            backdrop-filter: blur(10px);
        }

        .contador strong {
            display: block;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 56px;
            font-weight: 400;
            line-height: 1;
        }

        .contador span {
            display: block;
            margin-top: 7px;
            color: rgba(255, 255, 255, .68);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .catalogo {
            padding: clamp(70px, 9vw, 120px) 0;
        }

        .barra-catalogo {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(300px, 430px);
            align-items: end;
            gap: 45px;
            margin-bottom: 45px;
        }

        .barra-catalogo h2 {
            max-width: 650px;
            margin-top: 14px;
            font-size: clamp(36px, 5vw, 58px);
            font-weight: 500;
        }

        .barra-catalogo .eyebrow {
            color: var(--vino);
        }

        .buscador label {
            display: block;
            margin-bottom: 9px;
            color: var(--gris);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .campo-busqueda {
            position: relative;
        }

        .campo-busqueda::before {
            content: "⌕";
            position: absolute;
            left: 18px;
            top: 50%;
            color: var(--vino);
            font-size: 25px;
            transform: translateY(-54%);
        }

        .campo-busqueda input {
            width: 100%;
            height: 56px;
            padding: 0 48px;
            border: 1px solid var(--linea);
            border-radius: 16px;
            color: var(--tinta);
            background: var(--blanco);
            outline: none;
            transition: border-color .25s ease, box-shadow .25s ease;
        }

        .campo-busqueda input:focus {
            border-color: var(--vino);
            box-shadow: 0 0 0 4px rgba(114, 24, 50, .1);
        }

        .resultado-busqueda {
            margin-top: 9px;
            color: var(--gris);
            font-size: 13px;
        }

        .tramites-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .tramite-card {
            --acento: #721832;
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 100%;
            padding: clamp(26px, 4vw, 38px);
            border: 1px solid var(--linea);
            border-radius: var(--radio);
            background: var(--blanco);
            box-shadow: 0 10px 34px rgba(53, 35, 40, .05);
            overflow: hidden;
            transition: transform .4s cubic-bezier(.2, .8, .2, 1), box-shadow .4s ease, border-color .4s ease;
        }

        .tramite-card::after {
            content: "";
            position: absolute;
            right: -65px;
            bottom: -85px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: color-mix(in srgb, var(--acento) 13%, transparent);
            transition: transform .45s ease;
        }

        .tramite-card:hover {
            border-color: color-mix(in srgb, var(--acento) 35%, var(--linea));
            box-shadow: var(--sombra);
            transform: translateY(-8px);
        }

        .tramite-card:hover::after {
            transform: scale(1.2);
        }

        .card-cabecera,
        .card-contenido {
            position: relative;
            z-index: 1;
        }

        .card-cabecera {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 18px;
            align-items: start;
        }

        .card-numero {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border-radius: 17px;
            color: var(--acento);
            background: color-mix(in srgb, var(--acento) 11%, white);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 20px;
        }

        .card-codigo {
            color: var(--acento);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .tramite-card h3 {
            margin-top: 6px;
            font-size: clamp(24px, 3vw, 31px);
            font-weight: 500;
        }

        .card-descripcion {
            margin-top: 16px;
            color: var(--gris);
            font-size: 14px;
        }

        .card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 20px;
        }

        .etiqueta {
            padding: 7px 11px;
            border-radius: 999px;
            color: var(--acento);
            background: color-mix(in srgb, var(--acento) 9%, white);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .card-contenido {
            margin-top: 27px;
            padding-top: 23px;
            border-top: 1px solid var(--linea);
        }

        .card-contenido h4 {
            margin-bottom: 14px;
            font-size: 12px;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .requisitos-lista {
            display: grid;
            gap: 11px;
            list-style: none;
        }

        .requisitos-lista li {
            position: relative;
            padding-left: 29px;
            color: #46505a;
            font-size: 14px;
        }

        .requisitos-lista li::before {
            content: "✓";
            position: absolute;
            left: 0;
            top: 1px;
            width: 20px;
            height: 20px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: var(--blanco);
            background: var(--acento);
            font-size: 11px;
            font-weight: 900;
        }

        .nota,
        .fundamento {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            font-size: 13px;
        }

        .nota {
            color: #6b4a0c;
            background: #fff5d8;
            border: 1px solid #efd99b;
        }

        .fundamento {
            color: #35544f;
            background: var(--verde-claro);
            border: 1px solid #bfd8d2;
        }

        .sin-resultados {
            grid-column: 1 / -1;
            padding: 60px 25px;
            border: 1px dashed #bbb6ae;
            border-radius: var(--radio);
            color: var(--gris);
            text-align: center;
            background: rgba(255, 255, 255, .45);
        }

        .aviso-general {
            margin-top: 42px;
            padding: clamp(28px, 5vw, 46px);
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 24px;
            border-radius: var(--radio);
            color: var(--blanco);
            background: var(--verde);
        }

        .aviso-icono {
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 17px;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 28px;
        }

        .aviso-general h2 {
            font-size: clamp(26px, 4vw, 38px);
            font-weight: 500;
        }

        .aviso-general p {
            max-width: 850px;
            margin-top: 10px;
            color: rgba(255, 255, 255, .8);
        }

        .contacto {
            padding: 0 0 clamp(70px, 9vw, 110px);
        }

        .contacto-panel {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid var(--linea);
            border-radius: var(--radio);
            background: var(--blanco);
            overflow: hidden;
        }

        .contacto-panel > div {
            padding: clamp(30px, 5vw, 52px);
        }

        .contacto-panel > div + div {
            border-left: 1px solid var(--linea);
        }

        .contacto small {
            display: block;
            margin-bottom: 7px;
            color: var(--vino);
            font-weight: 800;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .contacto strong {
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(22px, 3vw, 30px);
            font-weight: 500;
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

        .pie strong {
            color: var(--blanco);
        }

        .pie p {
            font-size: 12px;
            text-align: right;
        }

        .volver-arriba {
            position: fixed;
            z-index: 900;
            right: 26px;
            bottom: 26px;
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 50%;
            color: var(--blanco);
            background: var(--vino);
            box-shadow: 0 14px 34px rgba(38, 8, 18, .28);
            font-size: 22px;
            opacity: 0;
            visibility: hidden;
            cursor: pointer;
            transform: translateY(16px);
            transition: opacity .3s ease, visibility .3s ease, transform .3s ease;
        }

        .volver-arriba.visible {
            opacity: 1;
            visibility: visible;
            transform: none;
        }

        .revelar {
            opacity: 0;
            transform: translateY(42px);
            transition: opacity .75s ease, transform .75s cubic-bezier(.2, .75, .25, 1);
        }

        .revelar.visible {
            opacity: 1;
            transform: none;
        }

        [hidden] {
            display: none !important;
        }

        @media (max-width: 850px) {
            .hero-contenido,
            .barra-catalogo {
                grid-template-columns: 1fr;
            }

            .contador {
                width: min(100%, 280px);
            }

            .tramites-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 680px) {
            .contenedor {
                width: min(calc(100% - 30px), var(--contenedor));
            }

            .navegacion {
                min-height: 76px;
            }

            .marca-texto span,
            .nav-enlaces li:not(:last-child) {
                display: none;
            }

            .marca-sello {
                width: 46px;
                height: 46px;
                border-radius: 14px;
            }

            .marca-texto strong {
                font-size: 13px;
            }

            .nav-enlaces {
                gap: 10px;
            }

            .nav-enlaces .boton-nav {
                padding: 9px 13px;
                white-space: nowrap;
            }

            .hero {
                padding-top: 72px;
            }

            .card-cabecera,
            .aviso-general {
                grid-template-columns: 1fr;
            }

            .contacto-panel {
                grid-template-columns: 1fr;
            }

            .contacto-panel > div + div {
                border-top: 1px solid var(--linea);
                border-left: 0;
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
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }

            .revelar {
                opacity: 1;
                transform: none;
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
                    <li><a href="#catalogo">Requisitos</a></li>
                    <li><a class="boton-nav" href="acceso.php">Iniciar sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="contenedor hero-contenido">
                <div>
                    <span class="eyebrow">Planeación y Desarrollo Urbano</span>
                    <h1>Requisitos para <em>trámites.</em></h1>
                    <p>Consulta los documentos y consideraciones registrados para cada servicio antes de acudir a ventanilla.</p>
                </div>
                <aside class="contador" aria-label="Cantidad de trámites disponibles">
                    <strong><?= $totalTramites ?></strong>
                    <span>Trámites disponibles</span>
                </aside>
            </div>
        </section>

        <section class="catalogo" id="catalogo">
            <div class="contenedor">
                <div class="barra-catalogo revelar">
                    <div>
                        <span class="eyebrow">Catálogo completo</span>
                        <h2>Encuentra tu trámite y prepara tus documentos.</h2>
                    </div>
                    <div class="buscador">
                        <label for="buscarTramite">Buscar trámite o requisito</label>
                        <div class="campo-busqueda">
                            <input type="search" id="buscarTramite" placeholder="Ej. construcción, predial, INE…" autocomplete="off">
                        </div>
                        <p class="resultado-busqueda" id="resultadoBusqueda" aria-live="polite"><?= $totalTramites ?> trámites disponibles</p>
                    </div>
                </div>

                <div class="tramites-grid" id="tramitesGrid">
                    <?php foreach ($tramites as $indice => $tramite): ?>
                        <?php
                        $numero = str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT);
                        $textoBusqueda = $tramite['nombre'] . ' ' . $tramite['descripcion'] . ' ' . implode(' ', $tramite['requisitos']) . ' ' . $tramite['nota'];
                        ?>
                        <article class="tramite-card revelar"
                                 style="--acento: <?= htmlspecialchars($tramite['acento'], ENT_QUOTES, 'UTF-8') ?>"
                                 data-busqueda="<?= htmlspecialchars($textoBusqueda, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="card-cabecera">
                                <span class="card-numero" aria-hidden="true"><?= $numero ?></span>
                                <div>
                                    <span class="card-codigo"><?= htmlspecialchars($tramite['codigo'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <h3><?= htmlspecialchars($tramite['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="card-descripcion"><?= htmlspecialchars($tramite['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <div class="card-meta">
                                        <span class="etiqueta">10 días hábiles</span>
                                        <span class="etiqueta"><?= count($tramite['requisitos']) ?> requisito<?= count($tramite['requisitos']) === 1 ? '' : 's' ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-contenido">
                                <h4>Documentos y datos requeridos</h4>
                                <ul class="requisitos-lista">
                                    <?php foreach ($tramite['requisitos'] as $requisito): ?>
                                        <li><?= htmlspecialchars($requisito, ENT_QUOTES, 'UTF-8') ?></li>
                                    <?php endforeach; ?>
                                </ul>

                                <?php if ($tramite['nota'] !== ''): ?>
                                    <p class="nota"><strong>Consideración:</strong> <?= htmlspecialchars($tramite['nota'], ENT_QUOTES, 'UTF-8') ?></p>
                                <?php endif; ?>

                                <?php if ($tramite['fundamento'] !== ''): ?>
                                    <p class="fundamento"><strong>Referencia:</strong> <?= htmlspecialchars($tramite['fundamento'], ENT_QUOTES, 'UTF-8') ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>

                    <div class="sin-resultados" id="sinResultados" hidden>
                        <h3>No encontramos coincidencias</h3>
                        <p>Prueba con el nombre del trámite o de algún documento, como “predial” o “INE”.</p>
                    </div>
                </div>

                <aside class="aviso-general revelar">
                    <span class="aviso-icono" aria-hidden="true">i</span>
                    <div>
                        <h2>Antes de acudir a ventanilla</h2>
                        <p>Todos los requisitos deberán entregarse en copia legible. Si el trámite será realizado por una tercera persona, deberá presentar carta poder. La autoridad puede solicitar información complementaria según las características particulares del predio o proyecto.</p>
                    </div>
                </aside>
            </div>
        </section>

        <section class="contacto">
            <div class="contenedor contacto-panel revelar">
                <div>
                    <small>WhatsApp</small>
                    <strong>449 807 78 99</strong>
                </div>
                <div>
                    <small>Correo de atención</small>
                    <strong>dir.planeacionydu@gmail.com</strong>
                </div>
            </div>
        </section>
    </main>

    <footer class="pie">
        <div class="contenedor pie-contenido">
            <strong>Dirección de Planeación y Desarrollo Urbano</strong>
            <p>© <?= date('Y') ?> Municipio de Rincón de Romos, Aguascalientes.<br>Sistema de Simplificación y Digitalización de Trámites.</p>
        </div>
    </footer>

    <button class="volver-arriba" id="volverArriba" type="button" aria-label="Volver al inicio" title="Volver arriba">↑</button>

    <script>
        const buscador = document.getElementById('buscarTramite');
        const tarjetas = Array.from(document.querySelectorAll('.tramite-card'));
        const resultado = document.getElementById('resultadoBusqueda');
        const sinResultados = document.getElementById('sinResultados');
        const volverArriba = document.getElementById('volverArriba');

        const normalizar = texto => texto
            .toLocaleLowerCase('es')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();

        buscador.addEventListener('input', () => {
            const consulta = normalizar(buscador.value);
            let visibles = 0;

            tarjetas.forEach(tarjeta => {
                const coincide = normalizar(tarjeta.dataset.busqueda || '').includes(consulta);
                tarjeta.hidden = !coincide;
                if (coincide) visibles++;
            });

            resultado.textContent = visibles === 1 ? '1 trámite encontrado' : `${visibles} trámites encontrados`;
            sinResultados.hidden = visibles !== 0;
        });

        const elementos = document.querySelectorAll('.revelar');
        if ('IntersectionObserver' in window) {
            const observador = new IntersectionObserver((entradas, observer) => {
                entradas.forEach(entrada => {
                    if (entrada.isIntersecting) {
                        entrada.target.classList.add('visible');
                        observer.unobserve(entrada.target);
                    }
                });
            }, { threshold: .1, rootMargin: '0px 0px -7% 0px' });

            elementos.forEach(elemento => observador.observe(elemento));
        } else {
            elementos.forEach(elemento => elemento.classList.add('visible'));
        }

        const actualizarBoton = () => {
            volverArriba.classList.toggle('visible', window.scrollY > 420);
        };

        volverArriba.addEventListener('click', () => {
            const reducirMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({ top: 0, behavior: reducirMovimiento ? 'auto' : 'smooth' });
        });

        window.addEventListener('scroll', actualizarBoton, { passive: true });
        actualizarBoton();
    </script>
</body>
</html>
