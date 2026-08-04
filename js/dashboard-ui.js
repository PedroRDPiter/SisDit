(function() {
  'use strict';

  const body = document.body;
  if (!body || !body.classList.contains('dashboard-shell')) return;

  const progress = document.createElement('div');
  progress.className = 'dashboard-scroll-progress';
  progress.setAttribute('aria-hidden', 'true');
  body.appendChild(progress);

  const backTop = document.createElement('button');
  backTop.type = 'button';
  backTop.className = 'dashboard-back-top';
  backTop.setAttribute('aria-label', 'Volver al inicio');
  backTop.title = 'Volver al inicio';
  backTop.innerHTML = '<i class="bi bi-arrow-up"></i>';
  backTop.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
  body.appendChild(backTop);

  const updateScrollUi = function() {
    const max = Math.max(document.documentElement.scrollHeight - window.innerHeight, 1);
    const ratio = Math.min(Math.max(window.scrollY / max, 0), 1);
    progress.style.width = (ratio * 100) + '%';
    backTop.classList.toggle('visible', window.scrollY > 520);
  };
  updateScrollUi();
  window.addEventListener('scroll', updateScrollUi, { passive: true });
  window.addEventListener('resize', updateScrollUi);

  const sections = Array.from(document.querySelectorAll('.content section[id]'));
  const navLinks = Array.from(document.querySelectorAll('.sidebar a[href^="#"], #menuMovil a[href^="#"]'));
  const linksBySection = new Map();

  navLinks.forEach(function(link) {
    const id = decodeURIComponent(link.getAttribute('href').slice(1));
    if (!linksBySection.has(id)) linksBySection.set(id, []);
    linksBySection.get(id).push(link);

    link.addEventListener('click', function() {
      const mobileMenu = document.getElementById('menuMovil');
      if (mobileMenu && mobileMenu.classList.contains('show') && window.bootstrap) {
        bootstrap.Collapse.getOrCreateInstance(mobileMenu).hide();
      }
      window.setTimeout(function() {
        window.dispatchEvent(new Event('resize'));
      }, 320);
    });
  });

  function setActiveSection(id) {
    navLinks.forEach(function(link) {
      const active = link.getAttribute('href') === '#' + id;
      link.classList.toggle('active', active);
      if (active) link.setAttribute('aria-current', 'location');
      else link.removeAttribute('aria-current');
    });
  }

  if ('IntersectionObserver' in window && sections.length) {
    const visible = new Map();
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) visible.set(entry.target.id, entry.intersectionRatio);
        else visible.delete(entry.target.id);
      });
      if (!visible.size) return;
      const current = Array.from(visible.entries()).sort(function(a, b) { return b[1] - a[1]; })[0][0];
      setActiveSection(current);
    }, { rootMargin: '-18% 0px -60% 0px', threshold: [0.05, 0.2, 0.45, 0.7] });
    sections.forEach(function(section) { observer.observe(section); });
  }

  const revealObserver = 'IntersectionObserver' in window
    ? new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('dashboard-reveal');
          observer.unobserve(entry.target);
        });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 })
    : null;

  document.querySelectorAll('.tramite-box, .hero + .row .card').forEach(function(element) {
    if (revealObserver) revealObserver.observe(element);
  });

  document.addEventListener('submit', function(event) {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    window.setTimeout(function() {
      if (event.defaultPrevented || !form.checkValidity()) return;
      const submitter = event.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
      if (!submitter || submitter.dataset.dashboardNoLoading === 'true') return;
      submitter.classList.add('dashboard-loading');
      submitter.setAttribute('aria-busy', 'true');
      if (submitter.tagName === 'BUTTON') {
        submitter.dataset.dashboardOriginalHtml = submitter.innerHTML;
        submitter.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Procesando…';
      }
    }, 0);
  }, true);

  document.addEventListener('keydown', function(event) {
    if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) return;
    const target = event.target;
    if (target && /INPUT|TEXTAREA|SELECT/.test(target.tagName)) return;
    const search = document.querySelector('.dataTables_filter input, input[name="folio"], input[type="search"]');
    if (!search) return;
    event.preventDefault();
    search.focus();
    if (typeof search.select === 'function') search.select();
  });

  document.querySelectorAll('.table-responsive').forEach(function(wrapper) {
    wrapper.setAttribute('tabindex', '0');
    wrapper.setAttribute('role', 'region');
    if (!wrapper.getAttribute('aria-label')) wrapper.setAttribute('aria-label', 'Tabla con desplazamiento horizontal');
  });
})();

