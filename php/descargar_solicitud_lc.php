<?php
// =====================================================
// DESCARGAR PDF DE SOLICITUD DE LICENCIA DE CONSTRUCCIÓN
// Recibe el id de solicitud_LC y devuelve el PDF
// =====================================================

require_once "funciones_pdf.php";
require_once "funciones_seguridad.php";

if (session_status() === PHP_SESSION_NONE) session_start();

// Autenticación
if (!isset($_SESSION['id'])) {
    die("No autenticado");
}

// ID de la solicitud LC
$solicitud_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($solicitud_id <= 0) die("ID de solicitud inválido");

// Verificar que la solicitud existe
$stmt = $conn->prepare("SELECT s.id, s.estatus, t.usuario_creador_id FROM solicitud_lc s INNER JOIN tramites t ON s.tramite_id = t.id WHERE s.id = ?");
$stmt->bind_param("i", $solicitud_id);
$stmt->execute();
$sol = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sol) die("Solicitud no encontrada");

// Permisos: creador del trámite, Verificador o Administrador
$puede_ver = (
    $sol['usuario_creador_id'] == $_SESSION['id'] ||
    esVerificador() ||
    esCalificador() ||
    esAdministrador() ||
    esVentanilla()
);
if (!$puede_ver) die("Sin permisos para ver esta solicitud");

// Registrar descarga
registrarLog($conn, $_SESSION['id'], 'Descargó PDF solicitud LC', 'solicitud_lc', $solicitud_id);

// Generar y enviar PDF
try {
    $nombreArchivo = generarPDFSolicitudLC($solicitud_id);
    $rutaCompleta  = __DIR__ . "/../uploads/pdfs/" . $nombreArchivo;

    if (!file_exists($rutaCompleta)) throw new Exception("Archivo PDF no encontrado");

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $nombreArchivo . '"');
    header('Content-Length: ' . filesize($rutaCompleta));
    readfile($rutaCompleta);
    exit;

} catch (Exception $e) {
    error_log("Error PDF solicitud LC: " . $e->getMessage());
    die("Error al generar PDF: " . $e->getMessage());
}
