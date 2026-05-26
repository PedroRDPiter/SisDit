<?php
// =====================================================
// REGISTRAR NUEVO TRÁMITE
// Recibe el formulario de Dash.php, valida todos los campos,
// sube los archivos adjuntos y lo inserta en BD con folio autogenerado
// =====================================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

require "db.php";
require "funciones_seguridad.php";

session_start();

/* ── Seguridad ─────────────────────────────────────────── */
if (!isset($_SESSION['id'])) {
    header("Location: ../acceso.php"); exit;
}
if (!validarCSRF()) {
    header("Location: ../Dash.php?error_msg=" . urlencode("Token de seguridad inválido")); exit;
}

/* ── Validar campos obligatorios ───────────────────────── */
$obligatorios = ['propietario','direccion','localidad','tipo_tramite_id','fecha_ingreso','solicitante','telefono'];
foreach ($obligatorios as $campo) {
    if (empty(trim($_POST[$campo] ?? ''))) {
        header("Location: ../Dash.php?error_msg=" . urlencode("El campo '$campo' es obligatorio."));
        exit;
    }
}

try {
    $conn->begin_transaction();

    /* ── Texto en MAYÚSCULAS ── */
    $propietario = limpiarMayusculas($_POST['propietario']);
    $direccion   = limpiarMayusculas($_POST['direccion']);
    $localidad   = limpiarMayusculas($_POST['localidad']);
    $colonia     = !empty($_POST['colonia'])   ? limpiarMayusculas($_POST['colonia'])   : null;
    $solicitante = limpiarMayusculas($_POST['solicitante']);
    $cp          = !empty($_POST['cp'])         ? trim($_POST['cp'])                    : null;
    $superficie  = !empty($_POST['superficie']) ? trim($_POST['superficie'])             : null;
    $calle       = !empty($_POST['calle'])      ? limpiarMayusculas($_POST['calle'])     : null;
    $entre_calle1= !empty($_POST['entre_calle1'])? limpiarMayusculas($_POST['entre_calle1']): null;
    $entre_calle2= !empty($_POST['entre_calle2'])? limpiarMayusculas($_POST['entre_calle2']): '';

    $tipo_tramite_id = (int) $_POST['tipo_tramite_id'];

    /* ── Cuenta catastral: solo dígitos (null → trigger la asigna automáticamente) ── */
    $cuenta_catastral = null;
    if (!empty($_POST['cuenta_catastral'])) {
        $cc = preg_replace('/\D/', '', $_POST['cuenta_catastral']);
        if ($cc !== '') $cuenta_catastral = $cc;
    }

    /* ── Teléfono ── */
    $telefono = trim($_POST['telefono']);
    $telSolo  = preg_replace('/\D/', '', $telefono);
    if (strlen($telSolo) < 10) throw new Exception("Teléfono inválido (mínimo 10 dígitos).");

    /* ── Correo (opcional) ── */
    $correo = null;
    if (!empty(trim($_POST['correo'] ?? ''))) {
        $correo = trim($_POST['correo']);
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL))
            throw new Exception("Correo electrónico inválido.");
    }

    /* ── Fechas ── */
    $fecha_ingreso = trim($_POST['fecha_ingreso']);
    if (!DateTime::createFromFormat('Y-m-d', $fecha_ingreso))
        throw new Exception("Fecha de ingreso inválida.");

    // Entrega = 10 días hábiles automático
    $f = new DateTime($fecha_ingreso);
    $c = 0;
    while ($c < 10) {
        $f->modify('+1 day');
        if ($f->format('N') <= 5) $c++;
    }
    $fecha_entrega = $f->format('Y-m-d');

    /* ── Coordenadas UTM ── */
    $lat = null;
    $lng = null;
    if (!empty($_POST['lat']) && !empty($_POST['lng'])) {
        $latVal = (float) $_POST['lat'];
        $lngVal = (float) $_POST['lng'];
        if ($latVal < 100000 || $latVal > 900000) throw new Exception("Coordenada UTM X inválida.");
        if ($lngVal < 0     || $lngVal > 10000000) throw new Exception("Coordenada UTM Y inválida.");
        $lat = $latVal;
        $lng = $lngVal;
    }

    /* ── Folio siguiente ── */
    $folio_anio = (int) date("Y");
    $stmtFolio = $conn->prepare(
        "SELECT COALESCE(MAX(CAST(folio_numero AS UNSIGNED)), 0) + 1 FROM tramites WHERE folio_anio = ? FOR UPDATE"
    );
    if (!$stmtFolio) throw new Exception("Error folio: " . $conn->error);
    $stmtFolio->bind_param("i", $folio_anio);
    $stmtFolio->execute();
    $stmtFolio->bind_result($nuevoFolio);
    $stmtFolio->fetch();
    $stmtFolio->close();
    $folio_numero = (int) $nuevoFolio;  // INT para coincidir con la columna

    /* ── Archivos (todos opcionales) ── */
    $carpeta = "../uploads/";
    if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);

    $archivos = [];
    $comentario_sin_doc = !empty($_POST['comentario_sin_doc'])
        ? htmlspecialchars(trim($_POST['comentario_sin_doc']), ENT_QUOTES, 'UTF-8') : null;

    $camposArchivo = ['ine', 'escritura', 'predial', 'formato_constancia'];
    foreach ($camposArchivo as $campo) {
        if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] !== UPLOAD_ERR_OK) continue;
        if (empty($_FILES[$campo]['name'])) continue;

        $ext  = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));
        $permitidos = ['pdf','jpg','jpeg','png'];
        if (!in_array($ext, $permitidos)) throw new Exception("Extensión no permitida: $campo ($ext)");
        if ($_FILES[$campo]['size'] > 5242880) throw new Exception("Archivo demasiado grande: $campo (máx 5MB)");

        $nombre = $campo . '_' . uniqid() . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($_FILES[$campo]['tmp_name'], $carpeta . $nombre))
            throw new Exception("Error al guardar archivo: $campo");
        $archivos[$campo] = $nombre;
    }

    $ine_archivo    = $archivos['ine']                ?? null;  // s 18
    $esc_archivo    = $archivos['escritura']          ?? null;  // s 19
    $pre_archivo    = $archivos['predial']            ?? null;  // s 20
    $fmt_constancia = $archivos['formato_constancia'] ?? null;  // s 21
    $datos_json     = null;                                     // s 22
    $usuario_id     = (int) $_SESSION['id'];                    // i 24

    /* ══════════════════════════════════════════════════════
       INSERT tramites — 28 columnas, 28 valores
       Mapeo columna → tipo:
       1-3  folio_numero, folio_anio, tipo_tramite_id        => i i i
       4-11 propietario, direccion, localidad, colonia, cp,
            calle, entre_calle1, entre_calle2                  => s s s s s s s s
       12-13 lat, lng                                         => d d
       14-15 fecha_ingreso, fecha_entrega                     => s s
       16-18 solicitante, telefono, correo                    => s s s
       19-20 cuenta_catastral, superficie                     => s s
       21-24 ine_archivo, escrituras_archivo, predial_archivo, formato_constancia  => s s s s
       25-26 datos_especificos, comentario_sin_doc            => s s
       27 cantidad_princial                                   => i
       28 usuario_creador_id                                  => i
       Total: 3i + 8s + 2d + 2s + 3s + 2s + 4s + 2s + 2i = 28
    ══════════════════════════════════════════════════════ */
    $cantidad_principal = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
    if ($cantidad_principal < 1) $cantidad_principal = 1;
    if ($cantidad_principal > 50) $cantidad_principal = 50;

    // 1. Insertar SIEMPRE el trámite principal como UNA sola fila (con su cantidad)
    //    Esto evita el problema de "MismoTipo (9) + MismoTipo (10)"
    $sql = "INSERT INTO tramites (
        folio_numero, folio_anio, tipo_tramite_id, propietario, direccion, localidad, colonia, cp,
        calle, entre_calle1, entre_calle2, lat, lng, fecha_ingreso, fecha_entrega,
        solicitante, telefono, correo, cuenta_catastral, superficie,
        ine_archivo, escrituras_archivo, predial_archivo, formato_constancia,
        datos_especificos, comentario_sin_doc, cantidad, usuario_creador_id, tramite_principal_id
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Error preparar INSERT principal: " . $conn->error);

    $bind_types = "iiissssssssddssssssssssssssii";
    $null_principal = null;

    $stmt->bind_param($bind_types,
        $folio_numero, $folio_anio, $tipo_tramite_id, $propietario, $direccion, $localidad, $colonia, $cp,
        $calle, $entre_calle1, $entre_calle2, $lat, $lng, $fecha_ingreso, $fecha_entrega,
        $solicitante, $telefono, $correo, $cuenta_catastral, $superficie,
        $ine_archivo, $esc_archivo, $pre_archivo, $fmt_constancia,
        $datos_json, $comentario_sin_doc, $cantidad_principal, $usuario_id, $null_principal
    );

    if (!$stmt->execute()) throw new Exception("Error insert principal: " . $stmt->error);

    $first_tramite_id = $stmt->insert_id;
    $stmt->close();

    // Historial y log del principal
    $hist = $conn->prepare("INSERT INTO historial_tramites (tramite_id, usuario_id, accion, estatus_nuevo, comentario) VALUES (?,?, 'Creado', 'En revisión', 'Trámite creado')");
    if ($hist) { $hist->bind_param("ii", $first_tramite_id, $usuario_id); $hist->execute(); $hist->close(); }

    $folioStr = str_pad($folio_numero, 3, "0", STR_PAD_LEFT) . "/" . $folio_anio;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido';
    $acc = $conn->prepare("INSERT INTO logs_actividad (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent) VALUES (?, 'Creó trámite', 'tramites', ?, ?, ?, ?)");
    if ($acc) {
        $det = "Folio: $folioStr (principal con cantidad $cantidad_principal)";
        $acc->bind_param("iisss", $usuario_id, $first_tramite_id, $det, $ip, $ua);
        $acc->execute();
        $acc->close();
    }

    // 2. Solo los Trámites Adicionales (ta1, ta2, ta3) se insertan como filas separadas (mismo folio)
    for ($i = 1; $i <= 3; $i++) {
        $ta_tipo_id = isset($_POST["ta{$i}_tipo_tramite_id"]) ? (int)$_POST["ta{$i}_tipo_tramite_id"] : 0;
        if (!$ta_tipo_id) continue;

        $ta_cantidad = isset($_POST["ta{$i}_cantidad"]) ? (int)$_POST["ta{$i}_cantidad"] : 1;
        if ($ta_cantidad < 1) $ta_cantidad = 1;
        if ($ta_cantidad > 50) $ta_cantidad = 50;

        $ta_prop = !empty($_POST["ta{$i}_propietario"]) ? limpiarMayusculas($_POST["ta{$i}_propietario"]) : $propietario;
        $ta_soli = !empty($_POST["ta{$i}_solicitante"]) ? limpiarMayusculas($_POST["ta{$i}_solicitante"]) : $solicitante;
        $ta_tel  = !empty($_POST["ta{$i}_telefono"]) ? preg_replace('/\D/','',$_POST["ta{$i}_telefono"]) : $telefono;
        $ta_correo = !empty($_POST["ta{$i}_correo"]) ? trim($_POST["ta{$i}_correo"]) : $correo;
        if (strlen($ta_tel) < 10) { $ta_tel = $telefono; }

        for ($k = 0; $k < $ta_cantidad; $k++) {
            $sql2 = "INSERT INTO tramites (
                folio_numero, folio_anio, tipo_tramite_id, propietario, direccion, localidad, colonia, cp,
                calle, entre_calle1, entre_calle2, lat, lng, fecha_ingreso, fecha_entrega,
                solicitante, telefono, correo, cuenta_catastral, superficie,
                ine_archivo, escrituras_archivo, predial_archivo, formato_constancia,
                datos_especificos, comentario_sin_doc, cantidad, usuario_creador_id, tramite_principal_id
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $stmt2 = $conn->prepare($sql2);
            if (!$stmt2) throw new Exception("Error preparar INSERT adicional: " . $conn->error);

            $stmt2->bind_param($bind_types,
                $folio_numero, $folio_anio, $ta_tipo_id, $ta_prop, $direccion, $localidad, $colonia, $cp,
                $calle, $entre_calle1, $entre_calle2, $lat, $lng, $fecha_ingreso, $fecha_entrega,
                $ta_soli, $ta_tel, $ta_correo, $cuenta_catastral, $superficie,
                $ine_archivo, $esc_archivo, $pre_archivo, $fmt_constancia,
                $datos_json, $comentario_sin_doc, $ta_cantidad, $usuario_id, $first_tramite_id
            );

            if (!$stmt2->execute()) throw new Exception("Error insert adicional: " . $stmt2->error);
            $stmt2->close();
        }
    }

    $tramite_id = $first_tramite_id;

    $conn->commit();

    // ── Agregar al GeoJSON si tiene lat/lng ──
    if ($lat !== null && $lng !== null) {
        $geojsonPath = "../Geojson/TRAMITES.geojson";
        if (file_exists($geojsonPath)) {
            $geojson = json_decode(file_get_contents($geojsonPath), true);
            if ($geojson && isset($geojson['features'])) {
                $newFeature = [
                    'type' => 'Feature',
                    'properties' => [
                        'FOLIO_INGR' => "$folio_numero/$folio_anio",
                        'NOM_SOLI' => $solicitante,
                        'TIP_TRAMIT' => 'Nuevo Trámite',
                        'UBICACION' => $direccion,
                        'FECH_INGRE' => $fecha_ingreso,
                        'FECH_ENTRE' => $fecha_entrega,
                        'ESTATUS' => 'Nuevo',
                        'CONTACTO' => $telefono,
                        'NUMERO' => null
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [$lng, $lat]
                    ]
                ];
                $geojson['features'][] = $newFeature;
                file_put_contents($geojsonPath, json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
    }

    header("Location: ../ficha.php?folio=" . str_pad($folio_numero, 3, "0", STR_PAD_LEFT) . "/$folio_anio");
    exit;

} catch (Exception $e) {
    $conn->rollback();

    // Borrar archivos subidos si los hubo
    if (!empty($archivos)) {
        foreach ($archivos as $a) {
            $ruta = ($carpeta ?? '../uploads/') . $a;
            if (file_exists($ruta)) unlink($ruta);
        }
    }

    header("Location: ../Dash.php?error_msg=" . urlencode($e->getMessage()));
    exit;
}
