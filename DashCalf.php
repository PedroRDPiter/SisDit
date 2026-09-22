<?php
require "seguridad.php";
require_once "php/funciones_seguridad.php";
?>


<?php


if(!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'Calificador' && $_SESSION['rol'] !== 'Administrador')){
    header("Location: acceso.php");
    exit();
}


require_once "php/db.php";

// ── Cargar la configuración editable de la constancia ──
// Estos valores se reutilizan en el formulario de configuración inferior.
$cfg = [];
$resCfgV = $conn->query("SELECT clave, valor FROM configuracion_sistema");
while ($rowCfgV = $resCfgV->fetch_assoc()) $cfg[$rowCfgV['clave']] = $rowCfgV['valor'];

// ── Consulta principal del tablero ──
// Se obtienen únicamente los trámites que puede revisar el calificador.
// Los filtros se agregan con parámetros enlazados para evitar inyección SQL.
$sql = "SELECT t.*, tt.nombre as tipo_tramite_nombre,
        (SELECT COUNT(*) FROM tramites t2 WHERE t2.folio_numero = t.folio_numero AND t2.folio_anio = t.folio_anio) as grupo_count,
        c.folio_salida_numero, c.folio_salida_anio, c.fecha_salida,
        c.estatus AS calf_estatus,
        slc.folio_numero AS lc_folio_solicitud
        FROM tramites t
        LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
        LEFT JOIN tramites_salida c ON c.tramite_id = t.id
        LEFT JOIN solicitud_lc slc ON slc.tramite_id = t.id
        WHERE t.tipo_tramite_id IN (2, 7)";
$params = [];
$types = "";

// Consulta base con filtros preparados para evitar inyecciones SQL.
/* ===== FILTRO FOLIO ===== */
if (!empty($_GET['folio']) && str_contains($_GET['folio'], '/')) {
    [$folio_numero, $folio_anio] = explode('/', $_GET['folio']);
    $sql .= " AND t.folio_numero = ? AND t.folio_anio = ?";
    $params[] = $folio_numero;
    $params[] = $folio_anio;
    $types .= "ii";
}

/* ===== FILTRO FECHA ===== */
if (!empty($_GET['fecha'])) {
    $sql .= " AND t.fecha_ingreso = ?";
    $params[] = $_GET['fecha'];
    $types .= "s";
}

/* ===== FILTRO TRÁMITE ===== */
if (!empty($_GET['tramite'])) {
    $sql .= " AND tt.nombre LIKE ?";
    $params[] = '%' . $_GET['tramite'] . '%';
    $types .= "s";
}

/* ===== FILTRO ESTATUS ===== */
if (!empty($_GET['estatus'])) {
    $sql .= " AND COALESCE(c.estatus, 'En revisión') = ?";
    $params[] = $_GET['estatus'];
    $types .= "s";
}

/* ===== FILTRO CUENTA CATASTRAL ===== */
if (!empty($_GET['catastral'])) {
    $sql .= " AND t.cuenta_catastral LIKE ?";
    $params[] = '%'.$_GET['catastral'].'%';
    $types .= "s";
}

/* ===== FILTRO NOMBRE ===== */
if (!empty($_GET['nombre'])) {
    $sql .= " AND (t.propietario LIKE ? OR t.solicitante LIKE ?)";
    $params[] = '%'.$_GET['nombre'].'%';
    $params[] = '%'.$_GET['nombre'].'%';
    $types .= "ss";
}

$sql .= " ORDER BY t.created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Sis Dit</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- BOOTSTRAP 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- LEAFLET -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<!-- CSS PROPIO -->
<link rel="stylesheet" href="./css/style.css?v=1">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Bootstrap 5 CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<!-- DataTables Bootstrap 5 JS -->
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script>
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};
</script>
<style>
    /* =====================================================
   DATATABLES - ALINEACIÓN PERFECTA
===================================================== */

/* Para pantallas mayores a 768px (tablet/desktop) */
@media (min-width: 769px) {
    .dataTables_wrapper .dataTables_length {
        float: left;
    }
    .dataTables_wrapper .dataTables_filter {
        float: right;
    }
}

/* Para móvil (menor o igual a 768px) */
@media (max-width: 768px) {
    .seguimiento-header {
        align-items: stretch !important;
        flex-direction: column;
        gap: 14px;
    }

    .filtros-rapidos {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px;
        width: 100%;
    }

    .filtros-rapidos > .btn {
        width: 100%;
        margin: 0 !important;
        border-radius: 4px !important;
        white-space: normal;
    }

    /* Contenedor principal como flexbox */
    .dataTables_wrapper {
        display: flex !important;
        align-items: center !important;  /* <-- ESTO ALINEA VERTICALMENTE */
        justify-content: space-between !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
    }

    /* Selector de registros */
    .dataTables_wrapper .dataTables_length {
        float: none !important;
        width: auto !important;
        order: 1 !important;
    }

    /* Buscador */
    .dataTables_wrapper .dataTables_filter {
        float: none !important;
        width: auto !important;
        order: 2 !important;
    }

    /* Labels - FORZAR MISMA ALTURA */
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        display: flex !important;
        align-items: center !important;  /* <-- ALINEACIÓN VERTICAL */
        margin: 0 !important;
        line-height: 1 !important;  /* <-- MISMA ALTURA DE LÍNEA */
        height: 36px !important;     /* <-- MISMA ALTURA FIJA */
    }

    /* Texto de los labels - MISMA PROPIEDADES */
    .dataTables_wrapper .dataTables_length label span,
    .dataTables_wrapper .dataTables_length label .fw-semibold,
    .dataTables_wrapper .dataTables_filter label {
        font-size: 14px !important;
        line-height: 36px !important;  /* <-- MISMA ALTURA DE LÍNEA */
    }

    /* Selector pequeño */
    .dataTables_wrapper .dataTables_length select {
        width: 65px !important;
        height: 32px !important;
        margin: 0 5px !important;
        padding: 4px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }

    /* Input de búsqueda */
    .dataTables_wrapper .dataTables_filter input {
        width: 150px !important;
        height: 32px !important;
        margin-left: 5px !important;
        padding: 4px 8px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }
}

/* Para móvil muy pequeño */
@media (max-width: 480px) {
    .dataTables_wrapper {
        flex-direction: column !important;
        align-items: stretch !important;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        width: 100% !important;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        width: 100% !important;
        justify-content: space-between !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        width: calc(100% - 60px) !important;
    }
}

/* Estilos para mejorar alineación del modal de constancia */
.modal .form-label { font-size: .9rem; }
.modal .form-control { height: calc(1.9em + .75rem + 2px); }
.modal .card-body .row > [class*="col-"] { display:flex; flex-direction:column; }

/* Ajuste específico: asegurar que los tres inputs principales estén alineados */
@media (min-width: 768px) {
  #modalConstancia .card-body .row.g-3 > .col-md-4 { display:flex; flex-direction:column; }
}
</style>
</head>

<body>
<!-- NAVBAR MÓVIL -->
<nav class="navbar navbar-dark bg-dark d-lg-none">
    <div class="container-fluid">
        <span class="navbar-brand">Sistema Georreferenciado</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuMovil">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>

    <div class="collapse navbar-collapse" id="menuMovil">
        <ul class="navbar-nav p-3">
            <li class="nav-item"><a class="nav-link" href="#inicio"><i class="bi bi-house me-2"></i> Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="#seguimiento"><i class="bi bi-search me-2"></i> Seguimiento</a></li>
            <li class="nav-item"><a class="nav-link" href="#config-constancia"><i class="bi bi-file-earmark-text me-2"></i> Formato Constancia</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php?csrf_token=<?= urlencode($_SESSION['csrf_token']) ?>"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a></li>
        </ul>
    </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar position-fixed d-none d-lg-flex flex-column p-3">
    <h5 class="text-white text-center mb-4"> <i class="bi bi-clipboard-check"></i> Calificador</h5>
    <a class="nav-link text-white" href="#inicio"><i class="bi bi-house me-2"></i> Inicio</a>
    <a class="nav-link text-white" href="#seguimiento"><i class="bi bi-search me-2"></i> Seguimiento</a>
    <a class="nav-link text-white" href="#config-constancia"><i class="bi bi-file-earmark-text me-2"></i> Formato Constancia</a>
    <a class="nav-link text-danger mt-auto" href="logout.php?csrf_token=<?= urlencode($_SESSION['csrf_token']) ?>"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a>
</div>

<!-- CONTENIDO -->
<div class="content">

<!-- ENCABEZADO -->
<section class="hero" id="inicio">
    <h1><i class="bi bi-clipboard-check"></i> Panel de Calificador</h1>
    <p>Bienvenido <?php echo $_SESSION['usuario'] ?? ''; ?>. Revisa, aprueba y gestiona los trámites georreferenciados.</p>
</section>

<!-- ESTADÍSTICAS RÁPIDAS -->
<div class="row g-3 mb-4">
    <?php
    // Obtener estadísticas resumidas para las tarjetas superiores.
    // Los conteos se calculan sobre los trámites de compatibilidad y licencia.
    $total_tramites = $conn->query("SELECT COUNT(*) AS total FROM tramites WHERE tipo_tramite_id IN (2, 7)")->fetch_assoc()['total'];
    $en_revision = $conn->query(
        "SELECT COUNT(*) AS total
         FROM tramites t
         LEFT JOIN tramites_salida ts ON ts.tramite_id = t.id
         WHERE t.tipo_tramite_id IN (2, 7) AND COALESCE(ts.estatus, 'En revisión') = 'En revisión'"
    )->fetch_assoc()['total'];
    $aprobados_hoy = $conn->query(
        "SELECT COUNT(*) AS total
         FROM tramites_salida ts
         INNER JOIN tramites t ON t.id = ts.tramite_id
         WHERE t.tipo_tramite_id IN (2, 7) AND ts.estatus = 'Aprobado' AND DATE(ts.fecha_salida) = CURDATE()"
    )->fetch_assoc()['total'];
    ?>

    <div class="col-md-3">
        <div class="card shadow-sm border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Trámites</h6>
                        <h3 class="mb-0"><?= $total_tramites ?></h3>
                    </div>
                    <i class="bi bi-folder-check text-primary fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">En Revisión</h6>
                        <h3 class="mb-0"><?= $en_revision ?></h3>
                    </div>
                    <i class="bi bi-hourglass-split text-warning fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Aprobados Hoy</h6>
                        <h3 class="mb-0"><?= $aprobados_hoy ?></h3>
                    </div>
                    <i class="bi bi-check-circle text-success fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SEGUIMIENTO: filtros, resultados y acciones disponibles para cada trámite -->
<section id="seguimiento" class="tramite-box mb-4">
<div class="seguimiento-header d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-primary m-0"><i class="bi bi-search"></i> Seguimiento de Trámites</h4>

    <!-- Filtros rápidos -->
    <div class="btn-group filtros-rapidos" role="group">
        <a href="?estatus=En revisión" class="btn btn-sm btn-outline-warning">
            <i class="bi bi-hourglass-split"></i> En Revisión
        </a>
        <a href="?estatus=Aprobado" class="btn btn-sm btn-outline-success">
            <i class="bi bi-check-circle"></i> Aprobados
        </a>
        <a href="?estatus=Rechazado" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-x-circle"></i> Rechazados
        </a>
        <a href="DashCalf.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Todos
        </a>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Folio</label>
        <div class="input-group">
            <form method="GET" class="input-group mb-3">
                <input type="text" name="folio" class="form-control" placeholder="001/2026" required>
                <button class="btn btn-primary" type="submit">Buscar</button>
            </form>
        </div>
    </div>
</div>




<form method="GET" class="row g-3 mb-4">

    <!-- FECHA -->
    <div class="col-md-3">
        <label class="form-label">Fecha de ingreso</label>
        <input type="date" name="fecha" class="form-control"
               value="<?= $_GET['fecha'] ?? '' ?>">
    </div>

    <!-- TRÁMITE -->
    <div class="col-md-3">
        <label class="form-label">Trámite</label>
        <select name="tramite" class="form-select">
            <option value="">Todos</option>
            <?php
            $filtro_tipos = $conn->query("SELECT nombre FROM tipos_tramite WHERE activo = 1 ORDER BY nombre");
            while ($ft = $filtro_tipos->fetch_assoc()):
            ?>
            <option value="<?= htmlspecialchars($ft['nombre']) ?>" <?= ($_GET['tramite'] ?? '') === $ft['nombre'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($ft['nombre']) ?>
            </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- ESTATUS -->
    <div class="col-md-3">
        <label class="form-label">Estatus</label>
        <select name="estatus" class="form-select">
            <option value="">Todos</option>
            <option value="En revisión" <?= ($_GET['estatus'] ?? '') === 'En revisión' ? 'selected' : '' ?>>
                En revisión
            </option>
            <option value="Aprobado" <?= ($_GET['estatus'] ?? '') === 'Aprobado' ? 'selected' : '' ?>>
                Aprobado
            </option>
            <option value="Rechazado" <?= ($_GET['estatus'] ?? '') === 'Rechazado' ? 'selected' : '' ?>>
                Rechazado
            </option>
        </select>
    </div>

    <!-- BOTONES -->
    <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-funnel"></i> Filtrar
        </button>
        <a href="DashCalf.php" class="btn btn-outline-secondary">
            Limpiar
        </a>
    </div>

</form>


<div  class="table-responsive">
<table id="tablaTramites" class="table table-bordered">
<thead class="table-secondary text-center">
<tr>
<th>Folio de ingreso</th>
<th>Folio de salida</th>
<th>Solicitante</th>
<th>Trámite</th>
<th>Fecha de ingreso</th>
<th>Fecha de salida</th>
<th>Estatus</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
<?php if ($resultado && $resultado->num_rows > 0): ?>
    <?php while ($t = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?= $t['folio_numero'] ?>/<?= $t['folio_anio'] ?></td>
        <td>
            <?php if ($t['folio_salida_numero'] !== null): ?>
                <?= str_pad($t['folio_salida_numero'], 3, '0', STR_PAD_LEFT) ?>/<?= $t['folio_salida_anio'] ?>
            <?php else: ?>
                <span class="text-muted">—</span>
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($t['propietario']) ?></td>
        <td><?= htmlspecialchars($t['tipo_tramite_nombre'] ?? 'Sin tipo') ?></td>
        <td><?= date('d/m/Y', strtotime($t['fecha_ingreso'])) ?></td>
        <td>
            <?= $t['fecha_salida'] ? date('d/m/Y', strtotime($t['fecha_salida'])) : '<span class="text-muted">—</span>' ?>
        </td>
        <td class="text-center">
            <?php
                $calfEstatus = $t['calf_estatus'] ?? 'Pendiente';
                $badge = match ($calfEstatus) {
                    'Aprobado' => 'bg-success',
                    'Rechazado' => 'bg-danger',
                    default => 'bg-warning text-dark'
                };
            ?>
            <span class="badge <?= $badge ?>">
                <?= htmlspecialchars($calfEstatus) ?>
            </span>
        </td>
        <td class="text-center">
            <div class="btn-group" role="group">
                <?php if (in_array($t['estatus'], ['Aprobado por Verificador', 'Aprobado']) && $t['tipo_tramite_id'] == 1): ?>
                <button
                    class="btn btn-sm btn-success btn-generar-constancia"

                    data-id="<?= (int)$t['id'] ?>"
                    data-folio="<?= $t['folio_numero'].'/'.$t['folio_anio'] ?>"
                    data-propietario="<?= htmlspecialchars($t['propietario']) ?>"
                    data-direccion="<?= htmlspecialchars($t['direccion']) ?>"
                    data-colonia="<?= htmlspecialchars($t['colonia'] ?? '') ?>"
                    data-localidad="<?= htmlspecialchars($t['localidad']) ?>"
                    data-numero-asignado="<?= htmlspecialchars($t['numero_asignado'] ?? '') ?>"
                    data-tipo-asignacion="<?= htmlspecialchars($t['tipo_asignacion'] ?? 'Asignacion') ?>"
                    data-referencia-anterior="<?= htmlspecialchars($t['referencia_anterior'] ?? '') ?>"
                    data-entre-calle1="<?= htmlspecialchars($t['entre_calle1'] ?? '') ?>"
                    data-entre-calle2="<?= htmlspecialchars($t['entre_calle2'] ?? '') ?>"
                    data-manzana="<?= htmlspecialchars($t['manzana'] ?? '') ?>"
                    data-lote="<?= htmlspecialchars($t['lote'] ?? '') ?>"
                    data-fecha-constancia="<?= $t['fecha_constancia'] ?? date('Y-m-d') ?>"
                    data-cuenta-catastral="<?= htmlspecialchars($t['cuenta_catastral'] ?? '') ?>"
                    data-superficie="<?= htmlspecialchars($t['superficie'] ?? '') ?>"
                     data-croquis="<?= htmlspecialchars(isset($t['croquis_archivo']) && !empty($t['croquis_archivo']) ? (strpos($t['croquis_archivo'], '.') === 0 ? $t['croquis_archivo'] : 'uploads/' . $t['croquis_archivo']) : '') ?>"
                     data-cantidad="<?= (int)($t['cantidad'] ?? 1) ?>"
                     title="Generar Constancia de Numero Oficial"
                 >
                    <i class="bi bi-file-earmark-check"></i> Constancia
                </button>
                <?php endif; ?>

                <?php
                $tipoCalificable = in_array((int)$t['tipo_tramite_id'], [2, 7], true);
                $tieneSolicitudLc = (int)$t['tipo_tramite_id'] !== 7 || $t['lc_folio_solicitud'] !== null;
                $aprobadoVerificador = in_array($t['estatus'], ['Pendiente por firmar', 'Firmado', 'Entregado y archivado', 'Aprobado por Verificador', 'Aprobado'], true);
                ?>
                <?php if ($tipoCalificable && $tieneSolicitudLc): ?>
                <button
                    class="btn btn-sm btn-outline-warning btn-calificar"
                    data-tramite-id="<?= (int)$t['id'] ?>"
                    data-tipo-tramite-id="<?= (int)$t['tipo_tramite_id'] ?>"
                    data-tipo-tramite="<?= htmlspecialchars($t['tipo_tramite_nombre'] ?? '') ?>"
                    data-verificador-aprobado="<?= $aprobadoVerificador ? '1' : '0' ?>"
                    data-folio="<?= $t['folio_numero'] ?>/<?= $t['folio_anio'] ?>"
                    data-propietario="<?= htmlspecialchars($t['propietario']) ?>"
                    data-direccion="<?= htmlspecialchars($t['direccion'] ?? '') ?>"
                    data-colonia="<?= htmlspecialchars($t['colonia'] ?? '') ?>"
                    data-localidad="<?= htmlspecialchars($t['localidad'] ?? '') ?>"
                    data-calle="<?= htmlspecialchars($t['calle'] ?? '') ?>"
                    data-numero="<?= htmlspecialchars($t['numero'] ?? '') ?>"
                    data-manzana="<?= htmlspecialchars($t['manzana'] ?? '') ?>"
                    data-lote="<?= htmlspecialchars($t['lote'] ?? '') ?>"
                    data-cuenta-catastral="<?= htmlspecialchars($t['cuenta_catastral'] ?? '') ?>"
                    data-observaciones-verificador="<?= htmlspecialchars($t['observaciones'] ?? '') ?>"
                    data-verificador="<?= htmlspecialchars($t['verificador_nombre'] ?? '') ?>"
                    title="Revisar y calificar <?= htmlspecialchars($t['tipo_tramite_nombre'] ?? 'trámite') ?>"
                >
                    <i class="bi bi-clipboard-check"></i> <?= $aprobadoVerificador ? 'Calificar' : 'Revisar' ?>
                </button>
                <?php elseif ((int)$t['tipo_tramite_id'] === 7 && $t['lc_folio_solicitud'] === null): ?>
                <button
                    class="btn btn-sm btn-outline-secondary btn-ver-lc-legacy"
                    data-bs-toggle="modal"
                    data-bs-target="#modalVerLC"
                    data-folio="<?= $t['folio_numero'] ?>/<?= $t['folio_anio'] ?>"
                    data-propietario="<?= htmlspecialchars($t['propietario']) ?>"
                    data-direccion="<?= htmlspecialchars($t['direccion'] ?? '') ?>"
                    data-colonia="<?= htmlspecialchars($t['colonia'] ?? '') ?>"
                    data-localidad="<?= htmlspecialchars($t['localidad'] ?? '') ?>"
                    data-telefono="<?= htmlspecialchars($t['telefono'] ?? '') ?>"
                    data-correo="<?= htmlspecialchars($t['correo'] ?? '') ?>"
                    data-tramites="<?= htmlspecialchars($t['tipo_tramite_nombre'] ?? 'Sin tipo') ?>"
                    data-fecha="<?= date('d/m/Y', strtotime($t['fecha_ingreso'])) ?>"
                    data-estatus="<?= htmlspecialchars($t['estatus'] ?? '') ?>"
                    data-observaciones="<?= htmlspecialchars($t['observaciones'] ?? '') ?>"
                    data-calle="<?= htmlspecialchars($t['calle'] ?? '') ?>"
                    data-numero="<?= htmlspecialchars($t['numero'] ?? '') ?>"
                    data-manzana="<?= htmlspecialchars($t['manzana'] ?? '') ?>"
                    data-lote="<?= htmlspecialchars($t['lote'] ?? '') ?>"
                    data-cuenta-catastral="<?= htmlspecialchars($t['cuenta_catastral'] ?? '') ?>"
                    data-entre-calle1="<?= htmlspecialchars($t['entre_calle1'] ?? '') ?>"
                    data-entre-calle2="<?= htmlspecialchars($t['entre_calle2'] ?? '') ?>"
                    data-referencia-anterior="<?= htmlspecialchars($t['referencia_anterior'] ?? '') ?>"
                    data-ine="<?= $t['ine_archivo'] ?? '' ?>"
                    data-titulo="<?= $t['escrituras_archivo'] ?? $t['titulo_archivo'] ?? '' ?>"
                    data-predial="<?= $t['predial_archivo'] ?? '' ?>"
                    data-foto1="<?= $t['foto1_archivo'] ?? '' ?>"
                    data-foto2="<?= $t['foto2_archivo'] ?? '' ?>"
                    data-lat="<?= htmlspecialchars($t['lat'] ?? '') ?>"
                    data-lng="<?= htmlspecialchars($t['lng'] ?? '') ?>"
                    title="Ver información (trámite anterior a solicitud_lc)"
                >
                    <i class="bi bi-eye"></i> Ver información
                </button>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="8" class="text-center text-muted">
            No se encontraron trámites
        </td>
    </tr>
<?php endif; ?>
</tbody>
</table>
</div>
</section>

<!-- ================================================ -->
<!-- FORMATO DE CONSTANCIA DE NÚMERO OFICIAL         -->
<!-- ================================================ -->
<section id="config-constancia" class="tramite-box mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="m-0" style="color:#7b0f2b;">
      <i class="bi bi-file-earmark-text me-2"></i>Formato de Constancia de Número Oficial
    </h4>
  </div>
  <div class="alert alert-info py-2 mb-4" style="font-size:.85rem;">
    <i class="bi bi-info-circle-fill me-2"></i>
    Aquí puedes editar el <strong>nombre del director</strong> que aparece en la firma y los <strong>reglamentos</strong> que se imprimen al reverso del croquis en la constancia.
  </div>

  <?php $csrf_ccv = generarCSRF(); ?>
  <form id="formConfigConstanciaVer">
    <input type="hidden" name="csrf_token" value="<?= $csrf_ccv ?>">

    <!-- Nombre del Director -->
    <div class="mb-4">
      <label class="fw-bold mb-1" style="color:#7b0f2b;">
        <i class="bi bi-person-badge me-1"></i> Nombre del Director
      </label>
      <input type="text" class="form-control" name="config[director_nombre]"
             value="<?= htmlspecialchars($cfg['director_nombre'] ?? 'DIRECTOR DE PLANEACIÓN Y DESARROLLO URBANO') ?>"
             placeholder="Ej: LIC. URB. JUAN PÉREZ GÓMEZ"
             style="text-transform:uppercase;"
             oninput="this.value=this.value.toUpperCase()">
      <small class="text-muted">Aparece en la sección de firma al pie de la constancia.</small>
    </div>

    <!-- Reglamentos -->
    <div class="mb-2">
      <label class="fw-bold mb-2" style="color:#7b0f2b;">
        <i class="bi bi-list-ol me-1"></i> Reglamentos (al reverso del croquis)
      </label>
      <p class="text-muted small mb-3">Estos textos aparecen enumerados con números romanos (I–IV) debajo del croquis de ubicación.</p>
    </div>

    <?php
    $regsV = [
      1 => $cfg['constancia_reglamento_1'] ?? 'En inmuebles construidos deberán colocarse en el exterior, al frente de la construcción junto al acceso principal;',
      2 => $cfg['constancia_reglamento_2'] ?? 'Los números oficiales en ningún caso deberán ser pintados sobre muros, bloques, columnas y/o en elementos de fácil destrucción;',
      3 => $cfg['constancia_reglamento_3'] ?? 'Deberán además ser de tipo de fuente legible y permitir una fácil lectura a un mínimo de veinte metros;',
      4 => $cfg['constancia_reglamento_4'] ?? 'Las placas de numeración deberán colocarse en una altura mínima de dos metros con cincuenta centímetros a partir del nivel de la banqueta.',
    ];
    $romanosV = ['I','II','III','IV'];
    foreach ($regsV as $i => $texto): ?>
    <div class="mb-3">
      <label class="form-label fw-semibold text-secondary small">
        Inciso <?= $romanosV[$i-1] ?>
      </label>
      <textarea class="form-control" name="config[constancia_reglamento_<?= $i ?>]"
                rows="2" style="font-size:.9rem;"><?= htmlspecialchars($texto) ?></textarea>
    </div>
    <?php endforeach; ?>

    <div class="d-flex align-items-center gap-3 mt-3">
      <button type="button" class="btn btn-success px-4" onclick="guardarConfigConstanciaVer()">
        <i class="bi bi-floppy me-1"></i> Guardar cambios
      </button>
      <span id="msg-config-constancia-ver" class="fw-semibold" style="font-size:.9rem;"></span>
    </div>
  </form>
</section>

</div>



<!-- MODAL: CONSTANCIA DE NUMERO OFICIAL
  Permite completar, guardar e imprimir los datos de la constancia. -->
<div class="modal fade" id="modalConstancia" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content shadow" style="max-height: 80vh; overflow-y: auto;">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="bi bi-file-earmark-check me-2"></i> Constancia de Número Oficial
        </h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formConstancia">
          <?php $csrf_constancia = generarCSRF(); ?>
          <input type="hidden" name="csrf_token" value="<?= $csrf_constancia ?>">
          <input type="hidden" name="folio" id="c_folio_hidden">
          <input type="hidden" name="id" id="c_id">
          <input type="hidden" name="solo_constancia" value="1">

          <!-- Info del tramite -->
          <div class="alert alert-light border mb-3">
            <div class="row">
              <div class="col-md-6">
                <p class="mb-1"><strong>Folio:</strong> <span id="c_folio"></span></p>
                <p class="mb-1"><strong>Propietario:</strong> <span id="c_propietario"></span></p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Dirección:</strong> <span id="c_direccion"></span></p>
                <p class="mb-0"><strong>Localidad:</strong> <span id="c_localidad"></span></p>
              </div>
            </div>
          </div>

          <!-- Buscar número oficial anterior -->
          <div class="card border-info mb-3">
            <div class="card-header bg-info text-white py-2">
              <i class="bi bi-search me-2"></i>Cargar datos de número oficial anterior <small class="fw-normal">(opcional)</small>
            </div>
            <div class="card-body py-3">
              <div class="row g-2 align-items-end">
                <div class="col-md-5">
                  <label class="form-label small fw-semibold mb-1">Folio de salida anterior</label>
                  <input type="text" class="form-control form-control-sm" id="c_buscar_folio" placeholder="Ej: 001/2026">
                </div>
                <div class="col-md-5">
                  <label class="form-label small fw-semibold mb-1">O nombre del propietario</label>
                  <input type="text" class="form-control form-control-sm" id="c_buscar_propietario" placeholder="Ej: JUAN PÉREZ">
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-info btn-sm w-100 text-white" onclick="cargarDatosAnterioresVer()">
                    <i class="bi bi-search me-1"></i>Buscar
                  </button>
                </div>
              </div>
              <div id="c_msg_busqueda" class="small mt-2" style="display:none;"></div>
            </div>
          </div>

          <div class="card border-success">
            <div class="card-header bg-success text-white">
              <i class="bi bi-file-earmark-text me-2"></i>Datos de la Constancia
            </div>
            <div class="card-body">
              <div class="row g-3">
                <!-- Tipo de asignacion -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                    <select class="form-select" name="tipo_asignacion" id="c_tipo_asignacion" required>
                        <option value="ASIGNACION">ASIGNACIÓN</option>
                        <option value="RECTIFICACION">RECTIFICACIÓN</option>
                        <option value="REPOSICION">REPOSICIÓN</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label small mt-1">Dirección<span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-mayusculas" name="direccion_constancia" id="c_direccion_constancia"
                           value="" pattern="[A-Za-z0-9\s\#\.\-]+"
                           title="Solo letras, numeros, espacios, #, puntos y guiones" required>

                    <label class="form-label small mt-1">Colonia <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-mayusculas" name="colonia_constancia" id="c_colonia_constancia"
                           value="" pattern="[A-Za-z0-9\s\#\.\-]+"
                           title="Solo letras, numeros, espacios, #, puntos y guiones" required>
                              <label class="form-label fw-bold">Número Asignado <span class="text-danger">*</span></label>
                  <input type="text" class="form-control input-mayusculas" name="numero_asignado" id="c_numero_asignado"
                         placeholder="Ej: 103" pattern="[A-Za-z0-9\s\-]+"
                         title="Solo letras, numeros, espacios y guiones" required>
                </div>

                <!-- Numero asignado -->
                <div class="col-md-4">

                </div>

                <!-- Referencia anterior -->
                <div class="col-md-4">
                  <label class="form-label fw-bold">Referencia Anterior</label>
                  <input type="text" class="form-control input-mayusculas" name="referencia_anterior" id="c_referencia_anterior"
                         placeholder="Opcional" pattern="[A-Za-z0-9\s\-]*"
                         title="Solo letras, numeros, espacios y guiones">
                  <small class="text-muted">Solo si aplica</small>
                </div>

                <!-- Entre calles -->
                <div class="col-md-6">
                  <label class="form-label fw-bold">Entre Calles <span class="text-danger">*</span></label>
                  <input type="text" class="form-control input-mayusculas" name="entre_calle1" id="c_entre_calle1"
                         placeholder="Ej: NINOS HEROES Y JUAREZ" pattern="[A-Za-z0-9\s\-]+" title="Solo letras, numeros, espacios y guiones" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold invisible">Entre Calles (continuación) <span class="text-danger invisible">*</span></label>
                  <input type="text" class="form-control input-mayusculas" name="entre_calle2" id="c_entre_calle2"
                         placeholder="Ej: INDEPENDENCIA Y HIDALGO" pattern="[A-Za-z0-9\s\-]*" title="Solo letras, numeros, espacios y guiones">
                </div>
                </div>
              </div>

              <div class="row g-3">
                <!-- Cuenta catastral (solo numeros) -->
                <div class="col-md-6">
                  <label class="form-label fw-bold">Cuenta Catastral <span class="text-danger">*</span></label>
                  <input type="text" class="form-control input-solo-numeros" name="cuenta_catastral_constancia" id="c_cuenta_catastral"
                         placeholder="Ej: 70104010022000" pattern="[0-9]+"
                         title="Solo numeros" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Superficie (m²) <span class="text-danger">*</span></label>
                  <input type="text" class="form-control input-solo-numeros" name="superficie_constancia" id="c_superficie_constancia"
                         placeholder="Ej: 250" pattern="[0-9]+" title="Solo numeros">
                </div>

                <!-- Manzana -->
                <div class="col-md-3">
                  <label class="form-label fw-bold">Manzana</label>
                  <input type="text" class="form-control input-mayusculas" name="manzana" id="c_manzana"
                         placeholder="Opcional" pattern="[A-Za-z0-9\s\-]*"
                         title="Solo letras, numeros, espacios y guiones">
                </div>


                <!-- Lote -->
                <div class="col-md-3">
                  <label class="form-label fw-bold">Lote</label>
                  <input type="text" class="form-control input-mayusculas" name="lote" id="c_lote"
                         placeholder="Opcional" pattern="[A-Za-z0-9\s\-]*"
                         title="Solo letras, numeros, espacios y guiones">
                </div>

                 <!-- Fecha de constancia -->
                 <div class="col-md-6">
                   <label class="form-label fw-bold">Fecha de Expedición <span class="text-danger">*</span></label>
                   <input type="date" class="form-control" name="fecha_constancia" id="c_fecha_constancia"
                          value="<?= date('Y-m-d') ?>" required>
                 </div>
                 <!-- Cantidad -->
                 <div class="col-md-6">
                   <label class="form-label fw-bold">Cantidad <span class="text-danger">*</span></label>
                   <input type="number" class="form-control" name="cantidad" id="c_cantidad"
                          min="1" value="1" required>
                 </div>

              </div>
            </div>
          </div>

          <!-- ── CROQUIS ── -->
    <div class="card-header bg-secondary text-white d-flex align-items-center gap-2 mt-3">
        <i class="bi bi-map-fill"></i>
        <span class="fw-bold">Croquis del Predio</span>
        <span class="badge bg-warning text-dark ms-1">Requerido para imprimir</span>
    </div>
    <div class="card-body">
        <div id="ver_alerta_croquis" class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3" style="display:none;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>Sin croquis. Selecciona una imagen y guárdala para poder imprimir.</span>
        </div>
        <div id="ver_ok_croquis" class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3" style="display:none;">
            <i class="bi bi-check-circle-fill"></i>
            <span>Croquis cargado correctamente.</span>
        </div>
        <div class="row g-2 align-items-start">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Imagen del croquis (JPG/PNG, máx. 10MB, se redimensiona a 500x800 píxeles)</label>
                <input type="file" class="form-control form-control-sm" id="ver_inp_croquis"
                       accept="image/jpeg,image/png,image/webp"
                       onchange="ver_prevCroquis(this)">
                <div class="text-muted small mt-1">
                    <i class="bi bi-clipboard me-1"></i>También puedes pegar una imagen con Ctrl+V
                </div>
                <button type="button" class="btn btn-sm btn-secondary w-100 mt-2"
                        id="ver_btn_subir" onclick="ver_subirCroquis()" style="display:none;">
                    <i class="bi bi-cloud-upload me-1"></i>Guardar croquis
                </button>
                <div id="ver_msg_croquis" class="small fw-semibold mt-1"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Vista previa:</label>
                <div id="ver_preview_box" style="border:2px dashed #ccc;border-radius:6px;min-height:200px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;overflow:hidden;">
                    <span id="ver_prev_ph" class="text-muted small text-center px-2">
                        <i class="bi bi-image fs-3 d-block mb-1"></i>Vista previa del croquis
                    </span>
                    <img id="ver_prev_img" src="" style="display:none;width:100%;max-height:100px;object-fit:contain;">
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-1"></i> Cerrar
        </button>
        <button type="button" id="btnSoloImprimir" class="btn btn-primary">
          <i class="bi bi-printer me-1"></i> Imprimir
        </button>
        <button type="submit" form="formConstancia" class="btn btn-success">
          <i class="bi bi-save me-1"></i> Guardar
        </button>
      </div>
    </div>
  </div>
</div>
</div>


<!-- MODAL: NOTIFICACIÓN AL CIUDADANO
  Muestra enlaces preparados para WhatsApp y correo electrónico. -->
<div class="modal fade" id="notifModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow border-0">
      <div class="modal-header" style="background:#7b0f2b;color:white;">
        <h5 class="modal-title" id="notif-modal-title">
          <i class="bi bi-bell me-2"></i>Notificar al Ciudadano
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3" id="notif-modal-desc"></p>

        <!-- Preview del mensaje a enviar -->
        <div id="notif-msg-preview" class="alert alert-light border mb-3" style="display:none;">
          <p class="mb-1 fw-bold small text-secondary"><i class="bi bi-chat-quote me-1"></i>Mensaje que se enviará:</p>
          <p id="notif-msg-texto" class="mb-0 small" style="white-space:pre-line;"></p>
        </div>

        <p class="mb-3" style="font-size:.87rem;">
          El mensaje ya está listo. Presiona el botón del canal preferido del ciudadano para enviarlo:
        </p>
        <a id="notif-wa-link" href="#" target="_blank" rel="noopener"
           class="d-flex align-items-center gap-3 p-3 rounded border mb-2 text-decoration-none text-dark"
           style="border-color:#25D366 !important;background:rgba(37,211,102,.06);transition:.2s;">
          <span style="font-size:2rem;">💬</span>
          <div>
            <div class="fw-bold" style="color:#25D366;">Enviar por WhatsApp</div>
            <div class="text-muted notif-sub" style="font-size:.78rem;">Abre WhatsApp con el mensaje ya escrito</div>
          </div>
        </a>
        <a id="notif-gm-link" href="#" target="_blank" rel="noopener"
           class="d-flex align-items-center gap-3 p-3 rounded border mb-2 text-decoration-none text-dark"
           style="border-color:#EA4335 !important;background:rgba(234,67,53,.06);transition:.2s;">
          <span style="font-size:2rem;">📧</span>
          <div>
            <div class="fw-bold" style="color:#EA4335;">Enviar por Correo</div>
            <div class="text-muted notif-sub" style="font-size:.78rem;">Abre tu cliente de correo con el mensaje listo</div>
          </div>
        </a>
        <p class="text-muted mt-3 mb-0" style="font-size:.72rem;">
          <i class="bi bi-info-circle me-1"></i>
          Si el ciudadano no proporcionó correo o teléfono, ese botón estará deshabilitado.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar sin notificar</button>
      </div>
    </div>
  </div>
</div>

<!-- ===================================== -->
<!-- MODAL DE CALIFICACIÓN (Licencia de Construcción)
  El contenido se carga dinámicamente según el trámite seleccionado. -->
<!-- ===================================== -->
<div class="modal fade" id="modalCalificacion" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content shadow">

      <div class="modal-header text-white" style="background:#7b0f2b;">
        <h5 class="modal-title">
          <i class="bi bi-clipboard-check me-2"></i><span id="calf_modal_titulo">Calificación de trámite</span>
        </h5>
        <div class="ms-auto d-flex gap-2 align-items-center">
          <span class="badge bg-light text-dark" id="calf_folio_ingreso"></span>
          <span class="badge bg-warning text-dark" id="calf_folio_salida" style="display:none;"></span>
        </div>
        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="calf_tramite_id">
        <input type="hidden" id="calf_ts_id">
        <input type="hidden" id="calf_tipo_tramite_id">

        <!-- ===== INFO GENERAL (solo lectura, viene de tramites) ===== -->
        <div class="tramite-header d-flex align-items-center pb-2 mb-3">
          <h6 class="mb-0" style="color:#7b0f2b;"><i class="bi bi-person-vcard me-2"></i>Datos del propietario y predio</h6>
        </div>
        <div class="row g-2 mb-3" style="font-size:.88rem;">
          <div class="col-md-6"><strong>Propietario:</strong> <span id="calf_propietario">—</span></div>
          <div class="col-md-6"><strong>Domicilio:</strong> <span id="calf_direccion">—</span></div>
          <div class="col-md-4"><strong>Colonia:</strong> <span id="calf_colonia">—</span></div>
          <div class="col-md-4"><strong>Localidad:</strong> <span id="calf_localidad">—</span></div>
          <div class="col-md-4"><strong>Cuenta catastral:</strong> <span id="calf_cuenta_catastral">—</span></div>
          <div class="col-md-4"><strong>Calle:</strong> <span id="calf_calle">—</span></div>
          <div class="col-md-4"><strong>Número:</strong> <span id="calf_numero">—</span></div>
          <div class="col-md-2"><strong>Manzana:</strong> <span id="calf_manzana">—</span></div>
          <div class="col-md-2"><strong>Lote:</strong> <span id="calf_lote">—</span></div>
        </div>
        <div class="alert alert-info mb-3">
          <div class="fw-bold"><i class="bi bi-chat-left-text me-1"></i>Observaciones del verificador</div>
          <div id="calf_observaciones_verificador" class="mt-1">Sin observaciones.</div>
          <small id="calf_verificador" class="d-block mt-2 text-muted"></small>
        </div>
        <div id="calf_pendiente_verificador" class="alert alert-warning d-none">
          El trámite todavía no ha sido aprobado por el verificador. Puedes consultar el expediente, pero no aprobarlo como calificador.
        </div>
        <!-- ===== DATOS DE LA OBRA (solo lectura, viene de solicitud_lc) ===== -->
        <div id="calf_datos_lc_wrap">
        <hr>
        <div class="tramite-header d-flex align-items-center pb-2 mb-3">
          <h6 class="mb-0" style="color:#7b0f2b;"><i class="bi bi-building me-2"></i>Datos de la obra</h6>
        </div>
        <div class="row g-2 mb-3" style="font-size:.88rem;">
          <div class="col-md-4"><strong>Tipo de obra:</strong> <span id="calf_tipo_obra">—</span></div>
          <div class="col-md-8"><strong>Urbanización existente:</strong> <span id="calf_urbanizacion">—</span></div>
          <div class="col-12">
            <strong>Descripción:</strong>
            <p class="mb-0" id="calf_descripcion_obra">—</p>
          </div>
        </div>

        <div class="mb-3">
          <strong style="font-size:.88rem;">Superficies:</strong>
          <div id="calf_superficies" class="mt-1" style="font-size:.88rem;">—</div>
        </div>

        <div class="mb-3">
          <strong style="font-size:.88rem;">Peritos de obra:</strong>
          <div id="calf_peritos" class="mt-1" style="font-size:.88rem;">—</div>
        </div>
        </div>
        <hr>

        <!-- ===== CALIFICACIÓN (editable) ===== -->
        <div class="tramite-header d-flex align-items-center pb-2 mb-3">
          <h6 class="mb-0" style="color:#7b0f2b;"><i class="bi bi-pencil-square me-2"></i>Calificación</h6>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label for="calf_estatus" class="form-label">Estatus</label>
            <select id="calf_estatus" class="form-select">
              <option value="En revisión">En revisión</option>
              <option value="Aprobado">Aprobado</option>
              <option value="Rechazado">Rechazado</option>
            </select>
          </div>
        </div>

        <!-- Reglamento: solo aplica cuando tipo_obra (de solicitud_lc) = "Otro" -->
        <div id="calf_reglamento_wrap" class="row g-3 mb-3" style="display:none;">
          <div class="col-md-6">
            <label for="calf_reglamento_id" class="form-label">Reglamento a usar en la licencia</label>
            <select id="calf_reglamento_id" class="form-select">
              <option value="">Seleccione...</option>
              <!-- Opciones cargadas por JS desde REGLAMENTOS_LC -->
            </select>
          </div>
        </div>

        <!-- Calles / metros lineales: solo si el reglamento elegido es "Obra Pública" -->
        <div id="calf_obra_publica_wrap" class="row g-3 mb-3" style="display:none;">
          <div class="col-md-8">
            <label for="calf_calles" class="form-label">Calles involucradas</label>
            <input type="text" id="calf_calles" class="form-control" placeholder="Ej. Calle Aguascalientes y Calle Chihuahua">
          </div>
          <div class="col-md-4">
            <label for="calf_metros_lineales" class="form-label">Metros lineales totales</label>
            <input type="number" id="calf_metros_lineales" class="form-control" step="0.01" min="0" placeholder="Ej. 2644.15">
          </div>
        </div>

        <!-- Vigencia/expiración: solo aplica si el estatus es Aprobado -->
        <div id="calf_vigencia_wrap" class="row g-3 mb-3" style="display:none;">
          <div class="col-md-3">
            <label for="calf_vigencia" class="form-label">Vigencia (fecha de inicio)</label>
            <input type="date" id="calf_vigencia" class="form-control">
          </div>
          <div class="col-md-3">
            <label for="calf_periodo_cantidad" class="form-label">Periodo</label>
            <input type="number" id="calf_periodo_cantidad" class="form-control" min="1" value="1">
          </div>
          <div class="col-md-3">
            <label class="form-label">Unidad</label>
            <select id="calf_periodo_unidad" class="form-select">
              <option value="meses" selected>Mes(es)</option>
              <option value="anios">Año(s)</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="calf_expiracion" class="form-label">Expiración</label>
            <input type="date" id="calf_expiracion" class="form-control" readonly>
          </div>
        </div>

        <div class="mb-2">
          <label for="calf_comentarios" class="form-label">Observaciones del calificador</label>
          <textarea id="calf_comentarios" class="form-control" rows="3" placeholder="Dictamen u observaciones que aparecerán en el documento"></textarea>
        </div>

        <div id="calf_alerta" class="alert d-none mt-3" role="alert"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cerrar
        </button>
        <button type="button" id="btnImprimirLicencia" class="btn btn-outline-primary" style="display:none;">
          <i class="bi bi-printer me-1"></i><span id="calf_texto_imprimir">Imprimir documento</span>
        </button>
        <button type="button" id="btnGuardarCalf" class="btn btn-success">
          <i class="bi bi-save me-1"></i>Guardar calificación
        </button>
      </div>

    </div>
  </div>
</div>

<!--
============================================================
     BLOQUE De Modal

     modal de solo lectura para trámites de Licencia
     de Construcción anteriores a la tabla solicitud_lc. Sin
     formulario, sin cambio de estatus, sin subida de archivos —
     únicamente muestra lo que ya existe en la base para consulta.
     Independiente del resto del código existente en este archivo.
============================================================ -->

<div class="modal fade" id="modalVerLC" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content shadow">

      <div class="modal-header text-white" style="background:#6c757d;">
        <h5 class="modal-title">
          <i class="bi bi-eye me-2"></i>Información del trámite
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="alert alert-secondary py-2" style="font-size:.85rem;">
          <i class="bi bi-info-circle me-1"></i>
          Este trámite es anterior a la implementación de las solicitudes en digital, así que no tiene los
          datos detallados de obra que sí capturan los trámites nuevos. Aquí solo se muestra la información
          general que ya existía en el sistema.
        </div>

        <!-- INFO GENERAL -->
        <div class="row mb-3">
          <div class="col-md-6">
            <p><strong>Folio:</strong> <span id="verlc_folio"></span></p>
            <p><strong>Propietario:</strong> <span id="verlc_propietario"></span></p>
            <p><strong>Dirección:</strong> <span id="verlc_direccion"></span></p>
            <p><strong>Colonia:</strong> <span id="verlc_colonia"></span></p>
            <p><strong>Localidad:</strong> <span id="verlc_localidad"></span></p>
            <p><strong>Teléfono:</strong> <span id="verlc_telefono"></span></p>
            <p><strong>Correo:</strong> <span id="verlc_correo"></span></p>
          </div>
          <div class="col-md-6">
            <p><strong>Trámite:</strong> <span id="verlc_tramites"></span></p>
            <p><strong>Fecha ingreso:</strong> <span id="verlc_fecha"></span></p>
            <p><strong>Estatus:</strong> <span id="verlc_estatus"></span></p>
            <p><strong>Observaciones:</strong> <span id="verlc_observaciones"></span></p>
          </div>
        </div>
        <hr>

        <!-- UBICACIÓN DEL PREDIO -->
        <div class="mb-3">
          <div class="tramite-header d-flex align-items-center pb-2 mb-2">
            <h6 class="mb-0" style="color:#7b0f2b;"><i class="bi bi-geo-alt me-2"></i>Ubicación del predio</h6>
          </div>
          <div class="row mb-2">
            <div class="col-md-3"><strong>Calle:</strong> <span id="verlc_calle">—</span></div>
            <div class="col-md-3"><strong>Número:</strong> <span id="verlc_numero">—</span></div>
            <div class="col-md-3"><strong>Manzana:</strong> <span id="verlc_manzana">—</span></div>
            <div class="col-md-3"><strong>Lote:</strong> <span id="verlc_lote">—</span></div>
          </div>
          <div class="row mb-2">
            <div class="col-md-6"><strong>Entre calle:</strong> <span id="verlc_entre_calles">—</span></div>
            <div class="col-md-6"><strong>Cuenta catastral:</strong> <span id="verlc_cuenta_catastral">—</span></div>
          </div>
          <div class="mb-2">
            <strong>Referencia anterior:</strong> <span id="verlc_referencia_anterior">—</span>
          </div>
          <div id="verlc_mapa_sin_coords" class="alert alert-secondary py-2 mb-0" style="display:none;">
            <i class="bi bi-info-circle me-1"></i>Este trámite no tiene coordenadas registradas.
          </div>
          <div id="verlc_mapa" style="height:250px; border-radius:6px; display:none;"></div>
        </div>
        <hr>

        <!-- DOCUMENTOS CARGADOS -->
        <div>
          <h6 class="text-primary">Documentos Cargados</h6>
          <div class="list-group mb-2">
            <a id="verlc_doc_ine" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank" style="display:none;">
              <span><i class="bi bi-file-earmark-person me-2"></i> INE</span>
              <span class="badge bg-primary">Ver</span>
            </a>
            <a id="verlc_doc_titulo" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank" style="display:none;">
              <span><i class="bi bi-file-earmark-text me-2"></i> Título / Escritura</span>
              <span class="badge bg-primary">Ver</span>
            </a>
            <a id="verlc_doc_predial" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank" style="display:none;">
              <span><i class="bi bi-file-earmark-text me-2"></i> Predial</span>
              <span class="badge bg-primary">Ver</span>
            </a>
            <a id="verlc_doc_foto1" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank" style="display:none;">
              <span><i class="bi bi-image me-2"></i> Fotografía 1</span>
              <span class="badge bg-primary">Ver</span>
            </a>
            <a id="verlc_doc_foto2" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank" style="display:none;">
              <span><i class="bi bi-image me-2"></i> Fotografía 2</span>
              <span class="badge bg-primary">Ver</span>
            </a>
          </div>
          <div id="verlc_sin_documentos" class="text-muted mb-0" style="display:none;">
            <i class="bi bi-info-circle me-1"></i> No hay documentos cargados para este trámite.
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Cerrar
        </button>
      </div>

    </div>
  </div>
</div>

<script>
// ── Consulta de trámites históricos de licencia ──
// Este modal es de solo lectura y conserva compatibilidad con registros antiguos.

let verlcMapaInstance = null;

document.getElementById('modalVerLC').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    if (!btn) return;
    const d = btn.dataset;

    document.getElementById('verlc_folio').textContent        = d.folio || '—';
    document.getElementById('verlc_propietario').textContent  = d.propietario || '—';
    document.getElementById('verlc_direccion').textContent    = d.direccion || '—';
    document.getElementById('verlc_colonia').textContent      = d.colonia || '—';
    document.getElementById('verlc_localidad').textContent    = d.localidad || '—';
    document.getElementById('verlc_telefono').textContent     = d.telefono || '—';
    document.getElementById('verlc_correo').textContent       = d.correo || '—';
    document.getElementById('verlc_tramites').textContent     = d.tramites || '—';
    document.getElementById('verlc_fecha').textContent        = d.fecha || '—';
    document.getElementById('verlc_estatus').textContent      = d.estatus || '—';
    document.getElementById('verlc_observaciones').textContent = d.observaciones || '—';

    document.getElementById('verlc_calle').textContent          = d.calle || '—';
    document.getElementById('verlc_numero').textContent         = d.numero || '—';
    document.getElementById('verlc_manzana').textContent        = d.manzana || '—';
    document.getElementById('verlc_lote').textContent           = d.lote || '—';
    document.getElementById('verlc_cuenta_catastral').textContent = d.cuentaCatastral || '—';
    document.getElementById('verlc_referencia_anterior').textContent = d.referenciaAnterior || '—';

    const entreCalles = [d.entreCalle1, d.entreCalle2].filter(Boolean).join(' y ');
    document.getElementById('verlc_entre_calles').textContent = entreCalles || '—';

    // Documentos: solo se muestra el link si hay archivo capturado
    const docs = [
        ['verlc_doc_ine', d.ine],
        ['verlc_doc_titulo', d.titulo],
        ['verlc_doc_predial', d.predial],
        ['verlc_doc_foto1', d.foto1],
        ['verlc_doc_foto2', d.foto2],
    ];
    let hayDocs = false;
    docs.forEach(([id, archivo]) => {
        const el = document.getElementById(id);
        if (archivo) {
            el.href = archivo.startsWith('uploads/') ? archivo : 'uploads/' + archivo;
            el.style.display = 'flex';
            hayDocs = true;
        } else {
            el.style.display = 'none';
        }
    });
    document.getElementById('verlc_sin_documentos').style.display = hayDocs ? 'none' : 'block';

    // Mapa (solo lectura) si hay coordenadas
    const lat = parseFloat(d.lat);
    const lng = parseFloat(d.lng);
    const mapaDiv = document.getElementById('verlc_mapa');
    const sinCoordsDiv = document.getElementById('verlc_mapa_sin_coords');

    if (verlcMapaInstance) {
        verlcMapaInstance.remove();
        verlcMapaInstance = null;
    }

    if (!isNaN(lat) && !isNaN(lng) && (lat !== 0 || lng !== 0)) {
        mapaDiv.style.display = 'block';
        sinCoordsDiv.style.display = 'none';
        setTimeout(() => {
            verlcMapaInstance = L.map('verlc_mapa', { zoomControl: true }).setView([lat, lng], 18);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(verlcMapaInstance);
            L.marker([lat, lng]).addTo(verlcMapaInstance);
            verlcMapaInstance.invalidateSize();
        }, 200);
    } else {
        mapaDiv.style.display = 'none';
        sinCoordsDiv.style.display = 'block';
    }
});

document.getElementById('modalVerLC').addEventListener('hidden.bs.modal', function () {
    if (verlcMapaInstance) {
        verlcMapaInstance.remove();
        verlcMapaInstance = null;
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/verificar.js?v=<?= filemtime(__DIR__ . '/js/verificar.js') ?>"></script>

<script>
// Mostrar alertas con SweetAlert2 - SOLO para errores del sistema, NO para validación de campos.
document.addEventListener('DOMContentLoaded', () => {
  <?php if(isset($_GET['error']) && $_GET['error'] !== 'sin_numero_asignado'): ?>
    let errorMsg = '';
    let errorTitle = 'Error';

    switch('<?= $_GET['error'] ?>') {
    case 'csrf':
        errorMsg = 'Error de seguridad. Por favor intenta de nuevo.';
        errorTitle = 'Error de Seguridad';
        break;
    case 'tramite_no_encontrado':
        errorMsg = 'No se encontró el trámite especificado.';
        errorTitle = 'Trámite No Encontrado';
        break;
    case 'archivo_invalido':
        errorMsg = 'El archivo no es válido o excede el tamaño permitido.';
        errorTitle = 'Archivo No Válido';
        break;
    default:
        errorMsg = '<?= htmlspecialchars($_GET['error'] ?? '') ?>';
    }

    Swal.fire({
      icon: 'error',
      title: errorTitle,
      text: errorMsg,
      confirmButtonText: 'Entendido',
      confirmButtonColor: '#2f7d6d'
    });
  <?php endif; ?>

  <?php if(isset($_GET['success'])): ?>
    let successMsg = '';
    let successTitle = '¡Éxito!';

    switch('<?= $_GET['success'] ?>') {
      case 'tramite_actualizado':
        successMsg = 'El trámite ha sido actualizado correctamente.';
        successTitle = '¡Trámite Actualizado!';
        break;
      case 'fotografias_subidas':
        successMsg = 'Las fotografías han sido subidas correctamente.';
        successTitle = '¡Fotografías Guardadas!';
        break;
      default:
        successMsg = 'Operación completada correctamente.';
    }

    Swal.fire({
      icon: 'success',
      title: successTitle,
      text: successMsg,
      confirmButtonText: 'Continuar',
      confirmButtonColor: '#2f7d6d',
      timer: 3000,
      timerProgressBar: true
    });
  <?php endif; ?>
});

// Guardar configuración de constancia mediante una petición AJAX.
function guardarConfigConstanciaVer() {
  var form = document.getElementById('formConfigConstanciaVer');
  var msg  = document.getElementById('msg-config-constancia-ver');
  var btn  = form.querySelector('button[onclick]');
  var fd   = new FormData(form);

  msg.textContent = 'Guardando...';
  msg.style.color = '#666';
  btn.disabled = true;

  fetch('php/actualizar_config_constancia.php', {
    method: 'POST',
    body: fd,
    credentials: 'same-origin'
  })
  .then(function(r){ return r.json(); })
  .then(function(data){
    btn.disabled = false;
    if (data.success) {
      msg.textContent = '✅ ' + data.message;
      msg.style.color = '#198754';
    } else {
      msg.textContent = '❌ ' + data.message;
      msg.style.color = '#dc3545';
    }
    setTimeout(function(){ msg.textContent = ''; }, 4000);
  })
  .catch(function(){
    btn.disabled = false;
    msg.textContent = '❌ Error de conexión.';
    msg.style.color = '#dc3545';
  });
}

    // Abrir el modal y precargar los datos de la fila seleccionada.
    function abrirModalConstancia(btn) {
    const folio = btn.getAttribute('data-folio');
    const tramiteId = btn.getAttribute('data-id') || '';
    const modalConstancia = new bootstrap.Modal(document.getElementById('modalConstancia'));

    // ID del subtrámite específico (cada fila tiene su propio folio de salida y croquis)
    document.getElementById('c_id').value = tramiteId;

    // Poblar los datos en el modal de constancia
    document.getElementById('c_folio').textContent = folio;
    document.getElementById('c_propietario').textContent = btn.getAttribute('data-propietario') || '';
    document.getElementById('c_direccion').textContent = btn.getAttribute('data-direccion') || '';
    document.getElementById('c_colonia_constancia').value = btn.getAttribute('data-colonia') || '';
    document.getElementById('c_localidad').textContent = btn.getAttribute('data-localidad') || '';
    document.getElementById('c_folio_hidden').value = folio;
    document.getElementById('c_numero_asignado').value = btn.getAttribute('data-numero-asignado') || '';

    // Seleccionar la opción correcta en el select
    const tipoAsignacion = btn.getAttribute('data-tipo-asignacion') || 'ASIGNACION';
    const selectTipo = document.getElementById('c_tipo_asignacion');
    if (selectTipo) {
        const tipoUpper = tipoAsignacion.toUpperCase();
        for (let i = 0; i < selectTipo.options.length; i++) {
            if (selectTipo.options[i].value === tipoUpper) {
                selectTipo.selectedIndex = i;
                break;
            }
        }
    }

    document.getElementById('c_referencia_anterior').value = btn.getAttribute('data-referencia-anterior') || '';
    document.getElementById('c_entre_calle1').value = btn.getAttribute('data-entre-calle1') || '';
    document.getElementById('c_entre_calle2').value = btn.getAttribute('data-entre-calle2') || '';
    document.getElementById('c_cuenta_catastral').value = btn.getAttribute('data-cuenta-catastral') || '';
    document.getElementById('c_superficie_constancia').value = btn.getAttribute('data-superficie') || '';
    document.getElementById('c_direccion_constancia').value = btn.getAttribute('data-direccion') || '';
    document.getElementById('c_manzana').value = btn.getAttribute('data-manzana') || '';
    document.getElementById('c_lote').value = btn.getAttribute('data-lote') || '';
    document.getElementById('c_fecha_constancia').value = btn.getAttribute('data-fecha-constancia') || new Date().toISOString().split('T')[0];

    // Mostrar croquis si existe
    const croquis = btn.getAttribute('data-croquis');
    const alertaCroquis = document.getElementById('ver_alerta_croquis');
    const okCroquis = document.getElementById('ver_ok_croquis');
    const prevImg = document.getElementById('ver_prev_img');
    const prevPh = document.getElementById('ver_prev_ph');

    if (croquis && croquis.trim()) {
        alertaCroquis.style.display = 'none';
        okCroquis.style.display = 'flex';
        prevImg.src = croquis;
        prevImg.style.display = 'block';
        prevPh.style.display = 'none';
    } else {
        alertaCroquis.style.display = 'flex';
        okCroquis.style.display = 'none';
        prevImg.style.display = 'none';
        prevPh.style.display = 'flex';
    }

    modalConstancia.show();
}

// Permitir abrir el modal de constancia desde el modal de detalle (botón "Llenar / imprimir Constancia")
document.addEventListener('DOMContentLoaded', function() {
    const btnDesdeDetalle = document.getElementById('btn_abrir_constancia_desde_detalle');
    if (btnDesdeDetalle) {
        btnDesdeDetalle.addEventListener('click', function(e) {
            // Tomar datos visibles en el modal de detalle y pasarlos al modal de constancia
            const folio = document.getElementById('m_folio').textContent.trim();
            const propietario = document.getElementById('m_propietario').textContent.trim();
            const direccion = document.getElementById('m_direccion').textContent.trim();
            const localidad = document.getElementById('m_localidad').textContent.trim();
            const tipoTramiteId = document.getElementById('m_tipo_tramite_id') ? document.getElementById('m_tipo_tramite_id').value : '';

            // Poblar campos del modal de constancia
            const subId = document.getElementById('m_tramite_id') ? document.getElementById('m_tramite_id').value : '';
            document.getElementById('c_id').value = subId;
            document.getElementById('c_folio').textContent = folio;
            document.getElementById('c_propietario').textContent = propietario;
            document.getElementById('c_direccion').textContent = direccion;
            document.getElementById('c_colonia_constancia').value = (document.getElementById('m_colonia') ? document.getElementById('m_colonia').textContent.trim() : '');
            document.getElementById('c_localidad').textContent = localidad;
            document.getElementById('c_folio_hidden').value = folio;
            document.getElementById('c_direccion_constancia').value = direccion;
            // Si por alguna razón el modal detalle no tiene colonia, intentar leer data-colonia del botón original (no es habitual)
            const posibleCol = (event && event.relatedTarget) ? (event.relatedTarget.getAttribute('data-colonia') || '') : '';
            if (!document.getElementById('c_colonia_constancia').value && posibleCol) document.getElementById('c_colonia_constancia').value = posibleCol;

            // Mostrar modal
            const modalConstancia = new bootstrap.Modal(document.getElementById('modalConstancia'));
            modalConstancia.show();
        });
    }
});

// Cargar datos de número oficial anterior en el modal de constancia.
// También permite reutilizar el croquis asociado al trámite encontrado.
function cargarDatosAnterioresVer() {
  const folio      = document.getElementById('c_buscar_folio').value.trim();
  const propietario = document.getElementById('c_buscar_propietario').value.trim();
  const msg        = document.getElementById('c_msg_busqueda');

  if (!folio && !propietario) {
    msg.style.display = 'block';
    msg.style.color   = '#dc3545';
    msg.textContent   = '⚠️ Escribe un folio de salida o el nombre del propietario.';
    return;
  }

  msg.style.display = 'block';
  msg.style.color   = '#0d6efd';
  msg.textContent   = '🔍 Buscando...';

  let url = 'php/obtener_tramite_anterior.php?incluir_constancia=true';
  if (folio) {
    url += '&folio=' + encodeURIComponent(folio) + '&buscar_por_folio_salida=true';
  } else {
    url += '&propietario=' + encodeURIComponent(propietario) + '&tipo_tramite_id=1';
  }

  fetch(url)
    .then(r => r.json())
    .then(data => {
      if (data.error) {
        msg.style.color = '#dc3545';
        msg.textContent = '❌ ' + data.error;
        return;
      }
      const c = data.tramite.constancia || {};
      const t = data.tramite;

      // Rellenar campos de constancia con los datos anteriores
      if (c.numero_asignado)     document.getElementById('c_numero_asignado').value      = c.numero_asignado;
      if (c.tipo_asignacion) {
        const sel = document.getElementById('c_tipo_asignacion');
        for (let i = 0; i < sel.options.length; i++) {
          if (sel.options[i].value === c.tipo_asignacion.toUpperCase()) { sel.selectedIndex = i; break; }
        }
      }
      if (c.referencia_anterior) document.getElementById('c_referencia_anterior').value  = c.referencia_anterior;
      if (c.entre_calle1)        document.getElementById('c_entre_calle1').value          = c.entre_calle1;
      if (c.entre_calle2)        document.getElementById('c_entre_calle2').value          = c.entre_calle2;
      if (c.manzana)             document.getElementById('c_manzana').value               = c.manzana;
      if (c.lote)                document.getElementById('c_lote').value                  = c.lote;
      if (t.cuenta_catastral)    document.getElementById('c_cuenta_catastral').value      = t.cuenta_catastral;

      // Cargar croquis si existe en el trámite anterior
      const croquis       = (t.archivos && t.archivos.croquis_archivo) ? t.archivos.croquis_archivo : '';
      const alertaCroquis = document.getElementById('ver_alerta_croquis');
      const okCroquis     = document.getElementById('ver_ok_croquis');
      const prevImg       = document.getElementById('ver_prev_img');
      const prevPh        = document.getElementById('ver_prev_ph');
      if (croquis) {
        // Mostrar imagen en vista previa
        alertaCroquis.style.display = 'none';
        okCroquis.style.display     = 'flex';
        prevImg.src                 = croquis;
        prevImg.style.display       = 'block';
        prevPh.style.display        = 'none';

        // Persistir en BD: asignar el croquis al trámite actual
        const folioActual = document.getElementById('c_folio_hidden').value;
        const idActual    = document.getElementById('c_id') ? document.getElementById('c_id').value : '';
        const fd = new FormData();
        if (idActual) fd.append('id_destino', idActual);
        fd.append('folio_destino',   folioActual);
        fd.append('croquis_archivo', croquis);
        fetch('php/asignar_croquis.php', { method: 'POST', body: fd, credentials: 'same-origin' })
          .then(r => r.json())
          .then(res => {
            if (!res.success) console.warn('No se pudo asignar croquis:', res.message);
          })
          .catch(err => console.warn('Error asignando croquis:', err));
      }

      msg.style.color = '#198754';
      const tramites = data.tramites || (data.tramite ? [data.tramite] : []);
      const countText = tramites.length > 1 ? ` (${tramites.length} subtrámites)` : '';
      msg.textContent = '✅ Datos cargados del folio ' + (tramites[0]?.folio_salida || tramites[0]?.folio) + ' — ' + (tramites[0]?.propietario || '') + countText;
    })
    .catch(() => {
      msg.style.color = '#dc3545';
      msg.textContent = '❌ Error de conexión.';
    });
}

// Limpiar campos de búsqueda al abrir el modal
document.getElementById('modalConstancia').addEventListener('show.bs.modal', function () {
  document.getElementById('c_buscar_folio').value       = '';
  document.getElementById('c_buscar_propietario').value = '';
  const msg = document.getElementById('c_msg_busqueda');
  msg.style.display = 'none';
  msg.textContent   = '';
});


document.addEventListener('DOMContentLoaded', function() {

    // Configurar botones de constancia
    const botonesConstancia = document.querySelectorAll('.btn-generar-constancia');
    botonesConstancia.forEach(btn => {
        const nuevoBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(nuevoBtn, btn);

        nuevoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            abrirModalConstancia(this);
        });
    });

    // Configurar botón de imprimir en modal de constancia - SIN ALERTA, usa validación nativa
    const btnImprimirConstancia = document.getElementById('btnSoloImprimir');
    if (btnImprimirConstancia) {
        const nuevoBoton = btnImprimirConstancia.cloneNode(true);
        btnImprimirConstancia.parentNode.replaceChild(nuevoBoton, btnImprimirConstancia);

        nuevoBoton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Obtener el formulario
            const form = document.getElementById('formConstancia');

            // Verificar si el formulario es válido usando la validación nativa del navegador
            if (form.checkValidity()) {
                // Si es válido, redirigir a imprimir (por id del subtrámite)
                const idSub = document.getElementById('c_id').value;
                const folio = document.getElementById('c_folio_hidden').value;
                const url = idSub
                    ? 'constancia_numero.php?id=' + encodeURIComponent(idSub) + '&imprimir=1'
                    : 'constancia_numero.php?folio=' + encodeURIComponent(folio) + '&imprimir=1';
                window.location.href = url;
            } else {
                // Si no es válido, mostrar los mensajes de validación nativos del navegador
                form.reportValidity();
            }
        });
    }
});
</script>

<!--
============================================================
     BLOQUE PARA LICENCIA

     modal de Calificación de Licencia de Construcción.
     Independiente del resto del código existente en este archivo.

============================================================ -->
<script>
// ── Calificación dinámica de trámites ──
// Administra la carga de datos, validaciones, guardado e impresión.
<?php
/*
Lista de reglamentos disponibles para Licencia de Construcción (tipo_tramite_id = 7),
embebida como JS para no tener que hacer una llamada AJAX extra.
requiere_ubicacion_manual ya viene resuelto como columna en la tabla
(no se compara texto de "variante" en JS, para no depender de acentos).
*/

$resReg = $conn->query("SELECT id, titulo, requiere_ubicacion_manual FROM reglamentos WHERE tipo_tramite_id = 7 AND activo = 1 ORDER BY titulo");
$reglamentosLC = [];
while ($rowReg = $resReg->fetch_assoc()) {
    $reglamentosLC[] = [
        'id' => $rowReg['id'],
        'titulo' => $rowReg['titulo'],
        'requiere_ubicacion_manual' => (bool)$rowReg['requiere_ubicacion_manual'],
    ];
}
?>
const REGLAMENTOS_LC = <?= json_encode($reglamentosLC, JSON_UNESCAPED_UNICODE) ?>;

/*
lee datos del botón mas traer solicitud_lc y tramites_salida
Se usa delegación de eventos (un solo listener en "document") en vez de
atarlo a cada botón individualmente: así funciona sin importar si la fila
se vuelve a dibujar o el botón se reemplaza en el DOM más adelante.
*/

document.addEventListener('click', function (event) {
    const btn = event.target.closest('.btn-calificar');
    if (!btn) return;

    const d = btn.dataset;
    calfTipoTramiteActual = Number(d.tipoTramiteId || 0);
    calfAprobadoVerificador = d.verificadorAprobado === '1';

    document.getElementById('calf_tramite_id').value = d.tramiteId;
    document.getElementById('calf_ts_id').value = '';
    document.getElementById('calf_tipo_tramite_id').value = calfTipoTramiteActual;
    document.getElementById('calf_modal_titulo').textContent = 'Calificación de ' + (d.tipoTramite || 'trámite');
    document.getElementById('calf_folio_ingreso').textContent = 'Folio: ' + d.folio;
    document.getElementById('calf_folio_salida').style.display = 'none';

    document.getElementById('calf_propietario').textContent      = d.propietario || '—';
    document.getElementById('calf_direccion').textContent        = d.direccion || '—';
    document.getElementById('calf_colonia').textContent          = d.colonia || '—';
    document.getElementById('calf_localidad').textContent        = d.localidad || '—';
    document.getElementById('calf_cuenta_catastral').textContent = d.cuentaCatastral || '—';
    document.getElementById('calf_calle').textContent            = d.calle || '—';
    document.getElementById('calf_numero').textContent           = d.numero || '—';
    document.getElementById('calf_manzana').textContent          = d.manzana || '—';
    document.getElementById('calf_lote').textContent             = d.lote || '—';
    document.getElementById('calf_observaciones_verificador').textContent = d.observacionesVerificador || 'Sin observaciones.';
    document.getElementById('calf_verificador').textContent = d.verificador ? 'Verificó: ' + d.verificador : '';
    document.getElementById('calf_pendiente_verificador').classList.toggle('d-none', calfAprobadoVerificador);
    document.getElementById('calf_datos_lc_wrap').style.display = calfTipoTramiteActual === 7 ? 'block' : 'none';
    document.getElementById('calf_texto_imprimir').textContent = calfTipoTramiteActual === 2 ? 'Imprimir constancia' : 'Imprimir licencia';

    const opcionAprobado = document.querySelector('#calf_estatus option[value="Aprobado"]');
    opcionAprobado.disabled = !calfAprobadoVerificador;

    // Reset de la sección editable mientras cargan los datos
    document.getElementById('calf_estatus').value = 'En revisión';
    document.getElementById('calf_comentarios').value = '';
    document.getElementById('calf_reglamento_id').value = '';
    document.getElementById('calf_calles').value = '';
    document.getElementById('calf_metros_lineales').value = '';
    calfTipoObraActual = '';
    calfFaltaUbicacionGuardada = false;
    actualizarVisibilidadReglamentoCalf();
    document.getElementById('calf_vigencia').value = '';
    document.getElementById('calf_periodo_cantidad').value = 1;
    document.getElementById('calf_periodo_unidad').value = 'meses';
    document.getElementById('calf_expiracion').value = '';
    actualizarVisibilidadVigenciaCalf();
    actualizarBotonImprimirLicencia();

    // Abrir el modal explícitamente
    const modalEl = document.getElementById('modalCalificacion');
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modalInstance.show();

    /*
    Se piden los dos datos EN PARALELO (más rápido que uno tras otro),
    pero se espera a que AMBOS terminen antes de decidir qué mostrar —
    si se procesa cada respuesta por separado en cuanto llega, la que
    responde primero (normalmente tramites_salida) puede pisar campos
    como calles/metros lineales antes de saber todavía si tipo_obra
    es "Otro" (ese dato viene en la otra respuesta), y los deja vacíos.
    */
    const peticionObra = calfTipoTramiteActual === 7
        ? fetch('php/obtener_solicitud_lc.php?tramite_id=' + d.tramiteId).then(r => r.json())
        : Promise.resolve({ success: true, data: null });
    const peticionTs   = fetch('php/obtener_tramite_salida.php?tramite_id=' + d.tramiteId).then(r => r.json());

    Promise.all([peticionObra, peticionTs]).then(([respObra, respTs]) => {
        /*
         1- Datos propios de la Licencia de Construcción (solicitud_lc)
        Se procesa primero para que calfTipoObraActual ya esté listo
        antes de tocar cualquier cosa relacionada a "Otro"/Obra Pública.
        */
        if (respObra.success && respObra.data) {
            llenarDatosObraCalf(respObra.data);
        }

        // 2 - Calificación ya guardada previamente (tramites_salida), si existe
        if (respTs.success && respTs.data) {
            const ts = respTs.data;

            document.getElementById('calf_ts_id').value = ts.id || '';
            document.getElementById('calf_estatus').value = ts.estatus || 'En revisión';
            document.getElementById('calf_comentarios').value = ts.comentarios || '';
            document.getElementById('calf_reglamento_id').value = ts.reglamento_id || '';
            document.getElementById('calf_calles').value = ts.calles_manual || '';
            document.getElementById('calf_metros_lineales').value = ts.metros_lineales_manual || '';
            actualizarVisibilidadObraPublicaCalf();

            /*
            Si el reglamento guardado requiere ubicación manual y en el
            registro de tramites_salida falta calles_manual y/o
            metros_lineales_manual, se avisa aquí en el modal en vez de
            dejar que el usuario se entere hasta imprimir (pantalla en
            blanco). Reemplaza el die() que antes vivía en
            licencia_construccion.php.
            */
            const requiereUbicacion = document.getElementById('calf_obra_publica_wrap').style.display !== 'none';
            const faltaCalles = !ts.calles_manual;
            const faltaMetrosLineales = ts.metros_lineales_manual === null || ts.metros_lineales_manual === '';
            calfFaltaUbicacionGuardada = requiereUbicacion && (faltaCalles || faltaMetrosLineales);
            if (calfFaltaUbicacionGuardada) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan datos de ubicación',
                    text: 'Este reglamento requiere calles y metros lineales totales, y todavía no están capturados. Ve al módulo de Calificador y complétalos antes de imprimir.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#2f7d6d'
                });
            }

            document.getElementById('calf_vigencia').value = ts.vigencia || '';
            document.getElementById('calf_expiracion').value = ts.expiracion || '';

            if (ts.folio_salida_numero) {
                document.getElementById('calf_folio_salida').style.display = 'inline-block';
                document.getElementById('calf_folio_salida').textContent =
                    'Folio de salida: ' + String(ts.folio_salida_numero).padStart(3, '0') + '/' + ts.folio_salida_anio;
            }

            actualizarVisibilidadVigenciaCalf();
            actualizarBotonImprimirLicencia();
        }
    });
});


let calfTipoObraActual = '';
let calfTipoTramiteActual = 0;
let calfAprobadoVerificador = false;
let calfFaltaUbicacionGuardada = false;

function llenarDatosObraCalf(d) {
    document.getElementById('calf_tipo_obra').textContent = d.tipo_obra || '—';
    document.getElementById('calf_descripcion_obra').textContent = d.descripcion_obra || '—';
    calfTipoObraActual = d.tipo_obra || '';
    actualizarVisibilidadReglamentoCalf();

    const servicios = Array.isArray(d.urbanizacion) ? d.urbanizacion : [];
    document.getElementById('calf_urbanizacion').textContent = servicios.length ? servicios.join(', ') : '—';

    // Superficies
    const sup = d.superficies || {};
    const etiquetas = {
        sotano: 'Sótano', planta_baja: 'Planta baja', primer_nivel: 'Primer nivel',
        segundo_nivel: 'Segundo nivel', tercer_nivel: 'Tercer nivel'
    };
    const partes = [];
    Object.keys(etiquetas).forEach(k => {
        const obj = sup[k];
        if (obj && obj.valor !== null && obj.valor !== '' && obj.valor !== undefined) {
            const unidad = obj.unidad === 'mlin' ? 'Mlin' : 'M²';
            partes.push(etiquetas[k] + ': ' + obj.valor + ' ' + unidad);
        }
    });
    if (sup.otra_area) {
        const otra = typeof sup.otra_area === 'object' ? (sup.otra_area.valor || '') : sup.otra_area;
        if (otra) partes.push('Otras áreas: ' + otra);
    }
    document.getElementById('calf_superficies').textContent = partes.length ? partes.join(' · ') : '—';

    // Peritos
    const peritos = d.peritos || {};
    const etiquetasPeritos = { dro: 'Responsable de obra', estructural: 'Estructural', especialista: 'Instalaciones especiales' };
    const partesPeritos = [];
    Object.keys(etiquetasPeritos).forEach(p => {
        const info = peritos[p];
        if (info && info.nombre) {
            partesPeritos.push(etiquetasPeritos[p] + ': ' + info.nombre + (info.registro ? ' (Reg. ' + info.registro + ')' : ''));
        }
    });
    document.getElementById('calf_peritos').textContent = partesPeritos.length ? partesPeritos.join(' · ') : 'Sin peritos capturados';
}

// Reglamento: poblar el <select> una sola vez con REGLAMENTOS_LC
(function poblarSelectReglamentoCalf() {
    const sel = document.getElementById('calf_reglamento_id');
    REGLAMENTOS_LC.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id;
        opt.textContent = r.titulo;
        opt.dataset.requiereUbicacion = r.requiere_ubicacion_manual ? '1' : '0';
        sel.appendChild(opt);
    });
})();

//  Mostrar el selector de reglamento solo cuando tipo_obra = "Otro"
function actualizarVisibilidadReglamentoCalf() {
    const wrap = document.getElementById('calf_reglamento_wrap');
    wrap.style.display = (calfTipoObraActual === 'Otro') ? 'flex' : 'none';
    if (calfTipoObraActual !== 'Otro') {
        document.getElementById('calf_reglamento_id').value = '';
    }
    actualizarVisibilidadObraPublicaCalf();
}

//  Mostrar Calles/Metros lineales solo si el reglamento elegido es "Obra Pública"
function actualizarVisibilidadObraPublicaCalf() {
    const sel = document.getElementById('calf_reglamento_id');
    const wrap = document.getElementById('calf_obra_publica_wrap');
    const opcionElegida = sel.options[sel.selectedIndex];
    const requiereUbicacion = calfTipoObraActual === 'Otro' && opcionElegida && opcionElegida.dataset.requiereUbicacion === '1';
    wrap.style.display = requiereUbicacion ? 'flex' : 'none';
    if (!requiereUbicacion) {
        document.getElementById('calf_calles').value = '';
        document.getElementById('calf_metros_lineales').value = '';
    }
}
document.getElementById('calf_reglamento_id').addEventListener('change', actualizarVisibilidadObraPublicaCalf);

//  Mostrar la sección de vigencia solo cuando el estatus es Aprobado
function actualizarVisibilidadVigenciaCalf() {
    const wrap = document.getElementById('calf_vigencia_wrap');
    const esAprobado = calfTipoTramiteActual === 7 && document.getElementById('calf_estatus').value === 'Aprobado';
    wrap.style.display = esAprobado ? 'flex' : 'none';
}
document.getElementById('calf_estatus').addEventListener('change', actualizarVisibilidadVigenciaCalf);

//  Mostrar el botón "Imprimir Licencia" solo si ya está Aprobado y guardado
function actualizarBotonImprimirLicencia() {
    const btn = document.getElementById('btnImprimirLicencia');
    const tsId = document.getElementById('calf_ts_id').value;
    const estatus = document.getElementById('calf_estatus').value;
    btn.style.display = (tsId && estatus === 'Aprobado') ? 'inline-block' : 'none';
}
document.getElementById('calf_estatus').addEventListener('change', actualizarBotonImprimirLicencia);

document.getElementById('btnImprimirLicencia').addEventListener('click', function () {
    const tsId = document.getElementById('calf_ts_id').value;
    if (!tsId) return;

    /* No se abre la impresión si el reglamento requiere calles/ML y esos
     campos no están guardados en tramites_salida (aunque el usuario los
     haya escrito en el modal, si no le dio "Guardar" no cuentan).
    */
    if (calfFaltaUbicacionGuardada) {
        Swal.fire({
            icon: 'warning',
            title: 'Faltan datos de ubicación',
            text: 'Este reglamento requiere calles y metros lineales totales, y todavía no están capturados. Complétalos y guarda la calificación antes de imprimir.',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#2f7d6d'
        });
        return;
    }

    const pagina = calfTipoTramiteActual === 2 ? 'constancia_compatibilidad.php' : 'licencia_construccion.php';
    window.open(pagina + '?id=' + tsId, '_blank');
});

//  Cálculo automático de expiración
function recalcularExpiracionCalf() {
    const vigencia = document.getElementById('calf_vigencia').value;
    const cantidad = parseInt(document.getElementById('calf_periodo_cantidad').value, 10);
    const unidad   = document.getElementById('calf_periodo_unidad').value;
    const expEl    = document.getElementById('calf_expiracion');

    if (!vigencia || !cantidad || cantidad <= 0) {
        expEl.value = '';
        return;
    }

    // Se parte la fecha manualmente para evitar el corrimiento de un día
    // por zona horaria  se interpreta como UTC).
    const [anio, mes, dia] = vigencia.split('-').map(Number);
    const fecha = new Date(anio, mes - 1, dia);

    if (unidad === 'anios') fecha.setFullYear(fecha.getFullYear() + cantidad);
    else fecha.setMonth(fecha.getMonth() + cantidad);

    const yyyy = fecha.getFullYear();
    const mm   = String(fecha.getMonth() + 1).padStart(2, '0');
    const dd   = String(fecha.getDate()).padStart(2, '0');
    expEl.value = `${yyyy}-${mm}-${dd}`;
}
['calf_vigencia', 'calf_periodo_cantidad', 'calf_periodo_unidad'].forEach(id => {
    document.getElementById(id).addEventListener('input', recalcularExpiracionCalf);
    document.getElementById(id).addEventListener('change', recalcularExpiracionCalf);
});

//  Guardar calificación
document.getElementById('btnGuardarCalf').addEventListener('click', function () {
    const tramiteId = document.getElementById('calf_tramite_id').value;
    if (!tramiteId) return;

    const estatus    = document.getElementById('calf_estatus').value;
    const vigencia   = document.getElementById('calf_vigencia').value;
    const expiracion = document.getElementById('calf_expiracion').value;
    const alerta     = document.getElementById('calf_alerta');

    // Si va a quedar Aprobado, la vigencia y expiración son obligatorias
    if (estatus === 'Aprobado' && !calfAprobadoVerificador) {
        alerta.className = 'alert alert-warning mt-3';
        alerta.textContent = 'El verificador debe aprobar primero este trámite.';
        return;
    }

    if (calfTipoTramiteActual === 7 && estatus === 'Aprobado' && (!vigencia || !expiracion)) {
        alerta.className = 'alert alert-warning mt-3';
        alerta.textContent = 'Para aprobar debes capturar la vigencia y el periodo (la expiración se calcula sola).';
        return;
    }

    if (calfTipoTramiteActual === 7 && estatus === 'Aprobado'
        && !['Construcción', 'Demolición', 'Otro'].includes(calfTipoObraActual)) {
        alerta.className = 'alert alert-warning mt-3';
        alerta.textContent = 'Completa y aprueba primero la Solicitud de Licencia de Construcción.';
        return;
    }

    if (calfTipoTramiteActual === 7 && calfTipoObraActual === 'Otro'
        && estatus === 'Aprobado' && !document.getElementById('calf_reglamento_id').value) {
        alerta.className = 'alert alert-warning mt-3';
        alerta.textContent = 'Selecciona el reglamento que se imprimirá en esta licencia.';
        return;
    }

    // Si el reglamento elegido requiere ubicación manual (Obra Pública),
    // calles y metros lineales son obligatorios antes de guardar.
    const obraPublicaVisible = document.getElementById('calf_obra_publica_wrap').style.display !== 'none';
    if (obraPublicaVisible) {
        const calles = document.getElementById('calf_calles').value.trim();
        const ml = document.getElementById('calf_metros_lineales').value.trim();
        if (!calles || !ml) {
            alerta.className = 'alert alert-warning mt-3';
            alerta.textContent = 'Este reglamento requiere capturar las calles involucradas y los metros lineales totales.';
            return;
        }
    }

    const btn = this;
    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';

    const fd = new FormData();
    fd.append('tramite_id', tramiteId);
    fd.append('csrf_token', <?= json_encode($_SESSION['csrf_token'] ?? '') ?>);
    fd.append('estatus', estatus);
    fd.append('comentarios', document.getElementById('calf_comentarios').value);
    fd.append('reglamento_id', calfTipoObraActual === 'Otro' ? document.getElementById('calf_reglamento_id').value : '');
    fd.append('calles_manual', document.getElementById('calf_obra_publica_wrap').style.display !== 'none' ? document.getElementById('calf_calles').value : '');
    fd.append('metros_lineales_manual', document.getElementById('calf_obra_publica_wrap').style.display !== 'none' ? document.getElementById('calf_metros_lineales').value : '');
    fd.append('vigencia', estatus === 'Aprobado' ? vigencia : '');
    fd.append('expiracion', estatus === 'Aprobado' ? expiracion : '');

    fetch('php/guardar_tramite_salida.php', { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(r => r.json())
        .then(resp => {
            if (resp.success) {
                document.getElementById('calf_ts_id').value = resp.id || '';
                if (resp.folio_salida_numero) {
                    document.getElementById('calf_folio_salida').style.display = 'inline-block';
                    document.getElementById('calf_folio_salida').textContent =
                        'Folio de salida: ' + String(resp.folio_salida_numero).padStart(3, '0') + '/' + resp.folio_salida_anio;
                }
                actualizarBotonImprimirLicencia();
                alerta.className = 'alert alert-success mt-3';
                alerta.textContent = resp.message || 'Calificación guardada correctamente.';
                setTimeout(() => window.location.reload(), 1200);
            } else {
                alerta.className = 'alert alert-danger mt-3';
                alerta.textContent = resp.message || 'No se pudo guardar.';
            }
        })
        .catch(err => {
            alerta.className = 'alert alert-danger mt-3';
            alerta.textContent = 'Error de conexión: ' + err.message;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
        });
});

//  Limpiar el modal al cerrarlo, sin importar cómo
document.getElementById('modalCalificacion').addEventListener('hidden.bs.modal', function () {
    document.getElementById('calf_alerta').className = 'alert d-none';
    document.getElementById('calf_alerta').textContent = '';
});
</script>
</body>
</html>
