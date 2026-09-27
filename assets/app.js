(() => {
  // ── TAB BAR ──
  const tabItems = document.querySelectorAll('.tab-item');
  const tabSections = document.querySelectorAll('.tab-section');
  const appHeader = document.querySelector('.app-header');

  function switchTab(id) {
    tabItems.forEach(t => t.classList.toggle('active', t.dataset.tab === id));
    tabSections.forEach(s => s.classList.toggle('active', s.id === 'tab-' + id));
    if (id === 'survey') {
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
    el.addEventListener('click', e => { e.preventDefault(); switchTab(el.dataset.tab); });
  });

  if (new URLSearchParams(location.search).get('joined') === '1') {
    switchTab('survey');
  }

  // ── SURVEY FORM ──
  const f = document.querySelector('#surveyForm');
  if (!f) return;

  const steps   = [...document.querySelectorAll('.step')];
  const prevBtn = document.querySelector('#prevBtn');
  const nextBtn = document.querySelector('#nextBtn');
  const subBtn  = document.querySelector('#submitBtn');
  const bar     = document.querySelector('#progressBar');
  const num     = document.querySelector('#stepNumber');
  const lbl     = document.querySelector('#stepLabel');
  const err     = document.querySelector('#formError');

  const stepLabels = ['About You','Digital Life','Your Interest','Features','Earning','Privacy & Safety','Early Access'];
  let i = 0;

  function showError(msg) {
    err.innerHTML = `<span class="err-icon">!</span>${msg}`;
    err.classList.add('visible');
    err.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function clearError() {
    err.textContent = '';
    err.classList.remove('visible');
  }

  function show() {
    steps.forEach((s, j) => s.classList.toggle('active', j === i));
    num.textContent = i + 1;
    lbl.textContent = stepLabels[i] || '';
    bar.style.width = ((i + 1) / steps.length * 100) + '%';
    prevBtn.classList.toggle('hidden', i === 0);
    nextBtn.classList.toggle('hidden', i === steps.length - 1);
    subBtn.classList.toggle('hidden', i !== steps.length - 1);
    clearError();
  }

  function valid() {
    for (const x of steps[i].querySelectorAll('[required]')) {
      if (x.type === 'radio') {
        if (!steps[i].querySelector('input[name="' + x.name + '"]:checked')) {
          showError('Please answer all required questions before continuing.');
          return false;
        }
      } else if (x.type === 'checkbox') {
        if (!x.checked) {
          showError('Please tick the required consent checkbox to continue.');
          return false;
        }
      } else if (!x.checkValidity()) {
        showError(x.validationMessage || 'Please fill in the highlighted field.');
        x.focus();
        return false;
      }
    }
    return true;
  }

  nextBtn.onclick = () => {
    if (valid() && i < steps.length - 1) {
      i++;
      show();
      document.querySelector('.survey-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  prevBtn.onclick = () => {
    if (i > 0) { i--; show(); document.querySelector('.survey-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
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
          showError('You can select up to ' + m + ' options for this question.');
        }
      };
    });
  });

  // ── FETCH SUBMISSION ──
  function showSuccess() {
    const card = document.querySelector('.survey-card');
    if (!card) return;
    card.innerHTML = `
      <div class="success">
        <div class="success-icon">✓</div>
        <small>YOU'RE ON THE LIST</small>
        <h2>Thank you for helping shape Cavibe.</h2>
        <p>Your response has been recorded. We'll reach out when it's time.</p>
        <a class="btn btn-whatsapp" href="https://chat.whatsapp.com/Fm9rPcBNIsOEmNo7bdxYWe" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          Join the Waiting List Group
        </a>
      </div>`;
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  f.onsubmit = async e => {
    e.preventDefault();
    if (!valid()) return;

    subBtn.disabled = true;
    subBtn.textContent = 'Submitting…';
    clearError();

    try {
      const res = await fetch('/submit.php', {
        method: 'POST',
        body: new FormData(f),
      });

      const data = await res.json().catch(() => null);

      if (res.ok && data?.ok) {
        showSuccess();
      } else {
        const msg = data?.error || 'Something went wrong. Please try again.';
        if (res.status === 409) {
          // Already registered — still show success path + WhatsApp
          showSuccess();
        } else {
          showError(msg);
          subBtn.disabled = false;
          subBtn.textContent = 'Join the waitlist';
        }
      }
    } catch {
      showError('No internet connection or server is unreachable. Please check your connection and try again.');
      subBtn.disabled = false;
      subBtn.textContent = 'Join the waitlist';
    }
  };

  show();
})();
