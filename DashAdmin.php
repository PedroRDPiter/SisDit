<?php
// Inicializar sesión y cargar las funciones de seguridad.
require "seguridad.php";
require_once "php/funciones_seguridad.php";

// Restringir el panel exclusivamente a usuarios con rol de Administrador.
if(!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador'){
    header("Location: acceso.php?error=no_autorizado");
    exit();
}
require_once "php/db.php";

// Obtener estadísticas generales de trámites y usuarios.
$stats_tramites = $conn->query("SELECT
    COUNT(*) as total,
    SUM(CASE WHEN estatus = 'En revisión' THEN 1 ELSE 0 END) as en_revision,
    SUM(CASE WHEN estatus IN ('Aprobado', 'Entregado y archivado') THEN 1 ELSE 0 END) as aprobados,
    SUM(CASE WHEN estatus = 'Rechazado' THEN 1 ELSE 0 END) as rechazados
    FROM tramites")->fetch_assoc();

$stats_usuarios = $conn->query("SELECT
    COUNT(*) as total,
    SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN rol = 'Administrador' THEN 1 ELSE 0 END) as admins,
    SUM(CASE WHEN rol = 'Verificador' THEN 1 ELSE 0 END) as verificadores,
    SUM(CASE WHEN rol = 'Ventanilla' THEN 1 ELSE 0 END) as ventanillas,
    SUM(CASE WHEN rol = 'Usuario' THEN 1 ELSE 0 END) as usuarios
    FROM usuarios")->fetch_assoc();

// Trámites ingresados durante el año actual, incluidos los meses sin registros.
$anio_estadisticas = (int)date('Y');
$tramites_por_mes = array_fill(0, 12, 0);
$inicio_estadisticas = $anio_estadisticas . '-01-01';
$fin_estadisticas = ($anio_estadisticas + 1) . '-01-01';
$consulta_mensual = $conn->prepare("SELECT MONTH(fecha_ingreso) AS mes, COUNT(*) AS total
    FROM tramites WHERE fecha_ingreso >= ? AND fecha_ingreso < ?
    GROUP BY MONTH(fecha_ingreso)");
$consulta_mensual->bind_param('ss', $inicio_estadisticas, $fin_estadisticas);
$consulta_mensual->execute();
$resultado_mensual = $consulta_mensual->get_result();
while ($mes = $resultado_mensual->fetch_assoc()) {
    $tramites_por_mes[(int)$mes['mes'] - 1] = (int)$mes['total'];
}
$consulta_mensual->close();

$tipos_estadisticas = [];
$totales_tipos_estadisticas = [];
$consulta_tipos = $conn->prepare("SELECT COALESCE(tt.nombre, 'Sin tipo asignado') AS tipo, COUNT(*) AS total
    FROM tramites t LEFT JOIN tipos_tramite tt ON tt.id = t.tipo_tramite_id
    WHERE t.fecha_ingreso >= ? AND t.fecha_ingreso < ?
    GROUP BY t.tipo_tramite_id, tt.nombre ORDER BY total DESC, tipo ASC");
$consulta_tipos->bind_param('ss', $inicio_estadisticas, $fin_estadisticas);
$consulta_tipos->execute();
$resultado_tipos = $consulta_tipos->get_result();
while ($tipo = $resultado_tipos->fetch_assoc()) {
    $tipos_estadisticas[] = $tipo['tipo'];
    $totales_tipos_estadisticas[] = (int)$tipo['total'];
}
$consulta_tipos->close();

// Cargar los usuarios para la tabla de gestión administrativa.
$usuarios_query = $conn->query("SELECT * FROM usuarios ORDER BY fecha_registro DESC");

// Cargar los trámites aprobados para imprimir sus constancias.
$tramites_aprobados = $conn->query("
    SELECT t.*, tt.nombre AS tipo_tramite_nombre,
           u.nombre AS solicitante_nombre, u.apellidos AS solicitante_apellidos,
           CONCAT(LPAD(t.folio_numero,3,'0'),'/',t.folio_anio) AS folio
    FROM tramites t
    LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
    LEFT JOIN usuarios u ON t.usuario_creador_id = u.id
    WHERE t.estatus IN ('Aprobado', 'Entregado y archivado')
    ORDER BY t.fecha_aprobacion DESC
");

// Obtener las actividades más recientes del sistema.
$logs_query = $conn->query("SELECT l.*, u.nombre, u.apellidos
    FROM logs_actividad l
    LEFT JOIN usuarios u ON l.usuario_id = u.id
    ORDER BY l.fecha DESC
    LIMIT 50");

// Obtener solicitudes de registro y contar las que siguen pendientes.
$solicitudes_query = $conn->query("SELECT * FROM solicitudes_registro ORDER BY FIELD(estado,'Pendiente','Aprobado','Rechazado'), fecha_solicitud DESC");
$total_pendientes  = $conn->query("SELECT COUNT(*) as c FROM solicitudes_registro WHERE estado='Pendiente'")->fetch_assoc()['c'];

// ── REPORTE: trámites agrupados por mes, año y tipo ──
$anio_filtro = isset($_GET['anio_reporte']) ? (int)$_GET['anio_reporte'] : (int)date('Y');

// Obtener los años disponibles para el filtro del reporte.
$anios_res = $conn->query("SELECT DISTINCT folio_anio FROM tramites ORDER BY folio_anio DESC");
$anios_disponibles = [];
while ($a = $anios_res->fetch_assoc()) $anios_disponibles[] = $a['folio_anio'];
if (empty($anios_disponibles)) $anios_disponibles[] = date('Y');

// Calcular los totales mensuales del año seleccionado.
$reporte_mes = $conn->query("
    SELECT
        MONTH(fecha_ingreso) AS mes,
        COUNT(*) AS total,
        SUM(CASE WHEN estatus IN ('Aprobado', 'Entregado y archivado') THEN 1 ELSE 0 END) AS aprobados,
        SUM(CASE WHEN estatus = 'En revisión' THEN 1 ELSE 0 END) AS en_revision,
        SUM(CASE WHEN estatus = 'En corrección' THEN 1 ELSE 0 END) AS en_correccion,
        SUM(CASE WHEN estatus = 'Rechazado' THEN 1 ELSE 0 END) AS rechazados
    FROM tramites
    WHERE folio_anio = $anio_filtro
    GROUP BY MONTH(fecha_ingreso)
    ORDER BY MONTH(fecha_ingreso)
");
$datos_mes = [];
while ($r = $reporte_mes->fetch_assoc()) $datos_mes[(int)$r['mes']] = $r;

// Calcular los totales agrupados por tipo de trámite.
$reporte_tipo = $conn->query("
    SELECT tt.nombre AS tipo,
           COUNT(*) AS total,
           SUM(CASE WHEN t.estatus IN ('Aprobado', 'Entregado y archivado') THEN 1 ELSE 0 END) AS aprobados,
           SUM(CASE WHEN t.estatus = 'Rechazado' THEN 1 ELSE 0 END) AS rechazados
    FROM tramites t
    LEFT JOIN tipos_tramite tt ON t.tipo_tramite_id = tt.id
    WHERE t.folio_anio = $anio_filtro
    GROUP BY tt.nombre
    ORDER BY total DESC
");
$datos_tipo = [];
while ($r = $reporte_tipo->fetch_assoc()) $datos_tipo[] = $r;

// Obtener el total anual y el total histórico.
$gran_total = $conn->query("SELECT COUNT(*) as c FROM tramites WHERE folio_anio = $anio_filtro")->fetch_assoc()['c'];
$total_global = $conn->query("SELECT COUNT(*) as c FROM tramites")->fetch_assoc()['c'];


?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="theme-color" content="#4b0e22">
<title>Administración | SisDiT</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- BOOTSTRAP 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- CSS PROPIO -->
<link rel="stylesheet" href="css/admin-oficios.css?v=<?= filemtime(__DIR__ . '/css/admin-oficios.css') ?>">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<script>
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};
</script>
<link rel="stylesheet" href="css/dashboard-modern.css?v=<?= filemtime(__DIR__ . '/css/dashboard-modern.css') ?>">
<link rel="stylesheet" href="css/dashboard-admin.css?v=<?= filemtime(__DIR__ . '/css/dashboard-admin.css') ?>">
<link rel="stylesheet" href="css/oficio-modal.css?v=<?= filemtime(__DIR__ . '/css/oficio-modal.css') ?>">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
#mapa-tramites-admin{height:min(68vh,680px);min-height:420px;border-radius:12px;z-index:1}.mapa-admin-leyenda{display:flex;flex-wrap:wrap;gap:.6rem 1rem}.mapa-admin-etapa{display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem}.mapa-admin-punto{width:.7rem;height:.7rem;border-radius:50%;display:inline-block}.mapa-admin-progreso{display:flex;gap:0;margin:.8rem 0}.mapa-admin-paso{flex:1;text-align:center;position:relative;font-size:.69rem;color:#737985}.mapa-admin-paso:not(:last-child)::after{content:'';position:absolute;height:2px;background:#d9dee5;top:.48rem;left:58%;right:-42%}.mapa-admin-paso .punto{display:block;width:.8rem;height:.8rem;border-radius:50%;margin:0 auto .3rem;background:#d9dee5;position:relative;z-index:1}.mapa-admin-paso.completado{color:#146c43}.mapa-admin-paso.completado .punto{background:#198754}.mapa-admin-paso.actual{color:#7b0f2b;font-weight:700}.mapa-admin-paso.actual .punto{background:#7b0f2b;box-shadow:0 0 0 3px #f2dce3}.mapa-admin-paso.finalizado .punto{background:#198754}.leaflet-interactive.mapa-admin-predio{transition:fill-opacity .12s ease}.leaflet-interactive.mapa-admin-predio:hover{fill-opacity:.72}@media(max-width:575px){#mapa-tramites-admin{min-height:360px}.mapa-admin-paso{font-size:.58rem}}
</style>

</head>

<body class="dashboard-shell dashboard-admin">

<!-- NAVBAR MÓVIL -->
<nav class="navbar navbar-dark bg-dark d-lg-none">
    <div class="container-fluid">
        <span class="navbar-brand"><i class="bi bi-shield-check me-2"></i>Administración</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuMovil" aria-controls="menuMovil" aria-expanded="false" aria-label="Abrir menú de administración">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>

    <div class="collapse navbar-collapse" id="menuMovil">
        <ul class="navbar-nav p-3">
            <li class="nav-item"><a class="nav-link" href="#inicio">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="#oficios-digitales"><i class="bi bi-pen"></i> Firma y cierre de oficios</a></li>
            <li class="nav-item"><a class="nav-link" href="#estadisticas">Estadísticas</a></li>
            <li class="nav-item"><a class="nav-link" href="#mapa-cuentas"><i class="bi bi-geo-alt me-1"></i>Mapa de cuentas</a></li>
            <li class="nav-item">
                <a class="nav-link" href="#solicitudes">
                    <i class="bi bi-person-check me-1"></i> Solicitudes
                    <?php if($total_pendientes > 0): ?><span class="badge bg-warning text-dark"><?= $total_pendientes ?></span><?php endif; ?>
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="#reporte"><i class="bi bi-bar-chart-line me-1"></i> Reporte</a></li>
            <li class="nav-item"><a class="nav-link" href="#tramites-aprobados"><i class="bi bi-printer"></i> Constancias</a></li>
            <li class="nav-item"><a class="nav-link" href="#actualizar-shp">Actualizar SHP</a></li>
            <li class="nav-item"><a class="nav-link" href="#usuarios">Gestión de Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="#logs">Registro de Actividad</a></li>
            <li class="nav-item"><a class="nav-link" href="Dash.php">Ver Trámites</a></li>
            <li class="nav-item"><a class="nav-link text-danger" href="logout.php?csrf_token=<?= urlencode($_SESSION['csrf_token']) ?>">Cerrar sesión</a></li>
        </ul>
    </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar d-none d-lg-flex" aria-label="Menú de administración">
    <h5><i class="bi bi-shield-check me-2"></i>Administración</h5>
    <a href="#inicio"><i class="bi bi-house me-2"></i>Inicio</a>
    <a class="nav-link text-white" href="#oficios-digitales"><i class="bi bi-pen me-1"></i> Firma y cierre de oficios</a>
    <a href="#estadisticas"><i class="bi bi-graph-up me-2"></i>Estadísticas</a>
    <a href="#mapa-cuentas"><i class="bi bi-geo-alt me-2"></i>Mapa de cuentas y trámites</a>
    <a class="nav-link text-white" href="#solicitudes">
        <i class="bi bi-person-check me-1"></i> Solicitudes
        <?php if($total_pendientes > 0): ?>
        <span class="badge bg-warning text-dark ms-1"><?= $total_pendientes ?></span>
        <?php endif; ?>
    </a>
    <a class="nav-link text-white" href="#reporte"><i class="bi bi-bar-chart-line me-1"></i> Reporte</a>
    <a class="nav-link text-white" href="#tramites-aprobados"><i class="bi bi-printer"></i> Constancias</a>
    <a class="nav-link text-white" href="#actualizar-shp"><i class="bi bi-map me-1"></i> Actualizar SHP</a>
    <a href="#usuarios"><i class="bi bi-people me-2"></i>Gestión de Usuarios</a>
    <a href="#logs"><i class="bi bi-clock-history me-2"></i>Registro de Actividad</a>
    <a href="Dash.php"><i class="bi bi-folder2-open me-2"></i>Ver trámites</a>
    <a class="text-danger mt-auto" href="logout.php?csrf_token=<?= urlencode($_SESSION['csrf_token']) ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
</div>

<!-- CONTENIDO -->
<div class="content">

<!-- ENCABEZADO -->
<section class="hero" id="inicio">
    <div class="dashboard-eyebrow">SisDiT · Administración y control</div>
    <h1><i class="bi bi-shield-check me-2"></i>Panel de Administrador</h1>
    <p>Bienvenido, <strong><?= e((string)($_SESSION['usuario'] ?? '')) ?></strong>.
       Firma oficios, cierra expedientes y administra la operación del sistema.</p>
    <div class="dashboard-hero-actions" aria-label="Accesos rápidos">
        <a class="dashboard-hero-action" href="#oficios-digitales"><i class="bi bi-pen"></i>Firmar oficios</a>
        <a class="dashboard-hero-action" href="#usuarios"><i class="bi bi-people"></i>Gestionar usuarios</a>
        <a class="dashboard-hero-action" href="#reporte"><i class="bi bi-bar-chart-line"></i>Ver reportes</a>
    </div>
</section>

<div class="row g-3 mb-4 admin-resumen" aria-label="Resumen del sistema">
        <!-- Tarjeta Trámites -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Total Trámites</p><h3 class="mb-0 fw-bold"><?= $stats_tramites['total'] ?></h3></div>
                    <i class="bi bi-folder-check text-primary fs-1"></i>
                </div>
            </div>
        </div>

        <!-- Tarjeta En Revisión -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">En Revisión</p><h3 class="mb-0 fw-bold"><?= $stats_tramites['en_revision'] ?></h3></div>
                    <i class="bi bi-hourglass-split text-warning fs-1"></i>
                </div>
            </div>
        </div>

        <!-- Tarjeta Aprobados -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Aprobados</p><h3 class="mb-0 fw-bold"><?= $stats_tramites['aprobados'] ?></h3></div>
                    <i class="bi bi-check-circle text-success fs-1"></i>
                </div>
            </div>
        </div>

        <!-- Tarjeta Usuarios -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm border-start border-info border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1 small">Usuarios Activos</p><h3 class="mb-0 fw-bold"><?= $stats_usuarios['activos'] ?></h3></div>
                    <i class="bi bi-people text-info fs-1"></i>
                </div>
            </div>
        </div>
    </div>

<?php require __DIR__ . '/php/vistas/admin_oficios.php'; ?>

<!-- ESTADÍSTICAS -->
<section id="estadisticas" class="tramite-box mb-6">
    <h4 class="text-primary mb-3"><i class="bi bi-graph-up"></i> Estadísticas del Sistema</h4>

    <!-- Gráficas -->
    <div class="row g-3 admin-graficas">
        <div class="col-md-6">
            <div class="card shadow-sm card-distribucion">
                <div class="card-body">
                    <h5 class="card-title">Distribución de Trámites</h5>
                    <p class="text-muted small">Distribución histórica por estado del trámite.</p>
                    <div class="admin-grafica-canvas"><canvas id="chartTramites"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm card-usuarios-rol">
                <div class="card-body">
                    <h5 class="card-title">Usuarios por Rol</h5>
                    <p class="text-muted small">Usuarios registrados por rol en el sistema.</p>
                    <div class="admin-grafica-canvas"><canvas id="chartUsuarios"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 admin-grafica-mensual">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Trámites por mes · <?= $anio_estadisticas ?></h5>
                    <p class="text-muted small">Cantidad de trámites registrados según su fecha de ingreso.</p>
                    <div class="admin-grafica-canvas">
                        <canvas id="chartTramitesMensuales" role="img" aria-label="Cantidad de trámites por mes del año <?= $anio_estadisticas ?>"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Trámites por tipo · <?= $anio_estadisticas ?></h5>
                    <p class="text-muted small">Cantidad registrada por cada tipo de trámite.</p>
                    <div class="admin-grafica-canvas admin-grafica-tipos">
                        <?php if (!$tipos_estadisticas): ?>
                        <p class="admin-grafica-vacia">No hay trámites registrados este año.</p>
                        <?php endif; ?>
                        <canvas id="chartTramitesTipos" role="img" aria-label="Cantidad de trámites por tipo del año <?= $anio_estadisticas ?>"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="mapa-cuentas" class="tramite-box mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3"><div><h4 class="text-primary mb-1"><i class="bi bi-geo-alt me-2"></i>Mapa de cuentas y trámites</h4><p class="text-muted small mb-0">Consulta la ubicación, cuenta catastral y avance de cada trámite. Esta vista es de solo consulta.</p></div><span class="badge text-bg-light border"><i class="bi bi-eye me-1"></i>Solo consulta</span></div>
    <!-- funcion para buscar poligono por numero de folio -->
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Buscar por folio..." aria-label="Buscar por folio..." id="buscador-folio">
        <button class="btn btn-outline-secondary" type="button" id="boton-buscar-folio"><i class="bi bi-search"></i></button>
    </div>               
    <div class="mapa-admin-leyenda mb-3" aria-label="Estados de trámites"><span class="mapa-admin-etapa"><i class="mapa-admin-punto" style="background:#e5e7eb;border:1px solid #adb5bd"></i>Sin trámite</span><span class="mapa-admin-etapa"><i class="mapa-admin-punto" style="background:#dc3545"></i>En revisión o corrección</span><span class="mapa-admin-etapa"><i class="mapa-admin-punto" style="background:#0d6efd"></i>Revisión aprobada</span><span class="mapa-admin-etapa"><i class="mapa-admin-punto" style="background:#ffc107"></i>Pendiente de firma</span><span class="mapa-admin-etapa"><i class="mapa-admin-punto" style="background:#198754"></i>Concluido</span></div>
    <div id="mapa-tramites-admin" role="region" aria-label="Polígonos catastrales y avance de trámites"></div><div id="mapa-tramites-admin-mensaje" class="small text-muted mt-2" aria-live="polite">Cargando polígonos, cuentas y trámites...</div>
</section>
<!-- ================================================ -->
<!-- SOLICITUDES DE REGISTRO                         -->
<!-- ================================================ -->
<section id="solicitudes" class="tramite-box mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-primary m-0">
            <i class="bi bi-person-check me-2"></i>Solicitudes de Registro
        </h4>
        <?php if($total_pendientes > 0): ?>
        <span class="badge bg-warning text-dark fs-6">
            <i class="bi bi-clock me-1"></i><?= $total_pendientes ?> pendiente(s)
        </span>
        <?php else: ?>
        <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>Sin pendientes</span>
        <?php endif; ?>
    </div>
    <p class="text-muted small mb-3">
        <i class="bi bi-info-circle me-1"></i>
        Los usuarios que soliciten registro como <strong>Usuario</strong>, <strong>Ventanilla</strong> o <strong>Verificador</strong>
        aparecen aquí. Al aprobar, se crea su cuenta y puedes notificarles sus credenciales.
    </p>

    <?php if(!$solicitudes_query || $solicitudes_query->num_rows === 0): ?>
    <div class="text-center text-muted py-4">
        <i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay solicitudes de registro.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table id="tablaSolicitudes" class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while($sol = $solicitudes_query->fetch_assoc()): ?>
            <?php
                $badgeSol = 'bg-secondary';
                if ($sol['estado'] === 'Pendiente')  $badgeSol = 'bg-warning text-dark';
                if ($sol['estado'] === 'Aprobado')   $badgeSol = 'bg-success';
                if ($sol['estado'] === 'Rechazado')  $badgeSol = 'bg-danger';
            ?>
            <tr>
                <td><?= $sol['id'] ?></td>
                <td><?= htmlspecialchars($sol['nombre'].' '.$sol['apellidos']) ?></td>
                <td><?= htmlspecialchars($sol['correo']) ?></td>
                <td><?= htmlspecialchars($sol['telefono'] ? $sol['telefono'] : '—') ?></td>
                <td>
                    <span class="badge <?=
                        $sol['rol']==='Verificador' ? 'bg-warning text-dark' :
                        ($sol['rol']==='Ventanilla' ? 'bg-info text-dark' :
                        ($sol['rol']==='Usuario' ? 'bg-secondary' : 'bg-secondary')) ?>">
                        <?= $sol['rol'] ?>
                    </span>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])) ?></td>
                <td><span class="badge <?= $badgeSol ?>"><?= $sol['estado'] ?></span></td>
                <td class="text-center">
                <?php if($sol['estado'] === 'Pendiente'): ?>
                    <button class="btn btn-sm btn-success btn-aprobar-sol"
                        data-id="<?= $sol['id'] ?>"
                        data-nombre="<?= htmlspecialchars($sol['nombre'].' '.$sol['apellidos']) ?>"
                        data-rol="<?= $sol['rol'] ?>">
                        <i class="bi bi-check-circle me-1"></i>Aprobar
                    </button>
                    <button class="btn btn-sm btn-danger btn-rechazar-sol"
                        data-id="<?= $sol['id'] ?>"
                        data-nombre="<?= htmlspecialchars($sol['nombre'].' '.$sol['apellidos']) ?>">
                        <i class="bi bi-x-circle me-1"></i>Rechazar
                    </button>
                <?php elseif($sol['estado'] === 'Rechazado' && $sol['motivo_rechazo']): ?>
                    <small class="text-muted fst-italic">
                        Motivo: <?= htmlspecialchars($sol['motivo_rechazo']) ?>
                    </small>
                <?php else: ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>

<!-- MODAL NOTIFICACIÓN SOLICITUD -->
<div class="modal fade" id="notifSolModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header" style="background:#7b0f2b;color:white;">
                <h5 class="modal-title"><i class="bi bi-bell me-2"></i>Notificar al Solicitante</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-2" id="notif-sol-desc"></p>
                <div id="notif-sol-preview" class="alert alert-light border mb-3" style="display:none;">
                    <p class="mb-1 fw-bold small text-secondary"><i class="bi bi-chat-quote me-1"></i>Mensaje a enviar:</p>
                    <p id="notif-sol-texto" class="mb-0 small" style="white-space:pre-line;"></p>
                </div>
                <a id="notif-sol-wa" href="#" target="_blank" rel="noopener"
                   class="d-flex align-items-center gap-3 p-3 rounded border mb-2 text-decoration-none text-dark"
                   style="border-color:#25D366!important;background:rgba(37,211,102,.06);">
                    <span style="font-size:2rem;">💬</span>
                    <div>
                        <div class="fw-bold" style="color:#25D366;">Enviar por WhatsApp</div>
                        <div class="text-muted notif-sol-sub small">Abre WhatsApp con el mensaje listo</div>
                    </div>
                </a>
                <a id="notif-sol-gm" href="#" target="_blank" rel="noopener"
                   class="d-flex align-items-center gap-3 p-3 rounded border mb-2 text-decoration-none text-dark"
                   style="border-color:#EA4335!important;background:rgba(234,67,53,.06);">
                    <span style="font-size:2rem;">📧</span>
                    <div>
                        <div class="fw-bold" style="color:#EA4335;">Enviar por Correo</div>
                        <div class="text-muted notif-sol-sub small">Abre tu cliente de correo listo</div>
                    </div>
                </a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCerrarNotifSol">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ================================================ -->
<!-- REPORTE DE TRÁMITES                             -->
<!-- ================================================ -->
<section id="reporte" class="tramite-box mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="text-primary m-0"><i class="bi bi-bar-chart-line me-2"></i>Reporte de Trámites Realizados</h4>
    <!-- Selector de año -->
    <form method="GET" class="d-flex align-items-center gap-2 mb-0">
      <a href="#reporte"></a>
      <label class="fw-semibold mb-0">Año:</label>
      <select name="anio_reporte" class="form-select form-select-sm" style="width:100px;" onchange="this.form.submit()">
        <?php foreach($anios_disponibles as $a): ?>
        <option value="<?= $a ?>" <?= $a == $anio_filtro ? 'selected' : '' ?>><?= $a ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <!-- Tarjetas resumen del año -->
  <?php
  $tot_año    = $gran_total;
  $apr_año    = 0; $rev_año = 0; $rec_año = 0; $cor_año = 0;
  foreach ($datos_mes as $d) {
    $apr_año += $d['aprobados'];
    $rev_año += $d['en_revision'];
    $rec_año += $d['rechazados'];
    $cor_año += $d['en_correccion'];
  }
  ?>
  <div class="row g-3 mb-4 admin-resumen admin-resumen-reporte" aria-label="Resumen del reporte">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm border-start border-primary border-4 h-100">
        <div class="card-body text-center py-3">
          <div class="fs-1 fw-bold text-primary"><?= $tot_año ?></div>
          <div class="text-muted small">Total <?= $anio_filtro ?></div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm border-start border-success border-4 h-100">
        <div class="card-body text-center py-3">
          <div class="fs-1 fw-bold text-success"><?= $apr_año ?></div>
          <div class="text-muted small">Aprobados</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm border-start border-warning border-4 h-100">
        <div class="card-body text-center py-3">
          <div class="fs-1 fw-bold text-warning"><?= $rev_año ?></div>
          <div class="text-muted small">En Revisión</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm border-start border-danger border-4 h-100">
        <div class="card-body text-center py-3">
          <div class="fs-1 fw-bold text-danger"><?= $rec_año ?></div>
          <div class="text-muted small">Rechazados</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráfica de barras mensual -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <canvas id="chartReporteMes" style="max-height:280px;"></canvas>
    </div>
  </div>

  <!-- Tabla por mes -->
  <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-calendar3 me-1"></i>Trámites por Mes — <?= $anio_filtro ?></h6>
  <?php
  $meses_nombre = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                   7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
  $sum_total=0; $sum_apr=0; $sum_rev=0; $sum_cor=0; $sum_rec=0;
  ?>
  <div class="table-responsive mb-4">
    <table class="table table-bordered table-hover align-middle" id="tablaReporteMes">
      <thead>
        <tr>
          <th>Mes</th>
          <th>Total</th>
          <th>Aprobados</th>
          <th>En Revisión</th>
          <th>En Corrección</th>
          <th>Rechazados</th>
        </tr>
      </thead>
      <tbody>
        <?php for($m=1; $m<=12; $m++):
          $d = isset($datos_mes[$m]) ? $datos_mes[$m] : ['total'=>0,'aprobados'=>0,'en_revision'=>0,'en_correccion'=>0,'rechazados'=>0];
          $sum_total += $d['total'];
          $sum_apr   += $d['aprobados'];
          $sum_rev   += $d['en_revision'];
          $sum_cor   += $d['en_correccion'];
          $sum_rec   += $d['rechazados'];
        ?>
        <tr <?= $d['total'] == 0 ? 'class="text-muted"' : '' ?>>
          <td class="fw-semibold"><?= $meses_nombre[$m] ?></td>
          <td class="text-center">
            <?php if($d['total'] > 0): ?>
            <span class="badge bg-primary fs-6"><?= $d['total'] ?></span>
            <?php else: ?>
            <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
          <td class="text-center"><?= $d['aprobados'] > 0 ? '<span class="badge bg-success">'.$d['aprobados'].'</span>' : '—' ?></td>
          <td class="text-center"><?= $d['en_revision'] > 0 ? '<span class="badge bg-warning text-dark">'.$d['en_revision'].'</span>' : '—' ?></td>
          <td class="text-center"><?= $d['en_correccion'] > 0 ? '<span class="badge bg-info text-dark">'.$d['en_correccion'].'</span>' : '—' ?></td>
          <td class="text-center"><?= $d['rechazados'] > 0 ? '<span class="badge bg-danger">'.$d['rechazados'].'</span>' : '—' ?></td>
        </tr>
        <?php endfor; ?>
      </tbody>
      <tfoot>
        <tr style="background:#f0f0f0;font-weight:700;">
          <td>TOTAL <?= $anio_filtro ?></td>
          <td class="text-center"><span class="badge bg-primary fs-6"><?= $sum_total ?></span></td>
          <td class="text-center"><span class="badge bg-success"><?= $sum_apr ?></span></td>
          <td class="text-center"><span class="badge bg-warning text-dark"><?= $sum_rev ?></span></td>
          <td class="text-center"><span class="badge bg-info text-dark"><?= $sum_cor ?></span></td>
          <td class="text-center"><span class="badge bg-danger"><?= $sum_rec ?></span></td>
        </tr>
        <tr style="background:#e8e8e8;font-weight:700;">
          <td colspan="2">TOTAL HISTÓRICO (todos los años)</td>
          <td colspan="4" class="text-center"><span class="badge bg-dark fs-6"><?= $total_global ?> trámites en total</span></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Tabla por tipo de trámite -->
  <?php if (!empty($datos_tipo)): ?>
  <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-list-task me-1"></i>Trámites por Tipo — <?= $anio_filtro ?></h6>
  <div class="table-responsive mb-3">
    <table class="table table-bordered table-hover align-middle" id="tablaReporteTipo">
      <thead>
        <tr>
          <th>Tipo de Trámite</th>
          <th>Total</th>
          <th>Aprobados</th>
          <th>Rechazados</th>
          <th>% del año</th>
        </tr>
         </thead>
      <tbody>
        <?php foreach($datos_tipo as $dt):
          $pct = $sum_total > 0 ? round(($dt['total'] / $sum_total) * 100, 1) : 0;
        ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($dt['tipo'] ? $dt['tipo'] : 'Sin tipo') ?></td>
          <td class="text-center"><span class="badge bg-primary"><?= $dt['total'] ?></span></td>
          <td class="text-center"><span class="badge bg-success"><?= $dt['aprobados'] ?></span></td>
          <td class="text-center"><span class="badge bg-danger"><?= $dt['rechazados'] ?></span></td>
          <td class="text-center">
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-fill" style="height:14px;">
                <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
              </div>
              <small class="fw-bold"><?= $pct ?>%</small>
            </div>
           </td>
         </tr>
        <?php endforeach; ?>
      </tbody>
     </table>
  </div>
  <?php endif; ?>

  <!-- Botón imprimir reporte -->
  <div class="text-end">
    <button onclick="imprimirReporte()" class="btn btn-outline-primary">
      <i class="bi bi-printer me-1"></i>Imprimir Reporte
    </button>
  </div>
</section>

<!-- TRÁMITES APROBADOS - CONSTANCIAS -->
<section id="tramites-aprobados" class="tramite-box mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-success m-0"><i class="bi bi-check-circle-fill"></i> Trámites Aprobados — Imprimir Constancia</h4>
        <span class="badge bg-success fs-6"><?= $tramites_aprobados->num_rows ?> aprobados</span>
    </div>
    <p class="text-muted small mb-3"><i class="bi bi-info-circle"></i> Haz clic en <strong>Imprimir</strong> para abrir la constancia lista para firmar y entregar al solicitante.</p>

    <div class="table-responsive">
        <table id="tablaAprobados" class="table table-bordered table-hover">
            <thead>
                 <tr>
                    <th>Folio Ingreso</th>
                    <th>Folio Salida</th>
                    <th>Tipo de Trámite</th>
                    <th>Propietario</th>
                    <th>Solicitante</th>
                    <th>Dirección</th>
                    <th>Número Asignado</th>
                    <th>Fecha Aprobación</th>
                    <th>Constancia</th>
                 </tr>
            </thead>
            <tbody>
                <?php while($tr = $tramites_aprobados->fetch_assoc()): ?>
                <?php
                    $folio_sal_a = !empty($tr['folio_salida_numero'])
                        ? str_pad($tr['folio_salida_numero'], 3, '0', STR_PAD_LEFT) . '/' . $tr['folio_salida_anio']
                        : '';
                ?>
                <tr>
                    <td><span class="badge bg-success"><?= htmlspecialchars($tr['folio']) ?></span></td>
                    <td>
                        <?php if($folio_sal_a): ?>
                            <span class="badge bg-primary"><?= htmlspecialchars($folio_sal_a) ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($tr['tipo_tramite_nombre'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($tr['propietario']) ?></td>
                    <td><?= htmlspecialchars($tr['solicitante']) ?></td>
                    <td><?= htmlspecialchars($tr['direccion']) ?></td>
                    <td>
                        <?php if(!empty($tr['numero_asignado'])): ?>
                            <span class="badge bg-primary fs-6"><?= htmlspecialchars($tr['numero_asignado']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $tr['fecha_aprobacion']
                            ? date('d/m/Y H:i', strtotime($tr['fecha_aprobacion']))
                            : '—' ?>
                    </td>
                    <td class="text-center">
                        <?php
                            $folio_url = urlencode($tr['folio']);
                        ?>
                        <a href="constancia_numero.php?folio=<?= $folio_url ?>"
                           target="_self"
                           class="btn btn-sm btn-success"
                           title="Abrir constancia para imprimir">
                            <i class="bi bi-printer-fill"></i> Imprimir
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- GESTIÓN DE USUARIOS -->
<section id="actualizar-shp" class="tramite-box mb-4">
    <h4 class="mb-3"><i class="bi bi-map me-2"></i>Actualizar SHP</h4>
    <p>Selecciona el archivo de polígonos catastrales para actualizar los mapas de ventanilla, verificador y calificador.</p>
    <form id="form-actualizar-shp" enctype="multipart/form-data" method="post" action="php/actualizar_shp.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generarCSRF(), ENT_QUOTES, 'UTF-8') ?>">
        <label for="archivo-shp" class="form-label">Archivo de polígonos (.shp)</label>
        <input id="archivo-shp" name="shp" type="file" accept=".shp" class="form-control mb-2" required aria-describedby="ayuda-shp">
        <p id="ayuda-shp" class="text-muted small">Máximo 30 MB. Utiliza el SHP municipal en UTM zona 13 norte. Se conserva una copia de la capa anterior. Las claves existentes se recuperan cuando coincide la geometría; los predios nuevos quedan sin clave porque el archivo SHP no incluye esos datos.</p>
        <button type="submit" class="btn btn-primary"><i class="bi bi-arrow-repeat me-1"></i>Cambiar a GeoJSON y actualizar mapas</button>
    </form>
    <div id="resultado-shp" class="d-none" role="status" aria-live="polite"></div>
</section>

<section id="usuarios" class="tramite-box mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-primary m-0"><i class="bi bi-people"></i> Gestión de Usuarios</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
            <i class="bi bi-plus-circle"></i> Nuevo Usuario
        </button>
    </div>

    <div class="table-responsive">
        <table id="tablaUsuarios" class="table table-bordered table-hover">
            <thead>
                 <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th>Acciones</th>
                 </tr>
            </thead>
            <tbody>
                <?php while($usuario = $usuarios_query->fetch_assoc()): ?>
                <tr>
                    <td><?= $usuario['id'] ?></td>
                    <td><?= htmlspecialchars($usuario['nombre'].' '.$usuario['apellidos']) ?></td>
                    <td><?= htmlspecialchars($usuario['correo']) ?></td>
                    <td>
                        <span class="badge bg-<?= match($usuario['rol']) {
                            'Administrador' => 'danger',
                            'Verificador' => 'warning',
                            'Ventanilla' => 'info',
                            'Usuario' => 'secondary',
                            default => 'secondary'
                        } ?>">
                            <?= $usuario['rol'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= $usuario['activo'] ? 'success' : 'secondary' ?>">
                            <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td><?= $usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : 'Nunca' ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary"
                                onclick='editarUsuario(<?= (int)$usuario['id'] ?>, <?= json_encode($usuario['nombre'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($usuario['apellidos'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($usuario['correo'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($usuario['rol'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= (int)$usuario['activo'] ?>)'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <?php if($usuario['id'] != $_SESSION['id']): ?>
                        <button class="btn btn-sm btn-outline-<?= $usuario['activo'] ? 'warning' : 'success' ?>"
                                onclick="toggleEstadoUsuario(<?= (int)$usuario['id'] ?>, <?= (int)$usuario['activo'] ?>)">
                            <i class="bi bi-<?= $usuario['activo'] ? 'x-circle' : 'check-circle' ?>"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- LOGS DE ACTIVIDAD -->
<section id="logs" class="tramite-box mb-4">
    <h4 class="text-primary mb-3"><i class="bi bi-clock-history"></i> Registro de Actividad</h4>

    <div class="table-responsive">
        <table id="tablaLogs" class="table table-sm table-bordered">
            <thead>
                 <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalles</th>
                    <th>IP</th>
                 </tr>
            </thead>
            <tbody>
                <?php while($log = $logs_query->fetch_assoc()): ?>
                <tr>
                    <td><?= date('d/m/Y H:i:s', strtotime($log['fecha'])) ?></td>
                    <td><?= htmlspecialchars($log['nombre'].' '.$log['apellidos']) ?></td>
                    <td>
                        <span class="badge bg-<?= match($log['accion']) {
                            'Login exitoso' => 'success',
                            'Logout' => 'secondary',
                            'Auto-registro' => 'info',
                            default => 'primary'
                        } ?>">
                            <?= htmlspecialchars($log['accion']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($log['detalles'] ?? '-') ?></td>
                    <td><small><?= htmlspecialchars($log['ip_address']) ?></small></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

</div>

<!-- MODAL NUEVO USUARIO -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Nuevo Usuario</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoUsuario">
                <?php $csrf = generarCSRF(); ?>
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="rol" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="Usuario">Usuario</option>
                            <option value="Ventanilla">Ventanilla</option>
                            <option value="Verificador">Verificador</option>
                            <option value="Calificador">Calificador</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR USUARIO -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Usuario</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarUsuario">
                <?php $csrf = generarCSRF(); ?>
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" id="edit_apellidos" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" id="edit_correo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="rol" id="edit_rol" class="form-select" required>
                            <option value="Usuario">Usuario</option>
                            <option value="Ventanilla">Ventanilla</option>
                            <option value="Verificador">Verificador</option>
                            <option value="Calificador">Calificador</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña (dejar vacío para mantener)</label>
                        <div class="password-container" style="position: relative;">
                            <input type="password" name="password" id="edit_password" class="form-control" minlength="8" style="padding-right: 45px;">
                            <button type="button" class="toggle-password" onclick="togglePassword('edit_password')"
                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                                        background: transparent; border: none; cursor: pointer; padding: 0; width: 30px; height: 30px;
                                        display: flex; align-items: center; justify-content: center; font-size: 18px; color: #7b0f2b; border-radius: 50%;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Gráfica de Trámites
const ctxTramites = document.getElementById('chartTramites').getContext('2d');
new Chart(ctxTramites, {
    type: 'doughnut',
    data: {
        labels: ['En Revisión', 'Aprobados', 'Rechazados'],
        datasets: [{
            data: [<?= $stats_tramites['en_revision'] ?>, <?= $stats_tramites['aprobados'] ?>, <?= $stats_tramites['rechazados'] ?>],
            backgroundColor: ['#ffc107', '#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfica de Usuarios
new Chart(document.getElementById('chartTramitesMensuales'), {
    type: 'bar',
    data: {
        labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        datasets: [{
            label: 'Cantidad de trámites',
            data: <?= json_encode($tramites_por_mes) ?>,
            backgroundColor: '#721832',
            hoverBackgroundColor: '#4b0e22',
            borderRadius: 5,
            maxBarThickness: 48
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {title: {display: true, text: 'Meses'}, grid: {display: false}, ticks: {autoSkip: false, minRotation: 45, maxRotation: 90}},
            y: {beginAtZero: true, suggestedMax: 1, title: {display: true, text: 'Cantidad de trámites'}, ticks: {precision: 0}}
        },
        plugins: {legend: {display: false}}
    }
});

new Chart(document.getElementById('chartTramitesTipos'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($tipos_estadisticas, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
        datasets: [{label: 'Cantidad de trámites', data: <?= json_encode($totales_tipos_estadisticas) ?>,
            backgroundColor: '#287468', borderRadius: 5, maxBarThickness: 32}]
    },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        scales: {
            x: {beginAtZero: true, suggestedMax: 1, ticks: {precision: 0}, title: {display: true, text: 'Cantidad de trámites'}},
            y: {grid: {display: false}, ticks: {autoSkip: false, callback: function(value) {
                const label = this.getLabelForValue(value);
                return label.length > 24 ? label.slice(0, 23) + '…' : label;
            }}}
        },
        plugins: {legend: {display: false}}
    }
});

const ctxUsuarios = document.getElementById('chartUsuarios').getContext('2d');
new Chart(ctxUsuarios, {
    type: 'bar',
    data: {
        labels: ['Admins', 'Verificadores', 'Ventanillas', 'Usuarios'],
        datasets: [{
            label: 'Cantidad',
            data: [<?= $stats_usuarios['admins'] ?>, <?= $stats_usuarios['verificadores'] ?>, <?= $stats_usuarios['ventanillas'] ?>, <?= $stats_usuarios['usuarios'] ?>],
            backgroundColor: ['#dc3545', '#ffc107', '#17a2b8', '#6c757d']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

<script>
(function(){
    var mapEl = document.getElementById('mapa-tramites-admin');
    if (!mapEl || typeof L === 'undefined') return;

    var map = L.map(mapEl, { preferCanvas: true }).setView([22.228, -102.320], 12),
        mensaje = document.getElementById('mapa-tramites-admin-mensaje');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    function esc(v) {
        var d = document.createElement('div');
        d.textContent = String(v == null ? '' : v);
        return d.innerHTML;
    }

    function norm(v) {
        return String(v || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    function cuentaKey(v) {
        return String(v || '').trim().toUpperCase().replace(/\s+/g, '');
    }
    function cuentaDigits(v) {
        return cuentaKey(v).replace(/\D+/g, '');
    }

    function cuentaAliases(v) {
        var raw = cuentaKey(v),
            digits = cuentaDigits(v),
            aliases = [];

        [raw, digits, digits.replace(/^0+/, '')].forEach(function(key) {
            if (key && aliases.indexOf(key) === -1) aliases.push(key);
        });
        return aliases;
    }

    function cuentaPredio(props) {
        props = props || {};
        return props.CVE_CAT_OR || props.cve_cat_or || props.CUENTA_CATASTRAL ||
            props.cuenta_catastral || props.CVE_CAT || props.CLAVE_CAT || props.CLAVE || '';
    }

    function guardarTramite(indice, cuenta, tramite) {
        cuentaAliases(cuenta).forEach(function(key) {
            (indice[key] || (indice[key] = [])).push(tramite);
        });
    }

    function buscarTramites(indice, cuenta) {
        var vistos = Object.create(null), salida = [];
        cuentaAliases(cuenta).forEach(function(key) {
            (indice[key] || []).forEach(function(t) {
                var id = t.ID_TRAMITE || JSON.stringify(t);
                if (!vistos[id]) {
                    vistos[id] = true;
                    salida.push(t);
                }
            });
        });
        return salida;
    }

    function folioAliases(v) {
        var raw = String(v || '').trim().toUpperCase().replace(/\s+/g, '');
        var aliases = [];
        var match;

        function add(key) {
            if (key && aliases.indexOf(key) === -1) aliases.push(key);
        }

        add(raw);
        match = raw.match(/^0*(\d+)\/(\d{4})$/);
        if (match) {
            add(match[1] + '/' + match[2]);
            add(match[1].padStart(3, '0') + '/' + match[2]);
            add(match[1]);
            add(match[1].padStart(3, '0'));
        } else if (/^0*\d+$/.test(raw)) {
            add(String(parseInt(raw, 10)));
            add(String(parseInt(raw, 10)).padStart(3, '0'));
        }
        return aliases;
    }

    function stateIndex(s) {
        s = norm(s);
        if (s.indexOf('rechaz') >= 0 || s.indexOf('correccion') >= 0 || s.indexOf('revision') >= 0) return 0;
        if (s.indexOf('aprobado por verificador') >= 0) return 1;
        if (s.indexOf('pendiente por firmar') >= 0 || s.indexOf('aprobado') >= 0) return 2;
        if (s.indexOf('entregado y archivado') >= 0 || s.indexOf('firmado') >= 0) return 3;
        return 0;
    }

    function flow(t) {
        var labels = ['Recepción', 'Revisión', 'Visto bueno', 'Firma y cierre'];
        var idx = stateIndex(t.ESTATUS);
        var done = idx === 3;

        return '<div class="mapa-admin-progreso">' + labels.map(function(label, i) {
            var cls = done || i < idx ? 'completado' : (i === idx ? 'actual' : '');
            return '<div class="mapa-admin-paso ' + cls + '"><i class="punto"></i>' + esc(label) + '</div>';
        }).join('') + '</div>';
    }

    function one(t) {
        var estatus = norm(t.ESTATUS);
        var concluido = estatus === 'entregado y archivado' || estatus === 'concluido';
        var boton = concluido && t.ID_TRAMITE
            ? '<div class="mt-2"><a class="btn btn-sm btn-outline-danger" target="_blank" rel="noopener" href="php/constancia_mapa.php?id=' + encodeURIComponent(t.ID_TRAMITE) + '"><i class="bi bi-file-earmark-pdf me-1"></i>Descargar constancia firmada</a></div>'
            : '';

        return '<article style="min-width:260px;max-width:360px">' +
            '<div class="fw-bold">Folio ' + esc(t.FOLIO_INGR || 'N/D') + ' · ' + esc(t.ESTATUS || 'Sin estatus') + '</div>' +
            '<div class="small text-muted mb-2">' + esc(t.TIP_TRAMIT || 'Trámite') + ' · ' + esc(t.NOM_SOLI || 'Solicitante sin nombre') + '</div>' +
            flow(t) +
            '<div class="small"><b>Ingreso:</b> ' + esc(t.FECH_INGRE || 'N/D') +
            '<br><b>Cuenta:</b> ' + esc(t.CUENTA_CATASTRAL || 'Sin cuenta') +
            '<br><b>Ubicación:</b> ' + esc(t.UBICACION || 'N/D') +
            (t.FOLIO_SALIDA ? '<br><b>Folio de salida:</b> ' + esc(t.FOLIO_SALIDA) : '') +
            '</div>' + boton + '</article>';
    }

    var estadoPorEstatus = function(s) {
        var n = norm(s);
        if (n.indexOf('entregado y archivado') >= 0 || n === 'firmado') return 3;
        if (n === 'aprobado' || n === 'pendiente por firmar') return 2;
        if (n.indexOf('aprobado por verificador') >= 0) return 1;
        if (n.indexOf('revision') >= 0 || n.indexOf('correccion') >= 0 || n.indexOf('rechaz') >= 0) return 0;
        return -1;
    };

    Promise.all([
        fetch('./Geojson/TRAMITES_reprojected.geojson', {
            credentials: 'same-origin',
            cache: 'no-cache'
        }).then(function(r) {
            if (!r.ok) throw new Error('GeoJSON HTTP ' + r.status);
            return r.json();
        }),
        fetch('./php/get_tramites_geojson.php', {
            credentials: 'same-origin',
            cache: 'no-store'
        }).then(function(r) {
            if (!r.ok) throw new Error('Trámites HTTP ' + r.status);
            return r.json();
        })
    ]).then(function(result) {
        var predios = result[0].features || [];
        var features = result[1].features || [];
        var tramitesPorCuenta = Object.create(null);
        var foliosPorClave = Object.create(null);
        var totalTramites = 0;
        var capaPredios = null;
        var capaSeleccionada = null;

        features.forEach(function(f) {
            var p = f.properties || {};
            var list = Array.isArray(p.TRAMITES) && p.TRAMITES.length ? p.TRAMITES : [p];

            list.forEach(function(t) {
                var cuenta = t.CUENTA_CATASTRAL || p.CUENTA_CATASTRAL;
                if (!cuentaKey(cuenta)) return;
                guardarTramite(tramitesPorCuenta, cuenta, t);
                totalTramites++;
            });
        });

        if (!predios.length) throw new Error('El GeoJSON no contiene polígonos.');

        var limite = L.latLngBounds([]);
        var enMapa = 0;
        var conTramite = 0;

        capaPredios = L.geoJSON({
            type: 'FeatureCollection',
            features: predios
        },
        {
            style: function(feature) {
                var cuenta = cuentaPredio(feature.properties);
                var list = buscarTramites(tramitesPorCuenta, cuenta);
                var steps = list.map(function(t) {
                    return estadoPorEstatus(t.ESTATUS);
                }).filter(function(x) {
                    return x >= 0;
                });
                var state = steps.length ? Math.min.apply(null, steps) : -1;
                var colors = ['#dc3545', '#0d6efd', '#ffc107', '#198754'];

                if (list.length) conTramite++;
                return {
                    color: list.length ? '#495057' : '#adb5bd',
                    weight: list.length ? 1 : .55,
                    opacity: .8,
                    fillColor: state < 0 ? '#e5e7eb' : colors[state],
                    fillOpacity: list.length ? .62 : .24,
                    className: 'mapa-admin-predio'
                };
            },
            onEachFeature: function(feature, layer) {
                var cuenta = cuentaKey(cuentaPredio(feature.properties));
                var list = buscarTramites(tramitesPorCuenta, cuenta);
                var nombre = cuenta || 'Cuenta catastral sin clave';
                var contenidoPopup = '<div class="small text-muted">Sin trámites asociados a esta cuenta.</div>';

                if (list.length) {
                    contenidoPopup = '<div class="small text-muted mb-2">' + list.length +
                        ' trámite(s) en este predio</div>' + list.map(one).join('<hr class="my-2">');
                }

                var popup = '<div style="max-height:400px;overflow:auto;min-width:270px">' +
                    '<div class="fw-bold mb-2">Cuenta catastral ' + esc(nombre) + '</div>' +
                    contenidoPopup + '</div>';

                layer.bindPopup(popup, { maxWidth: 410 });
                if (cuenta) layer.bindTooltip(esc(cuenta), { sticky: true, direction: 'top' });

                list.forEach(function(t) {
                    folioAliases(t.FOLIO_INGR).forEach(function(key) {
                        if (!foliosPorClave[key]) {
                            foliosPorClave[key] = { layer: layer, tramite: t, cuenta: nombre };
                        }
                    });
                });
            }
        }).addTo(map);

        capaPredios.eachLayer(function(layer) {
            if (layer.getBounds) {
                limite.extend(layer.getBounds());
                enMapa++;
            }
        });

        if (limite.isValid()) map.fitBounds(limite, { padding: [12, 12], maxZoom: 16 });
        mensaje.textContent = 'Mostrando ' + enMapa.toLocaleString('es-MX') +
            ' polígonos catastrales · ' + conTramite.toLocaleString('es-MX') +
            ' cuentas con ' + totalTramites.toLocaleString('es-MX') +
            ' trámite(s). Selecciona un polígono para consultar su avance.';
        setTimeout(function() {
            map.invalidateSize();
        }, 200);

        function buscarFolio() {
            var input = document.getElementById('buscador-folio');
            var valor = input ? input.value : '';
            var encontrado = null;

            folioAliases(valor).some(function(key) {
                if (foliosPorClave[key]) {
                    encontrado = foliosPorClave[key];
                    return true;
                }
                return false;
            });

            if (!encontrado) {
                mensaje.textContent = valor.trim()
                    ? 'No se encontró un polígono asociado al folio "' + valor.trim() + '".'
                    : 'Escribe un folio para buscar su polígono en el mapa.';
                if (input) input.focus();
                return;
            }

            if (capaSeleccionada && capaPredios) capaPredios.resetStyle(capaSeleccionada);
            capaSeleccionada = encontrado.layer;
            capaSeleccionada.setStyle({ color: '#111827', weight: 3, fillOpacity: .78 });
            if (capaSeleccionada.bringToFront) capaSeleccionada.bringToFront();
            if (capaSeleccionada.getBounds) map.fitBounds(capaSeleccionada.getBounds(), { padding: [40, 40], maxZoom: 18 });
            capaSeleccionada.openPopup();
            mensaje.textContent = 'Folio ' + (encontrado.tramite.FOLIO_INGR || valor.trim()) + ' localizado en la cuenta catastral ' + (encontrado.cuenta || 'sin clave') + '.';
        }

        var btnBuscarFolio = document.getElementById('boton-buscar-folio');
        var inputBuscarFolio = document.getElementById('buscador-folio');
        if (btnBuscarFolio) btnBuscarFolio.addEventListener('click', buscarFolio);
        if (inputBuscarFolio) {
            inputBuscarFolio.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    buscarFolio();
                }
            });
        }
    }).catch(function(error) {
        console.error('Error cargando polígonos del administrador:', error);
        mensaje.textContent = 'No fue posible cargar los polígonos o asociar los trámites. Verifica el GeoJSON y vuelve a cargar el panel.';
    });
})();
</script>
<script src="js/admin.js"></script>
<script src="js/dashboard-ui.js?v=<?= filemtime(__DIR__ . '/js/dashboard-ui.js') ?>"></script>
<script src="assets/vendor/pdf-lib/pdf-lib.min.js"></script>
<script type="module" src="js/admin-oficios.js?v=<?= filemtime(__DIR__ . '/js/admin-oficios.js') ?>"></script>
<script src="js/actualizar-shp.js?v=<?= filemtime(__DIR__ . '/js/actualizar-shp.js') ?>"></script>
<script>
$(document).ready(function() {
    $('#tablaAprobados').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json',
            emptyTable: '<i class="bi bi-inbox fs-3 d-block mb-2"></i> No hay trámites aprobados aún.'
        },
        order: [[7, 'desc']],
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: 8 }]
    });
    $('#tablaSolicitudes').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json' },
        order: [[6, 'asc'], [5, 'desc']],
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: 7 }]
    });
});
</script>

<script>
// ── CSRF token para solicitudes ──
var csrfToken = '<?php echo isset($_SESSION["csrf_token"]) ? $_SESSION["csrf_token"] : ""; ?>';

// ── APROBAR solicitud ──
document.querySelectorAll('.btn-aprobar-sol').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id     = btn.dataset.id;
        var nombre = btn.dataset.nombre;
        var rol    = btn.dataset.rol;

        Swal.fire({
            title: '¿Aprobar solicitud?',
            html: 'Se creará la cuenta de <strong>' + nombre + '</strong> con rol <strong>' + rol + '</strong>.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

            var fd = new FormData();
            fd.append('csrf_token', csrfToken);
            fd.append('accion', 'aprobar');
            fd.append('sol_id', id);

            fetch('php/gestion_solicitudes.php', { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    Swal.close();
                    _abrirNotifSol(data);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            })
            .catch(function(e) {
                Swal.fire({ icon: 'error', title: 'Error de conexión', text: e.message });
            });
        });
    });
});

// ── RECHAZAR solicitud ──
document.querySelectorAll('.btn-rechazar-sol').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id     = btn.dataset.id;
        var nombre = btn.dataset.nombre;

        Swal.fire({
            title: 'Rechazar solicitud de ' + nombre,
            html: '<label class="form-label">Motivo del rechazo (opcional):</label>' +
                  '<textarea id="motivoRechazo" class="swal2-textarea" placeholder="Ej: Documentación incompleta..."></textarea>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Rechazar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            preConfirm: function() {
                return document.getElementById('motivoRechazo').value;
            }
        }).then(function(result) {
            if (!result.isConfirmed) return;

            var motivo = result.value || '';
            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

            var fd = new FormData();
            fd.append('csrf_token', csrfToken);
            fd.append('accion', 'rechazar');
            fd.append('sol_id', id);
            fd.append('motivo', motivo);

            fetch('php/gestion_solicitudes.php', { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    Swal.close();
                    _abrirNotifSol(data);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            })
            .catch(function(e) {
                Swal.fire({ icon: 'error', title: 'Error de conexión', text: e.message });
            });
        });
    });
});

// ── Modal de notificación al solicitante ──
function _abrirNotifSol(data) {
    var accion = data.accion === 'aprobado' ? 'APROBADA ✅' : 'RECHAZADA ❌';
    document.getElementById('notif-sol-desc').innerHTML =
        'Solicitud de <strong>' + data.nombre + '</strong> ' + accion;

    if (data.mensaje) {
        document.getElementById('notif-sol-texto').textContent = data.mensaje;
        document.getElementById('notif-sol-preview').style.display = 'block';
    }

    var waEl = document.getElementById('notif-sol-wa');
    var gmEl = document.getElementById('notif-sol-gm');

    if (data.wa_link) {
        waEl.href = data.wa_link;
        waEl.style.opacity = '1'; waEl.style.pointerEvents = 'auto';
        waEl.querySelector('.notif-sol-sub').textContent = data.telefono ? 'Enviar a: ' + data.telefono : 'Abrir WhatsApp';
    } else {
        waEl.href = '#'; waEl.style.opacity = '0.35'; waEl.style.pointerEvents = 'none';
        waEl.querySelector('.notif-sol-sub').textContent = 'Sin número de teléfono registrado';
    }

    if (data.gm_link) {
        gmEl.href = data.gm_link;
        gmEl.style.opacity = '1'; gmEl.style.pointerEvents = 'auto';
        gmEl.querySelector('.notif-sol-sub').textContent = data.correo ? 'Enviar a: ' + data.correo : 'Abrir correo';
    } else {
        gmEl.href = '#'; gmEl.style.opacity = '0.35'; gmEl.style.pointerEvents = 'none';
        gmEl.querySelector('.notif-sol-sub').textContent = 'Sin correo registrado';
    }

    var modalEl = document.getElementById('notifSolModal');
    modalEl.removeAttribute('aria-hidden');
    new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false }).show();
}

// Al cerrar el modal → recargar para actualizar la tabla
document.getElementById('btnCerrarNotifSol').addEventListener('click', function() {
    location.reload();
});
</script>

<!-- Gráfica de barras: trámites por mes -->
<script>
(function() {
    var meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    var totales  = [<?php for($m=1;$m<=12;$m++) echo (isset($datos_mes[$m]) ? $datos_mes[$m]['total'] : 0).($m<12?',':''); ?>];
    var aprobados= [<?php for($m=1;$m<=12;$m++) echo (isset($datos_mes[$m]) ? $datos_mes[$m]['aprobados'] : 0).($m<12?',':''); ?>];
    var rechazados=[<?php for($m=1;$m<=12;$m++) echo (isset($datos_mes[$m]) ? $datos_mes[$m]['rechazados'] : 0).($m<12?',':''); ?>];

    var canvasEl = document.getElementById('chartReporteMes');
    if (!canvasEl) return;
    new Chart(canvasEl.getContext('2d'), {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [
                { label: 'Total',      data: totales,   backgroundColor: 'rgba(13,110,253,.7)',  borderRadius: 4 },
                { label: 'Aprobados',  data: aprobados, backgroundColor: 'rgba(25,135,84,.7)',   borderRadius: 4 },
                { label: 'Rechazados', data: rechazados,backgroundColor: 'rgba(220,53,69,.7)',   borderRadius: 4 }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' }, title: { display: true, text: 'Trámites por mes — <?= $anio_filtro ?>' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
})();

function imprimirReporte() {
    var t1 = document.getElementById('tablaReporteMes');
    var t2 = document.getElementById('tablaReporteTipo');

    if (!t1) { alert('No hay datos para imprimir.'); return; }

    function limpiarTabla(tabla) {
        var clon = tabla.cloneNode(true);
        clon.className = 'reporte-tabla';
        clon.querySelectorAll('[style]').forEach(function(el) {
            if (!el.classList.contains('progress-bar')) el.removeAttribute('style');
        });
        return clon.outerHTML;
    }

    var tabla1 = limpiarTabla(t1);
    var tabla2 = t2 ? '<h3>Trámites por tipo</h3>' + limpiarTabla(t2) : '';
    var fecha = new Date().toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' });
    var hora = new Date().toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });

    var w = window.open('', '_blank');
    if (!w) { alert('El navegador bloqueó la ventana de impresión. Permite ventanas emergentes para este sitio.'); return; }

    w.document.open();
    w.document.write([
        '<!doctype html><html lang="es"><head><meta charset="UTF-8">',
        '<title>Reporte de trámites <?= $anio_filtro ?></title>',
        '<style>',
        '@page{size:letter;margin:12mm;}',
        '*{box-sizing:border-box;}',
        'body{font-family:Arial,Helvetica,sans-serif;margin:0;color:#222;background:#fff;font-size:12px;}',
        '.hoja{max-width:980px;margin:0 auto;padding:18px;}',
        '.encabezado{display:flex;align-items:center;gap:16px;border-bottom:3px solid #7b0f2b;padding-bottom:12px;margin-bottom:16px;}',
        '.logos{display:flex;gap:10px;align-items:center;min-width:128px;}',
        '.logos img{height:58px;max-width:78px;object-fit:contain;}',
        '.titulo{flex:1;text-align:center;}',
        'h1{font-size:20px;color:#7b0f2b;margin:0 0 4px;text-transform:uppercase;letter-spacing:.02em;}',
        'h2{font-size:15px;margin:0;color:#333;font-weight:700;}',
        'h3{font-size:15px;margin:22px 0 8px;color:#7b0f2b;border-left:4px solid #7b0f2b;padding-left:8px;}',
        '.meta{font-size:11px;color:#666;margin-top:6px;}',
        '.resumen{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin:14px 0 18px;}',
        '.card{border:1px solid #d8d8d8;border-radius:8px;padding:10px;text-align:center;background:#fafafa;}',
        '.card strong{display:block;font-size:22px;color:#7b0f2b;line-height:1;}',
        '.card span{display:block;font-size:10px;color:#555;text-transform:uppercase;margin-top:5px;}',
        '.reporte-tabla{width:100%;border-collapse:collapse;margin-top:8px;font-size:11px;page-break-inside:auto;}',
        '.reporte-tabla tr{page-break-inside:avoid;page-break-after:auto;}',
        '.reporte-tabla th,.reporte-tabla td{border:1px solid #b9b9b9;padding:6px 7px;text-align:center;vertical-align:middle;}',
        '.reporte-tabla th{background:#7b0f2b;color:#fff;font-weight:700;}',
        '.reporte-tabla td:first-child,.reporte-tabla th:first-child{text-align:left;}',
        '.reporte-tabla tfoot td{background:#ededed;font-weight:700;}',
        '.badge{display:inline-block;min-width:26px;padding:2px 7px;border-radius:999px;font-size:10px;font-weight:700;color:#111;border:1px solid #bbb;background:#f6f6f6;}',
        '.bg-primary,.bg-success,.bg-warning,.bg-info,.bg-danger,.bg-dark{background:#f6f6f6!important;color:#111!important;}',
        '.text-muted{color:#777!important;}',
        '.progress{display:inline-block;width:80px;height:9px;background:#e8e8e8;border:1px solid #cfcfcf;border-radius:999px;vertical-align:middle;overflow:hidden;}',
        '.progress-bar{display:block;height:100%;background:#7b0f2b!important;}',
        '.pie{margin-top:18px;padding-top:8px;border-top:1px solid #ddd;color:#666;font-size:10px;display:flex;justify-content:space-between;gap:10px;}',
        '.acciones{position:sticky;bottom:0;background:#fff;border-top:1px solid #ddd;padding:12px 0;margin-top:18px;text-align:right;}',
        '.btn{border:0;border-radius:6px;padding:8px 16px;cursor:pointer;font-weight:700;}',
        '.btn-print{background:#7b0f2b;color:#fff;}',
        '.btn-close{background:#e9ecef;color:#222;margin-right:8px;}',
        '@media print{.hoja{max-width:none;padding:0}.acciones{display:none}.encabezado{break-inside:avoid}.resumen{break-inside:avoid}body{-webkit-print-color-adjust:exact;print-color-adjust:exact;}}',
        '@media(max-width:760px){.resumen{grid-template-columns:repeat(2,1fr)}.encabezado{flex-direction:column}.titulo{text-align:center}}',
        '</style></head><body><main class="hoja">',
        '<header class="encabezado"><div class="logos">',
        '<img src="logos/logo_urbano.jpeg" alt="Planeación" onerror="this.style.display=\'none\'">',
        '<img src="logos/logo_presi.jpeg" alt="Presidencia" onerror="this.style.display=\'none\'">',
        '</div><div class="titulo"><h1>Dirección de Planeación y Desarrollo Urbano</h1>',
        '<h2>Reporte de trámites <?= $anio_filtro ?></h2>',
        '<div class="meta">Generado el ' + fecha + ' a las ' + hora + '</div></div></header>',
        '<section class="resumen" aria-label="Resumen del reporte">',
        '<div class="card"><strong><?= (int)$tot_año ?></strong><span>Total <?= (int)$anio_filtro ?></span></div>',
        '<div class="card"><strong><?= (int)$apr_año ?></strong><span>Aprobados</span></div>',
        '<div class="card"><strong><?= (int)$rev_año ?></strong><span>En revisión</span></div>',
        '<div class="card"><strong><?= (int)$cor_año ?></strong><span>En corrección</span></div>',
        '<div class="card"><strong><?= (int)$rec_año ?></strong><span>Rechazados</span></div>',
        '</section>',
        '<p class="meta">Total histórico acumulado: <strong><?= (int)$total_global ?> trámites</strong>.</p>',
        '<h3>Trámites por mes</h3>', tabla1, tabla2,
        '<footer class="pie"><span>SisDiT · Panel de administración</span><span>Reporte anual <?= (int)$anio_filtro ?></span></footer>',
        '<div class="acciones"><button class="btn btn-close" onclick="window.close()">Cerrar</button><button class="btn btn-print" onclick="window.print()">Imprimir / Guardar PDF</button></div>',
        '</main></body></html>'
    ].join(''));
    w.document.close();
    w.focus();
    setTimeout(function() { w.print(); }, 400);
}
// Función para mostrar/ocultar contraseña
function togglePassword(inputId) {
    var input = document.getElementById(inputId);
    var icon = input.nextElementSibling.querySelector('i');

    if (!icon) {
        // Si el icono no está en el nextElementSibling, buscarlo de otra forma
        icon = document.querySelector('#' + inputId + ' + .toggle-password i');
        if (!icon) {
            var btn = input.nextElementSibling;
            if (btn && btn.classList.contains('toggle-password')) {
                icon = btn.querySelector('i');
            }
        }
    }

    if (input.type === "password") {
        input.type = "text";
        if (icon) icon.className = "fas fa-eye-slash";
    } else {
        input.type = "password";
        if (icon) icon.className = "fas fa-eye";
    }
}
</script>

</body>
</html>
