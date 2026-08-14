<?php
// =====================================================
// GUARDAR IMAGEN DE CROQUIS (AJAX)
// Recibe la imagen del croquis desde el modal del verificador
// y la guarda en uploads/ asociándola al trámite
// =====================================================
/**
 * GUARDAR CROQUIS DE CONSTANCIA
 * Recibe la imagen del croquis y la guarda en la BD
 */
error_reporting(0);
ini_set('display_errors', 0);
if (ob_get_length()) ob_clean();
if (session_status() === PHP_SESSION_NONE) session_start();

require_once "db.php";
require_once "funciones_seguridad.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id'])) {
    echo json_encode(array('success'=>false,'message'=>'Sesion expirada'));
    exit;
}
if (!esVerificador() && !esAdministrador() && !esVentanilla()) {
    echo json_encode(array('success'=>false,'message'=>'Sin permisos'));
    exit;
}
if (!validarCSRF()) {
    http_response_code(403);
    echo json_encode(array('success'=>false,'message'=>'Token de seguridad invalido'));
    exit;
}

// Identificación: preferir id del subtrámite (cada subtrámite tiene su propio croquis).
$id_post = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$folio   = isset($_POST['folio']) ? trim($_POST['folio']) : '';

if ($id_post > 0) {
    $stmt = $conn->prepare("SELECT id, croquis_archivo FROM tramites WHERE id=?");
    $stmt->bind_param("i", $id_post);
} else {
    if (!preg_match('/^(\d{1,4})\/(\d{4})$/', $folio, $m)) {
        echo json_encode(array('success'=>false,'message'=>'Folio o id invalido'));
        exit;
    }
    $folio_numero = (int)$m[1];
    $folio_anio   = (int)$m[2];
    // Sin id explícito: tomar la fila principal del grupo (compatibilidad)
    $stmt = $conn->prepare("SELECT id, croquis_archivo FROM tramites WHERE folio_numero=? AND folio_anio=? ORDER BY (tramite_principal_id IS NULL) DESC, id ASC LIMIT 1");
    $stmt->bind_param("ii", $folio_numero, $folio_anio);
}
$stmt->execute();
$result = $stmt->get_result();
$tramite_data = $result->fetch_assoc();
$stmt->close();

$tramite_id = $tramite_data['id'] ?? 0;
$current_croquis = $tramite_data['croquis_archivo'] ?? '';

if ($tramite_id === 0) {
    echo json_encode(array('success'=>false,'message'=>'Tramite no encontrado'));
    exit;
}

// NOTA: Ya no eliminamos el croquis anterior para mantener historial de versiones
// if (!empty($current_croquis)) {
//     $old_path = "../" . $current_croquis;
//     if (file_exists($old_path)) {
//         unlink($old_path);
//     }
// }

// Verificar que venga archivo
if (!isset($_FILES['croquis']) || $_FILES['croquis']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('success'=>false,'message'=>'No se recibio imagen'));
    exit;
}

$validacion = validarArchivo($_FILES['croquis'], array('jpg','jpeg','png','webp'));
if (!$validacion['valido']) {
    echo json_encode(array('success'=>false,'message'=>$validacion['mensaje']));
    exit;
}
$ext = $validacion['extension'];

// Usar el ID del trámite para organizar los archivos en lugar del folio
$carpeta = "../.private/{$tramite_id}/croquis/";
if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

// Encontrar el siguiente número disponible para evitar conflictos
$max_num = 0;
if ($handle = opendir($carpeta)) {
    while (false !== ($entry = readdir($handle))) {
        if (preg_match('/^croquis_(\d+)\.' . preg_quote($ext, '/') . '$/i', $entry, $matches)) {
            $num = (int)$matches[1];
            if ($num > $max_num) {
                $max_num = $num;
            }
        }
    }
    closedir($handle);
}
$next_num = $max_num + 1;

$nombre = 'croquis_' . $next_num . '.' . $ext;
$relative_path = ".private/{$tramite_id}/croquis/{$nombre}";
if (!move_uploaded_file($_FILES['croquis']['tmp_name'], "../" . $relative_path)) {
    echo json_encode(array('success'=>false,'message'=>'Error al guardar la imagen'));
    exit;
}

// Actualizar en BD: SOLO la fila de este subtrámite (por id)
$stmt = $conn->prepare("UPDATE tramites SET croquis_archivo=? WHERE id=?");
$stmt->bind_param("si", $relative_path, $tramite_id);
if (!$stmt->execute()) {
    echo json_encode(array('success'=>false,'message'=>'Error al guardar en BD'));
    exit;
}
$stmt->close();

// Log
$uid = (int)$_SESSION['id'];
$ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$det = "Croquis cargado para subtramite ID: $tramite_id | Archivo: $relative_path";
$log = $conn->prepare("INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, detalles, ip_address) VALUES (?, 'Croquis constancia', 'tramites', ?, ?)");
$log->bind_param("iss", $uid, $det, $ip);
$log->execute();
$log->close();

echo json_encode(array(
    'success'  => true,
    'message'  => 'Croquis guardado correctamente',
    'archivo'  => $relative_path,
    'url'      => '../' . $relative_path
));
