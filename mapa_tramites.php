<?php
require "seguridad.php";
require_once "php/funciones_seguridad.php";

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['Ventanilla', 'Administrador'])) {
    header("Location: acceso.php?error=no_autorizado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Trámites - SisDiT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background-color: #7b0f2b !important; }
        .tramite-box { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 20px; }
        #mapaTramites { height: 600px; border-radius: 8px; }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="DashVentanilla.php">
                <i class="bi bi-house-door me-2"></i>
                <span>SisDiT - Ventanilla</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="DashVentanilla.php">
                            <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="logout.php?csrf_token=<?= urlencode($_SESSION['csrf_token']) ?>">
                            <i class="bi bi-box-arrow-right me-1"></i> Salir
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <div class="container-fluid mt-4">
        <section class="tramite-box">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0" style="color:#7b0f2b;"><i class="bi bi-map-pin me-2"></i>Mapa de Trámites</h4>
            </div>
            <div class="alert alert-info py-2 mb-3" style="font-size:.85rem;">
                <i class="bi bi-info-circle me-2"></i>Mapa interactivo mostrando la ubicación de todos los trámites registrados.
            </div>
            <div id="mapaTramites"></div>
        </section>
    </div>

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/proj4@2.9.0/dist/proj4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.js"></script>

    <script>
        // Centro del municipio
        const CENTRO_MUNICIPIO = [22.228, -102.320];

        // Inicializar mapa
        const mapaTramites = L.map('mapaTramites').setView(CENTRO_MUNICIPIO, 12);

        // Capa base
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(mapaTramites);

        function escaparHtml(valor) {
            const div = document.createElement('div');
            div.textContent = String(valor ?? '');
            return div.innerHTML;
        }

        // Cargar los trámites actuales desde la base de datos.
        fetch('./php/get_tramites_geojson.php', { credentials: 'same-origin', cache: 'no-store' })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                L.geoJSON(data, {
                    pointToLayer: function(feature, latlng) {
                        const props = feature.properties;
                        const estado = String(props.ESTATUS || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                        const color = estado === 'en revision' ? '#dc3545'
                            : (estado === 'aprobado por verificador' ? '#ffc107'
                            : (estado === 'aprobado' ? '#198754' : '#6c757d'));
                        const marker = L.circleMarker(latlng, {
                            radius: 7, color: '#fff', weight: 2,
                            fillColor: color, fillOpacity: .92
                        });
                        const popupContent = `
                            <div style="max-width: 300px;">
                                <h6 class="mb-2"><i class="bi bi-file-earmark-text me-1"></i>Trámite ${escaparHtml(props.FOLIO_INGR || 'N/A')}</h6>
                                <strong>Solicitante:</strong> ${escaparHtml(props.NOM_SOLI || 'N/A')}<br>
                                <strong>Tipo de Trámite:</strong> ${escaparHtml(props.TIP_TRAMIT || 'N/A')}<br>
                                <strong>Ubicación:</strong> ${escaparHtml(props.UBICACION || 'N/A')}<br>
                                <strong>Fecha Ingreso:</strong> ${escaparHtml(props.FECH_INGRE || 'N/A')}<br>
                                <strong>Fecha Entrega:</strong> ${escaparHtml(props.FECH_ENTRE || 'N/A')}<br>
                                <strong>Estatus:</strong> ${escaparHtml(props.ESTATUS || 'N/A')}<br>
                                <strong>UTM X:</strong> ${Number.isFinite(props.X) ? props.X.toFixed(2) : 'N/A'}<br>
                                <strong>UTM Y:</strong> ${Number.isFinite(props.Y) ? props.Y.toFixed(2) : 'N/A'}<br>
                                <strong>Contacto:</strong> ${escaparHtml(props.CONTACTO || 'N/A')}<br>
                                <strong>Número:</strong> ${escaparHtml(props.NUMERO || 'N/A')}
                            </div>
                        `;
                        marker.bindPopup(popupContent);
                        return marker;
                    }
                }).addTo(mapaTramites);
            })
            .catch(error => {
                console.error('Error cargando TRAMITES.geojson:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar el mapa de trámites.',
                    confirmButtonColor: '#7b0f2b'
                });
            });
    </script>
</body>
</html>
