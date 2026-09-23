import {EditorFirma} from './firma-pdf.js';

const el = id => document.getElementById(id);
const form = el('formOficioAdmin');
const modalEl = el('modalOficioAdmin');
const modal = new bootstrap.Modal(modalEl);
let actual = null, controller = null, busy = false, refresh = false;
function actualizarAccionesModal() {
    const editable = actual && ['Pendiente por firmar', 'Firmado'].includes(actual.estatus) && !refresh;
    const enAccion = el('oficioTabAccion').classList.contains('active');
    el('oficioContinuar').hidden = !editable || enAccion;
    el('guardarOficio').hidden = !editable || !enAccion;
    if (busy) {
        el('oficioAyudaAccion').textContent = 'Guardando el documento y su historial…';
    } else if (refresh) {
        el('oficioAyudaAccion').textContent = 'Cambios guardados. Vuelve a la bandeja para consultar el expediente actualizado.';
    } else if (!editable) {
        el('oficioAyudaAccion').textContent = actual ? 'Expediente disponible para consulta.' : 'Cargando expediente…';
    } else if (!enAccion) {
        el('oficioAyudaAccion').textContent = 'Revisa los datos y documentos antes de continuar.';
    } else {
        el('oficioAyudaAccion').textContent = actual.estatus === 'Firmado'
            ? 'Confirma la entrega para cerrar y archivar el oficio.'
            : el('guardarOficio').disabled ? 'Prepara el PDF firmado para habilitar el guardado.' : 'Revisa el PDF y confirma la autorización para guardar.';
    }
}
function abrirAccion() { bootstrap.Tab.getOrCreateInstance(el('oficioTabAccion')).show(); }
el('oficioContinuar').addEventListener('click', abrirAccion);
el('oficioTabs').addEventListener('shown.bs.tab', () => {
    modalEl.querySelector('.modal-body').scrollTop = 0;
    actualizarAccionesModal();
});
const editor = new EditorFirma(ready => {
    if (actual?.estatus === 'Pendiente por firmar') el('guardarOficio').disabled = !ready;
    el('oficioConfirmacion').checked = false;
    actualizarAccionesModal();
});
const tabla = $('#tablaOficiosAdmin').DataTable({
    pageLength: 10, order: [], columnDefs: [{targets: 5, orderable: false, searchable: false}],
    language: {search: 'Buscar oficio:', lengthMenu: 'Mostrar _MENU_', info: '_START_–_END_ de _TOTAL_ oficios', infoEmpty: 'Sin oficios', infoFiltered: '(de _MAX_ en total)',
        emptyTable: 'No hay oficios pendientes de firma, firmados o cerrados.', zeroRecords: 'No hay oficios en esta bandeja con los filtros actuales.', paginate: {previous: 'Anterior', next: 'Siguiente'}}
});
function filtrar(estado) {
    tabla.column(3).search(estado ? '^' + $.fn.dataTable.util.escapeRegex(estado) + '$' : '', true, false).draw();
    document.querySelectorAll('.oficios-filtro').forEach(btn => {
        const selected = btn.dataset.estado === estado;
        btn.classList.toggle('activo', selected); btn.setAttribute('aria-pressed', String(selected));
    });
}
const initial = sessionStorage.getItem('sisditOficiosEstado');
filtrar(['Pendiente por firmar', 'Firmado', 'Entregado y archivado', ''].includes(initial) ? initial : 'Pendiente por firmar');
document.querySelectorAll('.oficios-filtro').forEach(btn => btn.addEventListener('click', () => filtrar(btn.dataset.estado)));
document.querySelector('.oficios-filtro-todos').addEventListener('click', () => filtrar(''));
function mensaje(texto, tipo = 'info') {
    el('oficioMensaje').textContent = texto; el('oficioMensaje').className = `alert alert-${tipo}`; el('oficioMensaje').hidden = false;
}
function enlace(label, url, estilo = 'btn-outline-secondary') {
    const link = document.createElement('a'); link.textContent = label; link.href = url; link.target = '_blank'; link.rel = 'noopener'; link.className = `btn btn-sm ${estilo}`; return link;
}
async function respuesta(response) {
    const data = await response.json().catch(() => { throw new Error('El servidor no respondió correctamente. Verifica tu sesión y vuelve a consultar el expediente.'); });
    if (!response.ok || !data.success) throw new Error(data.message || 'No se pudo completar la operación.');
    return data;
}
document.querySelector('#tablaOficiosAdmin tbody').addEventListener('click', async event => {
    const button = event.target.closest('.abrir-oficio');
    if (!button) return;
    controller?.abort(); controller = new AbortController();
    const signal = controller.signal;
    actual = null; refresh = false; form.reset(); editor.reset();
    el('oficioContenido').hidden = true;
    el('oficioTabs').hidden = true;
    el('oficioEstadoCabecera').hidden = true;
    el('oficioReferencia').textContent = '';
    bootstrap.Tab.getOrCreateInstance(el('oficioTabResumen')).show();
    actualizarAccionesModal();
    mensaje('Cargando expediente…');
    modal.show();
    try {
        const data = await respuesta(await fetch(`php/admin_oficios.php?id=${encodeURIComponent(button.dataset.id)}`, {signal}));
        if (signal.aborted) return;
        actual = data.tramite;
        const firmar = actual.estatus === 'Pendiente por firmar';
        const cerrar = actual.estatus === 'Firmado';
        el('oficioReferencia').textContent = `Folio ${actual.folio_numero}/${actual.folio_anio} · Registro #${actual.id}`;
        el('oficioEstadoCabecera').textContent = actual.estatus;
        el('oficioEstadoCabecera').dataset.estado = firmar ? 'pendiente' : 'completo';
        el('oficioEstadoCabecera').hidden = false;
        el('oficioTabAccion').hidden = !firmar && !cerrar;
        el('oficioTabAccionTexto').textContent = firmar ? 'Firmar PDF' : 'Cerrar oficio';
        el('oficioContinuar').textContent = firmar ? 'Continuar a firma →' : 'Continuar al cierre →';
        el('oficioFicha').replaceChildren();
        [['Propietario', actual.propietario], ['Estado', actual.estatus], ['Folio de salida', actual.folio_salida_numero ? `${actual.folio_salida_numero}/${actual.folio_salida_anio}` : 'Por asignar'], ['Dirección', actual.direccion], ['Cuenta catastral', actual.cuenta_catastral], ['Solicitante', actual.solicitante], ['Observaciones previas', actual.observaciones]].forEach(([label, value]) => {
            const item = document.createElement('div'), caption = document.createElement('small'), text = document.createElement('strong');
            caption.textContent = label; text.textContent = value || 'Sin registro'; item.append(caption, text); el('oficioFicha').append(item);
            if (label === 'Estado') text.id = 'oficioEstadoActual';
            if (label === 'Observaciones previas') item.className = 'oficio-ficha-notas';
        });
        el('oficioDocumentos').replaceChildren();
        el('oficioDocumentosCantidad').textContent = data.documentos.length;
        el('oficioDocumentosAyuda').textContent = firmar ? 'Abre un archivo para consultarlo o elige un PDF para firmar.' : 'Consulta los archivos conservados en este expediente.';
        for (const doc of data.documentos) {
            const group = document.createElement('div');
            group.className = 'oficio-documento' + (doc.final ? ' oficio-documento-final' : '');
            const link = enlace('', doc.url);
            link.className = 'oficio-documento-enlace';
            const icon = document.createElement('i'); icon.className = 'bi ' + (doc.final ? 'bi-file-earmark-check' : 'bi-file-earmark-text'); icon.setAttribute('aria-hidden', 'true');
            const info = document.createElement('span'), title = document.createElement('strong'), subtitle = document.createElement('small');
            title.textContent = doc.label; subtitle.textContent = doc.final ? 'Documento firmado · Abrir archivo' : 'Adjunto del expediente · Abrir archivo';
            info.append(title, subtitle);
            const arrow = document.createElement('i'); arrow.className = 'bi bi-box-arrow-up-right'; arrow.setAttribute('aria-hidden', 'true');
            link.append(icon, info, arrow); group.append(link);
            if (firmar && /\.pdf(?:&|$)/i.test(decodeURIComponent(doc.url))) {
                const use = document.createElement('button'); use.type = 'button'; use.className = 'btn btn-sm btn-outline-primary ms-1'; use.textContent = 'Usar para firmar';
                use.addEventListener('click', () => editor.run(async () => {
                    use.disabled = true;
                    abrirAccion();
                    try {
                        const response = await fetch(doc.url, {signal});
                        if (!response.ok || !response.headers.get('content-type')?.includes('application/pdf')) throw new Error('No se pudo cargar este PDF.');
                        const file = await response.blob();
                        if (!signal.aborted) await editor.cargarPdf(file);
                    } finally { use.disabled = false; }
                })); group.append(use);
            }
            el('oficioDocumentos').append(group);
        }
        if (!data.documentos.length) {
            const empty = document.createElement('div'); empty.className = 'oficio-empty';
            const icon = document.createElement('i'); icon.className = 'bi bi-folder2'; icon.setAttribute('aria-hidden', 'true');
            const text = document.createElement('p'); text.textContent = firmar ? 'No hay adjuntos todavía. Puedes cargar el PDF en la pestaña Firmar PDF.' : 'No hay documentos adjuntos disponibles en este expediente.';
            empty.append(icon, text); el('oficioDocumentos').append(empty);
        }
        el('oficioPlantilla').replaceChildren();
        if (data.plantilla) {
            el('oficioPlantilla').append(enlace('Abrir formato del oficio', data.plantilla, 'btn-outline-primary'));
            if (firmar) {
                const note = document.createElement('small'); note.className = 'd-block text-muted mt-1'; note.textContent = 'Puedes imprimir el formato como PDF y cargarlo aquí para colocar la firma.'; el('oficioPlantilla').append(note);
            }
        }
        el('oficioHistorial').replaceChildren();
        el('oficioHistorialCantidad').textContent = data.historial.length;
        for (const h of data.historial) {
            const item = document.createElement('li');
            const time = document.createElement('time'), title = document.createElement('strong'), who = document.createElement('span'), note = document.createElement('p');
            time.textContent = h.fecha; time.dateTime = h.fecha.replace(' ', 'T');
            title.textContent = h.estatus_nuevo || h.accion; who.textContent = h.responsable || 'Sistema'; note.textContent = h.comentario || '';
            item.append(time, title, who); if (h.comentario) item.append(note); el('oficioHistorial').append(item);
        }
        if (!data.historial.length) { const empty = document.createElement('li'); empty.className = 'oficio-empty'; empty.textContent = 'Todavía no hay movimientos registrados para este oficio.'; el('oficioHistorial').append(empty); }
        form.elements.id.value = actual.id; form.elements.accion.value = firmar ? 'firmar' : 'cerrar'; form.elements.estado_esperado.value = actual.estatus;
        el('editorFirma').hidden = !firmar; el('oficioCierre').hidden = !cerrar; el('oficioAcciones').hidden = !firmar && !cerrar;
        el('oficioConfirmacion').required = firmar || cerrar;
        el('oficioCampos').disabled = false;
        el('oficioArchivoCierre').hidden = data.firma_digital_disponible;
        el('oficioDocumentoFinal').required = cerrar && !data.firma_digital_disponible;
        el('oficioDocumentoFinal').disabled = !cerrar || data.firma_digital_disponible;
        el('oficioCierreTexto').textContent = data.firma_digital_disponible ? 'Se conservará el PDF firmado de este expediente. Confirma la entrega para cerrar y archivar el oficio.' : 'Adjunta el documento firmado para dejar evidencia de la entrega y cerrar este oficio.';
        el('oficioConfirmacionTexto').textContent = firmar ? 'Revisé el PDF y autorizo incorporar mi firma visible a este oficio.' : 'Confirmo que el oficio fue entregado y autorizo su cierre y archivo digital.';
        el('guardarOficio').textContent = firmar ? 'Guardar PDF y registrar firma' : 'Cerrar y archivar oficio';
        el('guardarOficio').disabled = !cerrar;
        el('oficioContenido').hidden = false; el('oficioMensaje').hidden = true;
        el('oficioTabs').hidden = false;
        actualizarAccionesModal();
    } catch (error) { if (error.name !== 'AbortError') mensaje(error.message, 'danger'); }
});
form.addEventListener('submit', async event => {
    event.preventDefault();
    if (busy || !actual || !form.reportValidity()) return;
    let data;
    try {
        data = new FormData(form);
        if (actual.estatus === 'Pendiente por firmar') editor.adjuntar(data);
        else {
            const file = el('oficioDocumentoFinal').files[0];
            if (file && file.size > 10 * 1024 * 1024) throw new Error('El documento final no debe superar 10 MiB.');
        }
    } catch (error) { mensaje(error.message, 'warning'); return; }
    busy = true;
    el('guardarOficio').disabled = true;
    el('oficioTabs').querySelectorAll('button').forEach(button => button.disabled = true);
    el('oficioCampos').disabled = true;
    actualizarAccionesModal();
    modalEl.querySelectorAll('[data-bs-dismiss]').forEach(button => button.disabled = true);
    mensaje('Guardando el oficio y su historial…');
    try {
        const result = await respuesta(await fetch('php/admin_oficios.php', {method: 'POST', body: data}));
        refresh = true;
        sessionStorage.setItem('sisditOficiosEstado', result.estatus);
        el('oficioEstadoActual').textContent = result.estatus;
        el('oficioEstadoCabecera').textContent = result.estatus;
        el('oficioEstadoCabecera').dataset.estado = 'completo';
        mensaje(result.message + ' Vuelve a la bandeja para consultar el documento guardado.', 'success');
        el('oficioAcciones').hidden = true;
    } catch (error) {
        mensaje(error.message + ' Si se interrumpió la conexión, consulta el expediente antes de repetir la acción.', 'danger');
        el('oficioCampos').disabled = false;
        el('guardarOficio').disabled = actual.estatus === 'Pendiente por firmar' && !editor.signed;
    } finally {
        busy = false;
        el('oficioTabs').querySelectorAll('button').forEach(button => button.disabled = false);
        actualizarAccionesModal();
        modalEl.querySelectorAll('[data-bs-dismiss]').forEach(button => button.disabled = false);
    }
});
modalEl.addEventListener('hide.bs.modal', event => { if (busy) event.preventDefault(); });
modalEl.addEventListener('hidden.bs.modal', () => {
    controller?.abort(); editor.reset(); actual = null;
    if (refresh) { location.hash = 'oficios-digitales'; location.reload(); }
});
