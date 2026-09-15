<?php
/*
-----------------------------------------------------------
 GUARDAR TRAMITE_SALIDA
 - Si es la primera vez que se guarda para este tramite_id,
   asigna un folio_salida_numero consecutivo para Licencia de
   Construcción en el año en curso.
 - Si ya existe, solo actualiza estatus/comentarios/vigencia/expiracion.
-------------------------------------------------------------
*/

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "db.php";
require_once "funciones_seguridad.php";

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesión expirada. Recarga la página.']);
    exit;
}

if (!esCalificador() && !esAdministrador()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Sin permisos para esta acción.']);
    exit;
}

if (!validarCSRF()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido.']);
    exit;
}

$tramite_id = isset($_POST['tramite_id']) ? (int)$_POST['tramite_id'] : 0;
if ($tramite_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de trámite inválido.']);
    exit;
}

/* Verificar que el trámite pertenece a un tipo atendido por Calificador. */
$stmtCheck = $conn->prepare(
    "SELECT t.id, t.tipo_tramite_id, t.estatus, t.folio_salida_numero, t.folio_salida_anio,
            slc.tipo_obra
     FROM tramites t
     LEFT JOIN solicitud_lc slc ON slc.tramite_id = t.id
     WHERE t.id = ? AND t.tipo_tramite_id IN (2, 7)"
);
if (!$stmtCheck) {
    echo json_encode(['success' => false, 'message' => 'Error interno: ' . $conn->error]);
    exit;
}
$stmtCheck->bind_param("i", $tramite_id);
$stmtCheck->execute();
$tramite = $stmtCheck->get_result()->fetch_assoc();
if (!$tramite) {
    echo json_encode(['success' => false, 'message' => 'Trámite no encontrado o no corresponde a Calificador.']);
    exit;
}
$stmtCheck->close();
$tipo_tramite_id = (int)$tramite['tipo_tramite_id'];
$aprobado_verificador = in_array($tramite['estatus'], ['Pendiente por firmar', 'Firmado', 'Entregado y archivado', 'Aprobado por Verificador', 'Aprobado'], true);

/* Leer y validar campos */
$estatus = trim($_POST['estatus'] ?? '');
if (!in_array($estatus, ['En revisión', 'Aprobado', 'Rechazado'], true)) {
    echo json_encode(['success' => false, 'message' => 'Estatus inválido.']);
    exit;
}

$comentarios = trim($_POST['comentarios'] ?? '');
$vigencia    = trim($_POST['vigencia']    ?? '');
$expiracion  = trim($_POST['expiracion']  ?? '');

// Solo aplican cuando el tipo de obra es "Otro"
$reglamento_id = isset($_POST['reglamento_id']) && $_POST['reglamento_id'] !== '' ? (int)$_POST['reglamento_id'] : null;
$calles_manual = trim($_POST['calles_manual'] ?? '');
$metros_lineales_manual = isset($_POST['metros_lineales_manual']) && $_POST['metros_lineales_manual'] !== ''
    ? (float)$_POST['metros_lineales_manual'] : null;
$calles_manual_val = $calles_manual !== '' ? $calles_manual : null;

if ($tipo_tramite_id !== 7) {
    $reglamento_id = null;
    $calles_manual_val = null;
    $metros_lineales_manual = null;
}

/* Si el reglamento elegido requiere ubicación manual (Obra Pública),
   calles y metros lineales son obligatorios. */
if ($reglamento_id !== null) {
    $stmtReqUbi = $conn->prepare(
        "SELECT requiere_ubicacion_manual
         FROM reglamentos
         WHERE id = ? AND tipo_tramite_id = 7 AND activo = 1"
    );
    $stmtReqUbi->bind_param("i", $reglamento_id);
    $stmtReqUbi->execute();
    $reqUbi = $stmtReqUbi->get_result()->fetch_assoc();
    $stmtReqUbi->close();

    if (!$reqUbi) {
        echo json_encode(['success' => false, 'message' => 'El reglamento seleccionado no es válido.']);
        exit;
    }

    if ((int)$reqUbi['requiere_ubicacion_manual'] === 1) {
        if ($calles_manual_val === null || $metros_lineales_manual === null) {
            echo json_encode(['success' => false, 'message' => 'Este reglamento requiere capturar las calles involucradas y los metros lineales totales.']);
            exit;
        }
        if ($metros_lineales_manual <= 0) {
            echo json_encode(['success' => false, 'message' => 'Los metros lineales deben ser mayores que cero.']);
            exit;
        }
    }
}

if ($estatus === 'Aprobado' && !$aprobado_verificador) {
    echo json_encode(['success' => false, 'message' => 'El verificador debe aprobar primero este trámite.']);
    exit;
}

if ($tipo_tramite_id === 7 && $estatus === 'Aprobado'
    && !in_array(($tramite['tipo_obra'] ?? ''), ['Construcción', 'Demolición', 'Otro'], true)) {
    echo json_encode(['success' => false, 'message' => 'Completa y aprueba primero la Solicitud de Licencia de Construcción.']);
    exit;
}

if ($tipo_tramite_id === 7 && $estatus === 'Aprobado'
    && ($tramite['tipo_obra'] ?? '') === 'Otro' && $reglamento_id === null) {
    echo json_encode(['success' => false, 'message' => 'Selecciona el reglamento que se imprimirá en esta licencia.']);
    exit;
}

/* La vigencia y expiración son datos propios de la Licencia de Construcción. */
if ($tipo_tramite_id === 7 && $estatus === 'Aprobado') {
    if ($vigencia === '' || $expiracion === '') {
        echo json_encode(['success' => false, 'message' => 'Para aprobar debes capturar vigencia y expiración.']);
        exit;
    }
    if (!validarFecha($vigencia) || !validarFecha($expiracion) || $expiracion < $vigencia) {
        echo json_encode(['success' => false, 'message' => 'La vigencia y expiración no forman un periodo válido.']);
        exit;
    }
} else {
    // Si no está Aprobado, no tiene sentido guardar vigencia/expiración
    $vigencia   = '';
    $expiracion = '';
}

$vigencia_val   = $vigencia   !== '' ? $vigencia   : null;
$expiracion_val = $expiracion !== '' ? $expiracion : null;

$uid = (int)$_SESSION['id'];

try {
    $conn->begin_transaction();


    $stmtExiste = $conn->prepare("SELECT id, folio_salida_numero, folio_salida_anio FROM tramites_salida WHERE tramite_id = ? FOR UPDATE");
    if (!$stmtExiste) throw new Exception("Error preparar SELECT: " . $conn->error);
    $stmtExiste->bind_param("i", $tramite_id);
    $stmtExiste->execute();
    $existente = $stmtExiste->get_result()->fetch_assoc();
    $stmtExiste->close();

    if ($existente) {
        $folio_numero = $existente['folio_salida_numero'];
        $folio_anio   = $existente['folio_salida_anio'];
        $ts_id        = (int)$existente['id'];

        $stmt = $conn->prepare(
            "UPDATE tramites_salida SET
                estatus      = ?,
                comentarios  = ?,
                vigencia     = ?,
                expiracion   = ?,
                reglamento_id = ?,
                calles_manual = ?,
                metros_lineales_manual = ?,
                fecha_salida = NOW(),
                calificado_por = ?
             WHERE tramite_id = ?"
        );
        if (!$stmt) throw new Exception("Error preparar UPDATE: " . $conn->error);
        $stmt->bind_param("ssssisdii", $estatus, $comentarios, $vigencia_val, $expiracion_val, $reglamento_id, $calles_manual_val, $metros_lineales_manual, $uid, $tramite_id);
        if (!$stmt->execute()) throw new Exception("Error al actualizar: " . $stmt->error);
        $stmt->close();

    } else {
        /* No existe: reutilizar el folio general o asignar el siguiente del tipo LC. */
        $anio_actual = (int)date('Y');

        if (!empty($tramite['folio_salida_numero']) && !empty($tramite['folio_salida_anio'])) {
            $folio_numero = (int)$tramite['folio_salida_numero'];
            $folio_anio = (int)$tramite['folio_salida_anio'];
        } else {
            $stmtFolio = $conn->prepare(
                "SELECT COALESCE(MAX(folio_salida_numero), 0) + 1 AS siguiente
                 FROM tramites
                 WHERE folio_salida_anio = ? AND tipo_tramite_id = ?"
            );
            if (!$stmtFolio) throw new Exception("Error preparar folio: " . $conn->error);
            $stmtFolio->bind_param("ii", $anio_actual, $tipo_tramite_id);
            $stmtFolio->execute();
            $candidato_folio = (int)$stmtFolio->get_result()->fetch_assoc()['siguiente'];
            $stmtFolio->close();

            $stmtReserva = $conn->prepare(
                "INSERT INTO folios_salida_tipo_secuencia
                    (tipo_tramite_id, anio, ultimo_numero)
                 VALUES (?, ?, LAST_INSERT_ID(?))
                 ON DUPLICATE KEY UPDATE
                    ultimo_numero = LAST_INSERT_ID(GREATEST(ultimo_numero + 1, VALUES(ultimo_numero)))"
            );
            if (!$stmtReserva) throw new Exception("Error preparar reserva de folio: " . $conn->error);
            $stmtReserva->bind_param("iii", $tipo_tramite_id, $anio_actual, $candidato_folio);
            if (!$stmtReserva->execute()) throw new Exception("Error reservar folio de salida: " . $stmtReserva->error);
            $stmtReserva->close();
            $folio_numero = (int)$conn->insert_id;
            $folio_anio = $anio_actual;
        }

        $stmt = $conn->prepare(
            "INSERT INTO tramites_salida
                (tramite_id, folio_salida_numero, folio_salida_anio, fecha_salida,
                 vigencia, expiracion, estatus, comentarios,
                 reglamento_id, calles_manual, metros_lineales_manual, calificado_por)
             VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) throw new Exception("Error preparar INSERT: " . $conn->error);
        $stmt->bind_param("iiissssisdi", $tramite_id, $folio_numero, $folio_anio, $vigencia_val, $expiracion_val, $estatus, $comentarios, $reglamento_id, $calles_manual_val, $metros_lineales_manual, $uid);
        if (!$stmt->execute()) throw new Exception("Error al guardar: " . $stmt->error);
        $ts_id = (int)$stmt->insert_id;
        $stmt->close();
    }

    // Mantener el folio visible para el resto de los dashboards del sistema general.
    $stmtTramite = $conn->prepare(
        "UPDATE tramites
         SET folio_salida_numero = ?, folio_salida_anio = ?,
             tiempo_salida = COALESCE(tiempo_salida, NOW())
         WHERE id = ?"
    );
    if (!$stmtTramite) throw new Exception("Error preparar actualización del trámite: " . $conn->error);
    $stmtTramite->bind_param("iii", $folio_numero, $folio_anio, $tramite_id);
    if (!$stmtTramite->execute()) throw new Exception("Error al sincronizar el folio de salida: " . $stmtTramite->error);
    $stmtTramite->close();

    $conn->commit();

    /* Log de actividad */
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
    $ua  = $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido';
    $accion = $existente ? 'Actualizó calificación' : 'Creó calificación';
    $det = "tramite_id: $tramite_id, estatus: $estatus";
    $log = $conn->prepare(
        "INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent)
         VALUES (?, ?, 'tramites_salida', ?, ?, ?, ?)"
    );
    if ($log) {
        $log->bind_param("isisss", $uid, $accion, $tramite_id, $det, $ip, $ua);
        $log->execute();
        $log->close();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Calificación guardada correctamente.',
        'id' => $ts_id,
        'folio_salida_numero' => $folio_numero,
        'folio_salida_anio'   => $folio_anio
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("[guardar_tramite_salida] " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
}
