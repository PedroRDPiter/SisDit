<?php
// =====================================================
// COPIAR DOCUMENTOS DE TRÁMITE ANTERIOR
// Si un predio ya tiene documentos de un trámite anterior,
// los copia al nuevo para evitar que suban lo mismo otra vez
// =====================================================
session_start();
require_once "db.php";
require_once "funciones_seguridad.php";

header('Content-Type: application/json');

// Confirmar que la sesión pertenece a un usuario autenticado.
if (!isset($_SESSION['id']) || !isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

// Restringir la operación al personal autorizado y validar el token CSRF.
if (!esPersonalAutorizado() || !validarCSRF()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

$folio_origen = isset($_POST['folio_origen']) ? trim($_POST['folio_origen']) : '';
$folio_destino = isset($_POST['folio_destino']) ? trim($_POST['folio_destino']) : '';

if (empty($folio_origen) || empty($folio_destino)) {
    echo json_encode(['success' => false, 'error' => 'Folios requeridos']);
    exit;
}

// Separar el número y el año de cada folio para usarlos en las consultas.
$partes_origen = explode('/', $folio_origen);
$partes_destino = explode('/', $folio_destino);

if (count($partes_origen) != 2 || count($partes_destino) != 2) {
    echo json_encode(['success' => false, 'error' => 'Formato de folio inválido']);
    exit;
}

$folio_origen_numero = intval($partes_origen[0]);
$folio_origen_anio = intval($partes_origen[1]);
$folio_destino_numero = intval($partes_destino[0]);
$folio_destino_anio = intval($partes_destino[1]);

// Obtener únicamente los documentos que Ventanilla puede reutilizar.
$sql = "SELECT ine_archivo, escrituras_archivo, titulo_archivo, predial_archivo
        FROM tramites 
        WHERE folio_numero = ? AND folio_anio = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $folio_origen_numero, $folio_origen_anio);
$stmt->execute();
$result = $stmt->get_result();
$tramite_origen = $result->fetch_assoc();
$stmt->close();

if (!$tramite_origen) {
    echo json_encode(['success' => false, 'error' => 'No se encontró el trámite origen']);
    exit;
}

// Todos los archivos de trámites se almacenan en el directorio de uploads.
$upload_dir = __DIR__ . '/../uploads/';

// Construir dinámicamente el UPDATE según los archivos que existan.
$campos = [];
$valores = [];
$tipos = "";

$archivos_copiados = [];

// Copiar un archivo con un nombre único asociado al folio destino.
function copiarArchivo(string $archivo_origen, string $destino_dir, string $folio_destino): ?string {
    if (empty($archivo_origen)) return null;
    
    $ruta_origen = $destino_dir . $archivo_origen;
    if (!file_exists($ruta_origen)) return null;
    
    // Generar un nombre nuevo para no sobrescribir archivos existentes.
    $extension = pathinfo($archivo_origen, PATHINFO_EXTENSION);
    $nuevo_nombre = 'tramite_' . str_replace('/', '_', $folio_destino) . '_' . time() . '_' . uniqid() . '.' . $extension;
    $ruta_destino = $destino_dir . $nuevo_nombre;
    
    if (copy($ruta_origen, $ruta_destino)) {
        return $nuevo_nombre;
    }
    return null;
}

// Copiar la identificación oficial únicamente cuando esté disponible.
if (!empty($tramite_origen['ine_archivo'])) {
    $nuevo_ine = copiarArchivo($tramite_origen['ine_archivo'], $upload_dir, $folio_destino);
    if ($nuevo_ine) {
        $campos[] = "ine_archivo = ?";
        $valores[] = $nuevo_ine;
        $tipos .= "s";
        $archivos_copiados['ine'] = $nuevo_ine;
    }
}

// Usar escritura como primera opción y título como alternativa.
$archivo_escritura = $tramite_origen['escrituras_archivo'] ?: $tramite_origen['titulo_archivo'];
if (!empty($archivo_escritura)) {
    $nuevo_escritura = copiarArchivo($archivo_escritura, $upload_dir, $folio_destino);
    if ($nuevo_escritura) {
        $campos[] = "escrituras_archivo = ?";
        $valores[] = $nuevo_escritura;
        $tipos .= "s";
        $archivos_copiados['escritura'] = $nuevo_escritura;
    }
}

// Copiar el recibo predial únicamente cuando esté disponible.
if (!empty($tramite_origen['predial_archivo'])) {
    $nuevo_predial = copiarArchivo($tramite_origen['predial_archivo'], $upload_dir, $folio_destino);
    if ($nuevo_predial) {
        $campos[] = "predial_archivo = ?";
        $valores[] = $nuevo_predial;
        $tipos .= "s";
        $archivos_copiados['predial'] = $nuevo_predial;
    }
}

// Actualizar el trámite destino solo si se copió al menos un documento.
if (!empty($campos)) {
    $sql_update = "UPDATE tramites SET " . implode(", ", $campos) . " 
                   WHERE folio_numero = ? AND folio_anio = ?";
    $valores[] = $folio_destino_numero;
    $valores[] = $folio_destino_anio;
    $tipos .= "ii";
    
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param($tipos, ...$valores);
    
    if ($stmt_update->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Documentos copiados correctamente',
            'archivos_copiados' => $archivos_copiados
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar la base de datos: ' . $stmt_update->error]);
    }
    $stmt_update->close();
} else {
    echo json_encode(['success' => true, 'message' => 'No había documentos para copiar']);
}

$conn->close();
?>
