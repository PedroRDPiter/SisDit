(function () {
  'use strict';

  function iniciarWizardConstancia() {
    const modal = document.getElementById('modalConstancia');
    const form = document.getElementById('formConstancia');
    if (!modal || !form) return;

    const panels = Array.from(form.querySelectorAll('[data-wizard-step]'));
    const steps = Array.from(form.querySelectorAll('[data-wizard-go]'));
    const previousButton = document.getElementById('constancia_btn_anterior');
    const nextButton = document.getElementById('constancia_btn_siguiente');
    const saveButton = document.getElementById('constancia_btn_guardar');
    const printButton = document.getElementById('btnSoloImprimir');
    const typeSelect = document.getElementById('c_tipo_asignacion');
    const assignmentButtons = Array.from(form.querySelectorAll('.assignment-choice[data-assignment]'));
    const summary = document.getElementById('constancia-resumen-texto');
    let currentStep = 1;

    function panelFor(step) {
      return panels.find(panel => Number(panel.dataset.wizardStep) === step);
    }

    function fieldsFor(step) {
      const panel = panelFor(step);
      return panel ? Array.from(panel.querySelectorAll('input, select, textarea')) : [];
    }

    function validateStep(step) {
      const invalid = fieldsFor(step).find(field => !field.disabled && !field.checkValidity());
      if (!invalid) return true;

      if (currentStep !== step) showStep(step);
      invalid.reportValidity();
      if (typeof invalid.focus === 'function') invalid.focus({ preventScroll: true });
      invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return false;
    }

    function hasCroquis() {
      const okBox = document.getElementById('ver_ok_croquis');
      const preview = document.getElementById('ver_prev_img');
      return window._ver_croquis_ok === true ||
        (okBox && okBox.style.display !== 'none' && getComputedStyle(okBox).display !== 'none') ||
        Boolean(preview && preview.src && preview.style.display !== 'none');
    }

    function setChecklistItem(id, complete) {
      const item = document.getElementById(id);
      if (!item) return;
      item.classList.toggle('complete', complete);
      const icon = item.querySelector('i');
      if (icon) {
        icon.classList.toggle('bi-circle', !complete);
        icon.classList.toggle('bi-check-circle-fill', complete);
      }
    }

    function updateChecklist() {
      const numero = document.getElementById('c_numero_asignado');
      const requiredLocationFields = fieldsFor(2).filter(field => field.required);
      setChecklistItem('check-numero', Boolean(numero && numero.value.trim()));
      setChecklistItem('check-ubicacion', requiredLocationFields.length > 0 && requiredLocationFields.every(field => field.checkValidity()));
      setChecklistItem('check-croquis', hasCroquis());
    }

    function syncAssignment() {
      const value = typeSelect ? typeSelect.value : 'ASIGNACION';
      assignmentButtons.forEach(button => {
        const active = button.dataset.assignment === value;
        button.classList.toggle('active', active);
        button.setAttribute('aria-checked', String(active));
      });

      if (summary) {
        const labels = {
          ASIGNACION: 'una asignación',
          RECTIFICACION: 'una rectificación',
          REPOSICION: 'una reposición'
        };
        summary.textContent = 'Se generará ' + (labels[value] || 'una asignación') + ' de número oficial.';
      }
    }

    function showStep(step) {
      currentStep = Math.max(1, Math.min(3, Number(step) || 1));

      panels.forEach(panel => {
        const active = Number(panel.dataset.wizardStep) === currentStep;
        panel.classList.toggle('d-none', !active);
        panel.setAttribute('aria-hidden', String(!active));
      });

      steps.forEach(button => {
        const number = Number(button.dataset.wizardGo);
        button.classList.toggle('active', number === currentStep);
        button.classList.toggle('complete', number < currentStep);
        button.setAttribute('aria-current', number === currentStep ? 'step' : 'false');
      });

      previousButton?.classList.toggle('d-none', currentStep === 1);
      nextButton?.classList.toggle('d-none', currentStep === 3);
      saveButton?.classList.toggle('d-none', currentStep !== 3);
      printButton?.classList.toggle('d-none', currentStep !== 3);
      updateChecklist();

      if (currentStep === 3) {
        requestAnimationFrame(function () {
          window.ver_actualizarTamanoCroquis?.();
          window.dispatchEvent(new Event('resize'));
        });
      }
    }

    function goForwardTo(target) {
      target = Math.max(1, Math.min(3, Number(target) || 1));
      if (target <= currentStep) {
        showStep(target);
        return;
      }

      while (currentStep < target) {
        if (!validateStep(currentStep)) return;
        showStep(currentStep + 1);
      }
    }

    assignmentButtons.forEach(button => {
      button.addEventListener('click', function () {
        if (!typeSelect) return;
        typeSelect.value = button.dataset.assignment || 'ASIGNACION';
        typeSelect.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });

    typeSelect?.addEventListener('change', syncAssignment);
    nextButton?.addEventListener('click', function () { goForwardTo(currentStep + 1); });
    previousButton?.addEventListener('click', function () { showStep(currentStep - 1); });
    saveButton?.addEventListener('click', function (event) {
      for (let step = 1; step <= 2; step += 1) {
        if (!validateStep(step)) {
          event.preventDefault();
          return;
        }
      }
    });
    steps.forEach(button => button.addEventListener('click', function () {
      goForwardTo(Number(button.dataset.wizardGo));
    }));

    form.addEventListener('input', updateChecklist);
    form.addEventListener('change', updateChecklist);
    form.addEventListener('submit', function (event) {
      for (let step = 1; step <= 2; step += 1) {
        if (!validateStep(step)) {
          event.preventDefault();
          event.stopImmediatePropagation();
          return;
        }
      }
    }, true);

    const croquisStatus = document.getElementById('ver_ok_croquis');
    if (croquisStatus) new MutationObserver(updateChecklist).observe(croquisStatus, { attributes: true, attributeFilter: ['style', 'class'] });

    modal.addEventListener('show.bs.modal', function () {
      form.classList.remove('was-validated');
      syncAssignment();
      showStep(1);
    });

    syncAssignment();
    showStep(1);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', iniciarWizardConstancia);
  } else {
    iniciarWizardConstancia();
  }
})();
