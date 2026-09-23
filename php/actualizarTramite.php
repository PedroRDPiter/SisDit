<?php

// Registrar warnings sin contaminar la respuesta JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Limpiar cualquier output previo
if (ob_get_length()) ob_clean();

// ── Iniciar sesión ──
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";
require_once "funciones_seguridad.php";
require_once __DIR__ . "/FolioSalida.php";

header('Content-Type: application/json; charset=utf-8');

// ── Autenticación ──────────────────────────────────────────
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesión expirada. Recarga la página.']);
    exit;
}

if (!esVerificador() && !esAdministrador() && !esVentanilla()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sin permisos para esta acción.']);
    exit;
}

// ── CSRF ──────────────────────────────────────────────────
$csrfEnviado  = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
$csrfSesion   = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
if (empty($csrfEnviado) || empty($csrfSesion) || !hash_equals($csrfSesion, $csrfEnviado)) {
    echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido. Recarga la página e intenta de nuevo.']);
    exit;
}

// ── Datos del POST ─────────────────────────────────────────
$folio               = trim(isset($_POST['folio']) ? $_POST['folio'] : '');
$estatus             = trim(isset($_POST['estatus']) ? $_POST['estatus'] : '');
$observaciones       = trim(isset($_POST['observaciones']) ? $_POST['observaciones'] : '');
$verificador_nombre  = mb_strtoupper(trim(isset($_POST['verificador_nombre']) ? $_POST['verificador_nombre'] : ''), 'UTF-8');
$solo_constancia     = isset($_POST['solo_constancia']) && $_POST['solo_constancia'] == '1';

$direccion_constancia = mb_strtoupper(trim(isset($_POST['direccion_constancia']) ? $_POST['direccion_constancia'] : ''), 'UTF-8');
// El formulario usa nombre colonia_constancia; aceptar ambos nombres por compatibilidad
$colonia_input = isset($_POST['colonia_constancia']) ? $_POST['colonia_constancia'] : (isset($_POST['colonia']) ? $_POST['colonia'] : '');
$colonia = mb_strtoupper(trim($colonia_input), 'UTF-8');
$cp_constancia = isset($_POST['cp']) ? preg_replace('/\D/', '', trim($_POST['cp'])) : null;
if ($cp_constancia === '') {
    $cp_constancia = null;
}
$numero_asignado     = trim(isset($_POST['numero_asignado']) ? $_POST['numero_asignado'] : '');
$tipo_asignacion     = trim(isset($_POST['tipo_asignacion']) ? $_POST['tipo_asignacion'] : 'Asignacion');
$referencia_anterior = trim(isset($_POST['referencia_anterior']) ? $_POST['referencia_anterior'] : '');
$entre_calle1        = mb_strtoupper(trim(isset($_POST['entre_calle1']) ? $_POST['entre_calle1'] : ''), 'UTF-8');
$entre_calle2        = mb_strtoupper(trim(isset($_POST['entre_calle2']) ? $_POST['entre_calle2'] : ''), 'UTF-8');
$cuenta_catastral_c  = trim(isset($_POST['cuenta_catastral_constancia']) ? $_POST['cuenta_catastral_constancia'] : '');
$superficie          = trim(isset($_POST['superficie_constancia']) ? $_POST['superficie_constancia'] : '');
$manzana             = trim(isset($_POST['manzana']) ? $_POST['manzana'] : '');
$lote                = trim(isset($_POST['lote']) ? $_POST['lote'] : '');
$fecha_constancia    = trim(isset($_POST['fecha_constancia']) ? $_POST['fecha_constancia'] : date('Y-m-d'));
$cantidad            = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
// ID del subtrámite específico (cada fila comparte folio de entrada pero tiene su
// propio folio de salida, croquis y datos de constancia).
$tramite_id_post     = isset($_POST['id'])
    ? (int)$_POST['id']
    : (isset($_POST['tramite_id']) ? (int)$_POST['tramite_id'] : 0);

// Si es solo constancia, solo necesitamos el folio y los datos de constancia
if ($solo_constancia) {
    if (empty($folio) && $tramite_id_post <= 0) {
        echo json_encode(['success' => false, 'message' => 'Falta el identificador del tramite.']);
        exit;
    }
    if (empty($numero_asignado)) {
        echo json_encode(['success' => false, 'message' => 'El numero asignado es obligatorio.']);
        exit;
    }
    if ($cp_constancia !== null && $cp_constancia !== '' && strlen($cp_constancia) !== 5) {
        echo json_encode(['success' => false, 'message' => 'El código postal debe contener 5 dígitos.']);
        exit;
    }
} else {
    if (empty($folio) || empty($estatus)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos: folio y estatus son obligatorios.']);
        exit;
    }
}

// ── Validar formato folio (ej. 001/2026) ──────────────────
$folio_numero = 0;
$folio_anio   = 0;
if (!empty($folio)) {
    if (!preg_match('/^(\d{1,4})\/(\d{4})$/', $folio, $m)) {
        echo json_encode(['success' => false, 'message' => 'Formato de folio inválido.']);
        exit;
    }
    $folio_numero = (int) $m[1];
    $folio_anio   = (int) $m[2];
} elseif ($tramite_id_post <= 0) {
    echo json_encode(['success' => false, 'message' => 'Falta el folio o el id del tramite.']);
    exit;
}

// ── Estatus permitidos (solo validar si no es solo_constancia) ──
if (!$solo_constancia) {
    $estatusPermitidos = ['En revisión', 'En Revisión por Validador', 'Pendiente por firmar', 'Firmado', 'Entregado y archivado', 'Rechazado', 'En corrección'];
    if (!in_array($estatus, $estatusPermitidos, true)) {
        echo json_encode(['success' => false, 'message' => 'Estatus no valido: ' . htmlspecialchars($estatus)]);
        exit;
    }
}

$documento_firmado_guardado = null;
$transaccion_confirmada = false;

try {
    $conn->begin_transaction();

    // ── Obtener trámite actual ─────────────────────────────
    // Si viene un id de subtrámite, lo usamos como identificador exacto
    // (los subtrámites comparten folio de entrada, así que el folio NO es único).
    $selectCols = "
        SELECT t.id, t.estatus, t.foto1_archivo, t.foto2_archivo,
               t.ine_archivo, t.titulo_archivo, t.predial_archivo,
               t.escrituras_archivo, t.formato_constancia,
               t.oficio_vobo, t.otros_archivos,
               t.telefono, t.correo, t.solicitante, t.propietario,
               t.tipo_tramite_id, t.folio_numero, t.folio_anio,
               t.folio_salida_numero, t.folio_salida_anio,
               tt.nombre AS tipo_tramite_nombre
        FROM tramites t
        LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id ";

    if ($tramite_id_post > 0) {
        $stmtGet = $conn->prepare($selectCols . "WHERE t.id = ? LIMIT 1 FOR UPDATE");
        if (!$stmtGet) throw new Exception("Error BD: " . $conn->error);
        $stmtGet->bind_param("i", $tramite_id_post);
    } else {
        $stmtGet = $conn->prepare($selectCols . "WHERE t.folio_numero = ? AND t.folio_anio = ? LIMIT 1 FOR UPDATE");
        if (!$stmtGet) throw new Exception("Error BD: " . $conn->error);
        $stmtGet->bind_param("ii", $folio_numero, $folio_anio);
    }
    $stmtGet->execute();
    $res = $stmtGet->get_result();

    if ($res->num_rows === 0) {
        throw new Exception("Trámite no encontrado: " . ($tramite_id_post > 0 ? "id $tramite_id_post" : $folio));
    }
    $tramite          = $res->fetch_assoc();
    $tramite_id       = (int) $tramite['id'];
    $estatus_anterior = $tramite['estatus'];
    // Asegurar folio_numero/anio desde la fila (cuando se ubicó por id)
    $folio_numero     = (int) $tramite['folio_numero'];
    $folio_anio       = (int) $tramite['folio_anio'];
    if (empty($folio)) {
        $folio = $folio_numero . '/' . $folio_anio;
    }
    $stmtGet->close();

    // El flujo de firma y archivo tiene transiciones y responsables definidos.
    if (!$solo_constancia) {
        if ($estatus === 'Pendiente por firmar' && !esVerificador() && !esAdministrador()) {
            throw new Exception('Solo el verificador puede aprobar y enviar un trámite a firma.');
        }
        if ($estatus === 'Firmado') {
            if (!esVentanilla() && !esAdministrador()) {
                throw new Exception('Solo Ventanilla puede registrar la firma.');
            }
            if ($estatus_anterior !== 'Pendiente por firmar') {
                throw new Exception('Solo se puede firmar un trámite con estatus Pendiente por firmar.');
            }
        }
        if ($estatus === 'Entregado y archivado') {
            if (!esVentanilla() && !esAdministrador()) {
                throw new Exception('Solo Ventanilla puede entregar y archivar el trámite.');
            }
            if ($estatus_anterior !== 'Firmado') {
                throw new Exception('Solo se puede archivar un trámite con estatus Firmado.');
            }
            if (!isset($_FILES['documento_firmado']) || $_FILES['documento_firmado']['error'] === UPLOAD_ERR_NO_FILE) {
                throw new Exception('Debes escanear y adjuntar el documento firmado para archivar el trámite.');
            }
        }
    }

    // ── MODO SOLO CONSTANCIA ───────────────────────────────
    // Guarda los datos de la constancia de UN solo subtrámite (por id) y le
    // asigna su propio folio de salida (consecutivo por tipo de trámite y año).
    if ($solo_constancia) {
        $uid = (int) $_SESSION['id'];

        // Actualizar SOLO la fila de este subtrámite (identificada por id)
        $sql = "UPDATE tramites SET
                 direccion           = ?,
                 colonia             = ?,
                 cp                  = COALESCE(?, cp),
                 numero_asignado     = ?,
                 tipo_asignacion     = ?,
                 referencia_anterior = ?,
                 entre_calle1        = ?,
                 entre_calle2        = ?,
                 cuenta_catastral    = ?,
                 superficie          = ?,
                 manzana             = ?,
                 lote                = ?,
                 fecha_constancia    = ?
                 WHERE id = ?";

        $stmtUp = $conn->prepare($sql);
        if (!$stmtUp) throw new Exception("Error prepare UPDATE: " . $conn->error);

        $stmtUp->bind_param(
            "sssssssssssssi",
            $direccion_constancia,
            $colonia,
            $cp_constancia,
            $numero_asignado,
            $tipo_asignacion,
            $referencia_anterior,
            $entre_calle1,
            $entre_calle2,
            $cuenta_catastral_c,
            $superficie,
            $manzana,
            $lote,
            $fecha_constancia,
            $tramite_id
        );

        if (!$stmtUp->execute()) throw new Exception("Error UPDATE: " . $stmtUp->error);
        $stmtUp->close();

         // -- ASIGNAR FOLIO DE SALIDA DE ESTE SUBTRÁMITE (si aún no tiene) --
         // Consecutivo por tipo de trámite y año; cada subtrámite obtiene uno distinto.
         $folio_salida_resp = null;
         if (empty($tramite['folio_salida_numero'])) {
             $anio_salida = (int) date('Y');
             $tipo_tramite_id = (int) $tramite['tipo_tramite_id'];
             $nuevo_salida = reservarFolioSalida($conn, $tipo_tramite_id, $anio_salida);

             $stmtUpS = $conn->prepare(
                 "UPDATE tramites
                  SET folio_salida_numero = ?, folio_salida_anio = ?, tiempo_salida = COALESCE(tiempo_salida, NOW())
                  WHERE id = ?"
             );
             $stmtUpS->bind_param("iii", $nuevo_salida, $anio_salida, $tramite_id);
             $stmtUpS->execute();
             $stmtUpS->close();

             $folio_salida_resp = str_pad($nuevo_salida, 3, '0', STR_PAD_LEFT) . '/' . $anio_salida;
         } else {
             $folio_salida_resp = str_pad($tramite['folio_salida_numero'], 3, '0', STR_PAD_LEFT) . '/' . $tramite['folio_salida_anio'];
         }

         // Log
         $ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'desconocida';
         $ua  = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'desconocido';
         $det = "Folio entrada: $folio | Subtramite id: $tramite_id | Datos constancia | Numero: $numero_asignado | Folio salida: $folio_salida_resp";
        $stmtL = $conn->prepare("
            INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent)
            VALUES (?, 'Actualizo datos constancia', 'tramites', ?, ?, ?, ?)
        ");
        if ($stmtL) {
            $stmtL->bind_param("iisss", $uid, $tramite_id, $det, $ip, $ua);
            $stmtL->execute();
            $stmtL->close();
        }

        $conn->commit();

        echo json_encode([
            'success'       => true,
            'message'       => 'Datos de constancia guardados correctamente',
            'folio'         => $folio,
            'id'            => $tramite_id,
            'folio_salida'  => $folio_salida_resp
        ]);
        exit;
    }

    // ── MODO NORMAL: Fotografias (opcionales) ──────────────
    $carpeta = "../uploads/";
    Utilidades::crearDirectorioSeguro($carpeta);

    $foto1_archivo = $tramite['foto1_archivo'];
    $foto2_archivo = $tramite['foto2_archivo'];

    foreach (['foto1' => &$foto1_archivo, 'foto2' => &$foto2_archivo] as $fKey => &$fVar) {
        if (!isset($_FILES[$fKey]) || $_FILES[$fKey]['error'] !== UPLOAD_ERR_OK) continue;
        if (empty($_FILES[$fKey]['name'])) continue;

        $validacion = validarArchivo($_FILES[$fKey], ['jpg', 'jpeg', 'png']);
        if (!$validacion['valido']) throw new ArchivoException($fKey . ': ' . $validacion['mensaje']);
        $ext = $validacion['extension'];

        $n = Utilidades::generarNombreArchivo($fKey, $ext);
        if (!move_uploaded_file($_FILES[$fKey]['tmp_name'], $carpeta . $n)) throw new ArchivoException('No se pudo guardar ' . $fKey . '.');
        $fVar = $n;
    }
    unset($fVar);

    // ── Documentos adicionales (ventanilla puede reemplazar) ──
    $ine_archivo                = $tramite['ine_archivo'];
    $titulo_archivo             = $tramite['titulo_archivo'];
    $predial_archivo            = $tramite['predial_archivo'];
    $escrituras_archivo         = $tramite['escrituras_archivo'];
    $formato_constancia_archivo = $tramite['formato_constancia'];
    $oficio_vobo_archivo        = $tramite['oficio_vobo'];

    $docMap = [
        'ine'                => ['campo' => &$ine_archivo,                'prefijo' => 'ine'],
        'escritura'          => ['campo' => &$escrituras_archivo,         'prefijo' => 'escritura'],
        'predial'            => ['campo' => &$predial_archivo,            'prefijo' => 'predial'],
        'formato_constancia' => ['campo' => &$formato_constancia_archivo, 'prefijo' => 'formato'],
        'oficio_vobo'        => ['campo' => &$oficio_vobo_archivo,        'prefijo' => 'oficio_vobo'],
    ];

    foreach ($docMap as $inputName => &$info) {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) continue;
        if (empty($_FILES[$inputName]['name'])) continue;

        $validacion = validarArchivo($_FILES[$inputName], ['jpg', 'jpeg', 'png', 'pdf']);
        if (!$validacion['valido']) throw new ArchivoException($inputName . ': ' . $validacion['mensaje']);
        $ext = $validacion['extension'];

        $n = Utilidades::generarNombreArchivo($info['prefijo'], $ext);
        if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $carpeta . $n)) throw new ArchivoException('No se pudo guardar ' . $inputName . '.');
        $info['campo'] = $n;
    }
    unset($info);

    // Documento final firmado por el Director (constancia o licencia).
    $otros_archivos = json_decode((string)($tramite['otros_archivos'] ?? ''), true);
    if (!is_array($otros_archivos)) $otros_archivos = [];

    if (isset($_FILES['documento_firmado']) && $_FILES['documento_firmado']['error'] !== UPLOAD_ERR_NO_FILE) {
        if (!esVentanilla() && !esAdministrador()) {
            throw new Exception('Sin permisos para cargar el documento firmado.');
        }
        if ($estatus !== 'Entregado y archivado') {
            throw new Exception('El documento firmado solo puede adjuntarse al entregar y archivar el trámite.');
        }

        $archivoFirmado = $_FILES['documento_firmado'];
        Utilidades::validarArchivo($archivoFirmado, ['pdf', 'jpg', 'jpeg', 'png']);
        if ($archivoFirmado['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No se pudo recibir el documento firmado.');
        }
        if ((int)$archivoFirmado['size'] > 10485760) {
            throw new Exception('El documento firmado excede el máximo de 10 MB.');
        }

        $extension = strtolower(pathinfo($archivoFirmado['name'], PATHINFO_EXTENSION));
        $mimesPermitidos = [
            'pdf' => ['application/pdf'],
            'jpg' => ['image/jpeg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'png' => ['image/png']
        ];
        if (!isset($mimesPermitidos[$extension])) {
            throw new Exception('El documento firmado debe ser PDF, JPG o PNG.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $archivoFirmado['tmp_name']) : '';
        // El recurso se libera automáticamente al salir de este bloque.
        // finfo_close() está obsoleto en versiones recientes de PHP.
        if (!in_array($mime, $mimesPermitidos[$extension], true)) {
            throw new Exception('El contenido del documento firmado no corresponde al formato indicado.');
        }

        $esLicenciaFirmada = (int)$tramite['tipo_tramite_id'] === 7;
        $tipoDocumentoFirmado = $esLicenciaFirmada ? 'documento_firmado_licencia' : 'documento_firmado_constancia';
        $etiquetaDocumentoFirmado = $esLicenciaFirmada ? 'Licencia firmada y escaneada' : 'Constancia firmada y escaneada';
        $nombreFirmado = Utilidades::generarNombreArchivo($tipoDocumentoFirmado . '_' . $tramite_id, $extension);
        $destinoFirmado = $carpeta . $nombreFirmado;
        if (!move_uploaded_file($archivoFirmado['tmp_name'], $destinoFirmado)) {
            throw new Exception('No se pudo guardar el documento firmado.');
        }
        $documento_firmado_guardado = $destinoFirmado;

        // Sustituir la version firmada anterior del mismo tipo.
        $otros_archivos = array_values(array_filter($otros_archivos, static function ($documento) use ($tipoDocumentoFirmado) {
            return !is_array($documento) || ($documento['tipo'] ?? '') !== $tipoDocumentoFirmado;
        }));
        $otros_archivos[] = [
            'tipo' => $tipoDocumentoFirmado,
            'label' => $etiquetaDocumentoFirmado,
            'archivo' => $nombreFirmado,
            'fecha' => date('Y-m-d H:i:s'),
            'usuario_id' => (int)$_SESSION['id']
        ];
    }
    $otros_archivos_json = json_encode($otros_archivos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($otros_archivos_json === false) throw new Exception('No se pudo registrar el documento firmado.');

    // ── Detectar trámite VOBO con oficio_vobo recién subido ──
    $tipo_tramite_id_actual = (int) $tramite['tipo_tramite_id'];
    $oficio_vobo_antes = $tramite['oficio_vobo'];
    $oficio_vobo_cargado_ahora = isset($_FILES['oficio_vobo']) && $_FILES['oficio_vobo']['error'] === UPLOAD_ERR_OK && !empty($_FILES['oficio_vobo']['name']);
    
    // Si es trámite VOBO (id=9) y se subió oficio_vobo, cambiar a "En Revisión por Validador"
    if ($tipo_tramite_id_actual === 9 && $oficio_vobo_cargado_ahora && empty($oficio_vobo_antes)) {
        $estatus = 'En Revisión por Validador';
    }

    // ── Sanitizar observaciones ────────────────────────────
    $observaciones      = htmlspecialchars($observaciones, ENT_QUOTES, 'UTF-8');
    $verificador_nombre = htmlspecialchars($verificador_nombre, ENT_QUOTES, 'UTF-8');
    $uid                = (int) $_SESSION['id'];

    // ── UPDATE tramite ─────────────────────────────────────
    $sql = "UPDATE tramites SET 
            estatus            = ?,
            observaciones      = ?,
            foto1_archivo      = ?,
            foto2_archivo      = ?,
            ine_archivo        = ?,
            titulo_archivo     = ?,
            predial_archivo    = ?,
            escrituras_archivo = ?,
            formato_constancia = ?,
            oficio_vobo        = ?,
            aprobado_por       = ?,
            verificador_nombre = ?,
            fecha_aprobacion   = NOW()";
    if ($estatus === 'Firmado') {
        $sql .= ",
            aprobado_director = 1,
            fecha_aprobacion_director = NOW()";
    }
    if (in_array($estatus, ['Entregado y archivado', 'Rechazado'], true)) {
        $sql .= ",
            tiempo_salida      = NOW()";
    }

    $params = [
        $estatus, $observaciones, $foto1_archivo, $foto2_archivo,
        $ine_archivo, $titulo_archivo, $predial_archivo,
        $escrituras_archivo, $formato_constancia_archivo, $oficio_vobo_archivo,
        $uid, $verificador_nombre
    ];
    $types = "ssssssssssis";

    // Los datos de constancia quedan consolidados al registrar la firma.
    if ($estatus === 'Firmado' && !empty($numero_asignado)) {
        $sql .= ",
            numero_asignado     = ?,
            tipo_asignacion     = ?,
            referencia_anterior = ?,
            entre_calle1        = ?,
            entre_calle2        = ?,
            cuenta_catastral    = ?,
            manzana             = ?,
            lote                = ?,
            fecha_constancia    = ?";

        $params[] = $numero_asignado;
        $params[] = $tipo_asignacion;
        $params[] = $referencia_anterior ?: null;
        $params[] = $entre_calle1;
        $params[] = $entre_calle2;
        $params[] = $cuenta_catastral_c ?: null;
        $params[] = $manzana ?: null;
        $params[] = $lote ?: null;
        $params[] = $fecha_constancia;
        $types   .= "sssssssss";
    }

    // Actualizar exactamente el trámite seleccionado. Varios subtrámites pueden
    // compartir el mismo folio de entrada y no deben cambiar juntos.
    $sql .= " WHERE id = ?";
    $params[] = $tramite_id;
    $types .= "i";

    $stmtUp = $conn->prepare($sql);
    if (!$stmtUp) throw new Exception("Error prepare UPDATE: " . $conn->error);

    $stmtUp->bind_param($types, ...$params);
    if (!$stmtUp->execute()) throw new Exception("Error UPDATE: " . $stmtUp->error);
    $stmtUp->close();

    // MySQL sin modo estricto puede convertir un valor ajeno al ENUM en una
    // cadena vacía y aun así reportar éxito. Confirmar que sí quedó persistido.
    $stmtEstado = $conn->prepare("SELECT estatus FROM tramites WHERE id = ? LIMIT 1");
    if (!$stmtEstado) throw new Exception("Error al verificar el estatus: " . $conn->error);
    $stmtEstado->bind_param("i", $tramite_id);
    if (!$stmtEstado->execute()) throw new Exception("Error al verificar el estatus: " . $stmtEstado->error);
    $estadoGuardado = $stmtEstado->get_result()->fetch_assoc();
    $stmtEstado->close();
    if (!$estadoGuardado || $estadoGuardado['estatus'] !== $estatus) {
        throw new Exception('El estatus no fue aceptado por la base de datos. Aplica la migración del flujo de firma y archivo.');
    }

    // El escaneo pertenece al tramite/poligono seleccionado, no a todo el grupo del folio.
    if ($documento_firmado_guardado !== null) {
        $stmtDocumentoFirmado = $conn->prepare("UPDATE tramites SET otros_archivos = ? WHERE id = ?");
        if (!$stmtDocumentoFirmado) throw new Exception('No se pudo preparar el registro del documento firmado.');
        $stmtDocumentoFirmado->bind_param('si', $otros_archivos_json, $tramite_id);
        if (!$stmtDocumentoFirmado->execute()) throw new Exception('No se pudo registrar el documento firmado.');
        $stmtDocumentoFirmado->close();
    }

    // -- CREAR REGISTROS ADICIONALES CUANDO SE APRUEBA Y SE ASIGNA NÚMERO --
    if ($estatus === 'Firmado' && !empty($numero_asignado)) {
         // Get current cantidad from database to see if we need to create adicionales
         $stmtCurrent = $conn->prepare("SELECT cantidad FROM tramites WHERE folio_numero = ? AND folio_anio = ?");
         $stmtCurrent->bind_param("ii", $folio_numero, $folio_anio);
         $stmtCurrent->execute();
         $resultCurrent = $stmtCurrent->get_result();
         $rowCurrent = $resultCurrent->fetch_assoc();
         $current_cantidad = $rowCurrent['cantidad'] ?? 1;
         $stmtCurrent->close();

         // If cantidad is greater than 1, we need to create additional records
         if ($cantidad > 1 && $current_cantidad < $cantidad) {
             $additionalCount = $cantidad - $current_cantidad;
             for ($i = 0; $i < $additionalCount; $i++) {
                 $sqlInsertAdicional = "INSERT INTO tramites_adicionales (
                     tramite_principal_id, tipo_tramite_id, propietario, solicitante, telefono, correo,
                     folio_numero_adicional, cantidad, estatus
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                 
                 $stmtInsertAdicional = $conn->prepare($sqlInsertAdicional);
                 if (!$stmtInsertAdicional) {
                     throw new Exception("Error preparar INSERT adicional: " . $conn->error);
                 }
                 
                 // Get the folio number for this additional constancia
                 $stmtFolio = $conn->prepare("SELECT COALESCE(MAX(folio_numero_adicional), 0) + 1 AS siguiente
                                              FROM tramites_adicionales
                                              WHERE tramite_principal_id = ?");
                 $stmtFolio->bind_param("i", $tramite_id);
                 $stmtFolio->execute();
                 $resultFolio = $stmtFolio->get_result();
                 $rowFolio = $resultFolio->fetch_assoc();
                 $folioAdicional = $rowFolio['siguiente'] ?? 1;
                 $stmtFolio->close();
                  
                   // Prepare correo value to avoid "Only variables should be passed by reference" error
                   $correo = $tramite['correo'] ?? '';
                   $estatusAdicional = 'En revisión';

                   $stmtInsertAdicional->bind_param("iisssissi",
                       $tramite_id,
                       $tramite['tipo_tramite_id'],
                       $tramite['propietario'],
                       $tramite['solicitante'],
                       $tramite['telefono'],
                       $correo,
                       $folioAdicional,
                       $cantidad, // Store the original requested quantity in each adicional
                       $estatusAdicional // Initial status
                   );
                 
                 if (!$stmtInsertAdicional->execute()) {
                     throw new Exception("Error INSERT adicional: " . $stmtInsertAdicional->error);
                 }
                 $stmtInsertAdicional->close();
             }
         }
     }
    if ($estatus === 'Firmado') {
        $anio_actual = (int) date('Y');

        // La firma corresponde al registro seleccionado, no a sus hermanos de folio.
        if (empty($tramite['folio_salida_numero'])) {
            $nuevo_salida = reservarFolioSalida($conn, (int)$tramite['tipo_tramite_id'], $anio_actual);
            $stmtUpS = $conn->prepare("UPDATE tramites SET folio_salida_numero = ?, folio_salida_anio = ? WHERE id = ?");
            $stmtUpS->bind_param("iii", $nuevo_salida, $anio_actual, $tramite_id);
            $stmtUpS->execute();
            $stmtUpS->close();
            $folio_asignado = $nuevo_salida;
            $det = "Folio: $folio | $estatus_anterior -> $estatus | Folio salida: $nuevo_salida/$anio_actual";
        }
    }

    // ── Historial ──────────────────────────────────────────
    $accionMap = [
        'En revisión'              => 'Modificado',
        'En Revisión por Validador' => 'En Revisión por Validador',
        'Pendiente por firmar'     => 'Pendiente por firmar',
        'Firmado'                  => 'Firmado',
        'Entregado y archivado'    => 'Entregado y archivado',
        'Rechazado'                => 'Rechazado',
        'En corrección'            => 'En corrección',
    ];
    $accionHist = isset($accionMap[$estatus]) ? $accionMap[$estatus] : 'Modificado';

    $stmtH = $conn->prepare("
        INSERT INTO historial_tramites
          (tramite_id, usuario_id, accion, estatus_anterior, estatus_nuevo, comentario)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    if ($stmtH) {
        $stmtH->bind_param("iissss", $tramite_id, $uid, $accionHist, $estatus_anterior, $estatus, $observaciones);
        $stmtH->execute();
        $stmtH->close();
    }

    // ── Comentario si hay observaciones ───────────────────
    if (!empty($observaciones)) {
        $stmtC = $conn->prepare("
            INSERT INTO comentarios_tramites (tramite_id, usuario_id, comentario, es_interno)
            VALUES (?, ?, ?, 0)
        ");
        if ($stmtC) {
            $stmtC->bind_param("iis", $tramite_id, $uid, $observaciones);
            $stmtC->execute();
            $stmtC->close();
        }
    }

    // ── Log ───────────────────────────────────────────────
    $ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'desconocida';
    $ua  = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'desconocido';
    // Si ya se actualizó $det en la asignación de folio, no sobrescribir
    if (!isset($folio_asignado)) {
        $det = "Folio: $folio | $estatus_anterior → $estatus | Verificador: $verificador_nombre";
    }
    $stmtL = $conn->prepare("
        INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent)
        VALUES (?, 'Actualizó trámite', 'tramites', ?, ?, ?, ?)
    ");
    if ($stmtL) {
        $stmtL->bind_param("iisss", $uid, $tramite_id, $det, $ip, $ua);
        $stmtL->execute();
        $stmtL->close();
    }

    $conn->commit();
    $transaccion_confirmada = true;

    // ── Actualizar GeoJSON ──
    $geojsonPath = "../Geojson/TRAMITES.geojson";
    if (file_exists($geojsonPath)) {
        $geojson = json_decode(file_get_contents($geojsonPath), true);
        if ($geojson && isset($geojson['features'])) {
            foreach ($geojson['features'] as &$feature) {
                if (isset($feature['properties']['FOLIO_INGR']) && $feature['properties']['FOLIO_INGR'] === $folio) {
                    $feature['properties']['ESTATUS'] = $estatus;
                    if ($numero_asignado) {
                        $feature['properties']['NUMERO'] = $numero_asignado;
                    }
                    break;
                }
            }
            file_put_contents($geojsonPath, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    // ── Generar links de notificación ─────────────────────
    $nombre       = $tramite['solicitante'] ?: $tramite['propietario'];
    $primerNombre = explode(' ', $nombre)[0];
    $tipoTramite  = isset($tramite['tipo_tramite_nombre']) ? $tramite['tipo_tramite_nombre'] : 'trámite';

    $msgs = [
        'En revisión'              => "Hola $primerNombre, su trámite *$folio* ($tipoTramite) está EN REVISIÓN. Le informaremos novedades. — Dirección de Planeación y D.U.",
        'En Revisión por Validador' => "Hola $primerNombre, su trámite *$folio* ($tipoTramite) está EN REVISIÓN por el Validador. Pronto nos comunicaremos con usted. — Dirección de Planeación y D.U.",
        'Pendiente por firmar'     => "Hola $primerNombre, su trámite *$folio* ($tipoTramite) fue aprobado por el verificador y está PENDIENTE POR FIRMAR. — Dirección de Planeación y D.U.",
        'Firmado'                  => "¡Hola $primerNombre! Su trámite *$folio* ($tipoTramite) ya fue FIRMADO. Puede pasar a recogerlo con esta papeleta. — Dirección de Planeación y D.U.",
        'Entregado y archivado'    => "Hola $primerNombre, su trámite *$folio* ($tipoTramite) fue ENTREGADO Y ARCHIVADO. — Dirección de Planeación y D.U.",
        'Rechazado'                => "Hola $primerNombre, lamentamos informarle que su trámite *$folio* ($tipoTramite) fue RECHAZADO." . (!empty($observaciones) ? " Motivo: $observaciones" : " Comuníquese con nosotros.") . " — Dirección de Planeación y D.U.",
        'En corrección'            => (function () use ($primerNombre, $folio, $tipoTramite, $observaciones) {
            $msg = "Hola $primerNombre, su trámite *$folio* ($tipoTramite) requiere CORRECCIÓN para continuar con el proceso.\n";
            if (!empty($observaciones)) {
                $partes = explode(' | ', $observaciones);
                foreach ($partes as $parte) {
                    if (strpos($parte, 'Documentos/requisitos: ') === 0) {
                        $docs = explode(', ', str_replace('Documentos/requisitos: ', '', $parte));
                        $msg .= "\nDocumentos/requisitos pendientes:\n";
                        foreach ($docs as $doc) {
                            $msg .= "• $doc\n";
                        }
                    } else {
                        $msg .= "\nIndicación adicional: $parte\n";
                    }
                }
            }
            $msg .= "\nFavor de presentarse con los documentos indicados en las oficinas de la Dirección de Planeación y Desarrollo Urbano.\n— Dirección de Planeación y D.U.";
            return $msg;
        })(),
    ];

    $asuntos = [
        'En revisión'              => "Trámite $folio en Revisión",
        'En Revisión por Validador' => "Trámite $folio — En Revisión por Validador",
        'Pendiente por firmar'     => "Trámite $folio — Pendiente por firmar",
        'Firmado'                  => "Trámite $folio — Firmado",
        'Entregado y archivado'    => "Trámite $folio — Entregado y archivado",
        'Rechazado'                => "Trámite $folio Rechazado",
        'En corrección'            => "Trámite $folio — Corrección requerida",
    ];

    $msg    = isset($msgs[$estatus])    ? $msgs[$estatus]    : "Actualización de trámite $folio.";
    $asunto = isset($asuntos[$estatus]) ? $asuntos[$estatus] : "Actualización Trámite $folio";

    $tel    = preg_replace('/\D/', '', isset($tramite['telefono']) ? $tramite['telefono'] : '');
    $waLink = $tel
              ? "https://wa.me/52{$tel}?text=" . rawurlencode($msg)
              : null;
    $gmLink = !empty($tramite['correo'])
              ? "mailto:{$tramite['correo']}?subject=" . rawurlencode($asunto) . "&body=" . rawurlencode($msg)
              : null;

    echo json_encode([
        'success'      => true,
        'message'      => 'Trámite actualizado correctamente',
        'estatus'      => $estatus,
        'folio'        => $folio,
        'foto1'        => $foto1_archivo,
        'foto2'        => $foto2_archivo,
        'notificacion' => [
            'nombre'   => $nombre,
            'telefono' => isset($tramite['telefono']) ? $tramite['telefono'] : '',
            'correo'   => isset($tramite['correo'])   ? $tramite['correo']   : '',
            'wa_link'  => $waLink,
            'gm_link'  => $gmLink,
            'mensaje'  => $msg,
        ],
    ]);

} catch (Throwable $e) {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->rollback();
    }
    if (!$transaccion_confirmada && !empty($documento_firmado_guardado) && is_file($documento_firmado_guardado)) {
        unlink($documento_firmado_guardado);
    }
    AppLogger::error($e, ['endpoint' => 'actualizarTramite', 'tramite_id' => $tramite_id_post]);
    $esValidacion = $e instanceof ValidacionException;
    http_response_code($esValidacion ? 400 : 500);
    echo json_encode(['success' => false, 'message' => $esValidacion ? $e->getMessage() : 'No fue posible actualizar el trámite.']);
}
