<?php
require "seguridad.php";

// ✅ DESPUÉS (usuario, ventanilla y admin)
require_once "php/funciones_seguridad.php";

$rolesPermitidos = ['Usuario', 'Ventanilla', 'Administrador'];

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
    header("Location: acceso.php?error=no_autorizado");
    exit();
}

// Variables de permisos
$esAdmin = esAdministrador();
$esVentanilla = esVentanilla();
$esUsuario = ($_SESSION['rol'] === 'Usuario');

require_once "php/db.php";

// ✅ DESPUÉS (filtra según rol)
$sql = "SELECT t.*, tt.nombre as tipo_tramite_nombre, 
        u.nombre as creador_nombre, u.apellidos as creador_apellidos
        FROM tramites t
        LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
        LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
        WHERE 1=1";

$params = [];
$types = "";

// USUARIOS solo ven sus propios trámites
if ($esUsuario && !$esAdmin && !$esVentanilla) {
    $sql .= " AND t.usuario_creador_id = ?";
    $params[] = $_SESSION['id'];
    $types .= "i";
}

/* ===== FILTRO FOLIO ===== */
if (!empty($_GET['folio'])) {
    $folioVal = $_GET['folio'];
    if (str_contains($folioVal, '/')) {
        [$fn, $fa] = explode('/', $folioVal);
        $sql .= " AND t.folio_numero = ? AND t.folio_anio = ?";
        $params[] = intval($fn); $params[] = intval($fa);
        $types .= "ii";
    } else {
        $sql .= " AND t.folio_numero LIKE ?";
        $params[] = '%'.$folioVal.'%'; $types .= "s";
    }
}

/* ===== FILTRO NOMBRE / PROPIETARIO ===== */
if (!empty($_GET['nombre'])) {
    $sql .= " AND (t.propietario LIKE ? OR t.solicitante LIKE ?)";
    $params[] = '%'.$_GET['nombre'].'%';
    $params[] = '%'.$_GET['nombre'].'%';
    $types .= "ss";
}

/* ===== FILTRO DIRECCIÓN ===== */
if (!empty($_GET['direccion'])) {
    $sql .= " AND (t.direccion LIKE ? OR t.colonia LIKE ? OR t.localidad LIKE ?)";
    $params[] = '%'.$_GET['direccion'].'%';
    $params[] = '%'.$_GET['direccion'].'%';
    $params[] = '%'.$_GET['direccion'].'%';
    $types .= "sss";
}

/* ===== FILTRO CUENTA CATASTRAL ===== */
if (!empty($_GET['catastral'])) {
    $sql .= " AND t.cuenta_catastral LIKE ?";
    $params[] = '%'.$_GET['catastral'].'%'; $types .= "s";
}

/* ===== FILTRO TRÁMITE ===== */
if (!empty($_GET['tramite'])) {
    $sql .= " AND tt.nombre LIKE ?";
    $params[] = '%' . $_GET['tramite'] . '%';
    $types .= "s";
}

/* ===== FILTRO ESTATUS ===== */
if (!empty($_GET['estatus'])) {
    $sql .= " AND t.estatus = ?";
    $params[] = $_GET['estatus'];
    $types .= "s";
}
/* ===== FILTRO TIPO TRAMITE ===== */
if (!empty($_GET['tipos_tramite'])) {
    $sql .= " AND tt.nombre = ?";
    $params[] = $_GET['tipos_tramite'];
    $types .= "s";
}

/* ===== FILTRO SIN FOTOGRAFÍA ===== */
if (isset($_GET['sin_foto']) && $_GET['sin_foto'] !== '') {
    if ($_GET['sin_foto'] === '1') {
        $sql .= " AND (t.foto1_archivo IS NULL OR t.foto1_archivo = '') AND (t.foto2_archivo IS NULL OR t.foto2_archivo = '')";
    } else {
        $sql .= " AND (t.foto1_archivo IS NOT NULL AND t.foto1_archivo != '')";
    }
}
// ── Todos los trámites (seguimiento) ──
$sql_seg = "SELECT t.*, tt.nombre AS tipo_tramite_nombre FROM tramites t
            LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id WHERE 1=1";
$params_seg = []; $types_seg = "";
if (!empty($_GET['folio'])) {
    if (str_contains($_GET['folio'],'/')) {
        [$fn,$fa] = explode('/',$_GET['folio']);
        $sql_seg .= " AND t.folio_numero=? AND t.folio_anio=?";
        $params_seg[]=(int)$fn; $params_seg[]=(int)$fa; $types_seg.="ii";
    } else {
        $sql_seg .= " AND t.folio_numero LIKE ?";
        $params_seg[]='%'.$_GET['folio'].'%'; $types_seg.="s";
    }
}
if (!empty($_GET['nombre'])) {
    $sql_seg .= " AND (t.propietario LIKE ? OR t.solicitante LIKE ?)";
    $params_seg[]='%'.$_GET['nombre'].'%'; $params_seg[]='%'.$_GET['nombre'].'%'; $types_seg.="ss";
}
if (!empty($_GET['estatus'])) {
    $sql_seg .= " AND t.estatus=?"; $params_seg[]=$_GET['estatus']; $types_seg.="s";
}
if (!empty($_GET['tipos_tramite'])) {
    $sql_seg .= " AND tt.nombre=?"; $params_seg[]=$_GET['tipos_tramite']; $types_seg.="s";
}
$sql_seg .= " ORDER BY t.created_at DESC";
$stmtSeg = $conn->prepare($sql_seg);
if (!empty($params_seg)) $stmtSeg->bind_param($types_seg,...$params_seg);
$stmtSeg->execute();
$seg_res = $stmtSeg->get_result();


$sql .= " ORDER BY t.created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$anio_actual = date("Y");

$stmt = $conn->prepare("
    SELECT COALESCE(MAX(CAST(folio_numero AS UNSIGNED)),0) + 1
    FROM tramites
    WHERE folio_anio = ?
");

$stmt->bind_param("i", $anio_actual);
$stmt->execute();
$stmt->bind_result($siguiente_folio);
$stmt->fetch();
$stmt->close();

/* formato 001 */
$siguiente_folio = str_pad($siguiente_folio, 3, "0", STR_PAD_LEFT);

// ✅ FUNCIÓN: Calcular días hábiles entre dos fechas
function calcularDiasHabilesPHP(DateTime $fechaInicio, DateTime $fechaFin): int {
    $diasHabiles = 0;
    $currentDate = clone $fechaInicio;
    $currentDate->modify('+1 day'); // Excluir el día actual, solo contar días futuros

    while ($currentDate <= $fechaFin) {
        $dayOfWeek = (int)$currentDate->format('w');
        if ($dayOfWeek !== 0 && $dayOfWeek !== 6) { // 0 = domingo, 6 = sábado
            $diasHabiles++;
        }
        $currentDate->modify('+1 day');
    }
    return $diasHabiles;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sis Dit</title>

<!-- BOOTSTRAP 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- LEAFLET -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- CSS PROPIO -->
 <link rel="stylesheet" href="./css/style.css?v=<?= time() ?>">
<style>
/* ================= VARIABLES ================= */
:root{
    --vino:#7b0f2b;
    --vino-oscuro:#5e0b20;
    --vino-claro:#a61c3c;
    --gris-fondo:#f4f6f9;
    --verde:#2f7d6d;
    --verde-oscuro:#246356;
}

/* ================= RESET ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html, body{
    overflow-x: hidden;
    width: 100%;
    background: var(--gris-fondo);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* ================= SIDEBAR ================= */
.sidebar{
    width: 260px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: linear-gradient(180deg, var(--vino), var(--vino-oscuro));
    color: white;
    padding: 20px;
    z-index: 1000;
    overflow-y: auto;
    box-shadow: 4px 0 20px rgba(0,0,0,.15);
}

.sidebar h5{
    text-align: center;
    font-weight: 700;
    margin-bottom: 30px;
    color: white;
}

.sidebar a{
    color: white;
    text-decoration: none;
    display: block;
    padding: 14px 22px;
    transition: .25s;
    font-weight: 500;
}

.sidebar a:hover{
    background: rgba(255,255,255,.12);
    padding-left: 28px;
}

.sidebar a.text-danger{
    color: #ff6b6b !important;
}

.sidebar a.text-danger:hover{
    background: rgba(255,255,255,.12);
}

/* ================= CONTENT ================= */
.content{
    margin-left: 260px;
    width: calc(100% - 260px);
    padding: 30px;
    min-height: 100vh;
    transition: all 0.3s ease;
}

/* ================= HERO - EXACTAMENTE COMO EL ORIGINAL ================= */
.hero{
    background: transparent;
    color: #7b0f2b;
    text-align: left;
    padding: 0;
    margin-bottom: 30px;
}

.hero h1{
    font-size: 28px;
    color: #7b0f2b;
    font-weight: 700;
    text-align: left;
}

.hero p{
    color: #6c757d;
    text-align: left;
    font-size: 16px;
    line-height: 1.5;
    margin-top: 10px;
}

/* ================= TRAMITE BOX ================= */
.tramite-box{
    background: white;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 4px 15px rgba(0,0,0,.08);
    margin-bottom: 30px;
    overflow-x: auto;
    width: 100%;
}

.tramite-header{
    border-bottom: 2px solid rgba(0,0,0,.05);
    margin-bottom: 20px;
    padding-bottom: 15px;
}

/* ================= MAPA ================= */
#mapa{
    width: 100%;
    height: 420px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
    background: #e8e8e8;
}

.leaflet-container{
    height: 100% !important;
    width: 100% !important;
    z-index: 1 !important;
    border-radius: 12px;
}

.leaflet-bar a {
    background-color: white !important;
    color: #7b0f2b !important;
    font-weight: 700;
    font-size: 16px;
}

.leaflet-bar a:hover {
    background-color: #7b0f2b !important;
    color: white !important;
}

.leaflet-control-zoom {
    border: 2px solid #7b0f2b !important;
    border-radius: 8px !important;
}

/* ================= BOTONES - COLORES ORIGINALES ================= */
.btn-primary{
    background: #7b0f2b !important;
    border-color: #7b0f2b !important;
    border-radius: 10px !important;
    color: white !important;
}

.btn-primary:hover{
    background: #5e0b20 !important;
    border-color: #5e0b20 !important;
}

.btn-outline-primary{
    border-color: #7b0f2b !important;
    color: #7b0f2b !important;
}

.btn-outline-primary:hover{
    background: #7b0f2b !important;
    color: white !important;
}

.btn-success{
    background: #7b0f2b !important;
    border: none !important;
    border-radius: 10px !important;
}

.btn-success:hover{
    background: #5e0b20 !important;
}

.btn-outline-secondary{
    color: #6c757d !important;
    border-color: #6c757d !important;
    border-radius: 10px !important;
}

.btn-outline-secondary:hover{
    background: #6c757d !important;
    color: white !important;
}

/* ================= TABLAS ================= */
.table-responsive{
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

table.dataTable thead{
    background: #7b0f2b;
    color: white;
}

table.dataTable thead th{
    background: #7b0f2b !important;
    color: white !important;
}

/* ================= DATATABLES ================= */
.dataTables_wrapper{
    width: 100% !important;
    overflow-x: hidden;
}



/* ================= FILTROS ================= */
.form-control, .form-select{
    border-radius: 10px;
    border: 1px solid #ccc;
    padding: 14px;
    height: auto;
}

.form-control:focus, .form-select:focus{
    border-color: #2f7d6d;
    outline: none;
    box-shadow: none;
}

/* ================= SELECTOR DE TRÁMITE ================= */
.tramite-selector-card{
    border-radius: 14px !important;
    transition: all 0.25s ease !important;
    border: 2px solid #dee2e6 !important;
    cursor: pointer;
}

.tramite-selector-card:hover{
    border-color: #7b0f2b !important;
    box-shadow: 0 6px 24px rgba(123,15,43,0.14) !important;
    transform: translateY(-3px) !important;
}

/* ================= ALERTAS ================= */
.alert-info{
    background: rgba(123,15,43,.12) !important;
    color: #7b0f2b !important;
    border: none !important;
}

/* ================= RESPONSIVE ================= */
@media(max-width: 992px){
    .sidebar{
        position: relative;
        width: 100%;
        height: auto;
        margin-bottom: 20px;
    }
    
    .content{
        margin-left: 0;
        width: 100%;
        padding: 20px;
    }
    
    #mapa{
        height: 350px;
    }
}

@media(max-width: 768px){
    .content{
        padding: 15px;
    }
    
    .tramite-box{
        padding: 15px;
    }
    
    #mapa{
        height: 300px;
    }
    
    .hide-mobile{
        display: none !important;
    }
}

.semaforo-verde {
    background-color: #198754 !important; /* bootstrap success */
    color: white !important;
}

.semaforo-naranja {
    background-color: #fd7e14 !important; /* bootstrap warning */
    color: white !important;
}

.semaforo-rojo {
    background-color: #dc3545 !important; /* bootstrap danger */
    color: white !important;
}

.semaforo-fila-verde {
    background-color: rgba(0, 128, 0, 0.69) !important;
}

.semaforo-fila-naranja {
    background-color: rgba(255, 166, 0, 0.64) !important;
}

.semaforo-fila-rojo {
    background-color: rgba(255, 0, 0, 0.69) !important;
}

/* ================= TABLA MEJORADA ================= */
.tabla-mejorada{
    border-collapse: separate !important;
    border-spacing: 0 8px;
}
.tabla-mejorada thead th{
    background: linear-gradient(0deg,#7b0f2b,#a61c3c) !important;
    color:#fff !important;
    border: none !important;
    padding: 12px 16px !important;
    font-weight: 600;
    vertical-align: middle;
}
.tabla-mejorada tbody tr{
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(123, 15, 44, 0.07);
}
.tabla-mejorada tbody tr td{
    vertical-align: middle;
    padding: 12px 16px !important;
    border-top: none !important;
}
.tabla-mejorada .badge-folio{
    background: rgba(123,15,43,0.08);
    color: #7b0f2b;
    font-weight:700;
    padding:6px 10px;
    border-radius:8px;
}
.tabla-mejorada .badge-folio-salida{
    background: rgba(46,125,81,0.08);
    color: #2e7d51;
    font-weight:700;
    padding:6px 10px;
    border-radius:8px;
}
.tabla-mejorada tbody tr:hover{
    transform: translateY(-3px);
    transition: all .12s ease;
}

.badge-status-revision{ background:#ffc107;color:#212529;padding:6px 10px;border-radius:8px }
.badge-status-verificador{ background:#0d6efd;color:#fff;padding:6px 10px;border-radius:8px }
.badge-status-aprobado{ background:#198754;color:#fff;padding:6px 10px;border-radius:8px }
.badge-status-rechazado{ background:#dc3545;color:#fff;padding:6px 10px;border-radius:8px }
.badge-status-correccion{ background:#fd7e14;color:#fff;padding:6px 10px;border-radius:8px }
.badge-status-default{ background:#6c757d;color:#fff;padding:6px 10px;border-radius:8px }
.badge-tramite-cno{ background:#d0f0fd;color:#0d6efd;border-radius:8px;padding:4px 8px;display:inline-block }
.badge-tramite-cmcu{ background:#A3FA8F;color:#197005;border-radius:8px;padding:4px 8px;display:inline-block }
.badge-tramite-fusion{ background:#F8B763;color:#C87509;border-radius:8px;padding:4px 8px;display:inline-block }
.badge-tramite-subdivision{ background:#DF8FFA;color:#77079C;border-radius:8px;padding:4px 8px;display:inline-block }
.badge-tramite-icu{ background:#8FF4FA;color:#07949C;border-radius:8px;padding:4px 8px;display:inline-block }
.badge-tramite-terminacion{ background:#DB8FFA;color:#72079C;border-radius:8px;padding:4px 8px;display:inline-block }
.badge-tramite-licencia{ background:#FA8FAB;color:#9C072F;border-radius:8px;padding:4px 8px;display:inline-block }
.badge-tramite-anuncios{ background:#FAF18F;color:#9C8F07;border-radius:8px;padding:4px 8px;display:inline-block }
</style>

<script>
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};
</script>

</head>

<body>

<!-- NAVBAR MÓVIL -->
<nav class="navbar navbar-dark d-lg-none" style="background-color: #7b0f2b !important;">
    <div class="container-fluid">
        <span class="navbar-brand">Sistema Georreferenciado</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuMovil">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse" id="menuMovil">
        <ul class="navbar-nav p-3">
            <li class="nav-item"><a class="nav-link" href="#inicio"><i class="bi bi-house me-2"></i> Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="#tramite"><i class="bi bi-pencil-square me-2"></i> Ingreso de Trámite</a></li>
            <li class="nav-item"><a class="nav-link" href="#mapaa"><i class="bi bi-map me-2"></i> Mapa</a></li>
            <li class="nav-item"><a class="nav-link" href="#seguimiento"><i class="bi bi-search me-2"></i> Seguimiento</a></li>
            <li class="nav-item"><a class="nav-link" href="Documentacion.php"><i class="bi bi-file-earmark-text me-2"></i> Documentación</a></li>
            <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
            <li class="nav-item"><a class="nav-link" href="DashAdmin.php"><i class="bi bi-shield-lock me-2"></i> Panel de administración</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php?csrf_token=<?= urlencode($_SESSION['csrf_token']) ?>"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a></li>
        </ul>
    </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar position-fixed d-none d-lg-flex flex-column p-3">
    <h5 class="text-white text-center mb-4">Menú</h5>
    <a class="nav-link text-white" href="#inicio"><i class="bi bi-house me-2"></i> Inicio</a>
    <a class="nav-link text-white" href="#tramite"><i class="bi bi-pencil-square me-2"></i> Ingreso de Trámite</a>
    <a class="nav-link text-white" href="#mapaa"><i class="bi bi-map me-2"></i> Mapa</a>
    <a class="nav-link text-white" href="#seguimiento"><i class="bi bi-search me-2"></i> Seguimiento</a>
    <a class="nav-link text-white" href="Documentacion.php"><i class="bi bi-file-earmark-text me-2"></i> Documentación</a>
    <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
    <a class="nav-link text-white border-top mt-2 pt-2" href="DashAdmin.php"><i class="bi bi-shield-lock me-2"></i> Panel de administración</a>
    <?php endif; ?>
    <a class="nav-link text-danger mt-auto" href="logout.php?csrf_token=<?= urlencode($_SESSION['csrf_token']) ?>"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a>
</div>

<!-- CONTENIDO -->
<div class="content">

    <!-- HERO - EXACTAMENTE COMO EL ORIGINAL -->
    <section class="hero" id="inicio">
        <h1>Bienvenido <?php echo $_SESSION['usuario']; ?></h1>
        <p>
            Plataforma digital para la gestión, captura y consulta de trámites
            georreferenciados del municipio. Desde aquí podrás acceder a los
            formatos oficiales y al registro de información territorial.
        </p>
   
<!-- ================================================ -->
<!-- SEGUIMIENTO DE TODOS LOS TRÁMITES                -->
<!-- ================================================ -->
<section id="seguimiento" class="tramite-box mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="m-0" style="color:#7b0f2b;"><i class="bi bi-search me-2"></i>Seguimiento de Trámites</h4>
    <span class="badge bg-secondary fs-6"><?= $seg_res->num_rows ?> trámite(s)</span>
  </div>

  <form method="GET" class="mb-4">
    <a name="seguimiento"></a>
    <div class="row g-3 align-items-end">
      <div class="col-12 col-sm-6 col-lg-3">
        <label class="form-label fw-semibold"><i class="bi bi-hash me-1"></i>Folio</label>
        <input type="text" name="folio" class="form-control" placeholder="001/2026" value="<?= htmlspecialchars(isset($_GET['folio']) ? $_GET['folio'] : '') ?>">
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
        <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>Nombre / Propietario</label>
        <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre" value="<?= htmlspecialchars(isset($_GET['nombre']) ? $_GET['nombre'] : '') ?>">
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
        <label class="form-label fw-semibold"><i class="bi bi-flag me-1"></i>Estatus</label>
        <select name="estatus" class="form-select">
          <option value="">Todos</option>
          <?php foreach(['En revisión','Aprobado por Verificador','Aprobado','Rechazado','En corrección'] as $es): 
            $sel = (isset($_GET['estatus']) && $_GET['estatus'] === $es) ? 'selected' : '';
          ?>
          <option value="<?= $es ?>" <?= $sel ?>><?= $es ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-sm-6 col-lg-3">
        <label class="form-label fw-semibold"><i class="bi bi-person me-1">Tipo de tramite</i></label>
        <select name="tipos_tramite" id="" class="form-select">
            <option value="">Todos</option>
        <?php foreach(['Anuncios Publicitarios','Licencia de Construcción','Terminacion de Obra','Informe de Compatibilidad Urbanística','Subdivisión de Predio','Fusión de Predios','Constancia de Compatibilidad Urbanística','Constancia de Número Oficial'] as $es):
            $sel = (isset($_GET['tipos_tramite']) && $_GET['tipos_tramite'] === $es) ? 'selected' : '';
        ?>
            <option value="<?= $es ?>" <?= $sel ?>><?= $es ?></option>
        <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-sm-6 col-lg-3 d-flex gap-2 align-items-end">
        <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i>Buscar</button>
        <a href="DashVentanilla.php#seguimiento" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
      </div>
    </div>
  </form>

  <div class="table-responsive">
    <table id="tablaSeguimiento" class="table table-hover align-middle tabla-mejorada">
      <thead>
                <tr>
                    <th><i class="bi bi-hash me-2"></i>Folio (Ingreso)</th>
                    <th><i class="bi bi-arrow-right me-2"></i>Folio (Salida)</th>
                    <th><i class="bi bi-person me-2"></i>Propietario / Solicitante</th>
                    <th><i class="bi bi-file-text me-2"></i>Tipo de trámite</th>
                    <th><i class="bi bi-calendar me-2"></i>Fecha de ingreso</th>
                    <th><i class="bi bi-calendar-check me-2"></i>Fecha de salida / entrega</th>
                    <th><i class="bi bi-flag me-2"></i>Estatus</th>
                    <th><i class="bi bi-hourglass-split me-2"></i>Días hábiles restantes</th>
                    <th class="text-center"><i class="bi bi-gear me-2"></i>Acciones</th>
                </tr>
      </thead>
      <tbody>
            <?php if($seg_res->num_rows===0): ?>
                <tr><td colspan="9"><div class="text-center text-muted py-5"><i class="bi bi-inbox me-2" style="font-size:2rem;"></i><p>No se encontraron trámites.</p></div></td></tr>
            <?php else:
                while($t=$seg_res->fetch_assoc()):
                    // Calcular días restantes para el semáforo
                    $fecha_entrega_dt = !empty($t['fecha_entrega']) ? new DateTime($t['fecha_entrega']) : null;
                    $hoy_dt = new DateTime();
                    $dias_restantes = 'N/A';
                    if ($fecha_entrega_dt) {
$dias_restantes = calcularDiasHabilesPHP($hoy_dt, $fecha_entrega_dt);
                    }

                    $folio       = $t['folio_numero'].'/'.$t['folio_anio'];
                    $folio_sal_s = !empty($t['folio_salida_numero']) ? str_pad($t['folio_salida_numero'],3,'0',STR_PAD_LEFT).'/'.$t['folio_salida_anio'] : '';
                    $tnombre     = isset($t['tipo_tramite_nombre']) ? $t['tipo_tramite_nombre'] : '—';
                    $ttelefono   = isset($t['telefono'])            ? $t['telefono']            : '';
                    $tsolicitante= isset($t['solicitante'])         ? $t['solicitante']         : '';
                    $tdireccion  = isset($t['direccion'])           ? $t['direccion']           : '';
                    $tnumero     = isset($t['numero'])              ? $t['numero']              : '';
                    $tlocalidad  = isset($t['localidad'])           ? $t['localidad']           : '';
                    $tcorreo     = isset($t['correo'])              ? $t['correo']              : '';
                    $tobs        = isset($t['observaciones'])       ? $t['observaciones']       : '';
                    $tine        = isset($t['ine_archivo'])         ? $t['ine_archivo']         : '';
                    $tescritura  = isset($t['escrituras_archivo'])  ? $t['escrituras_archivo']  : (isset($t['titulo_archivo']) ? $t['titulo_archivo'] : '');
                    $tpredial    = isset($t['predial_archivo'])     ? $t['predial_archivo']     : '';
                    $tformato    = isset($t['formato_constancia'])  ? $t['formato_constancia']  : '';
                    $tfoto1      = isset($t['foto1_archivo'])       ? $t['foto1_archivo']       : '';
                    $tfoto2      = isset($t['foto2_archivo'])       ? $t['foto2_archivo']       : '';
                    $estatus     = $t['estatus'];
                    $fecha       = date('d/m/Y', strtotime($t['created_at']));
                    $fecha_entrega = !empty($t['fecha_entrega']) ? date('d/m/Y', strtotime($t['fecha_entrega'])) : '';
                    if (!empty($t['create_at'])) {
                            $fecha_entrega = date('d/m/Y', strtotime($t['create_at']));
                    }
                    if ($estatus === 'En revisión')              $badge = 'badge-status-revision';
                    elseif ($estatus === 'Aprobado por Verificador') $badge = 'badge-status-verificador';
                    elseif ($estatus === 'Aprobado')             $badge = 'badge-status-aprobado';
                    elseif ($estatus === 'Rechazado')            $badge = 'badge-status-rechazado';
                    elseif ($estatus === 'En corrección')        $badge = 'badge-status-correccion';
                    else                                         $badge = 'badge-status-default';
            ?>
        <tr class="fila-tramite">
          <td><span class="badge-folio"><?= htmlspecialchars($folio) ?></span></td>
          <td><?= $folio_sal_s ? '<span class="badge-folio-salida">'.htmlspecialchars($folio_sal_s).'</span>' : '<span class="text-muted small">—</span>' ?></td>
          <td><strong><?= htmlspecialchars($t['propietario']) ?></strong></td>
          <td>
            <?php
              $badgeClass = 'text-muted';
              if ($tnombre === 'Constancia de Número Oficial') $badgeClass = 'badge-tramite-cno';
              elseif ($tnombre === 'Constancia de Compatibilidad Urbanística') $badgeClass = 'badge-tramite-cmcu';
              elseif ($tnombre === 'Fusión de Predios') $badgeClass = 'badge-tramite-fusion';
              elseif ($tnombre === 'Subdivisión de Predio') $badgeClass = 'badge-tramite-subdivision';
              elseif ($tnombre === 'Informe de Compatibilidad Urbanística') $badgeClass = 'badge-tramite-icu';
              elseif ($tnombre === 'Terminacion de Obra') $badgeClass = 'badge-tramite-terminacion';
              elseif ($tnombre === 'Licencia de Construcción') $badgeClass = 'badge-tramite-licencia';
              elseif ($tnombre === 'Anuncios Publicitarios') $badgeClass = 'badge-tramite-anuncios';
            ?>
            <small class="<?= $badgeClass ?>"><?= htmlspecialchars($tnombre) ?></small></td>
          <td><small><?= htmlspecialchars($fecha) ?></small></td>
          <td><small><?= htmlspecialchars($fecha_entrega) ?></small></td>
          <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($estatus) ?></span></td>
          <td class="text-center">
            <?php if (empty($folio_sal_s)): // Solo mostrar semáforo si no hay folio de salida ?>
                <span id="semaforo_<?= $t['id'] ?>" class="badge dias-badge"><?= htmlspecialchars($dias_restantes) ?></span>
            <?php else: // Si hay folio de salida, no mostrar semáforo ?>
                <span class="text-muted small">✓</span>
            <?php endif; ?>
          </td>
          <td class="text-center">
            <a href="ficha.php?folio=<?= urlencode($folio) ?>" target="_blank" class="btn btn-sm btn-ver-ficha" title="Ver ficha completa">
              <i class="bi bi-eye"></i>
            </a>
          </td>
        </tr>
      <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>
</section>


   

<script>


// ==========================================
// FECHAS — entrega AUTOMÁTICA 10 días hábiles
// ==========================================
const hoy = new Date();
const yyyy = hoy.getFullYear();
const mm = String(hoy.getMonth() + 1).padStart(2, '0');
const dd = String(hoy.getDate()).padStart(2, '0');
const fechaHoy = `${yyyy}-${mm}-${dd}`;

const fechaIngresoEl = document.getElementById('fechaIngreso');
const fechaEntregaEl = document.getElementById('fechaEntrega');

if (fechaIngresoEl) {
    fechaIngresoEl.value = fechaHoy;
}

function calcularDiasHabiles(fechaStr, dias) {
    const fecha = new Date(fechaStr + 'T00:00:00');
    let count = 0;
    while (count < dias) {
        fecha.setDate(fecha.getDate() + 1);
        const dow = fecha.getDay(); // 0=dom, 6=sab
        if (dow !== 0 && dow !== 6) count++;
    }
    return fecha.toISOString().split('T')[0];
}

function actualizarFechaEntrega() {
    if (!fechaIngresoEl || !fechaEntregaEl) return;
    const ing = fechaIngresoEl.value;
    if (ing) {
        fechaEntregaEl.value = calcularDiasHabiles(ing, 10);
    }
}

if (fechaIngresoEl) {
    actualizarFechaEntrega();
    fechaIngresoEl.addEventListener('change', actualizarFechaEntrega);
}

// ==========================================
// REQUISITOS POR TRÁMITE
// ==========================================
const requisitosPorTramite = {
    1: { titulo: "Constancia de Número Oficial", documentos: ['ine', 'escritura', 'predial'], nota: '' },
    2: { titulo: "Constancia de Compatibilidad Urbanística", documentos: ['ine', 'escritura', 'predial', 'formato_constancia'], nota: 'Predios menores a 10,000m²: plano catastral. Predios mayores: levantamiento topográfico catastral.' },
    3: { titulo: "Fusión de Predios", documentos: ['ine', 'escritura', 'predial'], nota: '' },
    4: { titulo: "Subdivisión de Predio", documentos: ['ine', 'escritura', 'predial'], nota: '' },
    5: { titulo: "Informe de Compatibilidad Urbanística", documentos: ['ine'], nota: 'Requiere cuenta catastral del predio.' }
};

const labelsDocumentos = {
    'ine': 'INE o Pasaporte',
    'escritura': 'Escritura Pública / Título de Propiedad',
    'predial': 'Boleta Predial Vigente',
    'formato_constancia': 'Formato de Constancia CMCU 2025'
};

function seleccionarTramite(id, nombre) {
    document.getElementById('tipo_tramite_id_hidden').value = id;
    document.getElementById('tipo_tramite_id').value = id;
    document.getElementById('label-tipo-tramite-form').textContent = nombre;
    document.getElementById('titulo-tramite-paso2').textContent = nombre;

    const tramite = requisitosPorTramite[id];
    if (tramite) {
        const lista = document.getElementById('lista-req-recordatorio');
        let html = tramite.documentos.map(d => `<div><i class="bi bi-check2-circle me-2 text-success"></i>${labelsDocumentos[d] || d}</div>`).join('');
        if (tramite.nota) {
            html += `<div class="mt-1 text-warning"><i class="bi bi-exclamation-triangle me-2"></i>${tramite.nota}</div>`;
        }
        html += `<div class="mt-1 text-muted" style="font-size:0.82rem;"><i class="bi bi-info-circle me-2"></i>Todos los requisitos en <strong>copia</strong>. Si es tercero: <strong>carta poder</strong>.</div>`;
        lista.innerHTML = html;
        actualizarRequisitos(id);
    }

    document.getElementById('paso1-seleccion').style.display = 'none';
    document.getElementById('paso2-formulario').style.display = 'block';
    document.getElementById('tramite').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function volverSeleccion() {
    document.getElementById('paso2-formulario').style.display = 'none';
    document.getElementById('paso1-seleccion').style.display = 'block';
    document.getElementById('tramite').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function mostrarCamposTA(indice, tipoId) {
    var panel = document.getElementById(indice + '_campos');
    var hidden = document.getElementById(indice + '_tipo_tramite_id');
    if (tipoId) {
        panel.style.display = 'block';
        hidden.value = tipoId;
    } else {
        panel.style.display = 'none';
        hidden.value = '';
    }
}

function actualizarRequisitos(tramiteId) {
    const seccionDocumentos = document.getElementById('seccion-documentos');
    const listaRequisitos = document.getElementById('lista-requisitos');

    resetearCampos();

    if (!tramiteId || !requisitosPorTramite[tramiteId]) {
        seccionDocumentos.style.display = 'none';
        return;
    }

    const tramite = requisitosPorTramite[tramiteId];
    seccionDocumentos.style.display = 'block';
    document.getElementById('titulo-tramite-seleccionado').textContent = tramite.titulo;

    listaRequisitos.innerHTML = tramite.documentos.map(doc => `
        <li class="list-group-item d-flex align-items-center">
            <i class="bi bi-check-circle text-success me-2"></i>
            ${labelsDocumentos[doc] || doc}
        </li>`).join('');

    tramite.documentos.forEach(doc => {
        const campo = document.getElementById(`grupo-${doc}`);
        if (campo) {
            campo.style.display = 'block';
        }
    });
}

function resetearCampos() {
    document.querySelectorAll('[id^="grupo-"]').forEach(grupo => {
        grupo.style.display = 'none';
        const input = grupo.querySelector('input');
        if (input) input.removeAttribute('required');
    });
    const seccionDocumentos = document.getElementById('seccion-documentos');
    if (seccionDocumentos) seccionDocumentos.style.display = 'none';
}

// ==========================================
// BUSCAR CUENTA CATASTRAL
// ==========================================
let timeoutBusqueda;
document.getElementById("cuenta_catastral")?.addEventListener("input", function(){
    clearTimeout(timeoutBusqueda);
    let cuenta = this.value.trim();
    
    timeoutBusqueda = setTimeout(() => {
        if(cuenta.length < 3) return;
        
        fetch("php/buscar_catastral.php?cuenta=" + encodeURIComponent(cuenta))
        .then(response => response.json())
        .then(data => {
            if(data && data.utm_x && data.utm_y){
                let utmX = parseFloat(data.utm_x);
                let utmY = parseFloat(data.utm_y);
                
                document.getElementById("lat").value = utmX.toFixed(2);
                document.getElementById("lng").value = utmY.toFixed(2);
                
                let latlng = proj4("EPSG:32613","EPSG:4326",[utmX, utmY]);
                let lat = latlng[1];
                let lng = latlng[0];
                let punto = L.latLng(lat, lng);
                
                if (typeof map !== 'undefined') {
                    map.setView(punto, 18);
                    if(typeof marker !== 'undefined' && marker){
                        map.removeLayer(marker);
                    }
                    marker = L.marker(punto).addTo(map);
                }
            }
        })
        .catch(error => console.error("Error:", error));
    }, 600);
});

// ==========================================
// MODAL DETALLE TRÁMITE
// ==========================================
function initDash() {
    document.querySelectorAll('[data-bs-target="#detalleTramite"]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('m_folio').textContent = btn.dataset.folio;
            document.getElementById('m_propietario').textContent = btn.dataset.propietario;
            document.getElementById('m_direccion').textContent = btn.dataset.direccion;
            document.getElementById('m_localidad').textContent = btn.dataset.localidad;
            document.getElementById('m_tramites').textContent = btn.dataset.tramites;
            document.getElementById('m_fecha').textContent = btn.dataset.fecha;
            document.getElementById('m_observaciones').textContent = btn.dataset.observaciones || 'Sin observaciones';

            const estatusSpan = document.getElementById('m_estatus');
            estatusSpan.textContent = btn.dataset.estatus;
            estatusSpan.className = 'badge';
            if (btn.dataset.estatus === 'En revisión') estatusSpan.classList.add('bg-warning','text-dark');
            else if (btn.dataset.estatus === 'Aprobado') estatusSpan.classList.add('bg-success');
            else if (btn.dataset.estatus === 'Rechazado') estatusSpan.classList.add('bg-danger');
            else estatusSpan.classList.add('bg-secondary');

            const docs = {
                ine: document.getElementById('doc_ine'),
                escritura: document.getElementById('doc_escritura'),
                predial: document.getElementById('doc_predial'),
                formato_constancia: document.getElementById('doc_formato_constancia')
            };
            const docsFaltantes = {
                ine: document.getElementById('doc_faltante_ine'),
                escritura: document.getElementById('doc_faltante_escritura'),
                predial: document.getElementById('doc_faltante_predial'),
                formato: document.getElementById('doc_faltante_formato')
            };
            const sinDocumentos = document.getElementById('modal-sin-documentos');
            const seccionFaltantes = document.getElementById('seccion-docs-faltantes');
            const comentarioFaltantes = document.getElementById('comentario-docs-faltantes');
            const textoComentario = document.getElementById('texto-comentario-faltantes');

            Object.values(docs).forEach(doc => { if (doc) doc.style.display = 'none'; });
            Object.values(docsFaltantes).forEach(doc => { if (doc) doc.style.display = 'none'; });
            if (sinDocumentos) sinDocumentos.style.display = 'none';
            if (seccionFaltantes) seccionFaltantes.style.display = 'none';
            if (comentarioFaltantes) comentarioFaltantes.style.display = 'none';

            let hayDocumentos = false;
            let hayFaltantes = false;

            const tramiteId = parseInt(btn.dataset.tramiteId) || 0;
            const docsRequeridos = requisitosPorTramite[tramiteId]?.documentos || ['ine', 'escritura', 'predial'];

            const ineArchivo = btn.dataset.ine || '';
            if (ineArchivo && ineArchivo.trim() !== '' && docs.ine) {
                docs.ine.style.display = 'flex';
                docs.ine.href = `uploads/${ineArchivo}`;
                hayDocumentos = true;
            } else if (docsRequeridos.includes('ine') && docsFaltantes.ine) {
                docsFaltantes.ine.style.display = 'block';
                hayFaltantes = true;
            }

            const escrituraArchivo = btn.dataset.escritura || '';
            if (escrituraArchivo && escrituraArchivo.trim() !== '' && docs.escritura) {
                docs.escritura.style.display = 'flex';
                docs.escritura.href = `uploads/${escrituraArchivo}`;
                hayDocumentos = true;
            } else if (docsRequeridos.includes('escritura') && docsFaltantes.escritura) {
                docsFaltantes.escritura.style.display = 'block';
                hayFaltantes = true;
            }

            const predialArchivo = btn.dataset.predial || '';
            if (predialArchivo && predialArchivo.trim() !== '' && docs.predial) {
                docs.predial.style.display = 'flex';
                docs.predial.href = `uploads/${predialArchivo}`;
                hayDocumentos = true;
            } else if (docsRequeridos.includes('predial') && docsFaltantes.predial) {
                docsFaltantes.predial.style.display = 'block';
                hayFaltantes = true;
            }

            const formatoArchivo = btn.dataset.formatoConstancia || '';
            if (formatoArchivo && formatoArchivo.trim() !== '' && docs.formato_constancia) {
                docs.formato_constancia.style.display = 'flex';
                docs.formato_constancia.href = `uploads/${formatoArchivo}`;
                hayDocumentos = true;
            } else if (docsRequeridos.includes('formato_constancia') && docsFaltantes.formato) {
                docsFaltantes.formato.style.display = 'block';
                hayFaltantes = true;
            }

            if (hayFaltantes && seccionFaltantes) {
                seccionFaltantes.style.display = 'block';
                const comentario = btn.dataset.comentarioSinDoc || '';
                if (comentario.trim() !== '' && comentarioFaltantes && textoComentario) {
                    comentarioFaltantes.style.display = 'block';
                    textoComentario.textContent = comentario;
                }
            }

            if (!hayDocumentos && !hayFaltantes && sinDocumentos) {
                sinDocumentos.style.display = 'block';
            }

            const folio = btn.dataset.folio;
            document.getElementById('btn_imprimir_ficha').href = `ficha.php?folio=${folio}`;
        });
    });
}

function actualizarSemaforos() {
    document.querySelectorAll('[id^="semaforo_"]').forEach(span => {
        const text = span.textContent.trim();
        const diasRestantes = parseInt(text);
        const elementId = span.id;
        if (!text || text === 'N/A' || !Number.isFinite(diasRestantes)) {
            return;
        }
        const row = span.closest('tr');
        semaforo(diasRestantes, elementId, row);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initDash();
        actualizarSemaforos();
    });
} else {
    initDash();
    actualizarSemaforos();
}

function semaforo(diasRestantes, elementId, rowElement = null){
    const element = document.getElementById(elementId);
    if (!element) return;

   // Remove all previous classes
    element.classList.remove('semaforo-verde', 'semaforo-naranja', 'semaforo-rojo');
    if (rowElement) {
        rowElement.classList.remove('semaforo-fila-verde', 'semaforo-fila-naranja', 'semaforo-fila-rojo');
    }
    

    if (!Number.isFinite(diasRestantes)) {
        return;
    }

    if (diasRestantes > 6) {
        element.classList.add('semaforo-verde');
        if (rowElement) rowElement.classList.add('semaforo-fila-verde');
    } else if (diasRestantes > 0) {
        element.classList.add('semaforo-naranja');
        if (rowElement) rowElement.classList.add('semaforo-fila-naranja');
    } else {
        element.classList.add('semaforo-rojo');
        if (rowElement) rowElement.classList.add('semaforo-fila-rojo');
    }
}
</script>

</body>
</html>
