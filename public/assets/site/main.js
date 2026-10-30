/* =====================================================================
   WEBSITE SCRIPT (do not edit)
   One shared script for every page:
   • form validation (same rules as the server)  • booking popup
   • form sending  • menu  • reviews slider  • animations
   • card filters  • offer countdown  • click tracking (GTM / GA4)
   ===================================================================== */
(function () {
  'use strict';
  var SENDING = (window.SITE && window.SITE.sending) || 'Sending…';

  /* ---------------- tracking (Google Tag Manager / GA4 / Google Ads) ---------------- */
  function push(event, data) {
    try {
      window.dataLayer = window.dataLayer || [];
      var o = { event: event }; for (var k in data) o[k] = data[k];
      window.dataLayer.push(o);
      if (typeof window.gtag === 'function') window.gtag('event', event, data || {});
    } catch (e) {}
  }
  document.addEventListener('click', function (e) {
    var a = e.target.closest ? e.target.closest('a[href^="tel:"], a[href*="wa.me/"], [data-track]') : null;
    if (!a) return;
    var t = a.getAttribute('data-track') || (String(a.getAttribute('href')).indexOf('tel:') === 0 ? 'call' : 'whatsapp');
    push(t + '_click', { link_url: a.href || '', page_path: location.pathname });
  });

  /* ---------------- validation rules ---------------- */
  function cleanMobile(v) {
    v = String(v || '').replace(/\D/g, '');
    if (v.length > 10 && v.indexOf('91') === 0) v = v.slice(2);
    if (v.length > 10 && v.charAt(0) === '0') v = v.slice(1);
    return v.slice(0, 10);
  }
  function validMobile(v) {
    if (!/^[6-9]\d{9}$/.test(v)) return false;
    if (/^(\d)\1{9}$/.test(v)) return false;
    if (v === '1234567890') return false;
    return true;
  }
  function today() {
    var d = new Date(); d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    return d.toISOString().slice(0, 10);
  }
  function stripTags(v) { return String(v).replace(/<[^>]*>/g, ''); }

  function isOff(el) { var f = el.closest('.field'); return !!(f && f.classList.contains('is-off')); }

  function isValid(el) {
    var t = el.getAttribute('data-v'), v = el.value, req = el.hasAttribute('data-req');
    var trimmed = String(v).trim();
    if (isOff(el)) return true; // field not used for the chosen trip type
    if (trimmed === '') return !req;
    var min = parseInt(el.getAttribute('data-min'), 10), max = parseInt(el.getAttribute('data-max'), 10);
    switch (t) {
      case 'name': return /^[A-Za-z .]{2,50}$/.test(trimmed.replace(/\s+/g, ' '));
      case 'mobile': return validMobile(cleanMobile(v));
      case 'email': return trimmed.length <= 100 && /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(trimmed);
      case 'service': case 'select': return trimmed !== '';
      case 'date': return /^\d{4}-\d{2}-\d{2}$/.test(trimmed) && trimmed >= today();
      case 'time': return /^([01]\d|2[0-3]):[0-5]\d$/.test(trimmed.slice(0, 5));
      case 'number':
        if (!/^\d+$/.test(trimmed)) return false;
        var n = parseInt(trimmed, 10);
        return n >= (isNaN(min) ? 1 : min) && n <= (isNaN(max) ? 50 : max);
      default:
        var len = stripTags(trimmed).length;
        return len >= (isNaN(min) ? 1 : min) && len <= (isNaN(max) ? 2000 : max);
    }
  }
  function mark(el, ok, msg) {
    var f = el.closest('.field'); if (!f) return ok;
    f.classList.toggle('is-error', !ok);
    f.classList.toggle('is-valid', ok && String(el.value).trim() !== '');
    var e = f.querySelector('.err');
    if (e) e.textContent = msg || el.getAttribute('data-err') || 'Please check this field';
    return ok;
  }
  function check(el) { return mark(el, isValid(el)); }

  /* trip type tabs: One Way / Round Trip / Local / Airport — show only the fields of that trip */
  function setTrip(form, trip) {
    var input = form.querySelector('[data-trip-input]');
    if (!input) return;
    var tabs = form.querySelectorAll('.trip-tabs .tab'), found = false;
    tabs.forEach(function (b) { if (b.getAttribute('data-trip') === trip) found = true; });
    if (!found) return;
    input.value = trip;
    tabs.forEach(function (b) {
      var on = b.getAttribute('data-trip') === trip;
      b.classList.toggle('active', on);
      b.setAttribute('aria-selected', on ? 'true' : 'false');
    });
    form.querySelectorAll('.field[data-show-for]').forEach(function (f) {
      var list = f.getAttribute('data-show-for').toLowerCase().split('|');
      var off = list.indexOf(trip.toLowerCase()) === -1;
      f.classList.toggle('is-off', off);
      if (off) f.classList.remove('is-error');
    });
  }

  function setupForm(form) {
    form.querySelectorAll('.trip-tabs .tab').forEach(function (b) {
      b.addEventListener('click', function () { setTrip(form, b.getAttribute('data-trip')); });
    });
    form.querySelectorAll('[data-v]').forEach(function (el) {
      var t = el.getAttribute('data-v');
      if (t === 'date') { el.min = today(); if (el.hasAttribute('data-today') && !el.value) el.value = today(); }
      if (t === 'mobile') {
        el.addEventListener('keydown', function (e) {
          if (e.key && e.key.length === 1 && !/\d/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
          if (e.key && /\d/.test(e.key) && el.value.length >= 10 && el.selectionStart === el.selectionEnd) e.preventDefault();
        });
        el.addEventListener('paste', function (e) {
          e.preventDefault();
          var txt = (e.clipboardData || window.clipboardData).getData('text');
          el.value = cleanMobile(txt); check(el);
        });
        el.addEventListener('input', function () { var c = cleanMobile(el.value); if (c !== el.value) el.value = c; });
      }
      if (t === 'name') el.addEventListener('input', function () { var c = el.value.replace(/[^A-Za-z .]/g, ''); if (c !== el.value) el.value = c; });
      if (t === 'number') el.addEventListener('input', function () { var c = el.value.replace(/\D/g, ''); if (c !== el.value) el.value = c; });
      el.addEventListener('blur', function () { if (String(el.value).trim() !== '' || el.closest('.field').classList.contains('is-error')) check(el); });
      el.addEventListener('input', function () { var c = el.closest('.field').classList; if (c.contains('is-error') || c.contains('is-valid')) check(el); });
      el.addEventListener('change', function () { check(el); });
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var ok = true, first = null;
      form.querySelectorAll('[data-v]').forEach(function (el) {
        if (!check(el)) { ok = false; if (!first) first = el; }
      });
      var msg = form.querySelector('.form-msg');
      if (msg) msg.className = 'form-msg';
      if (!ok) { first.focus(); return; }
      form.querySelectorAll('[data-v="mobile"]').forEach(function (m) { m.value = cleanMobile(m.value); });
      if (form.elements.page_url) form.elements.page_url.value = location.href;

      var btn = form.querySelector('button[type=submit]'), old = btn.innerHTML;
      btn.disabled = true; btn.textContent = btn.getAttribute('data-sending') || SENDING;
      var fd = new FormData(form);
      fd.append('ajax', '1');
      function fail(text) {
        btn.disabled = false; btn.innerHTML = old;
        if (msg) { msg.textContent = text; msg.className = 'form-msg show bad'; }
      }
      // never leave the button on "Sending…": give up after 25 seconds and show the call / retry message
      var ctrl = window.AbortController ? new AbortController() : null;
      var timer = ctrl ? setTimeout(function () { ctrl.abort(); }, 25000) : null;
      fetch(form.getAttribute('action'), { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin', signal: ctrl ? ctrl.signal : undefined })
        .then(function (r) { if (timer) clearTimeout(timer); return r.json(); })
        .then(function (res) {
          if (res && res.ok && res.redirect) {
            var info = { form_name: res.form_name || (form.elements.form_name ? form.elements.form_name.value : ''), enquiry_item: res.enquiry_item || '', page_url: location.href };
            if (res.fields) info.form_fields = res.fields;
            push('form_submit', info);
            if (res.event && res.event !== 'form_submit') push(res.event, info);
            // small pause so Google Tag Manager / Google Ads can record the event before the page changes
            setTimeout(function () { window.location.href = res.redirect; }, res.event === 'whatsapp_form_submit' ? 400 : 150);
            return;
          }
          var errs = (res && res.errors) || {}, firstBad = null;
          Object.keys(errs).forEach(function (name) {
            var el = form.querySelector('[name="' + name + '"]');
            if (el) { mark(el, false, errs[name]); if (!firstBad) firstBad = el; }
          });
          fail((res && res.message) || 'Something went wrong. Please try again.');
          if (firstBad) firstBad.focus();
        })
        .catch(function () {
          if (timer) clearTimeout(timer);
          fail((window.SITE && window.SITE.offline) || 'Could not send. Please check your internet connection, or call us directly.');
        });
    });
  }
  document.querySelectorAll('.lead-form').forEach(setupForm);

  /* ---------------- booking popup ---------------- */
  var modal = document.getElementById('modal');
  if (modal) {
    var mform = modal.querySelector('form'), meta = modal.querySelector('.m-meta');
    var defMeta = meta ? meta.getAttribute('data-default') || '' : '';
    var lastFocus = null;
    function fill(name, value) {
      var el = mform.elements[name];
      if (!el || !value) return;
      el.value = value;
      check(el);
    }
    function openPopup(btn) {
      lastFocus = btn;
      var item = btn.getAttribute('data-item') || '';
      var svc = btn.getAttribute('data-service') || '';
      var trip = btn.getAttribute('data-trip') || '';
      // line under the popup heading: "Selected: Toyota Innova · Starting ₹1,800 + fuel" / "Route: Dehradun → Mussoorie"
      if (meta) meta.textContent = btn.getAttribute('data-meta') || defMeta;
      if (mform.elements.enquiry_item) mform.elements.enquiry_item.value = item;
      if (trip) setTrip(mform, trip);
      fill('pickup', btn.getAttribute('data-pickup'));
      fill('drop', btn.getAttribute('data-drop'));
      var sel = mform.querySelector('[data-v="service"]');
      if (sel && (svc || item)) {
        sel.value = '';
        if (svc) { sel.value = svc; }
        if (!sel.value && item) {
          Array.prototype.forEach.call(sel.options, function (o) { if (!sel.value && (o.value === item || o.text === item)) sel.value = o.value; });
        }
        var f = sel.closest('.field');
        if (sel.value) check(sel); else if (f) f.classList.remove('is-valid', 'is-error');
      }
      var m = mform.querySelector('.form-msg'); if (m) m.className = 'form-msg';
      modal.classList.add('open'); modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
      setTimeout(function () {
        var first = null;
        mform.querySelectorAll('[data-v]').forEach(function (el) { if (!first && !isOff(el) && !String(el.value).trim()) first = el; });
        if (first && window.innerWidth > 680) first.focus();
      }, 260);
    }
    function closePopup() {
      modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
      if (lastFocus) lastFocus.focus();
    }
    document.addEventListener('click', function (e) {
      var b = e.target.closest ? e.target.closest('.js-popup, [data-popup]') : null;
      if (b) { e.preventDefault(); setNav(false, true); openPopup(b); }
    });
    modal.querySelector('.m-close').addEventListener('click', closePopup);
    modal.addEventListener('click', function (e) { if (e.target === modal) closePopup(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.classList.contains('open')) closePopup(); });
  }

  /* ---------------- mobile menu (drawer from the right) ---------------- */
  var burger = document.querySelector('.burger'), nav = document.getElementById('nav');
  var navBackdrop = document.querySelector('.nav-backdrop');
  function navIsOpen() { return !!(nav && nav.classList.contains('open')); }
  // open / close the drawer; keepFocus = do not move focus back to the menu button
  function setNav(open, keepFocus) {
    if (!nav || navIsOpen() === open) return;
    nav.classList.toggle('open', open);
    if (navBackdrop) navBackdrop.classList.toggle('show', open);
    document.body.classList.toggle('nav-open', open);
    if (burger) burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      setTimeout(function () { var c = nav.querySelector('.nav-close'); if (c) c.focus(); }, 60);
    } else if (!keepFocus && burger) {
      burger.focus();
    }
  }
  if (burger && nav) {
    burger.addEventListener('click', function () { setNav(!navIsOpen()); });
    var navClose = nav.querySelector('.nav-close');
    if (navClose) navClose.addEventListener('click', function () { setNav(false); });
    if (navBackdrop) navBackdrop.addEventListener('click', function () { setNav(false); });
    nav.addEventListener('click', function (e) { if (e.target.closest && e.target.closest('a')) setNav(false, true); });
    document.addEventListener('keydown', function (e) {
      if (!navIsOpen()) return;
      if (e.key === 'Escape') { setNav(false); return; }
      if (e.key === 'Tab') { // keep focus inside the open drawer
        var items = Array.prototype.filter.call(nav.querySelectorAll('a[href], button'), function (el) { return el.offsetParent !== null; });
        if (!items.length) return;
        var first = items[0], last = items[items.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    });
    // back to the computer layout: never leave the page locked
    window.addEventListener('resize', function () { if (window.innerWidth > 880) setNav(false, true); });
  }

  /* ---------------- reviews slider ---------------- */
  document.querySelectorAll('.rv-slider').forEach(function (sl) {
    var track = sl.querySelector('.rv-track');
    function step() { var c = track.querySelector('.review'); return c ? c.offsetWidth + 24 : 300; }
    var prev = sl.querySelector('.rv-prev'), next = sl.querySelector('.rv-next');
    if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function () {
      if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 5) track.scrollTo({ left: 0, behavior: 'smooth' });
      else track.scrollBy({ left: step(), behavior: 'smooth' });
    });
  });

  /* ---------------- service card filters ---------------- */
  document.querySelectorAll('.filters[data-for]').forEach(function (bar) {
    var grid = document.getElementById(bar.getAttribute('data-for'));
    if (!grid) return;
    bar.addEventListener('click', function (e) {
      var b = e.target.closest('button'); if (!b) return;
      bar.querySelectorAll('button').forEach(function (x) { x.classList.toggle('on', x === b); });
      var f = b.getAttribute('data-filter');
      grid.querySelectorAll('.card').forEach(function (c) { c.classList.toggle('hide', f !== '' && c.getAttribute('data-cat') !== f); });
    });
  });

  /* ---------------- reveal on scroll ---------------- */
  var els = document.querySelectorAll('.rv');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (en) {
      en.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('in'); io.unobserve(x.target); } });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    els.forEach(function (el) { io.observe(el); });
  } else { els.forEach(function (el) { el.classList.add('in'); }); }
  setTimeout(function () { els.forEach(function (el) { el.classList.add('in'); }); }, 4000);

  /* ---------------- offer countdown ---------------- */
  var boxes = document.querySelectorAll('.countdown[data-end]');
  if (boxes.length) {
    var pad = function (n) { return (n < 10 ? '0' : '') + n; };
    var tick = function () {
      boxes.forEach(function (b) {
        var left = new Date(b.getAttribute('data-end')).getTime() - Date.now();
        if (isNaN(left)) return;
        if (left <= 0) { b.classList.add('done'); b.textContent = b.getAttribute('data-ended') || ''; return; }
        var s = Math.floor(left / 1000);
        var v = { d: Math.floor(s / 86400), h: Math.floor(s % 86400 / 3600), m: Math.floor(s % 3600 / 60), s: s % 60 };
        for (var k in v) { var el = b.querySelector('[data-u=' + k + ']'); if (el) el.textContent = pad(v[k]); }
      });
    };
    tick(); setInterval(tick, 1000);
  }
})();
