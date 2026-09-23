<?php
// Incluido exclusivamente después de autenticar el panel administrador.
if (!isset($conn) || !function_exists('esAdministrador') || !esAdministrador()) {
    http_response_code(403);
    exit;
}
$oficios = $conn->query("SELECT t.id, t.folio_numero, t.folio_anio, t.folio_salida_numero, t.folio_salida_anio,
    t.propietario, t.estatus, t.fecha_aprobacion_director, t.tiempo_salida, tt.nombre AS tipo
    FROM tramites t LEFT JOIN tipos_tramite tt ON tt.id = t.tipo_tramite_id
    WHERE t.estatus IN ('Pendiente por firmar', 'Firmado', 'Entregado y archivado')
    ORDER BY FIELD(t.estatus, 'Pendiente por firmar', 'Firmado', 'Entregado y archivado'), t.id DESC")->fetch_all(MYSQLI_ASSOC);
$conteosOficios = array_count_values(array_column($oficios, 'estatus'));
?>
<section id="oficios-digitales" class="oficios-panel mb-4" aria-labelledby="oficios-titulo">
    <div class="oficios-cabecera">
        <div><span class="oficios-eyebrow">AUTORIZACIÓN Y ARCHIVO</span>
            <h2 id="oficios-titulo">Firma y cierre de oficios</h2>
            <p>Coloca tu firma en el PDF, revisa el documento y cierra el expediente al entregarlo.</p>
        </div>
        <span class="oficios-sello"><i class="bi bi-pen"></i> Administración</span>
    </div>
    <div class="oficios-resumen" role="group" aria-label="Filtrar oficios por estado">
        <?php foreach ([['Pendiente por firmar', 'Por firmar', 'pen'], ['Firmado', 'Listos para cerrar', 'file-earmark-check'], ['Entregado y archivado', 'Cerrados', 'archive']] as [$estado, $titulo, $icono]): ?>
        <button type="button" class="oficios-filtro <?= $estado === 'Pendiente por firmar' ? 'activo' : '' ?>" data-estado="<?= e($estado) ?>" aria-pressed="<?= $estado === 'Pendiente por firmar' ? 'true' : 'false' ?>">
            <i class="bi bi-<?= $icono ?>"></i><span><?= $titulo ?><strong><?= $conteosOficios[$estado] ?? 0 ?></strong></span>
        </button>
        <?php endforeach; ?>
    </div>
    <div class="p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <p class="text-muted small mb-0">Cada acción corresponde a un oficio individual y conserva su historial.</p>
            <button type="button" class="btn btn-sm btn-outline-secondary oficios-filtro-todos">Ver todos</button>
        </div>
        <div class="table-responsive">
            <table id="tablaOficiosAdmin" class="table table-hover align-middle w-100">
                <thead><tr><th>Folio / registro</th><th>Oficio</th><th>Propietario</th><th>Estado</th><th>Firma / cierre</th><th>Acciones</th></tr></thead>
                <tbody>
                <?php foreach ($oficios as $oficio): ?>
                    <tr>
                        <td data-label="Folio / registro"><strong><?= e(str_pad((string)$oficio['folio_numero'], 3, '0', STR_PAD_LEFT) . '/' . $oficio['folio_anio']) ?></strong><small class="d-block text-muted">Registro #<?= (int)$oficio['id'] ?></small></td>
                        <td data-label="Oficio"><?= e($oficio['tipo'] ?? 'Trámite') ?><small class="d-block text-muted">Salida: <?= !empty($oficio['folio_salida_numero']) ? e($oficio['folio_salida_numero'] . '/' . $oficio['folio_salida_anio']) : 'Por asignar' ?></small></td>
                        <td data-label="Propietario"><?= e($oficio['propietario'] ?? '') ?></td>
                        <td data-label="Estado"><span class="oficio-estado <?= $oficio['estatus'] === 'Pendiente por firmar' ? 'pendiente' : 'completo' ?>"><?= e($oficio['estatus']) ?></span></td>
                        <td data-label="Firma / cierre"><?= e(($oficio['estatus'] === 'Entregado y archivado' ? $oficio['tiempo_salida'] : $oficio['fecha_aprobacion_director']) ?: 'Pendiente') ?></td>
                        <td data-label="Acciones"><button type="button" class="btn btn-sm <?= $oficio['estatus'] === 'Pendiente por firmar' ? 'btn-primary' : 'btn-outline-primary' ?> abrir-oficio" data-id="<?= (int)$oficio['id'] ?>">
                            <i class="bi bi-<?= $oficio['estatus'] === 'Pendiente por firmar' ? 'pen' : ($oficio['estatus'] === 'Firmado' ? 'archive' : 'eye') ?>"></i>
                            <?= $oficio['estatus'] === 'Pendiente por firmar' ? 'Revisar y firmar' : ($oficio['estatus'] === 'Firmado' ? 'Revisar y cerrar' : 'Ver expediente') ?>
                        </button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="modal fade" id="modalOficioAdmin" tabindex="-1" aria-labelledby="tituloOficioAdmin" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header">
            <span class="oficio-header-icon" aria-hidden="true"><i class="bi bi-file-earmark-text"></i></span>
            <div class="oficio-header-text"><span class="oficio-kicker">ADMINISTRACIÓN · SISDIT</span><h3 class="modal-title" id="tituloOficioAdmin">Expediente del oficio</h3><small id="oficioReferencia" class="text-muted"></small></div>
            <span id="oficioEstadoCabecera" class="oficio-status-pill" hidden></span>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar expediente"></button>
        </div>
        <form id="formOficioAdmin" class="oficio-modal-form" enctype="multipart/form-data">
        <nav class="oficio-tabs nav" id="oficioTabs" role="tablist" aria-label="Secciones del expediente" hidden>
            <button class="nav-link active" id="oficioTabResumen" type="button" data-bs-toggle="tab" data-bs-target="#oficioPanelResumen" role="tab" aria-controls="oficioPanelResumen" aria-selected="true"><i class="bi bi-folder2-open" aria-hidden="true"></i>Expediente</button>
            <button class="nav-link" id="oficioTabAccion" type="button" data-bs-toggle="tab" data-bs-target="#oficioPanelAccion" role="tab" aria-controls="oficioPanelAccion" aria-selected="false"><i class="bi bi-pen" aria-hidden="true"></i><span id="oficioTabAccionTexto">Firmar PDF</span></button>
            <button class="nav-link" id="oficioTabHistorial" type="button" data-bs-toggle="tab" data-bs-target="#oficioPanelHistorial" role="tab" aria-controls="oficioPanelHistorial" aria-selected="false"><i class="bi bi-clock-history" aria-hidden="true"></i>Historial<span id="oficioHistorialCantidad" class="oficio-contador">0</span></button>
        </nav>
        <div class="modal-body">
            <div id="oficioMensaje" class="alert alert-info" role="status" aria-live="polite">Cargando expediente…</div>
            <div id="oficioContenido" class="tab-content" hidden>
                <section class="tab-pane show active" id="oficioPanelResumen" role="tabpanel" aria-labelledby="oficioTabResumen" tabindex="0">
                    <div class="oficio-section-heading"><div><h4>Datos del expediente</h4><p>Información registrada para este oficio.</p></div><i class="bi bi-card-heading" aria-hidden="true"></i></div>
                    <div class="oficio-ficha" id="oficioFicha"></div>
                    <div class="oficio-section-heading oficio-doc-heading"><div><h4>Documentos del expediente <span id="oficioDocumentosCantidad" class="oficio-contador">0</span></h4><p id="oficioDocumentosAyuda">Consulta los archivos conservados en este expediente.</p></div></div>
                    <div id="oficioDocumentos" class="oficio-documentos-grid"></div>
                    <div id="oficioPlantilla" class="oficio-plantilla"></div>
                </section>
                <section class="tab-pane" id="oficioPanelHistorial" role="tabpanel" aria-labelledby="oficioTabHistorial" tabindex="0">
                    <div class="oficio-section-heading"><div><h4>Actividad del oficio</h4><p>Movimientos más recientes, con fecha y responsable.</p></div><i class="bi bi-clock-history" aria-hidden="true"></i></div>
                    <ol id="oficioHistorial" class="oficio-historial"></ol>
                </section>
                <section class="tab-pane" id="oficioPanelAccion" role="tabpanel" aria-labelledby="oficioTabAccion" tabindex="0">
                    <input type="hidden" name="csrf_token" value="<?= e(generarCSRF()) ?>">
                    <input type="hidden" name="id"><input type="hidden" name="accion"><input type="hidden" name="estado_esperado">
                    <fieldset id="oficioCampos">
                        <div id="editorFirma" hidden>
                            <div class="oficio-section-heading"><div><h4>Prepara la firma del oficio</h4><p>Carga el PDF, coloca tu firma y revisa el resultado antes de guardar.</p></div></div>
                            <div class="oficio-editor-grid">
                                <div class="oficio-editor-controles">
                                    <div class="oficio-paso"><div class="oficio-paso-titulo"><span>1</span><h5>Documento</h5></div>
                                    <label for="firmaPdf" class="form-label fw-semibold">PDF del oficio</label>
                                    <input type="file" id="firmaPdf" class="form-control" accept="application/pdf,.pdf">
                                    <p class="form-text">Máximo 10 MiB. También puedes usar un PDF del expediente.</p>
                                    </div>
                                    <div class="oficio-paso"><div class="oficio-paso-titulo"><span>2</span><h5>Tu firma</h5></div>
                                    <label for="firmaImagen" class="form-label fw-semibold">Cargar imagen de firma</label>
                                    <input type="file" id="firmaImagen" class="form-control" accept="image/png,image/jpeg,.png,.jpg,.jpeg">
                                    <p class="form-text">PNG o JPG, máximo 2 MiB.</p>
                                    <div class="oficio-divisor"><span>o dibuja tu firma</span></div>
                                    <canvas id="firmaDibujo" width="720" height="220" aria-label="Área para dibujar la firma"></canvas>
                                    <div class="d-flex gap-2 mt-2 mb-3"><button id="usarFirmaDibujo" type="button" class="btn btn-sm btn-outline-primary">Usar dibujo</button><button id="limpiarFirmaDibujo" type="button" class="btn btn-sm btn-outline-secondary">Limpiar</button></div>
                                    </div>
                                    <div class="oficio-paso"><div class="oficio-paso-titulo"><span>3</span><h5>Ubicación y tamaño</h5></div>
                                    <label for="firmaPagina" class="form-label">Página a firmar</label><select id="firmaPagina" class="form-select mb-2" disabled><option>Carga un PDF</option></select>
                                    <label for="firmaTamano" class="form-label">Tamaño de firma</label><input id="firmaTamano" type="range" min="10" max="50" value="25" class="form-range">
                                    <label for="firmaHorizontal" class="form-label">Posición horizontal</label><input id="firmaHorizontal" type="range" min="0" max="100" value="50" class="form-range">
                                    <label for="firmaVertical" class="form-label">Posición vertical</label><input id="firmaVertical" type="range" min="0" max="100" value="80" class="form-range">
                                    <p class="form-text">También puedes arrastrar la firma sobre el documento.</p>
                                    </div>
                                </div>
                                <div class="oficio-editor-preview"><div class="oficio-preview-titulo"><span><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> Vista previa del oficio</span><small>Posición de la firma</small></div><div id="firmaEstado" class="alert alert-light border small" role="status" aria-live="polite">Carga un PDF para ver la página.</div>
                                    <div id="firmaVacia" class="oficio-preview-vacia"><i class="bi bi-file-earmark-pdf" aria-hidden="true"></i><strong>Tu documento aparecerá aquí</strong><p>Carga un PDF o selecciónalo desde la pestaña Expediente.</p></div>
                                    <div class="firma-papel-contenedor"><div id="firmaPapel"><canvas id="firmaPaginaCanvas"></canvas><img id="firmaSello" alt="Vista previa de tu firma" hidden draggable="false"></div></div>
                                    <div class="oficio-pdf-toolbar"><button type="button" id="prepararFirmaPdf" class="btn btn-outline-primary" disabled><i class="bi bi-file-earmark-pdf"></i> Preparar PDF firmado</button><a id="descargarFirmaPdf" class="btn btn-outline-success" hidden target="_blank" rel="noopener">Revisar PDF firmado</a><span id="firmaPreparada" class="text-success small" hidden>PDF preparado. Revísalo antes de guardar.</span></div>
                                </div>
                            </div>
                        </div>
                        <div id="oficioCierre" class="oficio-cierre-panel" hidden><span class="oficio-cierre-icono"><i class="bi bi-archive" aria-hidden="true"></i></span><h4 class="fs-5">Entrega y cierre digital</h4><p id="oficioCierreTexto"></p>
                            <div id="oficioArchivoCierre"><label class="form-label" for="oficioDocumentoFinal">Documento firmado</label><input type="file" id="oficioDocumentoFinal" name="documento_firmado" class="form-control" accept=".pdf,.png,.jpg,.jpeg"><p class="form-text">PDF, PNG o JPG. Máximo 10 MiB.</p></div>
                        </div>
                        <div id="oficioAcciones" class="oficio-confirmacion-panel">
                            <h4>Confirmación y observaciones</h4>
                            <label class="form-label" for="oficioObservaciones">Observaciones para el historial (opcional)</label><textarea id="oficioObservaciones" name="observaciones" class="form-control mb-3" maxlength="2000" rows="2"></textarea>
                            <div class="form-check mb-3"><input id="oficioConfirmacion" class="form-check-input" type="checkbox" name="confirmar" value="1" required><label id="oficioConfirmacionTexto" for="oficioConfirmacion" class="form-check-label"></label></div>
                        </div>
                    </fieldset>
                </section>
            </div>
        </div>
        <div class="modal-footer oficio-footer">
            <p id="oficioAyudaAccion" role="status" aria-live="polite">Consulta el expediente antes de continuar.</p>
            <div class="oficio-footer-botones"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Volver a la bandeja</button><button type="button" id="oficioContinuar" class="btn btn-primary" hidden>Continuar a firma <i class="bi bi-arrow-right" aria-hidden="true"></i></button><button id="guardarOficio" type="submit" class="btn btn-primary" disabled hidden>Guardar</button></div>
        </div>
        </form>
    </div></div>
</div>
