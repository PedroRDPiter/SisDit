<?php
/*
--------------------------
 LICENCIA DE CONSTRUCCIÓN
--------------------------

 Se imprime a partir de un registro ya Aprobado en tramites_salida.

 Página 1: la licencia en sí (datos, superficies, peritos, vigencia).

 Página 2 (reverso): reglamento fijo + observaciones + nombre del calificador.

 */

ini_set('session.cookie_httponly', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['usuario'])) {
    header("Location: acceso.php"); exit();
}

require "php/db.php";
require "php/funciones_seguridad.php";

if (!esCalificador() && !esVerificador() && !esVentanilla() && !esAdministrador()) {
    header("Location: acceso.php"); exit;
}

$ts_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($ts_id <= 0) die("ID de licencia inválido");

$stmt = $conn->prepare("
    SELECT ts.*,
           t.propietario, t.direccion, t.colonia, t.localidad,
           t.estatus AS tramite_estatus,
           t.calle, t.numero, t.manzana, t.lote, t.cuenta_catastral,
           s.descripcion_obra, s.tipo_obra, s.superficies, s.urbanizacion, s.peritos,
           CONCAT(u.nombre, ' ', u.apellidos) AS calificado_por_nombre
    FROM tramites_salida ts
    INNER JOIN tramites t ON ts.tramite_id = t.id
    LEFT JOIN solicitud_lc s ON s.tramite_id = t.id
    LEFT JOIN usuarios u ON u.id = ts.calificado_por
    WHERE ts.id = ? LIMIT 1
");
$stmt->bind_param("i", $ts_id);
$stmt->execute();
$l = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$l) die("Licencia no encontrada");

if ($l['estatus'] !== 'Aprobado') {
    die("Esta licencia aún no está aprobada. No se puede imprimir hasta que Calificador la apruebe.");
}
$estatusVerificadorPermitido = ['Pendiente por firmar', 'Firmado', 'Entregado y archivado', 'Aprobado por Verificador', 'Aprobado'];
if (!in_array($l['tramite_estatus'], $estatusVerificadorPermitido, true)) {
    die("Esta licencia aún no está aprobada por el Verificador.");
}
if (empty($l['vigencia']) || empty($l['expiracion'])) {
    die("Falta la vigencia/expiración de esta licencia. Complétala en el módulo de Calificador antes de imprimir.");
}

$superficies  = json_decode($l['superficies']  ?? '{}', true) ?? [];
$urbanizacion = json_decode($l['urbanizacion'] ?? '[]', true) ?? [];
$peritos      = json_decode($l['peritos']      ?? '{}', true) ?? [];

$folioLic = $l['folio_salida_numero'] !== null
    ? str_pad($l['folio_salida_numero'], 3, '0', STR_PAD_LEFT) . '/' . $l['folio_salida_anio']
    : '';

// Helpers de fecha: parten "YYYY-MM-DD" en DIA/MES/AÑO para las cajitas del formato
function partirFecha(?string $f): array {
    if (empty($f)) return ['', '', ''];
    $p = explode('-', substr($f, 0, 10));
    if (count($p) !== 3) return ['', '', ''];
    return [$p[2], $p[1], $p[0]]; // DIA, MES, AÑO
}
[$f_dia, $f_mes, $f_anio]   = partirFecha($l['fecha_salida']);
[$v_dia, $v_mes, $v_anio]   = partirFecha($l['vigencia']);
[$e_dia, $e_mes, $e_anio]   = partirFecha($l['expiracion']);

/* Superficies: cada campo (sótano, planta baja, etc.) puede estar capturado
en M2 o en ML (unidad propia por campo) */
$camposSup = ['sotano','planta_baja','primer_nivel','segundo_nivel','tercer_nivel'];
$supDato = function($k) use ($superficies) {
    $obj = $superficies[$k] ?? [];
    $v = is_array($obj) ? ($obj['valor'] ?? '') : '';
    $u = is_array($obj) ? ($obj['unidad'] ?? 'm2') : 'm2';
    return ['valor' => ($v === null ? '' : $v), 'unidad' => $u];
};
/* Para mostrar cada celda con SU unidad real (no siempre "M²")*/
$supVal = function($k) use ($supDato) {
    $d = $supDato($k);
    return $d['valor'];
};
$supUnidadTxt = function($k) use ($supDato) {
    $d = $supDato($k);
    if ($d['valor'] === '') return '';
    return $d['unidad'] === 'mlin' ? 'ML' : 'M²';
};

$totalM2 = 0; $totalML = 0;
foreach ($camposSup as $k) {
    $d = $supDato($k);
    if ($d['valor'] === '') continue;
    if ($d['unidad'] === 'mlin') $totalML += (float)$d['valor'];
    else $totalM2 += (float)$d['valor'];
}
$superficieTotal = $totalM2;

/* Superficie total combinada, mostrando solo las unidades que sí se usaron*/
$totalPartes = [];
if ($totalM2 > 0) $totalPartes[] = number_format($totalM2, 2) . ' M²';
if ($totalML > 0) $totalPartes[] = number_format($totalML, 2) . ' ML';
$superficieTotalTxt = implode(' + ', $totalPartes);

$otraAreaTxt = '';
if (isset($superficies['otra_area'])) {
    $otraAreaTxt = is_array($superficies['otra_area']) ? ($superficies['otra_area']['valor'] ?? '') : $superficies['otra_area'];
}

$tipo_obra = $l['tipo_obra'] ?? '';

/* Configuración general (para placeholders del reglamento) */
$config = [];
$resConfig = $conn->query("SELECT clave, valor FROM configuracion_sistema");
while ($row = $resConfig->fetch_assoc()) $config[$row['clave']] = $row['valor'];
$director_nombre = $config['director_nombre'] ?? '';

/* Selección del REGLAMENTO
Construcción y Demolición se resuelven automáticamente por tipo_obra.
"Otro" usa el reglamento que Calificador ya eligió y guardó en
tramites_salida.reglamento_id al momento de calificar — no se pide nada
aquí al imprimir.
*/
$reglamento = null;

// Cantidad total (M2 o ML, la que corresponda) — se usa para Demolición
//y para el placeholder {{superficie_total}} de Construcción.
$cantidadTxt = $superficieTotalTxt !== ''
    ? $superficieTotalTxt
    : ($otraAreaTxt !== '' ? $otraAreaTxt : '(superficie no especificada)');

if ($tipo_obra === 'Construcción' || $tipo_obra === 'Demolición') {
    $stmtReg = $conn->prepare(
        "SELECT * FROM reglamentos WHERE tipo_tramite_id = 7 AND variante = ? AND activo = 1 LIMIT 1"
    );
    $stmtReg->bind_param("s", $tipo_obra);
    $stmtReg->execute();
    $reglamento = $stmtReg->get_result()->fetch_assoc();
    $stmtReg->close();
} elseif (!empty($l['reglamento_id'])) {
    $stmtReg = $conn->prepare("SELECT * FROM reglamentos WHERE id = ? AND tipo_tramite_id = 7 AND activo = 1 LIMIT 1");
    $stmtReg->bind_param("i", $l['reglamento_id']);
    $stmtReg->execute();
    $reglamento = $stmtReg->get_result()->fetch_assoc();
    $stmtReg->close();
}

if (!$reglamento) {
    die("Falta configurar el reglamento de esta licencia. Ve al módulo de Calificador, abre esta calificación, selecciona el reglamento a usar (y guarda) antes de imprimir.");
}

/*
Si el reglamento requiere ubicación manual (calles/ML, como Obra Pública),
deben venir ya guardados en tramites_salida. el aviso se muestra como
alerta en el modal de Calificador , cuando falta el dato.
*/

if ($tipo_obra === 'Demolición') {
    $autorizacion_especifica = 'LA PRESENTE LICENCIA SOLO AUTORIZA LA DEMOLICIÓN DE ' . $cantidadTxt;
} else {
    $autorizacion_especifica = '';
}

// ── Reemplazo de placeholders del reglamento ──
$reglamentoHtml = strtr($reglamento['contenido'], [
    '{{director_nombre}}'         => htmlspecialchars($director_nombre),
    '{{autorizacion_especifica}}' => htmlspecialchars($autorizacion_especifica),
    '{{anio_fiscal}}'             => date('Y'),
    '{{colonia}}'                 => htmlspecialchars(strtoupper(trim($l['colonia'] ?? ''))),
    '{{calles}}'                  => htmlspecialchars(strtoupper(trim($l['calles_manual'] ?? ''))),
    '{{metros_lineales}}'         => $l['metros_lineales_manual'] !== null ? number_format((float)$l['metros_lineales_manual'], 2, '.', '') : '',
    '{{superficie_total}}'        => htmlspecialchars($cantidadTxt),
]);

$urb = function($srv) use ($urbanizacion) { return in_array($srv, $urbanizacion); };
$perito = function($p, $campo) use ($peritos) {
    return htmlspecialchars($peritos[$p][$campo] ?? '');
};

if (esAdministrador())     $back = 'DashAdmin.php';
elseif (esCalificador())   $back = 'DashCalf.php';
elseif (esVerificador())   $back = 'DashVer.php';
else                       $back = 'DashVentanilla.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Licencia de Construcción - <?= htmlspecialchars($folioLic) ?></title>
<style>
    @page { size: letter; margin: 0.8cm 1.2cm; }
    * { margin:0; padding:0; box-sizing:border-box; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    body { font-family:Arial,sans-serif; font-size:8.3pt; color:#000; background:#fff; line-height:1.25; }

    .no-print {
        position:fixed; top:0; left:0; right:0;
        background:#7b0f2b; color:#fff;
        padding:8px 16px; display:flex; gap:10px; align-items:center; z-index:1000;
    }
    .no-print span { font-size:8.5pt; font-weight:bold; margin-right:auto; }
    .no-print button { padding:6px 16px; border:none; border-radius:4px; cursor:pointer; font-size:9pt; font-weight:bold; }
    .btn-print { background:#fff; color:#7b0f2b; }
    .btn-back  { background:#555; color:#fff; }

    .hoja { max-width:21cm; margin:45px auto 10px; padding:0; }
    .hoja + .hoja { margin-top:0; }

    /* Header */
    .header {
        display:flex; justify-content:space-between; align-items:flex-start;
        margin-bottom:5px; border-bottom:3px solid #7b0f2b; padding-bottom:5px;
    }
    .header-left { display:flex; align-items:center; gap:8px; }
    .header-left img { height:50px; }
    .titulo-dep { font-size:10.5pt; font-weight:bold; color:#7b0f2b; line-height:1.15; }
    .subtitulo  { font-size:7.5pt; color:#666; }
    .header-right img { height:50px; }

    .titulo-principal {
        text-align:center; font-size:12pt; font-weight:bold;
        color:#7b0f2b; margin:5px 0 5px; letter-spacing:1px;
    }

    /* Cajita FOLIO/CMCU/FECHA */
    .caja-folio {
        border:1.5px solid #7b0f2b; font-size:7.5pt;
    }
    .caja-folio td { border:1px solid #7b0f2b; padding:2px 4px; }
    .caja-folio .label { background:#f5e6e9; font-weight:bold; text-align:center; }
    .caja-fecha-mini td { width:22px; text-align:center; border:1px solid #7b0f2b; font-size:7pt; }

    table.tabla-datos { width:100%; border-collapse:collapse; margin-bottom:4px; }
    .tabla-datos td, .tabla-datos th { border:1px solid #7b0f2b; padding:2.5px 5px; font-size:8pt; vertical-align:top; }
    .tabla-datos .header-row td {
        background:#f5e6e9; font-weight:bold; text-align:center;
        font-size:7.7pt; color:#7b0f2b;
    }
    .tabla-datos .label { background:#f9f9f9; font-weight:bold; color:#333; white-space:nowrap; }
    .tabla-datos .valor { text-transform:uppercase; }

    .checkbox {
        display:inline-block; width:9px; height:9px;
        border:1.3px solid #7b0f2b; margin-right:3px; vertical-align:middle;
    }
    .checkbox.checked { background-color:#7b0f2b !important; }

    .fila-chk { display:flex; justify-content:space-between; align-items:center; padding:1px 4px; font-size:7.7pt; }

    .firma-linea {
        border-top:1px solid #000; text-align:center;
        font-size:7pt; padding-top:2px; margin-top:22px;
    }

    /* Reverso */
    .reverso-titulo { font-size:12pt; font-weight:bold; color:#7b0f2b; margin-bottom:6px; }
    .observaciones-box {
        border:1px solid #7b0f2b; min-height:40px; padding:5px 7px;
        font-size:8pt; margin-bottom:10px; background:#fafafa;
    }
    .reglamento { font-size:7.3pt; text-align:justify; }
    .reglamento li { margin-bottom:5px; }
    .reglamento ul { padding-left:16px; }
    .firma-calificador {
        border:1px solid #000; width:230px; margin-top:18px; padding:6px;
        text-align:center; font-size:7.5pt; font-style:italic;
    }

    @media print {
        .no-print { display:none !important; }
        .hoja { margin:0; max-width:100%; }
        .salto-pagina { page-break-before: always; }
    }
</style>
</head>
<body>

<div class="no-print">
    <span>Licencia de Construcción — <?= htmlspecialchars($folioLic) ?></span>
    <button class="btn-print" onclick="window.print()">🖨️ Imprimir</button>
    <button class="btn-back"  onclick="window.location.href='<?= $back ?>'">← Volver</button>
</div>

<!-- ============================ PÁGINA 1: FRENTE ============================ -->
<div class="hoja">

    <div class="header">
        <div class="header-left">
            <img src="logos/LogoDepFondo.png" alt="Logo DPDU">
            <div>
                <div class="titulo-dep">Dirección de Planeación<br>y Desarrollo Urbano</div>
                <div class="subtitulo">Ventanilla de Trámites del Departamento de Atención y Gestión Urbana</div>
            </div>
        </div>
        <table class="caja-folio" style="width:170px;">
            <tr><td class="label" style="width:60%;">Folio N°</td><td><?= htmlspecialchars($folioLic) ?></td></tr>
            <tr><td class="label">N° de C.M.C.U.</td><td>&nbsp;</td></tr>
            <tr>
                <td class="label">Fecha</td>
                <td style="padding:0;">
                    <table style="width:100%;"><tr class="caja-fecha-mini">
                        <td><?= htmlspecialchars($f_dia) ?></td><td><?= htmlspecialchars($f_mes) ?></td><td><?= htmlspecialchars($f_anio) ?></td>
                    </tr></table>
                </td>
            </tr>
        </table>
    </div>

    <div class="titulo-principal">LICENCIA DE CONSTRUCCIÓN</div>

    <!-- DATOS DEL PROPIETARIO -->
    <table class="tabla-datos">
        <tr><td colspan="4" class="header-row">DATOS DEL PROPIETARIO</td></tr>
        <tr>
            <td class="label" style="width:14%;">Nombre:</td>
            <td class="valor" colspan="3"><?= htmlspecialchars($l['propietario']) ?></td>
        </tr>
        <tr>
            <td class="label">Calle:</td>
            <td class="valor" style="width:36%;"><?= htmlspecialchars($l['direccion'] ?? '') ?></td>
            <td class="label" style="width:14%;">Número:</td>
            <td class="valor"><?= htmlspecialchars($l['numero'] ?? '') ?></td>
        </tr>
        <tr>
            <td class="label">Colonia:</td>
            <td class="valor"><?= htmlspecialchars($l['colonia'] ?? '') ?></td>
            <td class="label">Localidad:</td>
            <td class="valor"><?= htmlspecialchars($l['localidad'] ?? '') ?></td>
        </tr>
    </table>

    <!-- UBICACIÓN Y DATOS DEL PREDIO + URBANIZACIÓN -->
    <table class="tabla-datos">
        <tr>
            <td class="header-row" style="width:72%;">UBICACIÓN Y DATOS DEL PREDIO</td>
            <td class="header-row">URBANIZACIÓN</td>
        </tr>
        <tr style="vertical-align:top;">
            <td style="padding:0;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td class="label" style="width:38%;">Uso de suelo autorizado:</td>
                        <td class="valor">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="label">Cuenta catastral:</td>
                        <td class="valor"><?= htmlspecialchars($l['cuenta_catastral'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="label">Domicilio:</td>
                        <td class="valor"><?= htmlspecialchars($l['calle'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="label">Fracc. o colonia:</td>
                        <td class="valor"><?= htmlspecialchars($l['colonia'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="label">Manzana / Lote:</td>
                        <td class="valor"><?= htmlspecialchars($l['manzana'] ?? '') ?> / <?= htmlspecialchars($l['lote'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="label">Localidad:</td>
                        <td class="valor"><?= htmlspecialchars($l['localidad'] ?? '') ?></td>
                    </tr>
                </table>
            </td>
            <td style="padding:4px 6px; vertical-align:top;">
                <?php foreach (['Agua potable','Electricidad','Drenaje','Guarnición','Banqueta','Pavimento'] as $srv): ?>
                <div class="fila-chk"><span><?= $srv ?></span><span class="checkbox <?= $urb($srv)?'checked':'' ?>"></span></div>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>

    <!-- DATOS DE LA OBRA -->
    <table class="tabla-datos">
        <tr>
            <td class="header-row" style="width:72%;">DESCRIPCIÓN DE LA OBRA</td>
            <td class="header-row">TIPO DE OBRA</td>
        </tr>
        <tr style="vertical-align:top;">
            <td style="min-height:35px; padding:5px 6px;"><?= nl2br(htmlspecialchars($l['descripcion_obra'] ?? '')) ?></td>
            <td style="padding:4px 6px;">
                <?php foreach (['Construcción','Demolición','Otro'] as $t_obra): ?>
                <div class="fila-chk"><span><?= strtoupper($t_obra) === 'OTRO' ? 'OTROS' : strtoupper($t_obra) ?></span><span class="checkbox <?= $tipo_obra===$t_obra?'checked':'' ?>"></span></div>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>

    <!-- SUPERFICIES + INFORMACIÓN PRESENTADA -->
    <table class="tabla-datos">
        <tr>
            <td class="header-row" style="width:60%;">SUPERFICIES</td>
            <td class="header-row">INFORMACIÓN PRESENTADA</td>
        </tr>
        <tr style="vertical-align:top;">
            <td style="padding:0;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td class="label" style="width:30%;">Sótano</td><td style="width:20%;"><?= htmlspecialchars($supVal('sotano')) ?> <?= htmlspecialchars($supUnidadTxt('sotano')) ?></td>
                        <td class="label" style="width:30%;">Tercer nivel</td><td><?= htmlspecialchars($supVal('tercer_nivel')) ?> <?= htmlspecialchars($supUnidadTxt('tercer_nivel')) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Planta baja</td><td><?= htmlspecialchars($supVal('planta_baja')) ?> <?= htmlspecialchars($supUnidadTxt('planta_baja')) ?></td>
                        <td class="label">Otros</td><td><?= htmlspecialchars($otraAreaTxt) ?> ML</td>
                    </tr>
                    <tr>
                        <td class="label">Primer nivel</td><td><?= htmlspecialchars($supVal('primer_nivel')) ?> <?= htmlspecialchars($supUnidadTxt('primer_nivel')) ?></td>
                        <td class="label" colspan="2">Superficie total: <?= htmlspecialchars($superficieTotalTxt) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Segundo nivel</td><td><?= htmlspecialchars($supVal('segundo_nivel')) ?> <?= htmlspecialchars($supUnidadTxt('segundo_nivel')) ?></td>
                        <td colspan="2"></td>
                    </tr>
                </table>
            </td>
            <td style="padding:4px 6px; vertical-align:top;">
                <?php foreach (['Planos arquitectónicos','Memoria de cálculo','Bitácora','Carnet del perito'] as $info): ?>
                <div class="fila-chk"><span><?= $info ?></span><span class="checkbox"></span></div>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>

    <!-- PERITOS DE OBRA -->
    <table class="tabla-datos">
        <tr><td colspan="5" class="header-row">PERITOS DE OBRA</td></tr>
        <tr>
            <td class="label" style="width:20%;">Tipo</td>
            <td class="label" style="width:26%;">Nombre</td>
            <td class="label" style="width:18%;">N° de registro</td>
            <td class="label" style="width:18%;">Cédula profesional</td>
            <td class="label" style="width:18%; text-align:center;">Firma</td>
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
            <td><div class="firma-linea"></div></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- DATOS DE AUTORIZACIÓN -->
    <table class="tabla-datos">
        <tr><td colspan="4" class="header-row">DATOS DE AUTORIZACIÓN — EMISIÓN DE LA LICENCIA</td></tr>
        <tr>
            <td class="label" style="width:20%;">Vigencia</td>
            <td colspan="3">
                <strong>Del:</strong> <?= htmlspecialchars($v_dia.'/'.$v_mes.'/'.$v_anio) ?>
                &nbsp;&nbsp; <strong>Al:</strong> <?= htmlspecialchars($e_dia.'/'.$e_mes.'/'.$e_anio) ?>
            </td>
        </tr>
        <tr style="vertical-align:bottom;">
            <td class="label" style="width:20%;">&nbsp;</td>
            <td style="width:26.6%; text-align:center;"><div class="firma-linea">Calificó<br><?= htmlspecialchars($l['calificado_por_nombre'] ?? '') ?></div></td>
            <td style="width:26.6%; text-align:center;"><div class="firma-linea">Revisó</div></td>
            <td style="width:26.6%; text-align:center;"><div class="firma-linea">Autorizó<br>Director de Planeación y<br>Desarrollo Urbano</div></td>
        </tr>
    </table>

</div>

<!-- ============================ PÁGINA 2: REVERSO ============================ -->
<div class="hoja salto-pagina">

    <div class="reverso-titulo">OBSERVACIONES:</div>
    <div class="observaciones-box"><?= nl2br(htmlspecialchars($l['comentarios'] ?? '')) ?></div>

    <div class="reglamento">
        <?= $reglamentoHtml ?>
    </div>

    <div class="firma-calificador">
        <?= htmlspecialchars(strtoupper($l['calificado_por_nombre'] ?? '')) ?><br>
        CALIFICADOR
    </div>

</div>

<?php if (isset($_GET['autoprint'])): ?>
<script>window.onload = function(){ setTimeout(function(){ window.print(); }, 600); };</script>
<?php endif; ?>

</body>
</html>
