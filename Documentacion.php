<?php
ini_set('session.cookie_httponly', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "php/funciones_seguridad.php";

if (!isset($_SESSION['id']) || !isset($_SESSION['usuario'])) {
    header("Location: acceso.php");
    exit();
}

$csrf = generarCSRF();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación</title>
    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS PROPIO -->
    <link rel="stylesheet" href="./css/style.css?v=<?= time() ?>">
    <style>
        .documentation-container {
            padding: 20px;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .documentation-header {
            color: #7b0f2b;
            margin-bottom: 20px;
        }
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        .dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
        <!-- card de documentos segun los que se tienen guardados en la base de datos con apartado de anexar adicional -->
    <div class="container">
        <div class="documentation-container">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
                <h1 class="documentation-header mb-0">Documentación</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                    <i class="bi bi-plus-circle me-2"></i>Añadir Documentación
                </button>
            </div>
            <div id="documentation-list" class="list-group">
                <p class="text-center text-muted empty-state">No se encontró documentación para mostrar. Añade una nueva.</p>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #7b0f2b; color: white;">
                    <h5 class="modal-title" id="addDocumentModalLabel">Añadir Nueva Documentación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadDocumentForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" id="folioInput" name="folio">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="documentType" class="form-label">Tipo de Documento</label>
                            <select class="form-select" id="documentType" name="documentType" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="ine">INE / Identificación</option>
                                <option value="escritura">Escritura / Título</option>
                                <option value="predial">Boleta Predial</option>
                                <option value="formato">Formato de Constancia</option>
                                <option value="oficio_vobo">Oficio Visto Bueno</option>
                                <option value="contrato_arrendamiento">Contrato de Arrendamiento o Escritura</option>
                                <option value="memoria_descriptiva">Memoria Descriptiva / Cálculo de Superficie</option>
                                <option value="poder_notariado">Poder Notariado</option>
                                <option value="acta_constitutiva">Acta Constitutiva</option>
                                <option value="solicitud_por_escrito">Solicitud por Escrito</option>
                                <option value="licencia_de_construccion">Licencia de Construcción</option>
                                <option value="bitacora_de_obra">Bitácora de Obra</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="documentFile" class="form-label">Archivo de Documentación (PDF, DOCX, JPG, PNG)</label>
                            <input type="file" class="form-control" id="documentFile" name="documentFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            <div class="form-text">El archivo se guardará en la carpeta privada del folio.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Subir Documento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
            const uploadForm = document.getElementById('uploadDocumentForm');
            if (uploadForm) {
                uploadForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const formData = new FormData(this);
            const documentType = formData.get('documentType');
            // get file safely from input element
            const fileInput = document.getElementById('documentFile');
            const documentFile = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

            if (!documentFile) {
                alert('Por favor selecciona un archivo antes de subir.');
                return;
            }

            // Log upload info safely
            console.log('Uploading document:', documentType, documentFile.name);

            try {
                const response = await fetch('save_document.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    const text = await response.text();
                    let message = `HTTP error! status: ${response.status}`;
                    try {
                        const errorResult = JSON.parse(text);
                        if (errorResult && errorResult.message) {
                            message = errorResult.message;
                        }
                    } catch (parseError) {}
                    throw new Error(message);
                }

                const text = await response.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    const errorLineMatch = text.match(/line <b>(\d+)<\/b>/i);
                    const errorLine = errorLineMatch ? errorLineMatch[1] : 'desconocida';
                    console.error('Invalid JSON response from save_document.php:', text);
                    console.error('Parse error:', parseError.message);
                    alert('Error en el servidor. Revisa la consola y valida save_document.php (línea ' + errorLine + ')');
                    return;
                }

                if (result && result.success) {
                    alert('Documento subido exitosamente: ' + result.message);
                    if (folio) {
                        loadDocuments(folio);
                    }
                        if (typeof bootstrap !== 'undefined') {
                            const modalEl = document.getElementById('addDocumentModal');
                            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            try { modal.hide(); } catch(e) { console.warn('Could not hide modal', e); }
                        }
                    this.reset();
                } else {
                    alert('Error al subir el documento: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Hubo un error al subir el documento.');
            }
        });
            }

        // Get folio from URL and set it in the hidden input
        const urlParams = new URLSearchParams(window.location.search);
        const folio = urlParams.get('folio');
            if (folio) {
                const folioInput = document.getElementById('folioInput');
                if (folioInput) folioInput.value = folio;
                loadDocuments(folio);
            }

        async function loadDocuments(folio) {
            try {
                const response = await fetch(`fetch_documents.php?folio=${encodeURIComponent(folio)}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();

                const list = document.getElementById('documentation-list');
                const emptyState = list.querySelector('.empty-state');
                if (emptyState) {
                    emptyState.remove();
                }

                if (result.success && result.documents.length > 0) {
                    result.documents.forEach(doc => addDocumentToList(doc));
                } else if (result.success && result.documents.length === 0) {
                    list.innerHTML = '<p class="text-center text-muted empty-state">No se encontró documentación para mostrar. Añade una nueva.</p>';
                } else {
                    console.error('Error loading documents:', result.message);
                    list.innerHTML = '<p class="text-center text-danger empty-state">Error al cargar la documentación.</p>';
                }
            } catch (error) {
                console.error('Error fetching documents:', error);
                const list = document.getElementById('documentation-list');
                list.innerHTML = '<p class="text-center text-danger empty-state">Error al cargar la documentación.</p>';
            }
        }

        function addDocumentToList(doc) {
            const list = document.getElementById('documentation-list');
            const emptyState = list.querySelector('.empty-state');
            if (emptyState) {
                emptyState.remove();
            }

            // Map document types to user-friendly titles
            const documentTypeTitles = {
                'ine': 'INE / Identificación',
                'escritura': 'Escritura / Título',
                'predial': 'Boleta Predial',
                'formato': 'Formato de Constancia',
                'oficio_vobo': 'Oficio Visto Bueno',
                'contrato_arrendamiento': 'Contrato de Arrendamiento o Escritura',
                'memoria_descriptiva': 'Memoria Descriptiva / Cálculo de Superficie',
                'poder_notariado': 'Poder Notariado',
                'acta_constitutiva': 'Acta Constitutiva',
                'solicitud_por_escrito': 'Solicitud por Escrito',
                'licencia_de_construccion': 'Licencia de Construcción',
                'bitacora_de_obra': 'Bitácora de Obra',
                'foto1': 'Fotografía 1 del Inmueble',
                'foto2': 'Fotografía 2 del Inmueble'
            };
            const displayTitle = doc.label || documentTypeTitles[doc.type] || doc.type;

            if (!doc.filePath || !doc.filePath.startsWith('uploads/')) {
                return;
            }

            const cardItem = document.createElement('div');
            cardItem.classList.add('card', 'card-documento', 'mb-3');

            const cardBody = document.createElement('div');
            cardBody.classList.add('card-body');

            const title = document.createElement('h5');
            title.classList.add('card-title');
            title.textContent = displayTitle;

            const file = document.createElement('p');
            file.classList.add('card-text');
            file.textContent = `Archivo: ${doc.fileName || ''}`;

            const link = document.createElement('a');
            link.href = doc.filePath;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.classList.add('btn', 'btn-sm', 'btn-outline-primary');
            link.innerHTML = '<i class="bi bi-eye me-2"></i>Ver Documento';

            cardBody.append(title, file, link);
            cardItem.appendChild(cardBody);
            list.appendChild(cardItem);
        }
    </script>
</body>
</html>