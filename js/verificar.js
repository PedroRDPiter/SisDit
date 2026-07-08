// =====================================================
// VERIFICAR.JS — Dashboard del Verificador
// Maneja los modales de detalle de trámite,
// el formulario de actualización de estatus,
// la constancia de número oficial, fotos y croquis
// =====================================================

document.addEventListener('DOMContentLoaded', () => {

  // =====================================================
  // VALIDACIONES DE INPUTS EN TIEMPO REAL
  // Para los campos del formulario de constancia
  // =====================================================

  // Forzar mayúsculas y filtrar caracteres raros en campos de dirección
  document.querySelectorAll('.input-mayusculas').forEach(input => {
    input.addEventListener('input', function() {
      this.value = this.value.toUpperCase();
      this.value = this.value.replace(/[^A-Z0-9\s\-]/g, '');
    });
    input.addEventListener('paste', function() {
      setTimeout(() => {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9\s\-]/g, '');
      }, 10);
    });
  });

  // Cuenta catastral: solo números (nada de letras ni guiones)
  document.querySelectorAll('.input-solo-numeros').forEach(input => {
    input.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
    input.addEventListener('paste', function() {
      setTimeout(() => {
        this.value = this.value.replace(/[^0-9]/g, '');
      }, 10);
    });
    input.addEventListener('keypress', function(e) {
      if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
        e.preventDefault();
      }
    });
  });

  // =====================================================
  // PANEL DE CORRECCIÓN
  // Cuando el verificador selecciona "En corrección",
  // aparece un panel para marcar qué documentos faltan
  // y se genera automáticamente el mensaje de WhatsApp
  // =====================================================

// Textos de ayuda que aparecen al seleccionar cada estatus
    const hints = {
        'En revision':              'El trámite continúa en proceso de revisión.',
        'En Revisión por Validador': 'El trámite está listo para ser revisado por el Validador.',
        'En correccion':            'Se notificará al ciudadano que debe corregir su expediente.',
        'Aprobado por Verificador': 'El trámite pasará a la Ventanilla para firma final del Director.',
        'Rechazado':                'El trámite se rechaza definitivamente.'
    };

  // Muestra u oculta el panel de corrección según el estatus elegido
  function actualizarPanelCorreccion() {
    const estatus = document.getElementById('m_estatus')?.value || '';
    const panel   = document.getElementById('panel-correccion');
    const obs     = document.getElementById('m_observaciones');
    if (!panel) return;

    if (estatus === 'En corrección') {
      panel.style.display = 'block';
      if (obs) obs.placeholder = 'Observaciones adicionales (opcional, ya incluidas arriba)...';
      construirMensajeCorreccion();
    } else {
      panel.style.display = 'none';
      if (obs) obs.placeholder = 'Escribe observaciones generales del trámite...';
    }
  }

  // Genera el texto del mensaje de WhatsApp con los docs marcados como faltantes
  function construirMensajeCorreccion() {
    const folio        = document.getElementById('m_folio')?.textContent || '';
    const nombre       = document.getElementById('m_propietario')?.textContent || '';
    const primerNombre = nombre.trim().split(' ')[0];
    const extra        = document.getElementById('correccion_extra')?.value.trim() || '';

    const checks = document.querySelectorAll('.check-correccion:checked');
    const docs   = Array.from(checks).map(c => '• ' + c.value);

    let msg = `Hola ${primerNombre}, su trámite *${folio}* requiere CORRECCIÓN para continuar con el proceso.\n`;
    if (docs.length > 0) {
      msg += `\nDocumentos/requisitos pendientes:\n${docs.join('\n')}\n`;
    }
    if (extra) {
      msg += `\nIndicación adicional: ${extra}\n`;
    }
    msg += `\nFavor de presentarse con los documentos indicados en las oficinas de la Dirección de Planeación y Desarrollo Urbano.\n— Dirección de Planeación y D.U.`;

    // También llenamos el campo de observaciones del formulario para que se guarde en BD
    const obsField = document.getElementById('m_observaciones');
    if (obsField) {
      const partesDoc = docs.length > 0 ? 'Documentos/requisitos: ' + docs.map(d => d.replace('• ', '')).join(', ') : '';
      obsField.value = [partesDoc, extra].filter(Boolean).join(' | ');
    }

    // Mostrar preview del mensaje en el mismo panel
    const preview    = document.getElementById('preview-msg-correccion');
    const previewTxt = document.getElementById('texto-preview-correccion');
    if (preview && previewTxt) {
      if (docs.length > 0 || extra) {
        previewTxt.textContent = msg;
        preview.style.display = 'block';
      } else {
        preview.style.display = 'none';
      }
    }

    return msg;
  }

  // Al cambiar el estatus, actualizar el hint y el panel de corrección
  document.getElementById('m_estatus')?.addEventListener('change', function() {
    const hint = document.getElementById('estatus-hint');
    if (hint) hint.textContent = hints[this.value] || '';
    actualizarPanelCorreccion();
  });

  // Reconstruir el mensaje cuando el verificador marca/desmarca docs o escribe indicaciones
  document.querySelectorAll('.check-correccion').forEach(chk => {
    chk.addEventListener('change', construirMensajeCorreccion);
  });
  document.getElementById('correccion_extra')?.addEventListener('input', construirMensajeCorreccion);

  // =====================================================
  // MODAL DE CONSTANCIA DE NÚMERO OFICIAL
  // Poblar los campos del modal cuando el verificador
  // hace clic en "Llenar Constancia" en la tabla
  // =====================================================
  document.querySelectorAll('.btn-generar-constancia').forEach(btn => {
    btn.addEventListener('click', () => {
      // Datos del trámite
      const cId = document.getElementById('c_id');
      if (cId) cId.value = btn.dataset.id || '';
      document.getElementById('c_folio').textContent     = btn.dataset.folio;
      document.getElementById('c_folio_hidden').value    = btn.dataset.folio;
      document.getElementById('c_propietario').textContent = btn.dataset.propietario;
      document.getElementById('c_direccion').textContent  = btn.dataset.direccion;
      document.getElementById('c_localidad').textContent  = btn.dataset.localidad;

       // Datos de la constancia (si ya tiene info guardada, se pre-llena)
       document.getElementById('c_numero_asignado').value     = (btn.dataset.numeroAsignado || '').toUpperCase();
       document.getElementById('c_tipo_asignacion').value     = (btn.dataset.tipoAsignacion || 'ASIGNACION').toUpperCase();
       document.getElementById('c_referencia_anterior').value = (btn.dataset.referenciaAnterior || '').toUpperCase();
       document.getElementById('c_entre_calle1').value        = (btn.dataset.entreCalle1 || '').toUpperCase();
       document.getElementById('c_entre_calle2').value        = (btn.dataset.entreCalle2 || '').toUpperCase();
       document.getElementById('c_cuenta_catastral').value    = btn.dataset.cuentaCatastral || '';
       document.getElementById('c_manzana').value             = (btn.dataset.manzana || '').toUpperCase();
       document.getElementById('c_lote').value                = (btn.dataset.lote || '').toUpperCase();
       document.getElementById('c_fecha_constancia').value    = btn.dataset.fechaConstancia || new Date().toISOString().split('T')[0];
       document.getElementById('c_cantidad').value            = btn.dataset.cantidad || 1;

      // Estado del croquis
      const croquis = btn.dataset.croquis || '';
      const inp  = document.getElementById('ver_inp_croquis');
      const msg  = document.getElementById('ver_msg_croquis');
      const btnS = document.getElementById('ver_btn_subir');
      if (inp)  inp.value = '';
      if (msg)  msg.textContent = '';
      if (btnS) btnS.style.display = 'none';

       croquis && croquis.trim() !== ''
         ? ver_mostrarEstado(true, croquis)
         : (() => {
            ver_mostrarEstado(false, null);
            const prev = document.getElementById('ver_prev_img');
            const ph   = document.getElementById('ver_prev_ph');
            if (prev) { prev.src = ''; prev.style.display = 'none'; }
            if (ph)   ph.style.display = 'block';
          })();
    });
  });

  // =====================================================
  // MODAL DE DETALLE DE TRÁMITE
  // El modal principal donde el verificador revisa todo
  // y puede cambiar el estatus del trámite
  // =====================================================
  document.querySelectorAll('[data-bs-target="#detalleTramite"]').forEach(btn => {
    btn.addEventListener('click', () => {

      // Datos básicos del trámite
      document.getElementById('m_folio').textContent       = btn.dataset.folio;
      document.getElementById('m_folio_hidden').value      = btn.dataset.folio;
      document.getElementById('m_propietario').textContent = btn.dataset.propietario;
      document.getElementById('m_direccion').textContent   = btn.dataset.direccion;
      document.getElementById('m_localidad').textContent   = btn.dataset.localidad;
      document.getElementById('m_tramites').textContent    = btn.dataset.tramites;
      document.getElementById('m_fecha').textContent       = btn.dataset.fecha;
      document.getElementById('m_telefono').textContent    = btn.dataset.telefono || '—';
      document.getElementById('m_correo').textContent      = btn.dataset.correo   || '—';

      const obsEl = document.getElementById('m_observaciones');
      if (obsEl) obsEl.value = btn.dataset.observaciones || '';

      // Estatus actual del trámite
      const estatusSelect = document.getElementById('m_estatus');
      if (estatusSelect) {
        estatusSelect.value = btn.dataset.estatus;
        const hint = document.getElementById('estatus-hint');
        if (hint) hint.textContent = hints[estatusSelect.value] || '';
      }

      const tipoTramiteId = btn.dataset.tipoTramiteId || '';
      const tipoTramiteInput = document.getElementById('m_tipo_tramite_id');
      if (tipoTramiteInput) tipoTramiteInput.value = tipoTramiteId;

      // ------------------------------------------------
      // DOCUMENTOS REQUERIDOS POR TIPO DE TRÁMITE
      // Cada tipo de trámite requiere documentos distintos
      // Solo mostramos como "faltante" lo que realmente se necesita
      // ------------------------------------------------
      const docsRequeridosPorTipo = {
        '1': ['ine', 'titulo', 'predial'],                        // Constancia de Nº Oficial
        '2': ['ine', 'titulo', 'predial', 'formato_constancia'],  // Compatibilidad Urbanística
        '3': ['ine', 'titulo', 'predial'],                        // Fusión
        '4': ['ine', 'titulo', 'predial'],                        // Subdivisión
        '5': ['ine'],                                             // Informe de Compatibilidad
      };
      const docsKeys = docsRequeridosPorTipo[tipoTramiteId] || ['ine', 'titulo', 'predial'];

      const docsFaltantesKeys = {
        'ine': 'doc_faltante_ine',
        'titulo': 'doc_faltante_titulo',
        'predial': 'doc_faltante_predial',
        'formato_constancia': 'doc_faltante_formato'
      };
      const todosLosDocs = ['ine', 'titulo', 'predial', 'formato_constancia'];

      const sinDocumentos      = document.getElementById('modal-sin-documentos');
      const seccionFaltantes   = document.getElementById('seccion-docs-faltantes');
      const comentarioFaltantes = document.getElementById('comentario-docs-faltantes');
      const textoComentario    = document.getElementById('texto-comentario-faltantes');

      // Resetear todo antes de mostrar el estado actual
      todosLosDocs.forEach(key => {
        const docEl      = document.getElementById('doc_' + key);
        const faltanteEl = document.getElementById(docsFaltantesKeys[key]);
        if (docEl)      docEl.style.display      = 'none';
        if (faltanteEl) faltanteEl.style.display = 'none';
      });
      if (sinDocumentos)      sinDocumentos.style.display      = 'none';
      if (seccionFaltantes)   seccionFaltantes.style.display   = 'none';
      if (comentarioFaltantes) comentarioFaltantes.style.display = 'none';

      let hayDocumentos = false;
      let hayFaltantes  = false;

      // Mostrar documentos que SÍ están cargados
      todosLosDocs.forEach(key => {
        const el      = document.getElementById('doc_' + key);
        const dataKey = key === 'formato_constancia' ? 'formatoConstancia' : key;
        const val     = btn.dataset[dataKey] || '';
        if (val && val.trim() !== '' && el) {
          el.href = `uploads/${val}`;
          el.style.display = 'flex';
          hayDocumentos = true;
        }
      });

      // Marcar cuáles documentos FALTAN (solo los requeridos para este tipo)
      docsKeys.forEach(key => {
        const dataKey = key === 'formato_constancia' ? 'formatoConstancia' : key;
        const val     = btn.dataset[dataKey] || '';
        if (!val || val.trim() === '') {
          const faltanteEl = document.getElementById(docsFaltantesKeys[key]);
          if (faltanteEl) { faltanteEl.style.display = 'block'; hayFaltantes = true; }
        }
      });

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

      // ------------------------------------------------
      // FOTOS DEL TRÁMITE
      // (se requieren para poder imprimir la constancia)
      // ------------------------------------------------
      const foto1Val      = btn.dataset['foto1'] || '';
      const foto2Val      = btn.dataset['foto2'] || '';
      const hayFotos      = (foto1Val.trim() !== '' || foto2Val.trim() !== '');
      const hayConstancia = (btn.dataset.numeroAsignado || '').trim() !== '';
      const esTipoNumOficial = (tipoTramiteId === '1');

      ['1', '2'].forEach(n => {
        const val  = btn.dataset['foto' + n];
        const prev = document.getElementById('preview_foto' + n);
        const cont = document.getElementById('preview_foto' + n + '_container');
        if (prev && cont) {
          if (val) { prev.src = `uploads/${val}`; cont.style.display = 'block'; }
          else     { prev.src = ''; cont.style.display = 'none'; }
        }
      });

      document.getElementById('input_foto1').value = '';
      document.getElementById('input_foto2').value = '';

      const alertaSinFotos = document.getElementById('alerta-sin-fotos');
      const alertaFotosOk  = document.getElementById('alerta-fotos-ok');
      if (alertaSinFotos) alertaSinFotos.style.display = hayFotos ? 'none' : 'block';
      if (alertaFotosOk)  alertaFotosOk.style.display  = hayFotos ? 'block' : 'none';

      // El botón "Ficha Fotografías" solo aparece si ya hay fotos
      const btnFotos = document.getElementById('btn_imprimir_fotos');
      if (btnFotos) {
        if (hayFotos) {
          btnFotos.href = `ficha_fotografias.php?folio=${btn.dataset.folio}`;
          btnFotos.style.display = 'inline-block';
        } else {
          btnFotos.style.display = 'none';
        }
      }

      // ------------------------------------------------
      // BLOQUE DE CONSTANCIA (solo para trámites tipo 1)
      // ------------------------------------------------
      const bloqueInfo     = document.getElementById('bloque-flujo-info');
      const bloquePaso2    = document.getElementById('bloque-paso2');
      const alertaPaso2Pen = document.getElementById('alerta-paso2-pendiente');
      const alertaConstOk  = document.getElementById('alerta-constancia-ok');
      const bloqueBtnConst = document.getElementById('bloque-btn-constancia-modal');
      const btnAbrirConst  = document.getElementById('btn_abrir_constancia_desde_detalle');

      if (bloqueInfo)  bloqueInfo.style.display  = esTipoNumOficial ? 'block' : 'none';
      if (bloquePaso2) bloquePaso2.style.display  = esTipoNumOficial ? 'block' : 'none';

      if (esTipoNumOficial) {
        if (alertaPaso2Pen) alertaPaso2Pen.style.display = (!hayFotos && !hayConstancia) ? 'block' : 'none';
        if (alertaConstOk)  alertaConstOk.style.display  = hayConstancia ? 'block' : 'none';
        if (bloqueBtnConst) bloqueBtnConst.style.display = (hayFotos || hayConstancia) ? 'block' : 'none';

        // Botón "Llenar / imprimir Constancia" dentro del modal de detalle
        if (btnAbrirConst) {
          btnAbrirConst.onclick = function() {
            // Cerrar el modal de detalle antes de abrir el de constancia
            const detalleModal = bootstrap.Modal.getInstance(document.getElementById('detalleTramite'));
            if (detalleModal) detalleModal.hide();

            // Un pequeño delay para que Bootstrap limpie el backdrop correctamente
            setTimeout(function() {
              const cId = document.getElementById('c_id');
              if (cId) cId.value = btn.dataset.id || '';
              document.getElementById('c_folio').textContent           = btn.dataset.folio;
              document.getElementById('c_folio_hidden').value          = btn.dataset.folio;
              document.getElementById('c_propietario').textContent     = btn.dataset.propietario;
              document.getElementById('c_direccion').textContent       = btn.dataset.direccion;
              document.getElementById('c_localidad').textContent       = btn.dataset.localidad;
              document.getElementById('c_numero_asignado').value       = (btn.dataset.numeroAsignado || '').toUpperCase();
              document.getElementById('c_tipo_asignacion').value       = (btn.dataset.tipoAsignacion || 'ASIGNACION').toUpperCase();
              document.getElementById('c_referencia_anterior').value   = (btn.dataset.referenciaAnterior || '').toUpperCase();
              document.getElementById('c_entre_calle1').value          = (btn.dataset.entreCalle1 || '').toUpperCase();
              document.getElementById('c_entre_calle2').value          = (btn.dataset.entreCalle2 || '').toUpperCase();
              document.getElementById('c_cuenta_catastral').value      = btn.dataset.cuentaCatastral || '';
              document.getElementById('c_manzana').value               = (btn.dataset.manzana || '').toUpperCase();
              document.getElementById('c_lote').value                  = (btn.dataset.lote || '').toUpperCase();
              document.getElementById('c_fecha_constancia').value      = btn.dataset.fechaConstancia || new Date().toISOString().split('T')[0];

              const croquis = btn.dataset.croquis || '';
              const inp = document.getElementById('ver_inp_croquis');
              const msgC = document.getElementById('ver_msg_croquis');
              const btnS = document.getElementById('ver_btn_subir');
              if (inp) inp.value = '';
              if (msgC) msgC.textContent = '';
              if (btnS) btnS.style.display = 'none';
               croquis && croquis.trim() !== ''
                 ? ver_mostrarEstado(true, croquis)
                 : (() => {
                    ver_mostrarEstado(false, null);
                    const prev = document.getElementById('ver_prev_img');
                    const ph   = document.getElementById('ver_prev_ph');
                    if (prev) { prev.src = ''; prev.style.display = 'none'; }
                    if (ph)   ph.style.display = 'block';
                  })();

              new bootstrap.Modal(document.getElementById('modalConstancia')).show();
            }, 350);
          };
        }
      }

      // Limpiar el panel de corrección al abrir el modal
      document.querySelectorAll('.check-correccion').forEach(c => c.checked = false);
      const extraField = document.getElementById('correccion_extra');
      if (extraField) extraField.value = '';
      const previewCorr = document.getElementById('preview-msg-correccion');
      if (previewCorr) previewCorr.style.display = 'none';
      actualizarPanelCorreccion();

      // Botones de la ficha
      const folio = btn.dataset.folio;
      document.getElementById('btn_imprimir_ficha').href = `ficha.php?folio=${folio}`;
      const btnVerDocs = document.getElementById('btn_ver_documentos');
      if (btnVerDocs) btnVerDocs.href = `imprimir_documentos.php?folio=${folio}`;
    });
  });

}); // fin DOMContentLoaded

// =====================================================
// GUARDAR ESTATUS DEL TRÁMITE
// Flujo: guardar → cerrar modal → abrir modal de notificación
// La notificación se abre solo cuando el modal ya cerró
// para que Bootstrap no se trabe con dos modales
// =====================================================
let _pendingNotifData = null; // Guardamos la respuesta aquí hasta que sea momento de usarla

document.getElementById('formActualizarTramite')?.addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  const estatus  = document.getElementById('m_estatus')?.value || '';

  Swal.fire({
    title: '¿Guardar cambios?',
    html: `Se cambiará el estatus a: <strong>${estatus}</strong>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, guardar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#7b0f2b',
    cancelButtonColor: '#6c757d'
  }).then(result => {
    if (!result.isConfirmed) return;

    fetch('php/actualizarTramite.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
      .then(res => {
        if (!res.ok) {
          return res.text().then(txt => { throw new Error(`HTTP ${res.status}: ${txt.substring(0, 200)}`); });
        }
        return res.json();
      })
      .then(data => {
        if (data.success) {
          _pendingNotifData = data;

          // Si se subieron fotos nuevas, activar el botón de fotos inmediatamente sin recargar
          const f1 = document.getElementById('input_foto1');
          const f2 = document.getElementById('input_foto2');
          if ((f1 && f1.files && f1.files.length > 0) || (f2 && f2.files && f2.files.length > 0)) {
            const btnFt   = document.getElementById('btn_imprimir_fotos');
            const folioVal = document.getElementById('m_folio_hidden')?.value || '';
            if (btnFt && folioVal) { btnFt.href = `ficha_fotografias.php?folio=${folioVal}`; btnFt.style.display = 'inline-block'; }

            const alertaSinFotos = document.getElementById('alerta-sin-fotos');
            const alertaFotosOk  = document.getElementById('alerta-fotos-ok');
            if (alertaSinFotos) alertaSinFotos.style.display = 'none';
            if (alertaFotosOk)  alertaFotosOk.style.display  = 'block';

            const bloqueBtnConst = document.getElementById('bloque-btn-constancia-modal');
            const alertaPaso2    = document.getElementById('alerta-paso2-pendiente');
            if (bloqueBtnConst) bloqueBtnConst.style.display = 'block';
            if (alertaPaso2)    alertaPaso2.style.display    = 'none';
          }

          // Cerrar el modal de trámite (el evento 'hidden' abrirá la notificación)
          const detalleModal = bootstrap.Modal.getInstance(document.getElementById('detalleTramite'));
          if (detalleModal) { detalleModal.hide(); } else { _abrirModalNotif(); }

          // Refrescar mapa si está disponible
          if (typeof refrescarMapa === 'function') refrescarMapa();

        } else {
          Swal.fire({ icon: 'error', title: 'Error al guardar', text: data.message || 'No se pudieron guardar los cambios' });
        }
      })
      .catch(err => {
        console.error('[actualizarTramite]', err);
        Swal.fire({
          icon: 'error',
          title: 'Error de conexión',
          html: 'No se pudo conectar con el servidor.<br><small style="color:#999">' + err.message + '</small>'
        });
      });
  });
});

// Cuando el modal de trámite termina de cerrarse, abrimos la notificación
// (timeout de 150ms para que Bootstrap limpie el backdrop y el aria)
document.getElementById('detalleTramite')?.addEventListener('hidden.bs.modal', function() {
  if (_pendingNotifData) {
    setTimeout(_abrirModalNotif, 150);
  }
});

// Cuando se cierra la notificación, recargar la página para reflejar el cambio de estatus
document.getElementById('notifModal')?.addEventListener('hidden.bs.modal', function() {
  _pendingNotifData = null;
  location.reload();
});

// Construye y muestra el modal de notificación (WhatsApp / Correo)
function _abrirModalNotif() {
  const data = _pendingNotifData;
  if (!data) return;

  const n       = data.notificacion || {};
  const waEl    = document.getElementById('notif-wa-link');
  const gmEl    = document.getElementById('notif-gm-link');
  const titleEl = document.getElementById('notif-modal-title');
  const descEl  = document.getElementById('notif-modal-desc');
  const msgPreview = document.getElementById('notif-msg-preview');
  const msgTexto   = document.getElementById('notif-msg-texto');

  if (titleEl) titleEl.textContent = `Notificar a ${n.nombre || ''}`;
  if (descEl)  descEl.innerHTML    = `Estatus: <strong>${data.estatus}</strong> &nbsp;|&nbsp; Folio: <strong>${data.folio}</strong>`;

  if (msgPreview && msgTexto && n.mensaje) {
    msgTexto.textContent = n.mensaje;
    msgPreview.style.display = 'block';
  }

  // Botón WhatsApp — solo activo si hay teléfono
  if (waEl) {
    if (n.wa_link) {
      waEl.href = n.wa_link;
      waEl.style.opacity = '1';
      waEl.style.pointerEvents = 'auto';
      waEl.querySelector('.notif-sub').textContent = n.telefono ? `Enviar a: ${n.telefono}` : 'Abre WhatsApp con el mensaje ya escrito';
    } else {
      waEl.href = '#';
      waEl.style.opacity = '0.35';
      waEl.style.pointerEvents = 'none';
      waEl.querySelector('.notif-sub').textContent = 'Sin número de teléfono registrado';
    }
  }

  // Botón Correo — solo activo si hay email
  if (gmEl) {
    if (n.gm_link) {
      gmEl.href = n.gm_link;
      gmEl.style.opacity = '1';
      gmEl.style.pointerEvents = 'auto';
      gmEl.querySelector('.notif-sub').textContent = n.correo ? `Enviar a: ${n.correo}` : 'Abre tu cliente de correo con el mensaje listo';
    } else {
      gmEl.href = '#';
      gmEl.style.opacity = '0.35';
      gmEl.style.pointerEvents = 'none';
      gmEl.querySelector('.notif-sub').textContent = 'Sin correo electrónico registrado';
    }
  }

  // Abrir el modal de notificación limpio
  const notifEl = document.getElementById('notifModal');
  notifEl.removeAttribute('aria-hidden');
  new bootstrap.Modal(notifEl, { backdrop: 'static', keyboard: false }).show();
}

// =====================================================
// PREVIEW DE FOTOS
// Mostrar vista previa cuando el verificador selecciona
// imágenes para subir al trámite
// =====================================================
['1', '2'].forEach(n => {
  document.getElementById('input_foto' + n)?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const prev = document.getElementById('preview_foto' + n);
    const cont = document.getElementById('preview_foto' + n + '_container');
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = ev => { prev.src = ev.target.result; cont.style.display = 'block'; };
      reader.readAsDataURL(file);
    }
  });
});

// =====================================================
// CONSTANCIA DE NÚMERO OFICIAL
// Guardar los datos de la constancia (sin cerrar modal)
// para poder seguir editando antes de imprimir
// =====================================================
document.getElementById('formConstancia')?.addEventListener('submit', function(e) {
  e.preventDefault();
  guardarConstancia();
});

// Botón "Solo imprimir" — requiere que el croquis ya esté guardado
document.getElementById('btnSoloImprimir')?.addEventListener('click', function() {
  const folio = document.getElementById('c_folio_hidden')?.value || '';
  const idSub = document.getElementById('c_id')?.value || '';
  if (!folio && !idSub) return;

  if (!_ver_croquis_ok) {
    Swal.fire({ icon: 'warning', title: 'Croquis requerido', text: 'Debes guardar la imagen del croquis antes de imprimir.', confirmButtonColor: '#7b0f2b' });
    return;
  }

  const url = idSub ? `constancia_numero.php?id=${idSub}` : `constancia_numero.php?folio=${folio}`;
  window.open(url, '_blank');
});

// Al cerrar el modal de constancia, recargar para reflejar cambios en la tabla
document.getElementById('modalConstancia')?.addEventListener('hidden.bs.modal', function() {
  location.reload();
});

// Llama al PHP para guardar los datos de la constancia sin recargar la página
function guardarConstancia() {
  const form     = document.getElementById('formConstancia');
  const formData = new FormData(form);

  Swal.fire({ title: 'Guardando...', html: 'Por favor espere', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

  fetch('php/actualizarTramite.php', { method: 'POST', body: formData, credentials: 'same-origin' })
    .then(res => {
      if (!res.ok) { return res.text().then(txt => { throw new Error(`HTTP ${res.status}: ${txt.substring(0, 200)}`); }); }
      return res.json();
    })
    .then(data => {
      if (data.success) {
        // Toast chico en la esquina para no interrumpir el flujo
        Swal.fire({ icon: 'success', title: 'Guardado', text: 'Los datos han sido guardados correctamente.', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
      } else {
        Swal.fire({ icon: 'error', title: 'Error al guardar', text: data.message || 'No se pudieron guardar los cambios' });
      }
    })
    .catch(err => {
      console.error('[guardarConstancia]', err);
      Swal.fire({ icon: 'error', title: 'Error de conexión', html: 'No se pudo conectar con el servidor.<br><small style="color:#999">' + err.message + '</small>' });
    });
}

// =====================================================
// CROQUIS DEL PREDIO
// El verificador puede subir una imagen del croquis
// del predio que se incluye en la constancia impresa
// =====================================================
var _ver_croquis_ok = false; // Bandera: true si hay croquis guardado válido

// Actualiza el estado visual del croquis (ok / pendiente)
function ver_mostrarEstado(ok, imgSrc) {
  const alerta = document.getElementById('ver_alerta_croquis');
  const okDiv  = document.getElementById('ver_ok_croquis');
  const prev   = document.getElementById('ver_prev_img');
  const ph     = document.getElementById('ver_prev_ph');

  if (ok) {
    if (alerta) alerta.style.display = 'none';
    if (okDiv)  okDiv.style.display  = 'flex';
    if (imgSrc && prev) { prev.src = imgSrc; prev.style.display = 'block'; if (ph) ph.style.display = 'none'; }
    _ver_croquis_ok = true;
  } else {
    if (alerta) alerta.style.display = 'flex';
    if (okDiv)  okDiv.style.display  = 'none';
    _ver_croquis_ok = false;
  }
}

// Mostrar preview local antes de subir (para que el verificador vea qué va a subir)
function ver_prevCroquis(input) {
  const file = input.files[0];
  if (!file) return;

  ver_mostrarPreviewCroquis(file);
}

// Función para mostrar preview del croquis
function ver_mostrarPreviewCroquis(file) {
  // Validar dimensiones mínimas: al menos 500x800 píxeles
  const img = new Image();
  img.onload = function() {
    const minWidth = 100;
    const minHeight = 100;

    if (this.width < minWidth || this.height < minHeight) {
      const msg = document.getElementById('ver_msg_croquis');
      if (msg) {
        msg.textContent = `❌ La imagen debe ser al menos ${minWidth}x${minHeight} píxeles. Actual: ${this.width}x${this.height}.`;
        msg.style.color = '#dc3545';
      }
      // Limpiar input
      const input = document.getElementById('ver_inp_croquis');
      if (input) input.value = '';
      return;
    }

    // Si pasa validación, mostrar preview
    const reader = new FileReader();
    reader.onload = function(e) {
      const prev = document.getElementById('ver_prev_img');
      const ph   = document.getElementById('ver_prev_ph');
      if (prev) { prev.src = e.target.result; prev.style.display = 'block'; }
      if (ph)   ph.style.display = 'none';

      // Mostrar botón de guardar y aviso de que falta confirmar
      const btn = document.getElementById('ver_btn_subir');
      if (btn) btn.style.display = 'block';
      const msg = document.getElementById('ver_msg_croquis');
      if (msg) { msg.textContent = '⚠️ Haz clic en "Guardar croquis" para confirmar.'; msg.style.color = '#856404'; }

      // Desmarcar como ok mientras no se guarde
      _ver_croquis_ok = false;
      if (document.getElementById('ver_alerta_croquis')) document.getElementById('ver_alerta_croquis').style.display = 'flex';
      if (document.getElementById('ver_ok_croquis'))    document.getElementById('ver_ok_croquis').style.display    = 'none';
    };
    reader.readAsDataURL(file);
  };
  img.src = URL.createObjectURL(file);
}

// Función para redimensionar imagen a 500x800
function redimensionarImagen(file, targetWidth, targetHeight) {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = function() {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

      canvas.width = targetWidth;
      canvas.height = targetHeight;

      // Calcular el aspect ratio para ajustar la imagen
      const imgAspect = this.width / this.height;
      const targetAspect = targetWidth / targetHeight;

      let drawWidth, drawHeight, offsetX, offsetY;

      if (imgAspect > targetAspect) {
        // Imagen más ancha que el target, ajustar por altura
        drawHeight = targetHeight;
        drawWidth = drawHeight * imgAspect;
        offsetX = (targetWidth - drawWidth) / 2;
        offsetY = 0;
      } else {
        // Imagen más alta que el target, ajustar por ancho
        drawWidth = targetWidth;
        drawHeight = drawWidth / imgAspect;
        offsetX = 0;
        offsetY = (targetHeight - drawHeight) / 2;
      }

      // Dibujar imagen escalada
      ctx.drawImage(this, offsetX, offsetY, drawWidth, drawHeight);

      canvas.toBlob(resolve, 'image/jpeg', 0.9);
    };
    img.onerror = reject;
    img.src = URL.createObjectURL(file);
  });
}

// Subir el croquis al servidor via AJAX
function ver_subirCroquis() {
  const input = document.getElementById('ver_inp_croquis');
  const msg   = document.getElementById('ver_msg_croquis');
  const btn   = document.getElementById('ver_btn_subir');
  const folio = document.getElementById('c_folio_hidden')?.value || '';
  const idSub = document.getElementById('c_id')?.value || '';

  if (!input.files || !input.files[0]) {
    if (msg) { msg.textContent = '⚠️ Selecciona una imagen primero.'; msg.style.color = '#856404'; }
    return;
  }

  if (msg) { msg.textContent = 'Procesando imagen...'; msg.style.color = '#555'; }
  if (btn) btn.disabled = true;

  // Redimensionar la imagen a 2000x1500
  redimensionarImagen(input.files[0], 2000, 1500)
    .then(resizedBlob => {
      if (msg) { msg.textContent = 'Guardando...'; msg.style.color = '#555'; }

      const fd = new FormData();
      if (idSub) fd.append('id', idSub);
      fd.append('folio', folio);
      fd.append('croquis', resizedBlob, 'croquis.jpg'); // Nombre fijo

      return fetch('php/guardar_croquis.php', { method: 'POST', body: fd, credentials: 'same-origin' });
    })
    .then(r => r.json())
    .then(data => {
      if (btn) btn.disabled = false;
      if (data.success) {
        if (msg) { msg.textContent = '✅ Croquis guardado. Ya puedes imprimir.'; msg.style.color = '#198754'; }
        if (btn) btn.style.display = 'none';
        ver_mostrarEstado(true, null);
      } else {
        if (msg) { msg.textContent = '❌ ' + data.message; msg.style.color = '#dc3545'; }
      }
    })
    .catch(err => {
      console.error('Error procesando imagen:', err);
      if (btn) btn.disabled = false;
      if (msg) { msg.textContent = '❌ Error procesando la imagen.'; msg.style.color = '#dc3545'; }
    });
}

// =====================================================
// PEGAR IMÁGENES CON CTRL+V
// Permite pegar imágenes directamente en áreas de carga
// =====================================================

// Función auxiliar para manejar pegado de imágenes
function manejarPegadoImagen(e, inputId, previewFunction, mensaje) {
  const items = e.clipboardData?.items;
  if (!items) return false;

  for (let i = 0; i < items.length; i++) {
    const item = items[i];
    if (item.type.indexOf('image') !== -1) {
      e.preventDefault();

      const file = item.getAsFile();
      if (file) {
        // Para croquis, validar dimensiones
        if (inputId === 'ver_inp_croquis') {
          const img = new Image();
          img.onload = function() {
            const minWidth = 200;
            const minHeight = 100;

            if (this.width < minWidth || this.height < minHeight) {
              const msg = document.getElementById('ver_msg_croquis');
              if (msg) {
                msg.textContent = `❌ La imagen pegada debe ser al menos ${minWidth}x${minHeight} píxeles. Actual: ${this.width}x${this.height}.`;
                msg.style.color = '#dc3545';
              }
              return;
            }

            // Si valida, asignar al input
            asignarArchivoPegado(inputId, file, mensaje);
          };
          img.src = URL.createObjectURL(file);
        } else {
          // Para otras imágenes (fotos), asignar directamente
          asignarArchivoPegado(inputId, file, mensaje);
        }
        return true; // Se encontró y manejó una imagen
      }
    }
  }
  return false;
}

// Función auxiliar para asignar archivo pegado al input
function asignarArchivoPegado(inputId, file, mensaje) {
  const input = document.getElementById(inputId);
  if (input) {
    // Crear un DataTransfer para asignar el archivo al input
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;

    // Disparar el evento change para mostrar preview
    input.dispatchEvent(new Event('change'));

    // Mostrar mensaje si se proporciona
    if (mensaje) {
      const msgEl = document.querySelector(mensaje.selector);
      if (msgEl) {
        msgEl.textContent = mensaje.texto;
        msgEl.style.color = mensaje.color || '#007bff';
      }
    }
  }
}

// Event listener global para pegar imágenes
document.addEventListener('paste', function(e) {
  // Solo manejar si estamos en un modal relevante
  const modalDetalle = document.getElementById('detalleTramite');
  const modalConstancia = document.getElementById('modalConstancia');
  const enModalDetalle = modalDetalle && modalDetalle.classList.contains('show');
  const enModalConstancia = modalConstancia && modalConstancia.classList.contains('show');

  if (!enModalDetalle && !enModalConstancia) return;

  // Intentar pegar en croquis si estamos en modal de constancia
  if (enModalConstancia) {
    const pegado = manejarPegadoImagen(e, 'ver_inp_croquis', null, {
      selector: '#ver_msg_croquis',
      texto: '🖼️ Imagen pegada. Haz clic en "Guardar croquis" para confirmar.',
      color: '#007bff'
    });
    if (pegado) return;
  }

  // Intentar pegar en fotos si estamos en modal de detalle
  if (enModalDetalle) {
    // Primero intentar foto1
    const pegado1 = manejarPegadoImagen(e, 'input_foto1', null, null);
    if (pegado1) return;

    // Luego foto2
    const pegado2 = manejarPegadoImagen(e, 'input_foto2', null, null);
    if (pegado2) return;
  }
});

// =====================================================
// DATATABLES — Tabla principal de trámites
// =====================================================
$(document).ready(function() {
  if ($('#tablaTramites').length) {
    $('#tablaTramites').DataTable({
      pageLength: 10,
      lengthChange: true,
      lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Todos']],
      ordering: true,
      order: [],
      responsive: true,
      columnDefs: [{ orderable: false, targets: -1 }], // Columna "Acciones" no ordenable
      language: {
        paginate: { previous: 'Anterior', next: 'Siguiente' },
        info:         'Mostrando _START_ a _END_ de _TOTAL_ trámites',
        infoEmpty:    'No hay trámites',
        infoFiltered: '(filtrado de _MAX_)',
        zeroRecords:  'No se encontraron resultados',
        search:       'Buscar:',
        lengthMenu:   'Mostrar _MENU_ registros'
      },
      dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
  }
});

// =====================================================
// CROQUIS CON MAPA Y POLIGONOS
// =====================================================
let verCroquisMap = null;
let verCroquisParcelas = null;
let verCroquisDrawn = null;
let verCroquisSelectedLayer = null;
let verCroquisLabel = null;
let verCroquisLabelLatLng = null;
let verCroquisLoaded = false;
let verCroquisCurrentTramiteId = '';
let verCroquisCurrentFolio = '';
let verCroquisActiveBaseLayer = 'Mapa';
let verCroquisBaseTiles = {};
let verCroquisSplitMode = false;
let verCroquisSplitPoints = [];
let verCroquisSplitLayer = null;
let verCroquisCaptureHadParcelas = false;
let verCroquisCaptureOverlay = null;

const verCroquisNormalStyle = { color: '#7b0f2b', weight: 1, opacity: 0.95, fillColor: 'transparent', fillOpacity: 0 };
const verCroquisSelectedStyle = { color: '#b21f3b', weight: 4, opacity: 1, fillColor: '#ffd166', fillOpacity: 0.45 };
const verCroquisDrawnStyle = { color: '#0f766e', weight: 3, opacity: 1, fillColor: '#5eead4', fillOpacity: 0.35 };

function ver_initCroquisMapa() {
  if (verCroquisMap || !document.getElementById('ver_mapa_croquis') || typeof L === 'undefined') return;
  if (typeof proj4 !== 'undefined') {
    proj4.defs('EPSG:32613', '+proj=utm +zone=13 +datum=WGS84 +units=m +no_defs');
  }

  verCroquisMap = L.map('ver_mapa_croquis', { zoomControl: true, scrollWheelZoom: true }).setView([22.228, -102.320], 14);
  const mapa = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'OpenStreetMap',
    maxZoom: 22,
    crossOrigin: true
  }).addTo(verCroquisMap);
  const satelite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles Esri',
    maxZoom: 22,
    crossOrigin: true
  });
  verCroquisBaseTiles = { 'Mapa': mapa, 'Satelital': satelite };

  verCroquisDrawn = new L.FeatureGroup().addTo(verCroquisMap);
  L.control.layers(verCroquisBaseTiles, { 'Poligonos guardados/dibujados': verCroquisDrawn }, { collapsed: true }).addTo(verCroquisMap);

  verCroquisMap.addControl(new L.Control.Draw({
    draw: {
      marker: false,
      circle: false,
      circlemarker: false,
      rectangle: false,
      polyline: false,
      polygon: { allowIntersection: false, showArea: true, shapeOptions: verCroquisDrawnStyle }
    },
    edit: { featureGroup: verCroquisDrawn, remove: true }
  }));

  verCroquisMap.on('baselayerchange', function(e) {
    verCroquisActiveBaseLayer = e.name || 'Mapa';
  });

  verCroquisMap.on(L.Draw.Event.CREATED, function(e) {
    const layer = e.layer;
    layer.setStyle(verCroquisDrawnStyle);
    layer._croquisSource = 'dibujado';
    layer.on('click', function() { ver_seleccionarCroquisPoligono(layer, 'dibujado'); });
    verCroquisDrawn.addLayer(layer);
    ver_seleccionarCroquisPoligono(layer, 'dibujado');
  });

  verCroquisMap.on(L.Draw.Event.EDITED, function(e) {
    e.layers.eachLayer(function(layer) {
      ver_seleccionarCroquisPoligono(layer, layer._croquisSource || 'dibujado');
    });
  });

  verCroquisMap.on(L.Draw.Event.DELETED, function() {
    verCroquisSelectedLayer = null;
    verCroquisLabelLatLng = null;
    ver_actualizarCroquisLabel('');
    ver_setCroquisInfo('Dibuja o selecciona un poligono para el croquis.');
  });

  document.getElementById('ver_texto_poligono')?.addEventListener('input', function() {
    ver_actualizarCroquisLabel(this.value);
  });
  document.getElementById('ver_btn_centrar_croquis')?.addEventListener('click', ver_centrarCroquisActual);
  document.getElementById('ver_btn_subdividir_croquis')?.addEventListener('click', ver_iniciarSubdivisionCroquis);

  ver_cargarParcelasCroquis();
}

function ver_cargarParcelasCroquis() {
  if (verCroquisLoaded || !verCroquisMap) return;
  verCroquisLoaded = true;
  fetch('./Geojson/TRAMITES_reprojected.geojson')
    .then(function(r) { return r.json(); })
    .then(function(data) {
      verCroquisParcelas = L.geoJSON(data, {
        interactive: true,
        style: verCroquisNormalStyle,
        onEachFeature: function(feature, layer) {
          layer._croquisSource = 'catastro';
          layer.on('click', function(e) {
            if (e && e.originalEvent) L.DomEvent.stop(e.originalEvent);
            ver_usarPoligonoCatastro(layer);
          });
        }
      }).addTo(verCroquisMap);
      if (verCroquisParcelas.bringToFront) verCroquisParcelas.bringToFront();
    })
    .catch(function(err) {
      console.warn('No se pudieron cargar poligonos base para croquis:', err);
      verCroquisLoaded = false;
    });
}

function ver_prepararCroquisMapa(tramiteId, folio, croquisUrl) {
  verCroquisCurrentTramiteId = tramiteId || '';
  verCroquisCurrentFolio = folio || '';
  ver_initCroquisMapa();
  setTimeout(function() {
    if (!verCroquisMap) return;
    verCroquisMap.invalidateSize();
    ver_cargarCroquisGuardado();
    if (croquisUrl) ver_mostrarPreviewCroquis(croquisUrl);
  }, 250);
}

function ver_cargarCroquisGuardado() {
  if (!verCroquisCurrentTramiteId || !verCroquisDrawn) return;
  fetch('php/obtener_croquis_poligono.php?id=' + encodeURIComponent(verCroquisCurrentTramiteId), { credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      verCroquisDrawn.clearLayers();
      verCroquisSelectedLayer = null;
      verCroquisLabelLatLng = null;
      ver_actualizarCroquisLabel('');

      if (!data.success || !data.poligono || !data.poligono.geojson) {
        const inputTexto = document.getElementById('ver_texto_poligono');
        if (inputTexto) inputTexto.value = '';
        ver_setCroquisInfo('Selecciona un poligono existente o dibuja uno nuevo.');
        ver_centrarCroquisActual();
        return;
      }

      const geojson = typeof data.poligono.geojson === 'string' ? JSON.parse(data.poligono.geojson) : data.poligono.geojson;
      const georef = ver_parseJsonSeguro(data.poligono.georeferencia);
      if (georef && georef.base_layer && verCroquisBaseTiles[georef.base_layer]) {
        Object.keys(verCroquisBaseTiles).forEach(function(name) {
          if (verCroquisMap.hasLayer(verCroquisBaseTiles[name])) verCroquisMap.removeLayer(verCroquisBaseTiles[name]);
        });
        verCroquisBaseTiles[georef.base_layer].addTo(verCroquisMap);
        verCroquisActiveBaseLayer = georef.base_layer;
      }

      let layerParaSeleccionar = null;
      const layerGroup = L.geoJSON(geojson, {
        style: verCroquisDrawnStyle,
        onEachFeature: function(feature, layer) {
          layer._croquisSource = data.poligono.origen || 'guardado';
          layer.on('click', function() { ver_seleccionarCroquisPoligono(layer, layer._croquisSource); });
          if (!layerParaSeleccionar || feature.properties?.croquis_selected) {
            layerParaSeleccionar = layer;
          }
        }
      });
      layerGroup.eachLayer(function(layer) {
        verCroquisDrawn.addLayer(layer);
      });

      const texto = data.poligono.texto || '';
      const inputTexto = document.getElementById('ver_texto_poligono');
      if (inputTexto) inputTexto.value = texto;
      if (georef && georef.label_position) {
        verCroquisLabelLatLng = L.latLng(georef.label_position.lat, georef.label_position.lng);
      }
      if (georef && georef.map_center && georef.map_zoom && verCroquisMap) {
        verCroquisMap.setView([georef.map_center.lat, georef.map_center.lng], georef.map_zoom, { animate: false });
      }
      if (layerParaSeleccionar) {
        ver_seleccionarCroquisPoligono(layerParaSeleccionar, layerParaSeleccionar._croquisSource || 'guardado');
      }
      ver_actualizarCroquisLabel(texto);
      if (data.poligono.croquis_archivo) ver_mostrarPreviewCroquis(data.poligono.croquis_archivo);
      if (!georef || !georef.map_center || !georef.map_zoom) {
        ver_centrarCroquisActual();
      }
    })
    .catch(function(err) { console.warn('No se pudo cargar croquis guardado:', err); });
}

function ver_usarPoligonoCatastro(layer) {
  if (!layer || !verCroquisDrawn) return;

  if (verCroquisSelectedLayer && verCroquisSelectedLayer._croquisSource === 'catastro-copia') {
    verCroquisDrawn.removeLayer(verCroquisSelectedLayer);
  } else if (verCroquisSelectedLayer && verCroquisSelectedLayer !== layer) {
    verCroquisSelectedLayer.setStyle(verCroquisSelectedLayer._croquisSource === 'catastro' ? verCroquisNormalStyle : verCroquisDrawnStyle);
  }

  const geojson = layer.toGeoJSON();
  const props = Object.assign({}, geojson.properties || {}, layer.feature?.properties || {});
  geojson.properties = props;

  const cloneGroup = L.geoJSON(geojson, {
    style: verCroquisSelectedStyle,
    onEachFeature: function(feature, editableLayer) {
      editableLayer._croquisSource = 'catastro-copia';
      editableLayer.feature = feature;
      editableLayer.on('click', function(e) {
        if (verCroquisSplitMode) return;
        if (e && e.originalEvent) L.DomEvent.stop(e.originalEvent);
        ver_seleccionarCroquisPoligono(editableLayer, 'catastro-copia');
      });
      verCroquisDrawn.addLayer(editableLayer);
      ver_seleccionarCroquisPoligono(editableLayer, 'catastro-copia');
    }
  });

  layer.setStyle(verCroquisNormalStyle);
  ver_setCroquisInfo('Poligono base copiado a la capa editable. Ahora puedes mover el texto, editar vertices o guardar.');
}

function ver_seleccionarCroquisPoligono(layer, source) {
  if (!layer || !verCroquisMap) return;
  if (verCroquisSelectedLayer && verCroquisSelectedLayer !== layer) {
    verCroquisSelectedLayer.setStyle(verCroquisSelectedLayer._croquisSource === 'catastro' ? verCroquisNormalStyle : verCroquisDrawnStyle);
  }
  layer._croquisSource = source || layer._croquisSource || 'seleccionado';
  layer.setStyle(verCroquisSelectedStyle);
  verCroquisSelectedLayer = layer;
  if (!verCroquisLabelLatLng || !layer.getBounds().contains(verCroquisLabelLatLng)) {
    verCroquisLabelLatLng = layer.getBounds().getCenter();
  }
  ver_actualizarCroquisLabel(document.getElementById('ver_texto_poligono')?.value || '');

  const utm = ver_obtenerCentroUtm(layer);
  const cuenta = layer.feature?.properties?.CVE_CAT_OR || '';
  ver_setCroquisInfo('Poligono seleccionado (' + layer._croquisSource + ')' + (cuenta ? ' | Cuenta: ' + cuenta : '') + (utm ? ' | UTM X: ' + utm.x + ' Y: ' + utm.y : ''));
}

function ver_iniciarSubdivisionCroquis() {
  const msg = document.getElementById('ver_msg_croquis');
  if (!verCroquisSelectedLayer) {
    if (msg) { msg.textContent = 'Selecciona un poligono antes de subdividir.'; msg.style.color = '#856404'; }
    return;
  }
  ver_cancelarSubdivisionCroquis(false);
  verCroquisSplitMode = true;
  verCroquisSplitPoints = [];
  verCroquisMap.getContainer().style.cursor = 'crosshair';
  verCroquisMap.on('click', ver_registrarPuntoSubdivision);
  if (msg) { msg.textContent = 'Subdividir: marca dos puntos para trazar la linea de corte.'; msg.style.color = '#856404'; }
}

function ver_cancelarSubdivisionCroquis(limpiarMensaje = true) {
  if (!verCroquisMap) return;
  verCroquisSplitMode = false;
  verCroquisSplitPoints = [];
  verCroquisMap.off('click', ver_registrarPuntoSubdivision);
  verCroquisMap.getContainer().style.cursor = '';
  if (verCroquisSplitLayer) {
    verCroquisMap.removeLayer(verCroquisSplitLayer);
    verCroquisSplitLayer = null;
  }
  if (limpiarMensaje) {
    const msg = document.getElementById('ver_msg_croquis');
    if (msg) msg.textContent = '';
  }
}

function ver_registrarPuntoSubdivision(e) {
  if (!verCroquisSplitMode || !verCroquisSelectedLayer) return;
  verCroquisSplitPoints.push(e.latlng);
  if (verCroquisSplitLayer) verCroquisMap.removeLayer(verCroquisSplitLayer);

  if (verCroquisSplitPoints.length === 1) {
    verCroquisSplitLayer = L.circleMarker(e.latlng, { radius: 5, color: '#f59e0b', fillOpacity: 1 }).addTo(verCroquisMap);
    return;
  }

  verCroquisSplitLayer = L.polyline(verCroquisSplitPoints, { color: '#f59e0b', weight: 4, dashArray: '6,6' }).addTo(verCroquisMap);
  ver_subdividirPoligonoSeleccionado(verCroquisSplitPoints[0], verCroquisSplitPoints[1]);
}

function ver_subdividirPoligonoSeleccionado(p1, p2) {
  const msg = document.getElementById('ver_msg_croquis');
  const partes = ver_calcularSubdivision(verCroquisSelectedLayer, p1, p2);
  if (!partes || partes.length !== 2) {
    ver_cancelarSubdivisionCroquis(false);
    if (msg) { msg.textContent = 'No se pudo subdividir. La linea debe cruzar el poligono de lado a lado.'; msg.style.color = '#dc3545'; }
    return;
  }

  const props = Object.assign({}, verCroquisSelectedLayer.feature?.properties || {});
  if (verCroquisDrawn.hasLayer(verCroquisSelectedLayer)) {
    verCroquisDrawn.removeLayer(verCroquisSelectedLayer);
  }

  const nuevasCapas = partes.map(function(ring, index) {
    const layer = L.polygon(ring, index === 0 ? verCroquisSelectedStyle : verCroquisDrawnStyle);
    layer._croquisSource = 'subdivision';
    layer.feature = {
      type: 'Feature',
      properties: Object.assign({}, props, { subdivision_part: index + 1 }),
      geometry: layer.toGeoJSON().geometry
    };
    layer.on('click', function() { ver_seleccionarCroquisPoligono(layer, 'subdivision'); });
    verCroquisDrawn.addLayer(layer);
    return layer;
  });

  ver_cancelarSubdivisionCroquis(false);
  ver_seleccionarCroquisPoligono(nuevasCapas[0], 'subdivision');
  if (msg) { msg.textContent = 'Poligono subdividido en dos partes. Puedes ajustar vertices antes de guardar.'; msg.style.color = '#198754'; }
}

function ver_calcularSubdivision(layer, p1, p2) {
  const raw = layer.getLatLngs();
  let ring = Array.isArray(raw[0]) ? raw[0] : raw;
  if (Array.isArray(ring[0])) ring = ring[0];
  ring = ring.slice();
  if (ring.length < 3) return null;
  const first = ring[0], last = ring[ring.length - 1];
  if (first.lat === last.lat && first.lng === last.lng) ring.pop();

  const bounds = layer.getBounds();
  const span = Math.max(bounds.getEast() - bounds.getWest(), bounds.getNorth() - bounds.getSouth()) * 3 || 1;
  const dx = p2.lng - p1.lng;
  const dy = p2.lat - p1.lat;
  const len = Math.sqrt(dx * dx + dy * dy);
  if (len === 0) return null;
  const ux = dx / len;
  const uy = dy / len;

  const a = { lng: p1.lng - ux * span, lat: p1.lat - uy * span };
  const b = { lng: p1.lng + ux * span, lat: p1.lat + uy * span };
  const intersecciones = [];

  for (let i = 0; i < ring.length; i++) {
    const c = ring[i];
    const d = ring[(i + 1) % ring.length];
    const hit = ver_interseccionSegmentos(a, b, c, d);
    if (hit && hit.u >= 0 && hit.u <= 1) {
      const existe = intersecciones.some(function(x) {
        return Math.abs(x.lat - hit.lat) < 1e-10 && Math.abs(x.lng - hit.lng) < 1e-10;
      });
      if (!existe) intersecciones.push(Object.assign(hit, { edge: i }));
    }
  }

  if (intersecciones.length !== 2 || intersecciones[0].edge === intersecciones[1].edge) return null;
  intersecciones.sort(function(x, y) { return x.edge - y.edge; });
  const i1 = intersecciones[0], i2 = intersecciones[1];
  const ip1 = L.latLng(i1.lat, i1.lng);
  const ip2 = L.latLng(i2.lat, i2.lng);

  const ringA = [ip1];
  for (let i = (i1.edge + 1) % ring.length; i !== (i2.edge + 1) % ring.length; i = (i + 1) % ring.length) {
    ringA.push(ring[i]);
  }
  ringA.push(ip2);

  const ringB = [ip2];
  for (let i = (i2.edge + 1) % ring.length; i !== (i1.edge + 1) % ring.length; i = (i + 1) % ring.length) {
    ringB.push(ring[i]);
  }
  ringB.push(ip1);

  if (ringA.length < 4 || ringB.length < 4) return null;
  return [ringA, ringB];
}

function ver_interseccionSegmentos(a, b, c, d) {
  const r = { x: b.lng - a.lng, y: b.lat - a.lat };
  const s = { x: d.lng - c.lng, y: d.lat - c.lat };
  const denom = r.x * s.y - r.y * s.x;
  if (Math.abs(denom) < 1e-12) return null;
  const qp = { x: c.lng - a.lng, y: c.lat - a.lat };
  const t = (qp.x * s.y - qp.y * s.x) / denom;
  const u = (qp.x * r.y - qp.y * r.x) / denom;
  if (u < -1e-9 || u > 1 + 1e-9) return null;
  return { lng: a.lng + t * r.x, lat: a.lat + t * r.y, t: t, u: u };
}

function ver_actualizarCroquisLabel(texto) {
  if (verCroquisLabel && verCroquisMap) {
    verCroquisMap.removeLayer(verCroquisLabel);
    verCroquisLabel = null;
  }
  if (!verCroquisSelectedLayer || !texto || !texto.trim() || !verCroquisMap) return;
  const center = verCroquisLabelLatLng || verCroquisSelectedLayer.getBounds().getCenter();
  verCroquisLabel = L.marker(center, {
    interactive: true,
    draggable: true,
    icon: L.divIcon({ className: 'croquis-map-label', html: ver_escapeHtml(texto.trim()), iconSize: [240, 24], iconAnchor: [120, 12] })
  }).addTo(verCroquisMap);
  verCroquisLabel.on('dragend', function() {
    verCroquisLabelLatLng = verCroquisLabel.getLatLng();
  });
}

function ver_setCroquisInfo(texto) {
  const info = document.getElementById('ver_croquis_info');
  if (info) info.textContent = texto;
}

function ver_obtenerCentroUtm(layer) {
  if (!layer || typeof proj4 === 'undefined') return null;
  const center = layer.getBounds().getCenter();
  const utm = proj4('EPSG:4326', 'EPSG:32613', [center.lng, center.lat]);
  return { x: Number(utm[0]).toFixed(2), y: Number(utm[1]).toFixed(2) };
}

function ver_obtenerVerticesUtm(layer) {
  if (!layer || typeof proj4 === 'undefined') return [];
  const latlngs = layer.getLatLngs();
  const ring = Array.isArray(latlngs[0]) ? latlngs[0] : latlngs;
  return ring.map(function(p) {
    const utm = proj4('EPSG:4326', 'EPSG:32613', [p.lng, p.lat]);
    return { x: Number(utm[0].toFixed(2)), y: Number(utm[1].toFixed(2)), lat: Number(p.lat.toFixed(8)), lng: Number(p.lng.toFixed(8)) };
  });
}

function ver_centrarCroquisActual() {
  if (!verCroquisMap) return;
  if (verCroquisSelectedLayer) {
    verCroquisMap.fitBounds(verCroquisSelectedLayer.getBounds(), { padding: [35, 35], maxZoom: 19 });
    return;
  }
  verCroquisMap.setView([22.228, -102.320], 14);
}

function ver_mostrarPreviewCroquis(url) {
  const prev = document.getElementById('ver_prev_img');
  const ph = document.getElementById('ver_prev_ph');
  if (prev && url) {
    prev.src = url;
    prev.style.display = 'block';
  }
  if (ph && url) ph.style.display = 'none';
}

function ver_canvasToBlob(canvas) {
  return new Promise(function(resolve) { canvas.toBlob(resolve, 'image/png', 0.95); });
}

function ver_parseJsonSeguro(value) {
  if (!value) return null;
  if (typeof value === 'object') return value;
  try { return JSON.parse(value); } catch (e) { return null; }
}

function ver_obtenerCroquisFeatureCollection() {
  const features = [];
  const selectedStamp = verCroquisSelectedLayer ? L.Util.stamp(verCroquisSelectedLayer) : null;
  let selectedIncluded = false;

  if (verCroquisDrawn) {
    verCroquisDrawn.eachLayer(function(layer) {
      const feature = layer.toGeoJSON();
      feature.properties = Object.assign({}, feature.properties || {}, {
        croquis_source: layer._croquisSource || 'dibujado',
        croquis_selected: selectedStamp === L.Util.stamp(layer)
      });
      if (feature.properties.croquis_selected) selectedIncluded = true;
      features.push(feature);
    });
  }

  if (verCroquisSelectedLayer && !selectedIncluded) {
    const feature = verCroquisSelectedLayer.toGeoJSON();
    feature.properties = Object.assign({}, feature.properties || {}, {
      croquis_source: verCroquisSelectedLayer._croquisSource || 'seleccionado',
      croquis_selected: true
    });
    features.push(feature);
  }

  return { type: 'FeatureCollection', features: features };
}

function ver_construirGeoreferenciaCroquis(bounds, texto, featureCollection) {
  const center = verCroquisMap.getCenter();
  const label = verCroquisLabelLatLng || (verCroquisSelectedLayer ? verCroquisSelectedLayer.getBounds().getCenter() : null);
  return {
    crs: 'EPSG:4326',
    utm_crs: 'EPSG:32613',
    bounds: { north: bounds.getNorth(), south: bounds.getSouth(), east: bounds.getEast(), west: bounds.getWest() },
    map_center: { lat: center.lat, lng: center.lng },
    map_zoom: verCroquisMap.getZoom(),
    base_layer: verCroquisActiveBaseLayer,
    label_position: label ? { lat: label.lat, lng: label.lng } : null,
    label_text: texto,
    layers: {
      catastro_visible: !!(verCroquisParcelas && verCroquisMap.hasLayer(verCroquisParcelas)),
      drawn_count: verCroquisDrawn ? verCroquisDrawn.getLayers().length : 0,
      feature_count: featureCollection.features.length
    }
  };
}

function ver_setCroquisCaptureMode(enabled) {
  const mapEl = document.getElementById('ver_mapa_croquis');
  if (!mapEl) return;
  mapEl.classList.toggle('croquis-capturando', enabled);
  if (enabled) {
    verCroquisCaptureHadParcelas = !!(verCroquisParcelas && verCroquisMap && verCroquisMap.hasLayer(verCroquisParcelas));
    if (verCroquisCaptureHadParcelas) verCroquisMap.removeLayer(verCroquisParcelas);
    if (verCroquisDrawn && verCroquisDrawn.bringToFront) verCroquisDrawn.bringToFront();
    ver_crearOverlayCapturaCroquis();
  } else if (verCroquisCaptureHadParcelas && verCroquisParcelas && verCroquisMap && !verCroquisMap.hasLayer(verCroquisParcelas)) {
    ver_removerOverlayCapturaCroquis();
    verCroquisParcelas.addTo(verCroquisMap);
    if (verCroquisParcelas.bringToFront) verCroquisParcelas.bringToFront();
    if (verCroquisDrawn.bringToFront) verCroquisDrawn.bringToFront();
    verCroquisCaptureHadParcelas = false;
  } else {
    ver_removerOverlayCapturaCroquis();
  }
}

function ver_crearOverlayCapturaCroquis() {
  ver_removerOverlayCapturaCroquis();
  if (!verCroquisMap) return;

  const mapEl = document.getElementById('ver_mapa_croquis');
  const featureCollection = ver_obtenerCroquisFeatureCollection();
  const size = verCroquisMap.getSize();
  const svgNS = 'http://www.w3.org/2000/svg';
  const svg = document.createElementNS(svgNS, 'svg');
  svg.setAttribute('class', 'croquis-capture-overlay');
  svg.setAttribute('width', size.x);
  svg.setAttribute('height', size.y);
  svg.setAttribute('viewBox', '0 0 ' + size.x + ' ' + size.y);

  featureCollection.features.forEach(function(feature) {
    ver_agregarFeatureSvgCaptura(svg, feature);
  });

  const texto = document.getElementById('ver_texto_poligono')?.value || '';
  const label = verCroquisLabelLatLng || (verCroquisSelectedLayer ? verCroquisSelectedLayer.getBounds().getCenter() : null);
  if (texto.trim() && label) {
    const p = verCroquisMap.latLngToContainerPoint(label);
    const txt = document.createElementNS(svgNS, 'text');
    txt.setAttribute('x', p.x);
    txt.setAttribute('y', p.y);
    txt.setAttribute('text-anchor', 'middle');
    txt.setAttribute('dominant-baseline', 'middle');
    txt.setAttribute('font-family', 'Arial, sans-serif');
    txt.setAttribute('font-size', '15');
    txt.setAttribute('font-weight', '700');
    txt.setAttribute('paint-order', 'stroke');
    txt.setAttribute('stroke', '#ffffff');
    txt.setAttribute('stroke-width', '4');
    txt.setAttribute('fill', '#111111');
    txt.textContent = texto.trim();
    svg.appendChild(txt);
  }

  mapEl.appendChild(svg);
  verCroquisCaptureOverlay = svg;
}

function ver_agregarFeatureSvgCaptura(svg, feature) {
  if (!feature || !feature.geometry) return;
  const geom = feature.geometry;
  if (geom.type === 'Polygon') {
    ver_agregarPolygonSvgCaptura(svg, geom.coordinates, feature.properties || {});
  } else if (geom.type === 'MultiPolygon') {
    geom.coordinates.forEach(function(coords) {
      ver_agregarPolygonSvgCaptura(svg, coords, feature.properties || {});
    });
  }
}

function ver_agregarPolygonSvgCaptura(svg, coordinates, props) {
  if (!coordinates || !coordinates[0]) return;
  const svgNS = 'http://www.w3.org/2000/svg';
  const path = document.createElementNS(svgNS, 'path');
  let d = '';

  coordinates.forEach(function(ring) {
    ring.forEach(function(coord, index) {
      const p = verCroquisMap.latLngToContainerPoint([coord[1], coord[0]]);
      d += (index === 0 ? 'M ' : ' L ') + p.x.toFixed(2) + ' ' + p.y.toFixed(2);
    });
    d += ' Z ';
  });

  const selected = !!props.croquis_selected;
  path.setAttribute('d', d);
  path.setAttribute('fill', selected ? '#ffd166' : '#5eead4');
  path.setAttribute('fill-opacity', selected ? '0.45' : '0.35');
  path.setAttribute('stroke', selected ? '#b21f3b' : '#0f766e');
  path.setAttribute('stroke-width', selected ? '4' : '3');
  path.setAttribute('stroke-linejoin', 'round');
  svg.appendChild(path);
}

function ver_removerOverlayCapturaCroquis() {
  if (verCroquisCaptureOverlay && verCroquisCaptureOverlay.parentNode) {
    verCroquisCaptureOverlay.parentNode.removeChild(verCroquisCaptureOverlay);
  }
  verCroquisCaptureOverlay = null;
}

function ver_guardarCroquisMapa() {
  const msg = document.getElementById('ver_msg_croquis');
  const btn = document.getElementById('ver_btn_subir');
  if (!verCroquisSelectedLayer) {
    if (msg) { msg.textContent = 'Selecciona o dibuja un poligono antes de guardar.'; msg.style.color = '#856404'; }
    return;
  }
  if (!verCroquisCurrentTramiteId && !verCroquisCurrentFolio) {
    if (msg) { msg.textContent = 'Falta el tramite destino del croquis.'; msg.style.color = '#dc3545'; }
    return;
  }
  if (typeof html2canvas === 'undefined') {
    if (msg) { msg.textContent = 'No se cargo la libreria de captura del mapa.'; msg.style.color = '#dc3545'; }
    return;
  }

  if (msg) { msg.textContent = 'Capturando croquis optimizado...'; msg.style.color = '#555'; }
  if (btn) btn.disabled = true;
  if (verCroquisMap) verCroquisMap.invalidateSize();

  ver_setCroquisCaptureMode(true);
  requestAnimationFrame(function() {
    html2canvas(document.getElementById('ver_mapa_croquis'), {
      useCORS: true,
      allowTaint: false,
      backgroundColor: '#ffffff',
      scale: 0.85,
      logging: false
    })
      .then(ver_canvasToBlob)
      .then(function(blob) {
        const fd = new FormData();
        const featureCollection = ver_obtenerCroquisFeatureCollection();
        const bounds = verCroquisSelectedLayer.getBounds();
        const texto = document.getElementById('ver_texto_poligono')?.value || '';
        const utmVertices = ver_obtenerVerticesUtm(verCroquisSelectedLayer);
        const centroUtm = ver_obtenerCentroUtm(verCroquisSelectedLayer);
        const georeferencia = ver_construirGeoreferenciaCroquis(bounds, texto, featureCollection);

        if (verCroquisCurrentTramiteId) fd.append('id', verCroquisCurrentTramiteId);
        fd.append('folio', verCroquisCurrentFolio);
        fd.append('texto', texto);
        fd.append('origen', verCroquisSelectedLayer._croquisSource || 'seleccionado');
        fd.append('cuenta_catastral_origen', verCroquisSelectedLayer.feature?.properties?.CVE_CAT_OR || '');
        fd.append('geojson', JSON.stringify(featureCollection));
        fd.append('utm_vertices', JSON.stringify(utmVertices));
        fd.append('utm_centro_x', centroUtm ? centroUtm.x : '');
        fd.append('utm_centro_y', centroUtm ? centroUtm.y : '');
        fd.append('georeferencia', JSON.stringify(georeferencia));
        fd.append('croquis', blob, 'croquis_mapa.png');
        if (msg) { msg.textContent = 'Guardando croquis...'; msg.style.color = '#555'; }
        return fetch('php/guardar_croquis_mapa.php', { method: 'POST', body: fd, credentials: 'same-origin' });
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (btn) btn.disabled = false;
        if (data.success) {
          if (msg) { msg.textContent = 'Croquis guardado. Ya puedes imprimir.'; msg.style.color = '#198754'; }
          ver_mostrarEstado(true, data.url || data.archivo || null);
          ver_mostrarPreviewCroquis(data.url || data.archivo || '');
        } else {
          if (msg) { msg.textContent = 'Error: ' + (data.message || 'No se pudo guardar.'); msg.style.color = '#dc3545'; }
        }
      })
      .catch(function(err) {
        console.error('Error guardando croquis de mapa:', err);
        if (btn) btn.disabled = false;
        if (msg) { msg.textContent = 'Error capturando el mapa. Intenta cambiar a la capa Mapa y guardar de nuevo.'; msg.style.color = '#dc3545'; }
      })
      .finally(function() {
        ver_setCroquisCaptureMode(false);
      });
  });
}

function ver_escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

document.getElementById('modalConstancia')?.addEventListener('shown.bs.modal', function() {
  const idSub = document.getElementById('c_id')?.value || '';
  const folio = document.getElementById('c_folio_hidden')?.value || '';
  const croquisUrl = document.getElementById('ver_prev_img')?.src || '';
  ver_prepararCroquisMapa(idSub, folio, croquisUrl);
});

// =====================================================
// CROQUIS CON OPENLAYERS
// Sustituye las funciones Leaflet anteriores para el modal de constancia.
// =====================================================
(function() {
  let map = null;
  let catastroSource = null;
  let catastroLayer = null;
  let callesSource = null;
  let callesLayer = null;
  let trabajoSource = null;
  let trabajoLayer = null;
  let baseLayers = {};
  let activeBaseLayer = 'Mapa';
  let selectedFeature = null;
  let labelOverlay = null;
  let labelElement = null;
  let labelTextElement = null;
  let labelCoordinate = null;
  let drawInteraction = null;
  let modifyInteraction = null;
  let splitMode = false;
  let splitPoints = [];
  let splitHelperSource = null;
  let currentTramiteId = '';
  let currentFolio = '';
  let captureHadCatastro = false;
  let toolbarButtons = {};
  let polygonLookupToken = 0;
  let extraTexts = [];
  let selectedExtraTextId = null;
  const defaultLabelSize = 14;
  const defaultLabelRotation = 0;

  const normalStyle = new ol.style.Style({
    stroke: new ol.style.Stroke({ color: '#7b0f2b', width: 1 }),
    fill: new ol.style.Fill({ color: 'rgba(145,201,223,0)' })
  });
  const selectedStyle = new ol.style.Style({
    stroke: new ol.style.Stroke({ color: '#b21f3b', width: 4 }),
    fill: new ol.style.Fill({ color: 'rgba(255,209,102,0.45)' })
  });
  const drawnStyle = new ol.style.Style({
    stroke: new ol.style.Stroke({ color: '#0f766e', width: 3 }),
    fill: new ol.style.Fill({ color: 'rgba(94,234,212,0.35)' })
  });
  const splitStyle = new ol.style.Style({
    stroke: new ol.style.Stroke({ color: '#f59e0b', width: 4, lineDash: [8, 8] }),
    image: new ol.style.Circle({
      radius: 5,
      fill: new ol.style.Fill({ color: '#f59e0b' }),
      stroke: new ol.style.Stroke({ color: '#fff', width: 1 })
    })
  });
  const callesStyle = new ol.style.Style({
    stroke: new ol.style.Stroke({ color: 'rgba(49, 65, 78, 0.72)', width: 1.4 }),
    fill: new ol.style.Fill({ color: 'rgba(255, 255, 255, 0.08)' })
  });

  function initOpenLayersCroquis() {
    if (map || typeof ol === 'undefined' || !document.getElementById('ver_mapa_croquis')) return;
    if (typeof proj4 !== 'undefined') {
      proj4.defs('EPSG:32613', '+proj=utm +zone=13 +datum=WGS84 +units=m +no_defs');
      if (ol.proj.proj4 && typeof ol.proj.proj4.register === 'function') {
        ol.proj.proj4.register(proj4);
      }
    }

    const osm = new ol.layer.Tile({
      source: new ol.source.XYZ({
        url: 'https://{a-c}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
        crossOrigin: 'anonymous',
        maxZoom: 19,
        attributions: '&copy; OpenStreetMap contributors &copy; CARTO'
      }),
      visible: true
    });
    const satelite = new ol.layer.Tile({
      source: new ol.source.XYZ({
        url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        crossOrigin: 'anonymous',
        maxZoom: 22
      }),
      visible: false
    });
    baseLayers = { 'Mapa': osm, 'Satelital': satelite };

    callesSource = new ol.source.Vector();
    callesLayer = new ol.layer.Vector({
      source: callesSource,
      style: callesStyle,
      visible: true,
      properties: { croquis_readonly: true, title: 'Calles' }
    });
    catastroSource = new ol.source.Vector();
    catastroLayer = new ol.layer.Vector({ source: catastroSource, style: normalStyle });
    trabajoSource = new ol.source.Vector();
    trabajoLayer = new ol.layer.Vector({
      source: trabajoSource,
      style: function(feature) {
        return feature === selectedFeature ? selectedStyle : drawnStyle;
      }
    });
    splitHelperSource = new ol.source.Vector();
    const splitHelperLayer = new ol.layer.Vector({ source: splitHelperSource, style: splitStyle });

    map = new ol.Map({
      target: 'ver_mapa_croquis',
      layers: [osm, satelite, callesLayer, catastroLayer, trabajoLayer, splitHelperLayer],
      view: new ol.View({
        center: ol.proj.fromLonLat([-102.320, 22.228]),
        zoom: 14,
        maxZoom: 22
      })
    });
    map.addControl(createCroquisToolbar());

    modifyInteraction = new ol.interaction.Modify({ source: trabajoSource });
    map.addInteraction(modifyInteraction);
    map.on('singleclick', onMapClick);

    document.getElementById('ver_texto_poligono')?.addEventListener('input', function() {
      if (selectedFeature) selectedFeature.set('croquis_text', this.value);
      updateLabel(this.value);
    });
    document.getElementById('ver_texto_tamano')?.addEventListener('input', function() {
      applyLabelControlValues();
    });
    document.getElementById('ver_texto_orientacion')?.addEventListener('input', function() {
      syncRotationSlider(this.value);
      applyLabelControlValues();
    });
    document.getElementById('ver_texto_orientacion_slider')?.addEventListener('input', function() {
      const input = document.getElementById('ver_texto_orientacion');
      if (input) input.value = this.value;
      applyLabelControlValues();
    });
    document.getElementById('ver_texto_libre_orientacion')?.addEventListener('input', function() {
      syncExtraRotationSlider(this.value);
      applySelectedExtraTextLive();
    });
    document.getElementById('ver_texto_libre')?.addEventListener('input', function() {
      applySelectedExtraTextLive();
    });
    document.getElementById('ver_texto_libre_tamano')?.addEventListener('input', function() {
      applySelectedExtraTextLive();
    });
    document.getElementById('ver_texto_libre_orientacion_slider')?.addEventListener('input', function() {
      const input = document.getElementById('ver_texto_libre_orientacion');
      if (input) input.value = this.value;
      applySelectedExtraTextLive();
    });
    document.getElementById('ver_btn_agregar_texto')?.addEventListener('click', addExtraTextFromControls);
    document.getElementById('ver_btn_actualizar_texto')?.addEventListener('click', updateSelectedExtraTextFromControls);
    document.getElementById('ver_btn_borrar_texto')?.addEventListener('click', deleteSelectedExtraText);
    document.getElementById('ver_btn_centrar_croquis')?.addEventListener('click', centerCurrentFeature);
    document.getElementById('ver_btn_dibujar_croquis')?.addEventListener('click', startDrawPolygon);
    document.getElementById('ver_btn_subdividir_croquis')?.addEventListener('click', startSplitPolygon);
    document.getElementById('ver_btn_capa_mapa')?.addEventListener('click', function() { setBaseLayer('Mapa'); });
    document.getElementById('ver_btn_capa_satelite')?.addEventListener('click', function() { setBaseLayer('Satelital'); });

    loadCallesLayer();
    loadCatastroPolygons();
  }

  function createCroquisToolbar() {
    const el = document.createElement('div');
    el.className = 'croquis-ol-toolbar ol-unselectable ol-control';
    const tools = [
      { key: 'select', icon: 'bi-cursor', title: 'Seleccionar / editar', action: function() { stopDrawPolygon(); cancelSplit(false); setToolbarMode('select'); } },
      { key: 'center', icon: 'bi-crosshair', title: 'Centrar poligono', action: centerCurrentFeature },
      { key: 'draw', icon: 'bi-pencil-square', title: 'Dibujar poligono', action: startDrawPolygon },
      { key: 'split', icon: 'bi-scissors', title: 'Subdividir poligono', action: startSplitPolygon },
      { key: 'text', icon: 'bi-fonts', title: 'Agregar texto de referencia', action: addExtraTextFromControls },
      { key: 'delete', icon: 'bi-trash', title: 'Borrar poligono seleccionado', action: deleteSelectedFeature },
      { key: 'baseMap', icon: 'bi-map', title: 'Mapa base CARTO', action: function() { setBaseLayer('Mapa'); } },
      { key: 'satellite', icon: 'bi-globe-americas', title: 'Vista satelital', action: function() { setBaseLayer('Satelital'); } },
      { key: 'streetLayer', icon: 'bi-signpost-split', title: 'Mostrar u ocultar capa Calles', action: toggleCallesLayer },
      { key: 'save', icon: 'bi-cloud-upload', title: 'Guardar croquis', action: saveMapCroquis }
    ];

    tools.forEach(function(tool) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.title = tool.title;
      btn.setAttribute('aria-label', tool.title);
      btn.innerHTML = '<i class="bi ' + tool.icon + '"></i>';
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        tool.action();
      });
      toolbarButtons[tool.key] = btn;
      el.appendChild(btn);
    });

    setToolbarMode('select');
    toolbarButtons.streetLayer?.classList.add('active');
    return new ol.control.Control({ element: el });
  }

  function setToolbarMode(mode) {
    ['select', 'draw', 'split'].forEach(function(key) {
      if (!toolbarButtons[key]) return;
      toolbarButtons[key].classList.toggle('active', key === mode);
    });
  }

  function toggleCallesLayer() {
    if (!callesLayer) return;
    const visible = !callesLayer.getVisible();
    callesLayer.setVisible(visible);
    toolbarButtons.streetLayer?.classList.toggle('active', visible);
    setInfo('Capa Calles ' + (visible ? 'visible.' : 'oculta.'));
  }

  function loadCallesLayer() {
    fetch('php/obtener_calles_geojson.php', {
      credentials: 'same-origin',
      cache: 'no-store'
    })
      .then(r => r.json())
      .then(function(data) {
        if (!data || data.success === false || !Array.isArray(data.features)) {
          throw new Error(data?.message || 'Respuesta de calles invalida');
        }
        const dataProjection = data.dataProjection
          || data.crs?.properties?.name
          || 'EPSG:32613';
        const features = new ol.format.GeoJSON().readFeatures(data, {
          dataProjection: dataProjection,
          featureProjection: 'EPSG:3857'
        });
        callesSource.clear();
        callesSource.addFeatures(features);
      })
      .catch(function(err) {
        console.warn('No se pudo cargar la capa Calles:', err);
        callesLayer.setVisible(false);
        toolbarButtons.streetLayer?.classList.remove('active');
      });
  }

  function loadCatastroPolygons() {
    fetch('./Geojson/TRAMITES_reprojected.geojson')
      .then(r => r.json())
      .then(data => {
        const features = new ol.format.GeoJSON().readFeatures(data, {
          dataProjection: 'EPSG:4326',
          featureProjection: 'EPSG:3857'
        });
        features.forEach(f => f.set('croquis_source', 'catastro'));
        catastroSource.addFeatures(features);
      })
      .catch(err => console.warn('No se pudieron cargar poligonos base OpenLayers:', err));
  }

  function prepareMap(tramiteId, folio, croquisUrl) {
    currentTramiteId = tramiteId || '';
    currentFolio = folio || '';
    initOpenLayersCroquis();
    setTimeout(function() {
      if (!map) return;
      map.updateSize();
      loadSavedCroquis();
      if (croquisUrl) ver_mostrarPreviewCroquis(croquisUrl);
    }, 180);
  }

  function loadSavedCroquis() {
    if (!currentTramiteId || !trabajoSource) {
      clearExtraTexts();
      return;
    }
    fetch('php/obtener_croquis_poligono.php?id=' + encodeURIComponent(currentTramiteId), { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        trabajoSource.clear();
        selectedFeature = null;
        labelCoordinate = null;
        updateLabel('');
        clearExtraTexts();

        if (!data.success || !data.poligono || !data.poligono.geojson) {
          const inputTexto = document.getElementById('ver_texto_poligono');
          if (inputTexto) inputTexto.value = '';
          setLabelControlValues(defaultLabelSize, defaultLabelRotation);
          setInfo('Selecciona un poligono existente o dibuja uno nuevo.');
          return;
        }

        const geojson = typeof data.poligono.geojson === 'string' ? JSON.parse(data.poligono.geojson) : data.poligono.geojson;
        const georef = parseJsonSafe(data.poligono.georeferencia);
        const features = new ol.format.GeoJSON().readFeatures(geojson, {
          dataProjection: 'EPSG:4326',
          featureProjection: 'EPSG:3857'
        });
        const detalles = Array.isArray(data.poligono.detalles) ? data.poligono.detalles : [];
        const detallesPorUid = {};
        detalles.forEach(function(detalle) {
          if (detalle && detalle.feature_uid) detallesPorUid[detalle.feature_uid] = detalle;
        });
        features.forEach((f, idx) => {
          const detalle = detallesPorUid[f.get('croquis_uid')] || detalles[idx] || null;
          f.set('croquis_source', f.get('croquis_source') || data.poligono.origen || 'guardado');
          if (detalle) {
            if (detalle.feature_uid) f.set('croquis_uid', detalle.feature_uid);
            if (detalle.numero_poligono) f.set('numero_poligono', detalle.numero_poligono);
            if (detalle.origen) f.set('croquis_source', detalle.origen);
            if (detalle.cuenta_catastral_origen) f.set('CVE_CAT_OR', detalle.cuenta_catastral_origen);
            if (detalle.texto) f.set('croquis_text', detalle.texto);
            if (detalle.label_lng !== null && detalle.label_lng !== undefined && detalle.label_lng !== '') f.set('croquis_label_lng', Number(detalle.label_lng));
            if (detalle.label_lat !== null && detalle.label_lat !== undefined && detalle.label_lat !== '') f.set('croquis_label_lat', Number(detalle.label_lat));
            applyStoredLabelStyle(f, detalle);
          }
          if (!f.get('croquis_text') && data.poligono.texto && features.length === 1) f.set('croquis_text', data.poligono.texto);
          ensureFeatureUid(f);
          trabajoSource.addFeature(f);
          if (!selectedFeature || f.get('croquis_selected')) selectedFeature = f;
        });

        const inputTexto = document.getElementById('ver_texto_poligono');
        if (inputTexto) inputTexto.value = '';
        setLabelControlValues(defaultLabelSize, defaultLabelRotation);

        if (georef && georef.base_layer) setBaseLayer(georef.base_layer);
        if (georef && georef.layers && typeof georef.layers.calles_visible === 'boolean') {
          callesLayer.setVisible(georef.layers.calles_visible);
          toolbarButtons.streetLayer?.classList.toggle('active', georef.layers.calles_visible);
        }
        if (georef && georef.map_center && georef.map_zoom) {
          map.getView().setCenter(ol.proj.fromLonLat([georef.map_center.lng, georef.map_center.lat]));
          map.getView().setZoom(georef.map_zoom);
        }
        loadExtraTexts(georef && Array.isArray(georef.extra_texts) ? georef.extra_texts : []);
        if (selectedFeature && selectedFeature.get('croquis_label_lng') && selectedFeature.get('croquis_label_lat')) {
          labelCoordinate = ol.proj.fromLonLat([Number(selectedFeature.get('croquis_label_lng')), Number(selectedFeature.get('croquis_label_lat'))]);
        } else if (georef && georef.label_position) {
          labelCoordinate = ol.proj.fromLonLat([georef.label_position.lng, georef.label_position.lat]);
        }
        if (selectedFeature) selectFeature(selectedFeature, selectedFeature.get('croquis_source') || 'guardado');
        if (data.poligono.croquis_archivo) ver_mostrarPreviewCroquis(data.poligono.croquis_archivo);
      })
      .catch(err => console.warn('No se pudo cargar croquis OpenLayers:', err));
  }

  function onMapClick(evt) {
    if (splitMode) {
      registerSplitPoint(evt.coordinate);
      return;
    }

    const hit = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
      return { feature, layer };
    }, {
      hitTolerance: 5,
      layerFilter: function(layer) {
        return layer === catastroLayer || layer === trabajoLayer;
      }
    });
    if (!hit) return;

    if (hit.layer === catastroLayer) {
      useCatastroFeature(hit.feature);
    } else if (hit.layer === trabajoLayer) {
      selectFeature(hit.feature, hit.feature.get('croquis_source') || 'dibujado');
    }
  }

  function useCatastroFeature(feature) {
    const prevCopy = selectedFeature && selectedFeature.get('croquis_source') === 'catastro-copia' ? selectedFeature : null;
    if (prevCopy) trabajoSource.removeFeature(prevCopy);

    const clone = feature.clone();
    clone.setProperties(feature.getProperties());
    clone.setGeometry(feature.getGeometry().clone());
    clone.set('croquis_source', 'catastro-copia');
    clone.set('croquis_text', feature.get('croquis_text') || '');
    clone.set('croquis_uid', createFeatureUid());
    trabajoSource.addFeature(clone);
    selectFeature(clone, 'catastro-copia');
    loadStoredPolygonData(clone);
  }

  function selectFeature(feature, source) {
    selectedFeature = feature;
    ensureFeatureUid(selectedFeature);
    selectedFeature.set('croquis_source', source || selectedFeature.get('croquis_source') || 'seleccionado');
    const savedLabel = getFeatureLabelCoordinate(feature);
    if (savedLabel && feature.getGeometry().intersectsCoordinate(savedLabel)) {
      labelCoordinate = savedLabel;
    } else if (!labelCoordinate || !feature.getGeometry().intersectsCoordinate(labelCoordinate)) {
      labelCoordinate = ol.extent.getCenter(feature.getGeometry().getExtent());
    }
    const texto = getFeatureText(feature);
    const inputTexto = document.getElementById('ver_texto_poligono');
    if (inputTexto) inputTexto.value = texto;
    setLabelControlValues(getFeatureLabelSize(feature), getFeatureLabelRotation(feature));
    trabajoLayer.changed();
    updateLabel(texto);
    const utm = getFeatureCenterUtm(feature);
    const numero = getFeatureNumber(feature);
    setInfo('Poligono seleccionado (' + selectedFeature.get('croquis_source') + ')' + (numero ? ' | Numero: ' + numero : '') + (utm ? ' | UTM X: ' + utm.x + ' Y: ' + utm.y : '') + (texto ? ' | Con texto guardado' : ''));
  }

  function loadStoredPolygonData(feature) {
    const numero = getFeatureNumber(feature);
    const msg = document.getElementById('ver_msg_croquis');
    if (!numero) {
      setInfo('Poligono base copiado a la capa editable. Puedes editarlo, subdividirlo o guardar.');
      return;
    }

    const token = ++polygonLookupToken;
    setInfo('Poligono numero ' + numero + ' seleccionado. Buscando datos guardados...');

    fetch('php/obtener_datos_poligono_croquis.php?cuenta=' + encodeURIComponent(numero), { credentials: 'same-origin' })
      .then(r => r.json())
      .then(function(data) {
        if (token !== polygonLookupToken || feature !== selectedFeature) return;

        if (!data.success || !data.poligono) {
          setInfo('Poligono numero ' + numero + ' copiado a la capa editable. Sin texto guardado previo.');
          if (msg) msg.textContent = '';
          return;
        }

        const guardado = data.poligono;
        if (guardado.feature_uid) feature.set('croquis_uid', guardado.feature_uid);
        if (guardado.texto) feature.set('croquis_text', guardado.texto);
        if (guardado.label_lng !== null && guardado.label_lng !== undefined && guardado.label_lng !== '') {
          feature.set('croquis_label_lng', Number(guardado.label_lng));
        }
        if (guardado.label_lat !== null && guardado.label_lat !== undefined && guardado.label_lat !== '') {
          feature.set('croquis_label_lat', Number(guardado.label_lat));
        }
        applyStoredLabelStyle(feature, guardado);

        const inputTexto = document.getElementById('ver_texto_poligono');
        const texto = getFeatureText(feature);
        if (inputTexto) inputTexto.value = texto;
        setLabelControlValues(getFeatureLabelSize(feature), getFeatureLabelRotation(feature));
        const savedLabel = getFeatureLabelCoordinate(feature);
        if (savedLabel && feature.getGeometry().intersectsCoordinate(savedLabel)) labelCoordinate = savedLabel;
        updateLabel(texto);

        setInfo('Poligono numero ' + numero + ' seleccionado | Datos guardados encontrados' + (guardado.tramite_id ? ' | Tramite ID: ' + guardado.tramite_id : ''));
        if (msg) { msg.textContent = 'Se cargaron datos guardados para este poligono.'; msg.style.color = '#198754'; }
      })
      .catch(function(err) {
        console.warn('No se pudieron consultar datos del poligono:', err);
        if (token === polygonLookupToken && feature === selectedFeature) {
          setInfo('Poligono numero ' + numero + ' copiado a la capa editable. Puedes editarlo, subdividirlo o guardar.');
        }
      });
  }

  function deleteSelectedFeature() {
    const msg = document.getElementById('ver_msg_croquis');
    stopDrawPolygon();
    cancelSplit(false);

    if (!selectedFeature) {
      if (msg) { msg.textContent = 'Selecciona un poligono para borrarlo.'; msg.style.color = '#856404'; }
      return;
    }

    if (!trabajoSource || !trabajoSource.hasFeature(selectedFeature)) {
      selectedFeature = null;
      labelCoordinate = null;
      updateLabel('');
      setInfo('Solo se pueden borrar poligonos de la capa editable.');
      if (msg) { msg.textContent = 'Primero copia o dibuja el poligono para poder borrarlo.'; msg.style.color = '#856404'; }
      return;
    }

    trabajoSource.removeFeature(selectedFeature);
    polygonLookupToken++;
    selectedFeature = null;
    labelCoordinate = null;
    updateLabel('');
    const inputTexto = document.getElementById('ver_texto_poligono');
    if (inputTexto) inputTexto.value = '';
    setLabelControlValues(defaultLabelSize, defaultLabelRotation);

    const restantes = trabajoSource.getFeatures();
    if (restantes.length) {
      selectFeature(restantes[restantes.length - 1], restantes[restantes.length - 1].get('croquis_source') || 'dibujado');
    } else {
      trabajoLayer.changed();
      setInfo('Poligono borrado. Selecciona uno existente o dibuja uno nuevo.');
    }

    if (msg) { msg.textContent = 'Poligono borrado de la capa editable.'; msg.style.color = '#198754'; }
  }

  function startDrawPolygon() {
    if (!map) return;
    stopDrawPolygon();
    cancelSplit(false);
    setToolbarMode('draw');
    drawInteraction = new ol.interaction.Draw({ source: trabajoSource, type: 'Polygon' });
    drawInteraction.on('drawend', function(e) {
      e.feature.set('croquis_source', 'dibujado');
      e.feature.set('croquis_uid', createFeatureUid());
      e.feature.set('croquis_text', document.getElementById('ver_texto_poligono')?.value || '');
      e.feature.set('croquis_label_size', getInputNumber('ver_texto_tamano', defaultLabelSize, 8, 72));
      e.feature.set('croquis_label_rotation', getInputNumber('ver_texto_orientacion', defaultLabelRotation, -180, 180));
      setTimeout(function() {
        selectFeature(e.feature, 'dibujado');
        stopDrawPolygon();
      }, 0);
    });
    map.addInteraction(drawInteraction);
    setInfo('Dibuja el poligono en el mapa. Doble clic para terminar.');
  }

  function stopDrawPolygon() {
    if (drawInteraction && map) map.removeInteraction(drawInteraction);
    drawInteraction = null;
    if (!splitMode) setToolbarMode('select');
  }

  function startSplitPolygon() {
    const msg = document.getElementById('ver_msg_croquis');
    if (!selectedFeature) {
      if (msg) { msg.textContent = 'Selecciona un poligono antes de subdividir.'; msg.style.color = '#856404'; }
      return;
    }
    stopDrawPolygon();
    splitMode = true;
    setToolbarMode('split');
    splitPoints = [];
    splitHelperSource.clear();
    map.getTargetElement().style.cursor = 'crosshair';
    if (msg) { msg.textContent = 'Subdividir: marca dos puntos para trazar la linea de corte.'; msg.style.color = '#856404'; }
  }

  function cancelSplit(clearMessage = true) {
    splitMode = false;
    splitPoints = [];
    splitHelperSource?.clear();
    if (map) map.getTargetElement().style.cursor = '';
    setToolbarMode('select');
    if (clearMessage) {
      const msg = document.getElementById('ver_msg_croquis');
      if (msg) msg.textContent = '';
    }
  }

  function registerSplitPoint(coordinate) {
    splitPoints.push(coordinate);
    splitHelperSource.clear();
    if (splitPoints.length === 1) {
      splitHelperSource.addFeature(new ol.Feature(new ol.geom.Point(coordinate)));
      return;
    }
    splitHelperSource.addFeature(new ol.Feature(new ol.geom.LineString(splitPoints)));
    splitSelectedPolygon(splitPoints[0], splitPoints[1]);
  }

  function splitSelectedPolygon(p1, p2) {
    const msg = document.getElementById('ver_msg_croquis');
    const parts = calculateSplit(selectedFeature, p1, p2);
    if (!parts || parts.length !== 2) {
      cancelSplit(false);
      if (msg) { msg.textContent = 'No se pudo subdividir. La linea debe cruzar el poligono de lado a lado.'; msg.style.color = '#dc3545'; }
      return;
    }

    const props = selectedFeature.getProperties();
    delete props.geometry;
    trabajoSource.removeFeature(selectedFeature);
    const newFeatures = parts.map(function(ring, idx) {
      const f = new ol.Feature(new ol.geom.Polygon([ring]));
      f.setProperties(Object.assign({}, props, {
        croquis_source: 'subdivision',
        subdivision_part: idx + 1,
        croquis_uid: createFeatureUid(),
        croquis_text: idx === 0 ? (props.croquis_text || '') : ''
      }));
      trabajoSource.addFeature(f);
      return f;
    });
    cancelSplit(false);
    selectFeature(newFeatures[0], 'subdivision');
    if (msg) { msg.textContent = 'Poligono subdividido en dos partes. Puedes ajustar vertices antes de guardar.'; msg.style.color = '#198754'; }
  }

  function calculateSplit(feature, p1, p2) {
    const geom = feature.getGeometry();
    if (!geom || geom.getType() !== 'Polygon') return null;
    let ring = geom.getCoordinates()[0].slice();
    if (ring.length < 4) return null;
    const first = ring[0], last = ring[ring.length - 1];
    if (first[0] === last[0] && first[1] === last[1]) ring.pop();

    const extent = geom.getExtent();
    const span = Math.max(extent[2] - extent[0], extent[3] - extent[1]) * 3 || 1;
    const dx = p2[0] - p1[0], dy = p2[1] - p1[1];
    const len = Math.sqrt(dx * dx + dy * dy);
    if (len === 0) return null;
    const a = [p1[0] - dx / len * span, p1[1] - dy / len * span];
    const b = [p1[0] + dx / len * span, p1[1] + dy / len * span];
    const hits = [];

    for (let i = 0; i < ring.length; i++) {
      const c = ring[i];
      const d = ring[(i + 1) % ring.length];
      const hit = segmentIntersection(a, b, c, d);
      if (hit && hit.u >= 0 && hit.u <= 1) {
        const exists = hits.some(x => Math.abs(x[0] - hit[0]) < 1e-7 && Math.abs(x[1] - hit[1]) < 1e-7);
        if (!exists) hits.push(Object.assign(hit, { edge: i }));
      }
    }

    if (hits.length !== 2 || hits[0].edge === hits[1].edge) return null;
    hits.sort((x, y) => x.edge - y.edge);
    const h1 = hits[0], h2 = hits[1];

    const ringA = [h1];
    for (let i = (h1.edge + 1) % ring.length; i !== (h2.edge + 1) % ring.length; i = (i + 1) % ring.length) ringA.push(ring[i]);
    ringA.push(h2, h1);

    const ringB = [h2];
    for (let i = (h2.edge + 1) % ring.length; i !== (h1.edge + 1) % ring.length; i = (i + 1) % ring.length) ringB.push(ring[i]);
    ringB.push(h1, h2);
    if (ringA.length < 4 || ringB.length < 4) return null;
    return [ringA, ringB];
  }

  function segmentIntersection(a, b, c, d) {
    const r = [b[0] - a[0], b[1] - a[1]];
    const s = [d[0] - c[0], d[1] - c[1]];
    const denom = r[0] * s[1] - r[1] * s[0];
    if (Math.abs(denom) < 1e-12) return null;
    const qp = [c[0] - a[0], c[1] - a[1]];
    const t = (qp[0] * s[1] - qp[1] * s[0]) / denom;
    const u = (qp[0] * r[1] - qp[1] * r[0]) / denom;
    if (u < -1e-9 || u > 1 + 1e-9) return null;
    const hit = [a[0] + t * r[0], a[1] + t * r[1]];
    hit.t = t;
    hit.u = u;
    return hit;
  }

  function updateLabel(text) {
    if (labelOverlay) {
      map.removeOverlay(labelOverlay);
      labelOverlay = null;
      labelElement = null;
      labelTextElement = null;
    }
    if (!selectedFeature || !text || !text.trim() || !map) return;
    selectedFeature.set('croquis_text', text);
    if (!labelCoordinate) labelCoordinate = ol.extent.getCenter(selectedFeature.getGeometry().getExtent());
    setFeatureLabelCoordinate(selectedFeature, labelCoordinate);

    labelElement = document.createElement('div');
    labelElement.className = 'croquis-map-label';
    labelTextElement = document.createElement('span');
    labelTextElement.className = 'croquis-map-label-text';
    labelTextElement.textContent = text.trim();
    labelElement.appendChild(labelTextElement);
    applyLabelElementStyle();
    labelOverlay = new ol.Overlay({ element: labelElement, position: labelCoordinate, positioning: 'center-center', stopEvent: false });
    map.addOverlay(labelOverlay);

    let dragging = false;
    labelElement.addEventListener('pointerdown', function(e) {
      dragging = true;
      e.preventDefault();
    });
    document.addEventListener('pointermove', function(e) {
      if (!dragging) return;
      const rect = map.getTargetElement().getBoundingClientRect();
      labelCoordinate = map.getCoordinateFromPixel([e.clientX - rect.left, e.clientY - rect.top]);
      labelOverlay.setPosition(labelCoordinate);
      setFeatureLabelCoordinate(selectedFeature, labelCoordinate);
    });
    document.addEventListener('pointerup', function() { dragging = false; });
  }

  function applyLabelControlValues() {
    if (!selectedFeature) return;
    const size = getInputNumber('ver_texto_tamano', defaultLabelSize, 8, 72);
    const rotation = getInputNumber('ver_texto_orientacion', defaultLabelRotation, -180, 180);
    selectedFeature.set('croquis_label_size', size);
    selectedFeature.set('croquis_label_rotation', rotation);
    applyLabelElementStyle();
    updateLabel(getFeatureText(selectedFeature));
  }

  function applyLabelElementStyle() {
    if (!labelTextElement || !selectedFeature) return;
    const size = getFeatureLabelSize(selectedFeature);
    const rotation = getFeatureLabelRotation(selectedFeature);
    labelTextElement.style.fontSize = size + 'px';
    labelTextElement.style.lineHeight = Math.max(size + 4, 18) + 'px';
    labelTextElement.style.transform = 'rotate(' + rotation + 'deg)';
  }

  function setLabelControlValues(size, rotation) {
    const sizeInput = document.getElementById('ver_texto_tamano');
    const rotationInput = document.getElementById('ver_texto_orientacion');
    if (sizeInput) sizeInput.value = sanitizeNumber(size, defaultLabelSize, 8, 72);
    if (rotationInput) rotationInput.value = sanitizeNumber(rotation, defaultLabelRotation, -180, 180);
    syncRotationSlider(rotationInput ? rotationInput.value : rotation);
  }

  function syncRotationSlider(value) {
    const slider = document.getElementById('ver_texto_orientacion_slider');
    if (slider) slider.value = sanitizeNumber(value, defaultLabelRotation, -180, 180);
  }

  function getInputNumber(id, fallback, min, max) {
    return sanitizeNumber(document.getElementById(id)?.value, fallback, min, max);
  }

  function sanitizeNumber(value, fallback, min, max) {
    const num = Number(value);
    if (!Number.isFinite(num)) return fallback;
    return Math.min(max, Math.max(min, num));
  }

  function addExtraTextFromControls() {
    if (!map) return;
    const text = (document.getElementById('ver_texto_libre')?.value || '').trim();
    const msg = document.getElementById('ver_msg_croquis');
    if (!text) {
      if (msg) { msg.textContent = 'Escribe el texto de referencia antes de agregarlo.'; msg.style.color = '#856404'; }
      return;
    }
    const item = {
      id: createFeatureUid(),
      text: text,
      size: getInputNumber('ver_texto_libre_tamano', defaultLabelSize, 8, 72),
      rotation: getInputNumber('ver_texto_libre_orientacion', defaultLabelRotation, -180, 180),
      coordinate: map.getView().getCenter()
    };
    createExtraTextOverlay(item);
    extraTexts.push(item);
    selectExtraText(item.id);
    renderExtraTextList();
    if (msg) { msg.textContent = 'Texto agregado. Arrastralo sobre el mapa para ubicarlo.'; msg.style.color = '#198754'; }
  }

  function updateSelectedExtraTextFromControls() {
    const item = getSelectedExtraText();
    const msg = document.getElementById('ver_msg_croquis');
    if (!item) {
      if (msg) { msg.textContent = 'Selecciona un texto de referencia para actualizarlo.'; msg.style.color = '#856404'; }
      return;
    }
    item.text = (document.getElementById('ver_texto_libre')?.value || '').trim();
    item.size = getInputNumber('ver_texto_libre_tamano', defaultLabelSize, 8, 72);
    item.rotation = getInputNumber('ver_texto_libre_orientacion', defaultLabelRotation, -180, 180);
    updateExtraTextElement(item);
    renderExtraTextList();
  }

  function applySelectedExtraTextLive() {
    const item = getSelectedExtraText();
    if (!item) return;
    item.text = (document.getElementById('ver_texto_libre')?.value || '').trim();
    item.size = getInputNumber('ver_texto_libre_tamano', defaultLabelSize, 8, 72);
    item.rotation = getInputNumber('ver_texto_libre_orientacion', defaultLabelRotation, -180, 180);
    updateExtraTextElement(item);
    renderExtraTextList();
  }

  function deleteSelectedExtraText() {
    const item = getSelectedExtraText();
    const msg = document.getElementById('ver_msg_croquis');
    if (!item) {
      if (msg) { msg.textContent = 'Selecciona un texto de referencia para borrarlo.'; msg.style.color = '#856404'; }
      return;
    }
    removeExtraTextOverlay(item);
    extraTexts = extraTexts.filter(function(t) { return t.id !== item.id; });
    selectedExtraTextId = null;
    renderExtraTextList();
    if (msg) { msg.textContent = 'Texto de referencia borrado.'; msg.style.color = '#198754'; }
  }

  function createExtraTextOverlay(item) {
    if (!map || !item) return;
    const element = document.createElement('div');
    element.className = 'croquis-map-label croquis-extra-text';

    const textElement = document.createElement('span');
    textElement.className = 'croquis-map-label-text';
    textElement.style.whiteSpace = 'pre';
    element.appendChild(textElement);

    item.element = element;
    item.textElement = textElement;
    item.overlay = new ol.Overlay({
      element: element,
      position: item.coordinate,
      positioning: 'center-center',
      stopEvent: false
    });
    map.addOverlay(item.overlay);
    updateExtraTextElement(item);

    let dragging = false;
    element.addEventListener('pointerdown', function(e) {
      dragging = true;
      selectExtraText(item.id);
      e.preventDefault();
      e.stopPropagation();
    });
    document.addEventListener('pointermove', function(e) {
      if (!dragging || selectedExtraTextId !== item.id) return;
      const rect = map.getTargetElement().getBoundingClientRect();
      item.coordinate = map.getCoordinateFromPixel([e.clientX - rect.left, e.clientY - rect.top]);
      item.overlay.setPosition(item.coordinate);
    });
    document.addEventListener('pointerup', function() {
      dragging = false;
    });
  }

  function updateExtraTextElement(item) {
    if (!item || !item.textElement) return;
    item.textElement.textContent = item.text;
    item.textElement.style.fontSize = sanitizeNumber(item.size, defaultLabelSize, 8, 72) + 'px';
    item.textElement.style.lineHeight = Math.max(sanitizeNumber(item.size, defaultLabelSize, 8, 72) + 4, 18) + 'px';
    item.textElement.style.transform = 'rotate(' + sanitizeNumber(item.rotation, defaultLabelRotation, -180, 180) + 'deg)';
  }

  function selectExtraText(id) {
    selectedExtraTextId = id;
    extraTexts.forEach(function(item) {
      if (item.element) item.element.classList.toggle('croquis-extra-text-selected', item.id === id);
    });
    const item = getSelectedExtraText();
    if (!item) return;
    const textInput = document.getElementById('ver_texto_libre');
    const sizeInput = document.getElementById('ver_texto_libre_tamano');
    const rotationInput = document.getElementById('ver_texto_libre_orientacion');
    if (textInput) textInput.value = item.text || '';
    if (sizeInput) sizeInput.value = sanitizeNumber(item.size, defaultLabelSize, 8, 72);
    if (rotationInput) rotationInput.value = sanitizeNumber(item.rotation, defaultLabelRotation, -180, 180);
    syncExtraRotationSlider(rotationInput ? rotationInput.value : item.rotation);
    renderExtraTextList();
  }

  function getSelectedExtraText() {
    return extraTexts.find(function(item) { return item.id === selectedExtraTextId; }) || null;
  }

  function syncExtraRotationSlider(value) {
    const slider = document.getElementById('ver_texto_libre_orientacion_slider');
    if (slider) slider.value = sanitizeNumber(value, defaultLabelRotation, -180, 180);
  }

  function renderExtraTextList() {
    const list = document.getElementById('ver_textos_libres_lista');
    if (!list) return;
    if (!extraTexts.length) {
      list.innerHTML = '<span class="text-muted">Sin referencias agregadas.</span>';
      return;
    }
    list.innerHTML = '';
    extraTexts.forEach(function(item) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-sm w-100 text-start mb-1 ' + (item.id === selectedExtraTextId ? 'btn-primary' : 'btn-outline-secondary');
      btn.textContent = item.text.length > 38 ? item.text.slice(0, 38) + '...' : item.text;
      btn.addEventListener('click', function() { selectExtraText(item.id); });
      list.appendChild(btn);
    });
  }

  function serializeExtraTexts() {
    return extraTexts.map(function(item) {
      const lonLat = ol.proj.toLonLat(item.coordinate);
      return {
        id: item.id,
        text: item.text,
        size: sanitizeNumber(item.size, defaultLabelSize, 8, 72),
        rotation: sanitizeNumber(item.rotation, defaultLabelRotation, -180, 180),
        lng: Number(lonLat[0].toFixed(8)),
        lat: Number(lonLat[1].toFixed(8))
      };
    });
  }

  function loadExtraTexts(items) {
    clearExtraTexts();
    items.forEach(function(raw) {
      if (!raw || !raw.text || raw.lng === undefined || raw.lat === undefined) return;
      const item = {
        id: raw.id || createFeatureUid(),
        text: String(raw.text),
        size: sanitizeNumber(raw.size, defaultLabelSize, 8, 72),
        rotation: sanitizeNumber(raw.rotation, defaultLabelRotation, -180, 180),
        coordinate: ol.proj.fromLonLat([Number(raw.lng), Number(raw.lat)])
      };
      createExtraTextOverlay(item);
      extraTexts.push(item);
    });
    selectedExtraTextId = null;
    renderExtraTextList();
  }

  function clearExtraTexts() {
    extraTexts.forEach(removeExtraTextOverlay);
    extraTexts = [];
    selectedExtraTextId = null;
    renderExtraTextList();
  }

  function removeExtraTextOverlay(item) {
    if (item && item.overlay && map) map.removeOverlay(item.overlay);
  }

  function centerCurrentFeature() {
    if (!map || !selectedFeature) return;
    map.getView().fit(selectedFeature.getGeometry().getExtent(), { padding: [35, 35, 35, 35], maxZoom: 19, duration: 250 });
  }

  function getFeatureCollection() {
    const features = trabajoSource.getFeatures().map(f => {
      const clone = f.clone();
      ensureFeatureUid(f);
      clone.setProperties(Object.assign({}, f.getProperties(), {
        croquis_text: getFeatureText(f),
        croquis_label_size: getFeatureLabelSize(f),
        croquis_label_rotation: getFeatureLabelRotation(f),
        croquis_selected: f === selectedFeature
      }));
      return clone;
    });
    return new ol.format.GeoJSON().writeFeaturesObject(features, {
      featureProjection: 'EPSG:3857',
      dataProjection: 'EPSG:4326'
    });
  }

  function getFeatureGeoJson(feature) {
    return new ol.format.GeoJSON().writeFeatureObject(feature, {
      featureProjection: 'EPSG:3857',
      dataProjection: 'EPSG:4326'
    });
  }

  function getPolygonDetails() {
    return trabajoSource.getFeatures().map(function(feature) {
      ensureFeatureUid(feature);
      const labelLng = feature.get('croquis_label_lng');
      const labelLat = feature.get('croquis_label_lat');
      const centroUtm = getFeatureCenterUtm(feature);
      return {
        feature_uid: feature.get('croquis_uid'),
        origen: feature.get('croquis_source') || 'seleccionado',
        cuenta_catastral_origen: getFeatureNumber(feature),
        numero_poligono: getFeatureNumber(feature),
        texto_poligono: getFeatureText(feature),
        geojson: getFeatureGeoJson(feature),
        utm_vertices: getVerticesUtm(feature),
        utm_centro_x: centroUtm ? centroUtm.x : null,
        utm_centro_y: centroUtm ? centroUtm.y : null,
        label_lng: labelLng !== undefined && labelLng !== '' ? Number(labelLng) : null,
        label_lat: labelLat !== undefined && labelLat !== '' ? Number(labelLat) : null,
        label_size: getFeatureLabelSize(feature),
        label_rotation: getFeatureLabelRotation(feature),
        seleccionado: feature === selectedFeature
      };
    });
  }

  function getFeatureCenterUtm(feature) {
    if (!feature || typeof proj4 === 'undefined') return null;
    const center = ol.extent.getCenter(feature.getGeometry().getExtent());
    const lonLat = ol.proj.toLonLat(center);
    const utm = proj4('EPSG:4326', 'EPSG:32613', [lonLat[0], lonLat[1]]);
    return { x: Number(utm[0]).toFixed(2), y: Number(utm[1]).toFixed(2) };
  }

  function getVerticesUtm(feature) {
    if (!feature || typeof proj4 === 'undefined') return [];
    const geom = feature.getGeometry();
    if (!geom || geom.getType() !== 'Polygon') return [];
    return geom.getCoordinates()[0].map(coord => {
      const lonLat = ol.proj.toLonLat(coord);
      const utm = proj4('EPSG:4326', 'EPSG:32613', [lonLat[0], lonLat[1]]);
      return { x: Number(utm[0].toFixed(2)), y: Number(utm[1].toFixed(2)), lat: Number(lonLat[1].toFixed(8)), lng: Number(lonLat[0].toFixed(8)) };
    });
  }

  function createFeatureUid() {
    return 'croquis_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 9);
  }

  function ensureFeatureUid(feature) {
    if (feature && !feature.get('croquis_uid')) feature.set('croquis_uid', createFeatureUid());
  }

  function getFeatureText(feature) {
    if (!feature) return '';
    return feature.get('croquis_text') || feature.get('texto_poligono') || feature.get('label_text') || '';
  }

  function getFeatureLabelSize(feature) {
    if (!feature) return defaultLabelSize;
    return sanitizeNumber(feature.get('croquis_label_size') || feature.get('label_size'), defaultLabelSize, 8, 72);
  }

  function getFeatureLabelRotation(feature) {
    if (!feature) return defaultLabelRotation;
    return sanitizeNumber(feature.get('croquis_label_rotation') || feature.get('label_rotation'), defaultLabelRotation, -180, 180);
  }

  function applyStoredLabelStyle(feature, record) {
    if (!feature || !record) return;
    const rawGeojson = record.geojson;
    const geojson = parseJsonSafe(rawGeojson);
    const props = geojson && geojson.properties ? geojson.properties : {};
    const size = record.label_size ?? record.croquis_label_size ?? props.croquis_label_size ?? props.label_size;
    const rotation = record.label_rotation ?? record.croquis_label_rotation ?? props.croquis_label_rotation ?? props.label_rotation;
    if (size !== undefined && size !== null && size !== '') feature.set('croquis_label_size', sanitizeNumber(size, defaultLabelSize, 8, 72));
    if (rotation !== undefined && rotation !== null && rotation !== '') feature.set('croquis_label_rotation', sanitizeNumber(rotation, defaultLabelRotation, -180, 180));
  }

  function getFeatureNumber(feature) {
    if (!feature) return '';
    return feature.get('CVE_CAT_OR') || feature.get('NUMERO') || feature.get('numero') || feature.get('numero_poligono') || feature.get('NO_POLIGONO') || feature.get('id') || '';
  }

  function getFeatureLabelCoordinate(feature) {
    if (!feature) return null;
    const lng = feature.get('croquis_label_lng');
    const lat = feature.get('croquis_label_lat');
    if (lng === undefined || lat === undefined || lng === '' || lat === '') return null;
    return ol.proj.fromLonLat([Number(lng), Number(lat)]);
  }

  function setFeatureLabelCoordinate(feature, coordinate) {
    if (!feature || !coordinate) return;
    const lonLat = ol.proj.toLonLat(coordinate);
    feature.set('croquis_label_lng', Number(lonLat[0].toFixed(8)));
    feature.set('croquis_label_lat', Number(lonLat[1].toFixed(8)));
  }

  function buildGeoreference(text, featureCollection) {
    const extent = selectedFeature.getGeometry().getExtent();
    const viewCenter = ol.proj.toLonLat(map.getView().getCenter());
    const labelLonLat = labelCoordinate ? ol.proj.toLonLat(labelCoordinate) : null;
    const bottomLeft = ol.proj.toLonLat([extent[0], extent[1]]);
    const topRight = ol.proj.toLonLat([extent[2], extent[3]]);
    return {
      crs: 'EPSG:4326',
      map_projection: 'EPSG:3857',
      utm_crs: 'EPSG:32613',
      bounds: { west: bottomLeft[0], south: bottomLeft[1], east: topRight[0], north: topRight[1] },
      map_center: { lng: viewCenter[0], lat: viewCenter[1] },
      map_zoom: map.getView().getZoom(),
      base_layer: activeBaseLayer,
      label_position: labelLonLat ? { lng: labelLonLat[0], lat: labelLonLat[1] } : null,
      label_text: text,
      label_style: selectedFeature ? {
        size: getFeatureLabelSize(selectedFeature),
        rotation: getFeatureLabelRotation(selectedFeature)
      } : null,
      extra_texts: serializeExtraTexts(),
      layers: {
        catastro_visible: catastroLayer.getVisible(),
        calles_visible: callesLayer ? callesLayer.getVisible() : false,
        feature_count: featureCollection.features.length
      }
    };
  }

  function setBaseLayer(name) {
    Object.keys(baseLayers).forEach(key => baseLayers[key].setVisible(key === name));
    activeBaseLayer = baseLayers[name] ? name : 'Mapa';
    document.getElementById('ver_btn_capa_mapa')?.classList.toggle('active', activeBaseLayer === 'Mapa');
    document.getElementById('ver_btn_capa_satelite')?.classList.toggle('active', activeBaseLayer === 'Satelital');
    toolbarButtons.baseMap?.classList.toggle('active', activeBaseLayer === 'Mapa');
    toolbarButtons.satellite?.classList.toggle('active', activeBaseLayer === 'Satelital');
  }

  function setCaptureMode(enabled) {
    const mapEl = document.getElementById('ver_mapa_croquis');
    if (!mapEl) return;
    mapEl.classList.toggle('croquis-capturando', enabled);
    if (enabled) captureHadCatastro = catastroLayer.getVisible();
    if (!enabled) captureHadCatastro = false;
  }

  function saveMapCroquis() {
    const msg = document.getElementById('ver_msg_croquis');
    const btn = document.getElementById('ver_btn_subir');
    if (!selectedFeature) {
      if (msg) { msg.textContent = 'Selecciona o dibuja un poligono antes de guardar.'; msg.style.color = '#856404'; }
      return;
    }
    if (!currentTramiteId && !currentFolio) {
      if (msg) { msg.textContent = 'Falta el tramite destino del croquis.'; msg.style.color = '#dc3545'; }
      return;
    }
    if (typeof html2canvas === 'undefined') {
      if (msg) { msg.textContent = 'No se cargo la libreria de captura del mapa.'; msg.style.color = '#dc3545'; }
      return;
    }

    stopDrawPolygon();
    cancelSplit(false);
    if (msg) { msg.textContent = 'Capturando croquis OpenLayers...'; msg.style.color = '#555'; }
    if (btn) btn.disabled = true;
    map.updateSize();
    setCaptureMode(true);

    requestAnimationFrame(function() {
      html2canvas(document.getElementById('ver_mapa_croquis'), {
        useCORS: true,
        allowTaint: false,
        backgroundColor: '#ffffff',
        scale: 0.9,
        logging: false
      })
        .then(canvas => new Promise(resolve => canvas.toBlob(resolve, 'image/png', 0.95)))
        .then(function(blob) {
          const fd = new FormData();
          const featureCollection = getFeatureCollection();
          const texto = document.getElementById('ver_texto_poligono')?.value || '';
          const centroUtm = getFeatureCenterUtm(selectedFeature);
          const georef = buildGeoreference(texto, featureCollection);

          if (currentTramiteId) fd.append('id', currentTramiteId);
          fd.append('folio', currentFolio);
          fd.append('texto', texto);
          fd.append('origen', selectedFeature.get('croquis_source') || 'seleccionado');
          fd.append('cuenta_catastral_origen', getFeatureNumber(selectedFeature));
          fd.append('geojson', JSON.stringify(featureCollection));
          fd.append('poligonos_detalle', JSON.stringify(getPolygonDetails()));
          fd.append('utm_vertices', JSON.stringify(getVerticesUtm(selectedFeature)));
          fd.append('utm_centro_x', centroUtm ? centroUtm.x : '');
          fd.append('utm_centro_y', centroUtm ? centroUtm.y : '');
          fd.append('georeferencia', JSON.stringify(georef));
          fd.append('croquis', blob, 'croquis_mapa.png');
          if (msg) { msg.textContent = 'Guardando croquis...'; msg.style.color = '#555'; }
          return fetch('php/guardar_croquis_mapa.php', { method: 'POST', body: fd, credentials: 'same-origin' });
        })
        .then(r => r.json())
        .then(function(data) {
          if (btn) btn.disabled = false;
          if (data.success) {
            if (msg) { msg.textContent = 'Croquis guardado. Ya puedes imprimir.'; msg.style.color = '#198754'; }
            ver_mostrarEstado(true, data.url || data.archivo || null);
            ver_mostrarPreviewCroquis(data.url || data.archivo || '');
          } else if (msg) {
            msg.textContent = 'Error: ' + (data.message || 'No se pudo guardar.');
            msg.style.color = '#dc3545';
          }
        })
        .catch(function(err) {
          console.error('Error guardando croquis OpenLayers:', err);
          if (btn) btn.disabled = false;
          if (msg) { msg.textContent = 'Error capturando el mapa.'; msg.style.color = '#dc3545'; }
        })
        .finally(function() {
          setCaptureMode(false);
        });
    });
  }

  function parseJsonSafe(value) {
    if (!value) return null;
    if (typeof value === 'object') return value;
    try { return JSON.parse(value); } catch (e) { return null; }
  }

  function setInfo(text) {
    const info = document.getElementById('ver_croquis_info');
    if (info) info.textContent = text;
  }

  window.ver_prepararCroquisMapa = prepareMap;
  window.ver_guardarCroquisMapa = saveMapCroquis;
  window.ver_initCroquisMapa = initOpenLayersCroquis;
})();
