<?php
/**
 * Constancia de Compatibilidad Urbanística aprobada por Verificador y Calificador.
 */
ini_set('session.cookie_httponly', 1);
if (session_status() === PHP_SESSION_NONE) session_start();

// Verificar que la sesión pertenezca a un usuario autenticado.
if (!isset($_SESSION['id']) || !isset($_SESSION['usuario'])) {
    header('Location: acceso.php');
    exit;
}

require 'php/db.php';
require 'php/funciones_seguridad.php';

// Restringir la consulta de constancias a los perfiles autorizados.
if (!esCalificador() && !esVerificador() && !esVentanilla() && !esAdministrador()) {
    header('Location: acceso.php');
    exit;
}

$salida_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($salida_id <= 0) die('ID de constancia inválido.');

// Obtener la constancia junto con los datos del trámite y de los responsables.
$stmt = $conn->prepare("
    SELECT ts.*, t.id AS tramite_id, t.tipo_tramite_id, t.estatus AS tramite_estatus,
           t.folio_numero AS folio_ingreso_numero, t.folio_anio AS folio_ingreso_anio,
           t.folio_salida_numero AS tramite_folio_salida_numero,
           t.folio_salida_anio AS tramite_folio_salida_anio,
           t.propietario, t.solicitante, t.direccion, t.calle, t.numero,
           t.colonia, t.localidad, t.manzana, t.lote, t.cp,
           t.cuenta_catastral, t.superficie, t.observaciones AS observaciones_verificador,
           t.verificador_nombre,
           CONCAT(u.nombre, ' ', u.apellidos) AS calificado_por_nombre
    FROM tramites_salida ts
    INNER JOIN tramites t ON t.id = ts.tramite_id
    LEFT JOIN usuarios u ON u.id = ts.calificado_por
    WHERE ts.id = ? AND t.tipo_tramite_id = 2
    LIMIT 1
");
$stmt->bind_param('i', $salida_id);
$stmt->execute();
$c = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$c) die('Constancia de Compatibilidad Urbanística no encontrada.');

// Solo permitir la impresión cuando la salida y el trámite estén aprobados.
$estatusVerificadorPermitido = ['Pendiente por firmar', 'Firmado', 'Entregado y archivado', 'Aprobado por Verificador', 'Aprobado'];
if ($c['estatus'] !== 'Aprobado' || !in_array($c['tramite_estatus'], $estatusVerificadorPermitido, true)) {
    die('La constancia debe estar aprobada por Verificador y Calificador antes de imprimirse.');
}

// Cargar la información institucional configurable para el documento.
$config = [];
$resConfig = $conn->query('SELECT clave, valor FROM configuracion_sistema');
while ($fila = $resConfig->fetch_assoc()) $config[$fila['clave']] = $fila['valor'];

$director = $config['director_nombre'] ?? 'DIRECTOR DE PLANEACION Y DESARROLLO URBANO';
$municipio = $config['municipio_nombre'] ?? 'Rincon de Romos';
$folioNumero = $c['folio_salida_numero'] ?: $c['tramite_folio_salida_numero'];
$folioAnio = $c['folio_salida_anio'] ?: $c['tramite_folio_salida_anio'];
$folioSalida = str_pad((string)$folioNumero, 3, '0', STR_PAD_LEFT) . '/' . $folioAnio;
$folioIngreso = str_pad((string)$c['folio_ingreso_numero'], 3, '0', STR_PAD_LEFT) . '/' . $c['folio_ingreso_anio'];
$fecha = !empty($c['fecha_salida']) ? substr($c['fecha_salida'], 0, 10) : date('Y-m-d');
$meses = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
$fechaPartes = explode('-', $fecha);
$fechaTexto = (int)$fechaPartes[2] . ' DE ' . $meses[(int)$fechaPartes[1]] . ' DE ' . $fechaPartes[0];
$dictamen = trim((string)($c['comentarios'] ?: $c['observaciones_verificador']));
if ($dictamen === '') $dictamen = 'COMPATIBLE CONFORME A LA REVISIÓN TÉCNICA DEL EXPEDIENTE.';

// Determinar el panel al que regresará el usuario.
if (esAdministrador()) $back = 'DashAdmin.php';
elseif (esCalificador()) $back = 'DashCalf.php';
elseif (esVerificador()) $back = 'DashVer.php';
else $back = 'DashVentanilla.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Constancia de Compatibilidad Urbanística - <?= e($folioSalida) ?></title>
<style>
  /* Estilos de impresión y presentación de la constancia. */
  @page {
    size: letter;
    margin: 1.4cm 1.7cm;
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    color: #171717;
    background: #fff;
    font-family: Arial, sans-serif;
    font-size: 10.5pt;
    line-height: 1.45;
  }

  .barra {
    position: fixed;
    inset: 0 0 auto;
    z-index: 5;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 16px;
    background: #7b0f2b;
    color: #fff;
  }

  .barra strong {
    margin-right: auto;
  }

  .barra button {
    border: 0;
    border-radius: 4px;
    padding: 7px 14px;
    cursor: pointer;
    font-weight: bold;
  }

  .imprimir {
    background: #fff;
    color: #7b0f2b;
  }

  .volver {
    background: #555;
    color: #fff;
  }

  .hoja {
    width: 100%;
    max-width: 21cm;
    margin: 55px auto 0;
  }

  .encabezado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
    padding-bottom: 8px;
    border-bottom: 3px solid #7b0f2b;
  }

  .encabezado img {
    max-height: 62px;
    max-width: 145px;
    object-fit: contain;
  }

  .dependencia {
    flex: 1;
    text-align: center;
    color: #7b0f2b;
    font-weight: bold;
    font-size: 12pt;
  }

  h1 {
    margin: 20px 0 12px;
    color: #7b0f2b;
    text-align: center;
    font-size: 16pt;
    letter-spacing: 0;
  }

  .folios {
    display: flex;
    justify-content: flex-end;
    gap: 24px;
    margin-bottom: 18px;
    font-weight: bold;
  }

  .folios span {
    border-bottom: 1px solid #333;
    padding: 0 10px 2px;
  }

  .fecha {
    text-align: right;
    margin-bottom: 22px;
  }

  .texto {
    text-align: justify;
  }

  .datos {
    width: 100%;
    border-collapse: collapse;
    margin: 16px 0;
  }

  .datos th,
  .datos td {
    border: 1px solid #7b0f2b;
    padding: 6px 8px;
    vertical-align: top;
  }

  .datos th {
    width: 21%;
    color: #7b0f2b;
    background: #f8eef1;
    text-align: left;
  }

  .dictamen {
    margin: 18px 0;
    padding: 13px 15px;
    border: 1.5px solid #7b0f2b;
    text-align: justify;
    white-space: pre-wrap;
  }

  .nota {
    margin-top: 18px;
    font-size: 9pt;
    text-align: justify;
  }

  .firmas {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 55px;
    margin-top: 65px;
    text-align: center;
    font-size: 9pt;
  }

  .firma {
    border-top: 1px solid #222;
    padding-top: 5px;
  }

  .director {
    grid-column: 1 / -1;
    width: 52%;
    margin: 35px auto 0;
  }

  @media print {
    .barra {
      display: none;
    }

    .hoja {
      margin-top: 0;
    }
  }
</style>
</head>
<body>
<!-- Barra de acciones visible en pantalla y oculta al imprimir. -->
<div class="barra no-print">
  <strong>Constancia <?= e($folioSalida) ?></strong>
  <button class="imprimir" onclick="window.print()">Imprimir</button>
  <button class="volver" onclick="window.location.href='<?= e($back) ?>'">Volver</button>
</div>

<main class="hoja">
  <!-- Encabezado institucional con los logotipos oficiales. -->
  <header class="encabezado">
    <img src="logos/logoPresi.png" alt="Presidencia Municipal">
    <div class="dependencia">DIRECCIÓN DE PLANEACIÓN<br>Y DESARROLLO URBANO</div>
    <img src="logos/LogoDepFondo.png" alt="Planeación y Desarrollo Urbano">
  </header>

  <h1>CONSTANCIA DE COMPATIBILIDAD URBANÍSTICA</h1>
  <div class="folios">
    <div>Folio de ingreso: <span><?= e($folioIngreso) ?></span></div>
    <div>Folio de salida: <span><?= e($folioSalida) ?></span></div>
  </div>
  <p class="fecha"><?= e(strtoupper($municipio)) ?>, AGS., A <?= e($fechaTexto) ?></p>

  <p class="texto">A QUIEN CORRESPONDA:</p>
  <p class="texto">Por medio de la presente se hace constar el resultado de la revisión de compatibilidad urbanística del predio cuyos datos se describen a continuación:</p>

  <!-- Datos principales del predio asociado al trámite. -->
  <table class="datos">
    <tr><th>Propietario</th><td><?= e(strtoupper($c['propietario'])) ?></td></tr>
    <tr><th>Solicitante</th><td><?= e(strtoupper($c['solicitante'])) ?></td></tr>
    <tr><th>Ubicación</th><td><?= e(strtoupper(trim(($c['calle'] ?: $c['direccion']) . ' ' . $c['numero']))) ?></td></tr>
    <tr><th>Colonia / localidad</th><td><?= e(strtoupper(trim(($c['colonia'] ?? '') . ', ' . ($c['localidad'] ?? '')))) ?></td></tr>
    <tr><th>Cuenta catastral</th><td><?= e($c['cuenta_catastral']) ?></td></tr>
    <tr><th>Manzana / lote</th><td><?= e(($c['manzana'] ?: '-') . ' / ' . ($c['lote'] ?: '-')) ?></td></tr>
    <tr><th>Superficie</th><td><?= e($c['superficie'] ?: 'No especificada') ?></td></tr>
  </table>

  <!-- Resultado de la revisión de compatibilidad urbanística. -->
  <div class="dictamen"><strong>DICTAMEN:</strong><br><?= e(strtoupper($dictamen)) ?></div>
  <p class="nota">La presente constancia se expide con base en la información y documentación integrada al expediente. No acredita propiedad ni sustituye las licencias, permisos o autorizaciones que resulten aplicables.</p>

  <!-- Firmas de verificación, calificación y dirección responsable. -->
  <section class="firmas">
    <div class="firma"><strong><?= e(strtoupper($c['verificador_nombre'] ?: 'VERIFICADOR')) ?></strong><br>VERIFICO</div>
    <div class="firma"><strong><?= e(strtoupper(trim($c['calificado_por_nombre'] ?: 'CALIFICADOR'))) ?></strong><br>CALIFICO</div>
    <div class="firma director"><strong><?= e(strtoupper($director)) ?></strong><br>DIRECTOR DE PLANEACIÓN Y DESARROLLO URBANO</div>
  </section>
</main>
</body>
</html>
