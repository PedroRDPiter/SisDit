<?php
/*
        SOLICITUD DE LICENCIA DE CONSTRUCCIÓN
*/

ini_set('session.cookie_httponly', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['usuario'])) {
    header("Location: acceso.php"); exit();
}

require "php/db.php";
require "php/funciones_seguridad.php";

if (!esVerificador() && !esAdministrador() && !esVentanilla() && !esCalificador()) {
    header("Location: acceso.php"); exit;
}

$solicitud_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($solicitud_id <= 0) die("ID de solicitud inválido");

$stmt = $conn->prepare("
    SELECT s.*,
           t.folio_numero AS t_folio_numero, t.folio_anio AS t_folio_anio,
           t.propietario, t.direccion, t.localidad,
           t.calle, t.colonia, t.cuenta_catastral, t.numero
    FROM solicitud_lc s
    INNER JOIN tramites t ON s.tramite_id = t.id
    WHERE s.id = ? LIMIT 1
");
$stmt->bind_param("i", $solicitud_id);
$stmt->execute();
$s = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$s) die("Solicitud no encontrada");

if (trim((string)($s['descripcion_obra'] ?? '')) === '' || trim((string)($s['tipo_obra'] ?? '')) === '') {
    die("Esta solicitud está incompleta (falta descripción o tipo de obra). Complétala en el módulo de Ventanilla antes de imprimirla.");
}

$superficies  = json_decode($s['superficies']  ?? '{}', true) ?? [];
$urbanizacion = json_decode($s['urbanizacion'] ?? '[]', true) ?? [];
$peritos      = json_decode($s['peritos']      ?? '{}', true) ?? [];

$config = [];
$resConfig = $conn->query("SELECT clave, valor FROM configuracion_sistema");
while ($row = $resConfig->fetch_assoc()) $config[$row['clave']] = $row['valor'];
$municipio = $config['municipio_nombre'] ?? 'RINCÓN DE ROMOS';
$director  = $config['director_nombre']  ?? '';
$cargo     = $config['director_cargo']   ?? 'DIRECTOR DE PLANEACIÓN Y DESARROLLO URBANO';

$folioSol     = str_pad($s['folio_numero'],    3,'0',STR_PAD_LEFT) . '/' . $s['folio_anio'];
$folioTramite = 'ING.' . str_pad($s['t_folio_numero'],3,'0',STR_PAD_LEFT) . '/' . $s['t_folio_anio'];

$meses = ['','ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
$fp = explode('-', date('Y-m-d', strtotime($s['fecha_registro'])));
$fecha_formateada = (int)$fp[2] . ' DE ' . $meses[(int)$fp[1]] . ' DE ' . $fp[0];

// Helpers
$urb = function($srv) use ($urbanizacion) { return in_array($srv, $urbanizacion); };
$sup = function($k) use ($superficies) {
    // otra_area es texto libre
    if ($k === 'otra_area') {
        $v = $superficies['otra_area'] ?? '';
        if (is_array($v)) $v = $v['valor'] ?? '';
        if ($v === null || $v === '') return '';
        return nl2br(htmlspecialchars($v));
    }
    $obj = $superficies[$k] ?? [];
    $v   = is_array($obj) ? ($obj['valor'] ?? '') : ($obj ?? '');
    $u   = is_array($obj) ? ($obj['unidad'] ?? 'm2') : 'm2';
    if ($v === null || $v === '') return '';
    return htmlspecialchars($v) . ' ' . ($u === 'mlin' ? 'Mlin' : 'M²');
};
$perito = function($p, $campo) use ($peritos) {
    return htmlspecialchars($peritos[$p][$campo] ?? '');
};
$tipo_obra = $s['tipo_obra'] ?? '';

if (esAdministrador())  $back = 'DashAdmin.php';
elseif (esCalificador()) $back = 'DashCalf.php';
elseif (esVentanilla()) $back = 'DashVentanilla.php';
else                    $back = 'DashVer.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Solicitud LC - <?= htmlspecialchars($folioSol) ?></title>
<style>
    @page { size: letter; margin: 0.8cm 1.2cm; }
    * { margin:0; padding:0; box-sizing:border-box; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    body { font-family:Arial,sans-serif; font-size:8.5pt; color:#000; background:#fff; line-height:1.3; }

    .no-print {
        position:fixed; top:0; left:0; right:0;
        background:#7b0f2b; color:#fff;
        padding:8px 16px; display:flex; gap:10px; align-items:center; z-index:1000;
    }
    .no-print span { font-size:8.5pt; font-weight:bold; margin-right:auto; }
    .no-print button { padding:6px 16px; border:none; border-radius:4px; cursor:pointer; font-size:9pt; font-weight:bold; }
    .btn-print { background:#fff; color:#7b0f2b; }
    .btn-back  { background:#555; color:#fff; }

    .container { max-width:21cm; margin:45px auto 10px; padding:0; }

    /* Header igual a constancia */
    .header {
        display:flex; justify-content:space-between; align-items:flex-start;
        margin-bottom:5px; border-bottom:3px solid #7b0f2b; padding-bottom:5px;
    }
    .header-left { display:flex; align-items:center; gap:8px; }
    .header-left img { height:55px; }
    .titulo-dep { font-size:11pt; font-weight:bold; color:#7b0f2b; line-height:1.2; }
    .subtitulo  { font-size:8pt; color:#666; }
    .header-right img { height:55px; }

    .titulo-principal {
        text-align:center; font-size:12pt; font-weight:bold;
        color:#7b0f2b; margin:6px 0 5px; letter-spacing:1px;
    }

    /* Línea de folios y fecha */
    .folios-line {
        display:flex; justify-content:space-between;
        font-size:8pt; font-weight:bold; margin-bottom:5px;
        border-bottom:1px solid #7b0f2b; padding-bottom:3px;
    }

    /* Tablas de datos */
    .tabla-datos { width:100%; border-collapse:collapse; margin-bottom:4px; }
    .tabla-datos td, .tabla-datos th { border:1px solid #7b0f2b; padding:3px 5px; font-size:8.5pt; }
    .tabla-datos .header-row td {
        background:#f5e6e9; font-weight:bold; text-align:center;
        font-size:8pt; color:#7b0f2b;
    }
    .tabla-datos .label { background:#f9f9f9; font-weight:bold; color:#333; white-space:nowrap; }
    .tabla-datos .valor { text-transform:uppercase; }

    /* Checkbox igual a constancia */
    .checkbox {
        display:inline-block; width:10px; height:10px;
        border:1.5px solid #7b0f2b; margin-right:3px; vertical-align:middle;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .checkbox.checked {
        background-color:#7b0f2b !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Firma propietario */
    .firma-prop {
        border-top:1px solid #000; text-align:center;
        font-size:7.5pt; padding-top:2px; margin-top:25px;
    }

    /* Firmas peritos */
    .firma-perito {
        border-top:1px solid #000; text-align:center;
        font-size:7pt; padding-top:2px; height:28px;
    }

    /* Nota al pie */
    .notas {
        border:1px solid #999; padding:4px 6px;
        margin:4px 0; font-size:7.5pt; background:#fafafa;
    }
    .notas-titulo { font-weight:bold; margin-bottom:2px; color:#7b0f2b; }
    .notas p { margin:1px 0; text-align:justify; }

    @media print {
        .no-print { display:none !important; }
        .container { margin:0; max-width:100%; }
    }
</style>
</head>
<body>

<div class="no-print">
    <span>Solicitud de Licencia de Construcción — <?= htmlspecialchars($folioSol) ?></span>
    <button class="btn-print" onclick="window.print()">🖨️ Imprimir</button>
    <button class="btn-back"  onclick="window.location.href='<?= $back ?>'">← Volver</button>
</div>

<div class="container">

    <!-- ENCABEZADO -->
    <div class="header">
        <div class="header-left">
            <img src="logos/LogoDepFondo.png" alt="Logo DPDU">
            <div>
                <div class="titulo-dep">Dirección de Planeación<br>y Desarrollo Urbano</div>
                <div class="subtitulo"><?= htmlspecialchars($municipio) ?></div>
            </div>
        </div>
        <div class="header-right">
            <img src="logos/logoPresi.png" alt="Logo Presidencia">
        </div>
    </div>

    <div class="titulo-principal">SOLICITUD DE LICENCIA DE CONSTRUCCIÓN</div>

    <!-- FOLIOS Y FECHA -->
    <div class="folios-line">
        <span>Folio de solicitud: <?= htmlspecialchars($folioSol) ?></span>
        <span>Folio de trámite: <?= htmlspecialchars($folioTramite) ?></span>
        <span>Fecha: <?= $fecha_formateada ?></span>
    </div>

    <!-- DATOS DEL PROPIETARIO -->
    <table class="tabla-datos">
        <tr><td colspan="4" class="header-row">DATOS DEL PROPIETARIO</td></tr>
        <tr>
            <td class="label" style="width:18%;">Nombre:</td>
            <td class="valor" colspan="2" ><?= htmlspecialchars($s['propietario']) ?></td>
            <td rowspan="3" style="width:30%; text-align:center; vertical-align:bottom; padding-bottom:4px;">
                <div class="firma-prop">Firma del propietario</div>
            </td>
        </tr>
        <tr>
            <td class="label">Domicilio:</td>
            <td class="valor" colspan="2"><?= htmlspecialchars($s['direccion']) ?></td>
        </tr>
        <tr>
            <td class="label">Localidad:</td>
            <td class="valor" colspan="2"><?= htmlspecialchars($s['localidad']) ?></td>
        </tr>
    </table>

    <!-- DATOS DEL PREDIO -->
    <table class="tabla-datos">
        <tr><td colspan="4" class="header-row">DATOS DEL PREDIO</td></tr>
        <tr>
            <td class="label" style="width:20%;">Calle:</td>
            <td class="valor" style="width:30%;"><?= htmlspecialchars($s['calle'] ?? '') ?></td>
            <td class="label" style="width:20%;">Fracc. / Colonia:</td>
            <td class="valor"><?= htmlspecialchars($s['colonia'] ?? '') ?></td>
        </tr>
        <tr>
            <td class="label">Manzana:</td>
            <td class="valor">&nbsp;</td>
            <td class="label">Lote:</td>
            <td class="valor">&nbsp;</td>

        </tr>
        <tr>
            <td class="label" style="width:20%;">Cuenta catastral:</td>
            <td class="valor" style="width:30%;"><?= htmlspecialchars($s['cuenta_catastral'] ?? '') ?></td>
            <td class="label" style="width:20%;">Numero:</td>
            <td class="valor" style="width:30%;"># <?= htmlspecialchars($s['numero'] ?? '') ?></td>
        </tr>
    </table>

    <!-- TIPO DE OBRA + URBANIZACIÓN + SUPERFICIES (3 columnas) -->
    <table class="tabla-datos">
        <tr>
            <td class="header-row" style="width:22%;">TIPO DE OBRA</td>
            <td class="header-row" style="width:35%;">URBANIZACIÓN EXISTENTE</td>
            <td class="header-row">SUPERFICIES</td>
        </tr>
        <tr style="vertical-align:top;">
            <!-- Tipo de obra -->
            <td style="padding:6px 8px;">
                <?php foreach (['Construcción','Demolición','Otro'] as $t_obra): ?>
                <div style="margin-bottom:5px;">
                    <span class="checkbox <?= $tipo_obra===$t_obra?'checked':'' ?>"></span>
                    <?= $t_obra ?>
                </div>
                <?php endforeach; ?>
            </td>
            <!-- Urbanización -->
            <td style="padding:6px 8px;">
                <table style="width:100%; border-collapse:collapse; font-size:9pt;">
                    <tr>
                        <td style="width:60%;"></td>
                        <td style="width:20%; text-align:center; font-weight:bold; color:#7b0f2b; font-size:8pt;">SÍ</td>
                        <td style="width:20%; text-align:center; font-weight:bold; color:#7b0f2b; font-size:8pt;">NO</td>
                    </tr>
                    <?php foreach (['Agua potable','Drenaje','Electricidad','Guarnición','Banqueta','Pavimento'] as $srv): ?>
                    <tr>
                        <td><?= $srv ?></td>
                        <td style="text-align:center;"><span class="checkbox <?= $urb($srv)?'checked':'' ?>"></span></td>
                        <td style="text-align:center;"><span class="checkbox <?= !$urb($srv)?'checked':'' ?>"></span></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </td>
            <!-- Superficies -->
            <td style="padding:6px 8px;">
                <table style="width:100%; border-collapse:collapse; font-size:9pt;">
                    <?php
                    $campos_sup = [
                        'sotano'        => 'Sótano',
                        'planta_baja'   => 'Planta baja',
                        'primer_nivel'  => 'Primer nivel',
                        'segundo_nivel' => 'Segundo nivel',
                        'tercer_nivel'  => 'Tercer nivel',
                        'otra_area'     => 'Otro',
                    ];
                    foreach ($campos_sup as $k => $label):
                    ?>
                    <tr>
                        <td style="font-weight:bold; white-space:nowrap; padding:2px 0; width:55%;"><?= $label ?>:</td>
                        <td style="border-bottom:1px solid #ccc; padding:2px 4px;"><?= $sup($k) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </td>
        </tr>
    </table>

        <!-- DESCRIPCIÓN -->
    <table class="tabla-datos">
        <tr><td class="header-row">DESCRIPCIÓN DE LA CONSTRUCCIÓN</td></tr>
        <tr><td style="min-height:45px; height:50px; vertical-align:top; padding:4px 6px;">
            <?= nl2br(htmlspecialchars($s['descripcion_obra'] ?? '')) ?>
        </td></tr>
    </table>

    <!-- PERITOS DE OBRA -->
    <table class="tabla-datos">
        <tr><td colspan="5" class="header-row">PERITOS DE OBRA</td></tr>
        <tr>
            <td class="label" style="width:22%;">Tipo</td>
            <td class="label" style="width:28%;">Nombre</td>
            <td class="label" style="width:18%;">N° de registro</td>
            <td class="label" style="width:18%;">Cédula profesional</td>
            <td class="label" style="width:14%; text-align:center;">Firma</td>
        </tr>
        <?php foreach ([
            'dro'          => 'D.R.O.',
            'estructural'  => 'Estructural',
            'especialista' => 'Instalaciones especiales'
        ] as $p => $label): ?>
        <tr>
            <td class="label"><?= $label ?></td>
            <td class="valor"><?= $perito($p,'nombre') ?></td>
            <td class="valor"><?= $perito($p,'registro') ?></td>
            <td class="valor"><?= $perito($p,'cedula') ?></td>
            <td><div class="firma-perito"></div></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- NOTA -->
    <div class="notas">
        <div class="notas-titulo">NOTA:</div>
        <p>Deberá registrar la obra en el IMSS incluyendo autoconstrucción antes de iniciar los trabajos.</p>
        <p>Todo el escombro producto de la construcción deberá depositarlo en tiradero de escombro municipal.</p>
        <p>Al terminar la obra deberá presentar la manifestación de predio urbano en la receptoría de rentas.</p>
    </div>

</div>

<?php if (isset($_GET['autoprint'])): ?>
<script>window.onload = function(){ setTimeout(function(){ window.print(); }, 600); };</script>
<?php endif; ?>

</body>
</html>
