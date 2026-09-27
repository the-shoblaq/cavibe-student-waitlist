(() => {
  // ── TAB BAR ──
  const tabItems = document.querySelectorAll('.tab-item');
  const tabSections = document.querySelectorAll('.tab-section');
  const appHeader = document.querySelector('.app-header');

  function switchTab(id) {
    tabItems.forEach(t => t.classList.toggle('active', t.dataset.tab === id));
    tabSections.forEach(s => s.classList.toggle('active', s.id === 'tab-' + id));

    if (id === 'survey') {
      // Let the section render, then scroll the survey card into view
      requestAnimationFrame(() => {
        const card = document.querySelector('.survey-card');
        const headerH = appHeader ? appHeader.offsetHeight : 0;
        if (card) {
          const top = card.getBoundingClientRect().top + window.scrollY - headerH - 8;
          window.scrollTo({ top, behavior: 'smooth' });
        }
      });
    } else {
      window.scrollTo({ top: 0 });
    }
  }

  tabItems.forEach(t => t.addEventListener('click', () => switchTab(t.dataset.tab)));

  document.querySelectorAll('.tab-trigger').forEach(el => {
    el.addEventListener('click', e => {
      e.preventDefault();
      switchTab(el.dataset.tab);
    });
  });

  // Auto-switch to survey tab on success redirect
  if (new URLSearchParams(location.search).get('joined') === '1') {
    switchTab('survey');
  }

  // ── SURVEY FORM ──
  const f = document.querySelector('#surveyForm');
  if (!f) return;

  const steps = [...document.querySelectorAll('.step')];
  const prevBtn = document.querySelector('#prevBtn');
  const nextBtn = document.querySelector('#nextBtn');
  const subBtn = document.querySelector('#submitBtn');
  const bar = document.querySelector('#progressBar');
  const num = document.querySelector('#stepNumber');
  const lbl = document.querySelector('#stepLabel');
  const err = document.querySelector('#formError');

  const stepLabels = ['About You','Digital Life','Your Interest','Features','Earning','Privacy & Safety','Early Access'];
  let i = 0;

  function show() {
    steps.forEach((s, j) => s.classList.toggle('active', j === i));
    num.textContent = i + 1;
    lbl.textContent = stepLabels[i] || '';
    bar.style.width = ((i + 1) / steps.length * 100) + '%';
    prevBtn.classList.toggle('hidden', i === 0);
    nextBtn.classList.toggle('hidden', i === steps.length - 1);
    subBtn.classList.toggle('hidden', i !== steps.length - 1);
    err.textContent = '';
  }

  function valid() {
    for (const x of steps[i].querySelectorAll('[required]')) {
      if (x.type === 'radio') {
        if (!steps[i].querySelector('input[name="' + x.name + '"]:checked')) {
          err.textContent = 'Please answer all required questions.';
          return false;
        }
      } else if (x.type === 'checkbox') {
        if (!x.checked) {
          err.textContent = 'Please confirm the required consent.';
          return false;
        }
      } else if (!x.checkValidity()) {
        x.reportValidity();
        return false;
      }
    }
    return true;
  }

  nextBtn.onclick = () => {
    if (valid() && i < steps.length - 1) {
      i++;
      show();
      // scroll card into view
      document.querySelector('.survey-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  prevBtn.onclick = () => {
    if (i > 0) {
      i--;
      show();
      document.querySelector('.survey-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  // Other text inputs
  document.querySelectorAll('[data-other]').forEach(c => {
    const x = document.getElementById(c.dataset.other);
    if (!x) return;
    c.onchange = () => { x.hidden = !c.checked; if (!c.checked) x.value = ''; };
  });

  // Max selection enforcement
  document.querySelectorAll('.limited').forEach(g => {
    g.querySelectorAll('input[type=checkbox]').forEach(c => {
      c.onchange = () => {
        const m = +g.dataset.max;
        if (g.querySelectorAll('input:checked').length > m) {
          c.checked = false;
          err.textContent = 'Please select up to ' + m + ' options.';
        }
      };
    });
  });

  f.onsubmit = e => { if (!valid()) e.preventDefault(); };

  show();
})();
