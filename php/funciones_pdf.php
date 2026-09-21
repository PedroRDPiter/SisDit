<?php
// =====================================================
// GENERACIÓN DE PDFs CON mPDF
// Funciones para generar los documentos oficiales en PDF.
// Requiere composer require mpdf/mpdf antes de usar
// =====================================================
/**
 * FUNCIONES PARA GENERAR PDFs
 * Archivo: php/funciones_pdf.php
 * Descripción: Generación de documentos PDF de trámites
 *
 * IMPORTANTE: Instalar mPDF primero con:
 * composer require mpdf/mpdf
 */

$autoloadFile = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadFile)) {
    throw new Exception("No se encontró vendor/autoload.php. Ejecuta composer install.");
}

require_once $autoloadFile;
require_once 'db.php';

/**
 * Generar PDF de Constancia de Número Oficial
 */
function generarPDFNumeroOficial(int $tramite_id): string {
    global $conn;

    // Obtener datos del trámite
    $sql = "SELECT t.*, tt.nombre as tipo_tramite_nombre,
                   u.nombre as creador_nombre, u.apellidos as creador_apellidos
            FROM tramites t
            INNER JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
            INNER JOIN usuarios u ON t.usuario_creador_id = u.id
            WHERE t.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tramite_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        throw new Exception("Trámite no encontrado");
    }

    $tramite = $resultado->fetch_assoc();
    $stmt->close();

    // Obtener configuración
    $municipio = obtenerConfiguracion('municipio_nombre');
    $director = obtenerConfiguracion('director_nombre');
    $director_cargo = obtenerConfiguracion('director_cargo');

    // Crear instancia de mPDF
    // Instanciación dinámica para evitar errores del analizador cuando la
    // dependencia de mPDF se carga mediante Composer en tiempo de ejecución.
    $mpdfClass = 'Mpdf\\Mpdf';
    $mpdf = new $mpdfClass([
        'format' => 'Letter',
        'margin_left' => 20,
        'margin_right' => 20,
        'margin_top' => 25,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10
    ]);

    // Construir HTML del documento
    $html = construirHTMLNumeroOficial($tramite, $municipio, $director, $director_cargo);

    // Escribir HTML
    $mpdf->WriteHTML($html);

    // Generar nombre de archivo
    $folio = str_pad($tramite['folio_numero'], 3, '0', STR_PAD_LEFT) . '-' . $tramite['folio_anio'];
    $nombreArchivo = "constancia_numero_oficial_{$folio}.pdf";

    // Guardar PDF
    $rutaPDF = __DIR__ . "/../uploads/pdfs/";
    if (!is_dir($rutaPDF)) {
        mkdir($rutaPDF, 0755, true);
    }

    $mpdf->Output($rutaPDF . $nombreArchivo, 'F');

    return $nombreArchivo;
}

/**
 * Construir HTML para Constancia de Número Oficial
 */
function construirHTMLNumeroOficial(array $tramite, string $municipio, string $director, string $director_cargo): string {

    $folio = str_pad($tramite['folio_numero'], 3, '0', STR_PAD_LEFT) . '/' . $tramite['folio_anio'];
    $fecha = date('d \d\e F \d\e Y', strtotime($tramite['created_at']));

    // Datos específicos del trámite
    $numero_asignado = $tramite['datos_especificos'] ?
                       json_decode($tramite['datos_especificos'], true)['numero_oficial'] ?? 'S/N' :
                       'S/N';

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .titulo {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .folio {
            text-align: right;
            font-weight: bold;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table td, table th {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .centrado {
            text-align: center;
        }
        .firma {
            margin-top: 80px;
            text-align: center;
        }
        .firma-linea {
            border-top: 2px solid #000;
            width: 50%;
            margin: 0 auto;
            margin-top: 60px;
        }
        .nota {
            font-size: 9pt;
            margin-top: 30px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logos/logo_urbano.jpeg" style="width: 80px; float: left;">
        <img src="logos/logo_presi.jpeg" style="width: 80px; float: right;">
        <div style="clear: both;"></div>
        <h3>RINCÓN DE ROMOS<br>REQUISITOS PARA REALIZAR EL TRÁMITE</h3>
    </div>
    <div class="folio">FOLIO: {$folio}</div>
    <div class="titulo">CONSTANCIA DE NÚMERO OFICIAL</div>
    <table>
        <tr>
            <th colspan="3">ASIGNACIÓN</th>
            <th>RECTIFICACIÓN</th>
            <th>REPOSICIÓN</th>
        </tr>
        <tr>
            <td class="centrado">SE ASIGNA EL NÚMERO</td>
            <td class="centrado" colspan="2"><strong>{$numero_asignado}</strong></td>
            <td class="centrado">REFERENCIA ANTERIOR</td>
            <td></td>
        </tr>
    </table>
    <table>
        <tr>
            <td colspan="2"><strong>CALLE:</strong> {$tramite['direccion']}</td>
        </tr>
        <tr>
            <td><strong>ENTRE CALLES:</strong></td>
            <td><strong>NIÑOS HÉROES Y LA PEDRERA</strong></td>
        </tr>
        <tr>
            <td><strong>UBICACIÓN:</strong> {$tramite['localidad']}</td>
            <td><strong>LOTE:</strong></td>
        </tr>
        <tr>
            <td><strong>COLONIA Y/O FRACCIONAMIENTO:</strong> {$tramite['colonia']}</td>
            <td><strong>NORTE</strong></td>
        </tr>
        <tr>
            <td><strong>POBLADO:</strong> {$municipio}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2"><strong>CÓDIGO POSTAL:</strong> {$tramite['cp']}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>CUENTA CATASTRAL No.:</strong> {$tramite['cuenta_catastral']}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="2"><strong>SE EXTIENDE A NOMBRE DE:</strong> {$tramite['propietario']}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="2">
                <strong>LUGAR Y FECHA DE EXPEDICIÓN:</strong><br>
                {$fecha}<br>
                {$municipio}, AGS.
            </td>
        </tr>
    </table>

    <div class="nota">
        <strong>NOTA:</strong><br>
        1.- EL NÚMERO OFICIAL DEBERÁ COLOCARSE EN PARTE VISIBLE DEL FRENTE DEL PREDIO, Y DEBERÁ SER
        LEGIBLEMENTE DE UN TAMAÑO NO MENOR A 10 CMS. DE ALTURA.<br>
        2.- ESTRICTAMENTE NO CONSTITUYE APOYO O DESLINDE AL RESPECTO DEL INMUEBLE, NI ACREDITA LA PROPIEDAD O POSESIÓN DEL MISMO.<br>
        3.- ESTRICTAMENTE LA CONSTANCIA EN EL ARTÍCULO 6-24 FRACC. II INCISO I DEL CÓDIGO DE POLICÍA Y GOBIERNO DE RINCÓN DE ROMOS, AGS.
    </div>

    <div class="firma">
        <strong>ATENTAMENTE:</strong>
        <div class="firma-linea"></div>
        <strong>{$director}</strong><br>
        {$director_cargo}<br>
        DEL MUNICIPIO DE {$municipio}, AGS.<br>
        C.C.P. ARCHIVO.
    </div>
</body>
</html>
HTML;

    return $html;
}

/**
 * Generar PDF según tipo de trámite
 */
function generarPDFTramite(int $tramite_id): string {
    global $conn;

    // Obtener tipo de trámite
    $sql = "SELECT tt.codigo
            FROM tramites t
            INNER JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
            WHERE t.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tramite_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        throw new Exception("Trámite no encontrado");
    }

    $tipo = $resultado->fetch_assoc()['codigo'];
    $stmt->close();

    // Llamar función específica según tipo
    switch ($tipo) {
        case 'NUM_OFICIAL':
            return generarPDFNumeroOficial($tramite_id);

        case 'CMCU':
            return generarPDFCMCU($tramite_id);

        case 'FUSION':
            return generarPDFFusion($tramite_id);

        case 'SUBDIVISION':
            return generarPDFSubdivision($tramite_id);
        case 'VOBO':
            return generarPDFVOBO($tramite_id);

        default:
            throw new Exception("Tipo de trámite no soportado para PDF");
    }
}

/**
 * Descargar PDF de trámite
 */
function descargarPDF(int $tramite_id): void {
    try {
        $nombreArchivo = generarPDFTramite($tramite_id);
        $rutaCompleta = __DIR__ . "/../uploads/pdfs/" . $nombreArchivo;

        if (!file_exists($rutaCompleta)) {
            throw new Exception("Archivo PDF no encontrado");
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Content-Length: ' . filesize($rutaCompleta));
        readfile($rutaCompleta);
        exit;

    } catch (Exception $e) {
        error_log("Error al descargar PDF: " . $e->getMessage());
        die("Error al generar PDF: " . $e->getMessage());
    }
}

// Funciones placeholder para otros tipos (implementar después)
function generarPDFCMCU(int $_tramite_id): string {
    // TODO: Implementar
    throw new Exception("Función no implementada aún");
}

function generarPDFFusion(int $_tramite_id): string {
    // TODO: Implementar
    throw new Exception("Función no implementada aún");
}

function generarPDFSubdivision(int $_tramite_id): string {
    // TODO: Implementar
    throw new Exception("Función no implementada aún");
}
function generarPDFVOBO(int $_tramite_id): string {
    // TODO: Implementar
    throw new Exception("Función no implementada aún");
}

/**
 * Generar PDF de Solicitud de Licencia de Construcción
 * Recibe el id del registro en solicitud_LC (no el tramite_id)
 *
 */
function generarPDFSolicitudLC(int $solicitud_id): string {
    global $conn;

    // Traer solicitud + datos del trámite ligado
    $sql = "SELECT s.*,
                   LPAD(s.folio_numero, 3, '0') AS folio_sol_fmt,
                   s.folio_anio AS folio_sol_anio,
                   t.folio_numero AS t_folio_numero,
                   t.folio_anio   AS t_folio_anio,
                   t.propietario, t.direccion, t.localidad,
                   t.calle, t.colonia, t.cuenta_catastral
            FROM solicitud_lc s
            INNER JOIN tramites t ON s.tramite_id = t.id
            WHERE s.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $solicitud_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) throw new Exception("Solicitud LC no encontrada");

    // Decodificar JSON
    $superficies  = json_decode($row['superficies']  ?? '{}', true) ?? [];
    $urbanizacion = json_decode($row['urbanizacion'] ?? '[]', true) ?? [];
    $peritos      = json_decode($row['peritos']      ?? '{}', true) ?? [];

    // Configuración del municipio
    $municipio = obtenerConfiguracion('municipio_nombre') ?? 'RINCÓN DE ROMOS';

    // Crear instancia mPDF — carta, márgenes ajustados para el formato
    $mpdfClass = 'Mpdf\\Mpdf';
    $mpdf = new $mpdfClass([
        'format'        => 'Letter',
        'margin_left'   => 15,
        'margin_right'  => 15,
        'margin_top'    => 12,
        'margin_bottom' => 12,
    ]);

    $html = construirHTMLSolicitudLC($row, $superficies, $urbanizacion, $peritos, $municipio);
    $mpdf->WriteHTML($html);

    // Guardar en disco
    $rutaPDF = __DIR__ . "/../uploads/pdfs/";
    if (!is_dir($rutaPDF)) mkdir($rutaPDF, 0755, true);

    $folioSol = str_pad($row['folio_numero'], 3, '0', STR_PAD_LEFT) . '-' . $row['folio_anio'];
    $nombreArchivo = "solicitud_lc_{$folioSol}.pdf";
    $mpdf->Output($rutaPDF . $nombreArchivo, 'F');

    return $nombreArchivo;
}

/**
 * Construir HTML para Solicitud de Licencia de Construcción
 */
function construirHTMLSolicitudLC(
  array $row,
  array $superficies,
  array $urbanizacion,
  array $peritos,
  string $municipio
): string {

    // Folio de solicitud y del trámite
    $folioSol    = str_pad($row['folio_numero'], 3, '0', STR_PAD_LEFT) . '/' . $row['folio_anio'];
    $folioTramite= str_pad($row['t_folio_numero'], 3, '0', STR_PAD_LEFT) . '/' . $row['t_folio_anio'];
    $fecha       = date('d/m/Y', strtotime($row['fecha_registro']));

    // Helper: marcar checkbox si el servicio está en urbanización
    $urb = function($servicio) use ($urbanizacion) {
        return in_array($servicio, $urbanizacion) ? '&#10003;' : '&nbsp;';
    };

    // Helper: valor + unidad de superficie
    $sup = function($clave) use ($superficies) {
        $obj    = $superficies[$clave] ?? [];
        $valor  = is_array($obj) ? ($obj['valor']  ?? '') : ($obj ?? '');
        $unidad = is_array($obj) ? ($obj['unidad'] ?? 'm2') : 'm2';
        if ($valor === null || $valor === '') return '&nbsp;';
        $etiqueta = $unidad === 'mlin' ? 'ML' : 'M²';
        return htmlspecialchars($valor) . ' ' . $etiqueta;
    };

    // Tipo de obra
    $tipo_obra = htmlspecialchars($row['tipo_obra'] ?? '');
    $chkCons = $tipo_obra === 'Construcción' ? '&#10003;' : '&nbsp;';
    $chkDemo = $tipo_obra === 'Demolición'   ? '&#10003;' : '&nbsp;';
    $chkOtro = $tipo_obra === 'Otro'         ? '&#10003;' : '&nbsp;';

    // Peritos
    $dro          = htmlspecialchars($peritos['dro']['nombre']          ?? '');
    $estructural  = htmlspecialchars($peritos['estructural']['nombre']  ?? '');
    $especialista = htmlspecialchars($peritos['especialista']['nombre'] ?? '');

    // Datos del trámite
    $propietario     = htmlspecialchars($row['propietario']      ?? '');
    $direccion       = htmlspecialchars($row['direccion']        ?? '');
    $localidad       = htmlspecialchars($row['localidad']        ?? '');
    $calle           = htmlspecialchars($row['calle']            ?? '');
    $colonia         = htmlspecialchars($row['colonia']          ?? '');
    $cuenta_catastral= htmlspecialchars($row['cuenta_catastral'] ?? '');
    $descripcion     = htmlspecialchars($row['descripcion_obra'] ?? '');

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 8.5pt; line-height: 1.3; color: #000; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
  td, th { border: 1px solid #000; padding: 3px 5px; vertical-align: top; }
  .no-border td { border: none; padding: 1px 3px; }
  .section-title { background: #e0e0e0; font-weight: bold; font-size: 8pt; padding: 3px 5px; }
  .header-center { text-align: center; font-weight: bold; }
  .small { font-size: 7.5pt; }
  .firma-box { height: 40px; border-top: 1px solid #000; text-align: center; font-size: 7.5pt; padding-top: 2px; }
  .chk { text-align: center; width: 20px; font-size: 10pt; }
  .label { font-weight: bold; white-space: nowrap; }
  .underline { border-bottom: 1px solid #000; min-width: 80px; display: inline-block; }
</style>
</head>
<body>

<!-- ENCABEZADO -->
<table>
  <tr>
    <td style="width:15%; border:none; text-align:center;">
      <img src="logos/logo_urbano.jpeg" style="height:60px;">
    </td>
    <td style="border:none; text-align:center;">
      <div style="font-size:10pt; font-weight:bold;">MUNICIPIO DE {$municipio}, AGS.</div>
      <div style="font-size:8.5pt;">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO URBANO</div>
      <div style="font-size:10pt; font-weight:bold; margin-top:5px;">SOLICITUD DE LICENCIA DE CONSTRUCCIÓN</div>
    </td>
    <td style="width:15%; border:none; text-align:center;">
      <img src="logos/logo_presi.jpeg" style="height:60px;">
    </td>
  </tr>
</table>

<!-- FOLIOS Y FECHA -->
<table class="no-border" style="margin-bottom:4px;">
  <tr>
    <td><span class="label">Folio de solicitud:</span> {$folioSol}</td>
    <td><span class="label">Folio de trámite:</span> {$folioTramite}</td>
    <td><span class="label">Fecha:</span> {$fecha}</td>
  </tr>
</table>

<!-- DATOS DEL SOLICITANTE -->
<table>
  <tr><td colspan="4" class="section-title">DATOS DEL SOLICITANTE</td></tr>
  <tr>
    <td class="label" style="width:18%;">Nombre:</td>
    <td colspan="3">{$propietario}</td>
  </tr>
  <tr>
    <td class="label">Domicilio:</td>
    <td colspan="2">{$direccion}</td>
    <td rowspan="2" style="width:20%; text-align:center; vertical-align:middle;">
      <div style="height:50px;"></div>
      <div class="firma-box">Firma del propietario</div>
    </td>
  </tr>
  <tr>
    <td class="label">Localidad:</td>
    <td colspan="2">{$localidad}</td>
  </tr>
</table>

<!-- DATOS DEL PREDIO + URBANIZACIÓN -->
<table>
  <tr>
    <td style="width:55%;">
      <table style="border:none; margin:0;">
        <tr><td colspan="2" class="section-title" style="border:none;">DATOS DEL PREDIO</td></tr>
        <tr>
          <td class="label" style="border:none; width:35%;">Calle:</td>
          <td style="border:none;">{$calle}</td>
        </tr>
        <tr>
          <td class="label" style="border:none;">Fracc. o Colonia:</td>
          <td style="border:none;">{$colonia}</td>
        </tr>
        <tr>
          <td class="label" style="border:none;">Manzana:</td>
          <td style="border:none;">&nbsp;</td>
        </tr>
        <tr>
          <td class="label" style="border:none;">Lote:</td>
          <td style="border:none;">&nbsp;</td>
        </tr>
        <tr>
          <td class="label" style="border:none;">Cuenta catastral:</td>
          <td style="border:none;">{$cuenta_catastral}</td>
        </tr>
      </table>
    </td>
    <td style="width:45%; vertical-align:top;">
      <table style="border:none; margin:0; width:100%;">
        <tr>
          <td colspan="3" class="section-title" style="border:none;">URBANIZACIÓN</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Agua potable</td>
          <td class="chk" style="border:none;">{$urb('Agua potable')}</td>
          <td style="border:none; color:#666; font-size:7pt;">{$urb('Agua potable')} SÍ &nbsp; NO</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Drenaje</td>
          <td class="chk" style="border:none;">{$urb('Drenaje')}</td>
          <td style="border:none; color:#666; font-size:7pt;">{$urb('Drenaje')} SÍ &nbsp; NO</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Electricidad</td>
          <td class="chk" style="border:none;">{$urb('Electricidad')}</td>
          <td style="border:none; color:#666; font-size:7pt;">{$urb('Electricidad')} SÍ &nbsp; NO</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Guarnición</td>
          <td class="chk" style="border:none;">{$urb('Guarnición')}</td>
          <td style="border:none; color:#666; font-size:7pt;">{$urb('Guarnición')} SÍ &nbsp; NO</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Banqueta</td>
          <td class="chk" style="border:none;">{$urb('Banqueta')}</td>
          <td style="border:none; color:#666; font-size:7pt;">{$urb('Banqueta')} SÍ &nbsp; NO</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Pavimento</td>
          <td class="chk" style="border:none;">{$urb('Pavimento')}</td>
          <td style="border:none; color:#666; font-size:7pt;">{$urb('Pavimento')} SÍ &nbsp; NO</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<!-- TIPO DE OBRA + SUPERFICIES -->
<table>
  <tr>
    <td style="width:40%; vertical-align:top;">
      <div class="section-title">TIPO DE OBRA</div>
      <table style="border:none; margin:4px 0 0 0;">
        <tr>
          <td class="chk" style="border:none;">{$chkCons}</td>
          <td style="border:none;">Construcción</td>
        </tr>
        <tr>
          <td class="chk" style="border:none;">{$chkDemo}</td>
          <td style="border:none;">Demolición</td>
        </tr>
        <tr>
          <td class="chk" style="border:none;">{$chkOtro}</td>
          <td style="border:none;">Otro</td>
        </tr>
      </table>
    </td>
    <td style="width:60%; vertical-align:top;">
      <div class="section-title">SUPERFICIES</div>
      <table style="border:none; margin:4px 0 0 0; width:100%;">
        <tr>
          <td style="border:none; width:45%;" class="label">Sótano:</td>
          <td style="border:none;">{$sup('sotano')}</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Planta baja:</td>
          <td style="border:none;">{$sup('planta_baja')}</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Primer nivel:</td>
          <td style="border:none;">{$sup('primer_nivel')}</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Segundo nivel:</td>
          <td style="border:none;">{$sup('segundo_nivel')}</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Tercer nivel:</td>
          <td style="border:none;">{$sup('tercer_nivel')}</td>
        </tr>
        <tr>
          <td style="border:none;" class="label">Otra área:</td>
          <td style="border:none;">{$sup('otra_area')}</td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<!-- PERITOS -->
<table>
  <tr><td colspan="4" class="section-title">PERITOS DE OBRA</td></tr>
  <tr>
    <td class="label" style="width:25%;">D.R.O.:</td>
    <td style="width:25%;">{$dro}</td>
    <td class="label" style="width:25%; text-align:center;">Firma:</td>
    <td style="width:25%;">&nbsp;</td>
  </tr>
  <tr>
    <td class="label">Estructural:</td>
    <td>{$estructural}</td>
    <td class="label" style="text-align:center;">Firma:</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="label">Instalaciones especiales:</td>
    <td>{$especialista}</td>
    <td class="label" style="text-align:center;">Firma:</td>
    <td>&nbsp;</td>
  </tr>
</table>

<!-- DESCRIPCIÓN DE LA CONSTRUCCIÓN -->
<table>
  <tr><td class="section-title">DESCRIPCIÓN DE LA CONSTRUCCIÓN</td></tr>
  <tr><td style="height:60px; vertical-align:top;">{$descripcion}</td></tr>
</table>

<!-- NOTA -->
<div class="small" style="margin-top:6px;">
  <strong>NOTA:</strong>
  Deberá registrar la obra en el IMSS incluyendo autoconstrucción antes de iniciar los trabajos.
  Todo el escombro producto de la construcción deberá depositarlo en tiradero de escombro municipal.
  Al terminar la obra deberá presentar la manifestación de predio urbano en la receptoría de rentas.
</div>

</body>
</html>
HTML;

    return $html;
}
?>
