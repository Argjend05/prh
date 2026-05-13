/* =======================================================
   Outils de Terrain — filtres + modal détail
   ======================================================= */
(function () {
    'use strict';

    /* ── Données ────────────────────────────────────────── */
    var TOOLS = window.prhOutils || [];

    /* ── Icons SVG (pour le modal) ──────────────────────── */
    var ICONS = {
        school: '<svg viewBox="0 0 48 48" fill="none" width="80" height="80"><path d="M6 42V22L24 10l18 12v20" stroke="rgba(255,255,255,.9)" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/><rect x="18" y="30" width="12" height="12" rx="1.5" stroke="rgba(255,255,255,.9)" stroke-width="2.5"/><circle cx="24" cy="20" r="4" stroke="rgba(255,255,255,.45)" stroke-width="1.5"/></svg>',
        eye:    '<svg viewBox="0 0 48 48" fill="none" width="80" height="80"><ellipse cx="24" cy="24" rx="18" ry="11" stroke="rgba(255,255,255,.9)" stroke-width="2.5"/><circle cx="24" cy="24" r="6" fill="rgba(255,255,255,.2)" stroke="rgba(255,255,255,.9)" stroke-width="2"/><circle cx="24" cy="24" r="2.5" fill="rgba(255,255,255,.9)"/></svg>',
        chat:   '<svg viewBox="0 0 48 48" fill="none" width="80" height="80"><rect x="6" y="7" width="36" height="26" rx="6" stroke="rgba(255,255,255,.9)" stroke-width="2.5"/><path d="M13 40l6-7h10" stroke="rgba(255,255,255,.9)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 19h20M14 25h13" stroke="rgba(255,255,255,.55)" stroke-width="2" stroke-linecap="round"/></svg>',
        shield: '<svg viewBox="0 0 48 48" fill="none" width="80" height="80"><path d="M24 5l16 6v13c0 9-6.5 15.5-16 18-9.5-2.5-16-9-16-18V11z" stroke="rgba(255,255,255,.9)" stroke-width="2.5" stroke-linejoin="round"/><path d="M16 24l5.5 5.5L33 18" stroke="rgba(255,255,255,.95)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        home:   '<svg viewBox="0 0 48 48" fill="none" width="80" height="80"><path d="M8 24L24 10l16 14" stroke="rgba(255,255,255,.9)" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/><path d="M12 20v20h9v-9h6v9h9V20" stroke="rgba(255,255,255,.9)" stroke-width="2.5" stroke-linejoin="round"/></svg>',
        heart:  '<svg viewBox="0 0 48 48" fill="none" width="80" height="80"><path d="M24 40S8 30 8 18.5C8 13.5 11.5 9 16.5 9c2.8 0 5.5 1.5 7.5 4.5C26 10.5 28.7 9 31.5 9 36.5 9 40 13.5 40 18.5 40 30 24 40 24 40z" fill="rgba(255,255,255,.18)" stroke="rgba(255,255,255,.9)" stroke-width="2.5" stroke-linejoin="round"/></svg>',
    };

    /* ── État des filtres ───────────────────────────────── */
    var activeEnvs      = [];   // [] = tous
    var activeStructure = 'tous';
    var searchQuery     = '';

    /* ── DOM refs ───────────────────────────────────────── */
    var grid, cards, noResultsEl, activeFiltersEl,
        overlay, modal, modalContent, modalVisual, modalIcon, modalTags,
        searchInput, structureSelect;

    /* ══════════════════════════════════════════════════════
       FILTRES
       ══════════════════════════════════════════════════════ */

    function matchesEnv(card) {
        if (!activeEnvs.length) return true;
        var cardEnvs = (card.dataset.envs || '').split(',');
        return activeEnvs.some(function (e) { return cardEnvs.indexOf(e) !== -1; });
    }

    function matchesStructure(card) {
        if (activeStructure === 'tous') return true;
        return card.dataset.structure === activeStructure || card.dataset.structure === 'tous';
    }

    function matchesSearch(card) {
        if (!searchQuery) return true;
        return (card.dataset.search || '').indexOf(searchQuery) !== -1;
    }

    function applyFilters() {
        var visible = 0;
        cards.forEach(function (card) {
            var show = matchesEnv(card) && matchesStructure(card) && matchesSearch(card);
            card.hidden = !show;
            if (show) visible++;
        });
        noResultsEl.hidden = visible > 0;
        renderActiveFilters();
    }

    /* ── Chips filtres actifs ───────────────────────────── */
    function renderActiveFilters() {
        var chips = [];

        activeEnvs.forEach(function (env) {
            chips.push({ label: ucFirst(env), key: 'env', val: env });
        });

        if (activeStructure !== 'tous') {
            var opt = structureSelect.querySelector('option[value="' + activeStructure + '"]');
            chips.push({ label: opt ? opt.textContent : activeStructure, key: 'struct', val: activeStructure });
        }

        if (searchQuery) {
            chips.push({ label: '« ' + searchQuery + ' »', key: 'search', val: '' });
        }

        if (!chips.length) {
            activeFiltersEl.innerHTML = '';
            return;
        }

        activeFiltersEl.innerHTML = '<span class="ot-active-filters-label">Filtres actifs :</span>'
            + chips.map(function (c) {
                return '<span class="ot-filter-chip">'
                    + escHtml(c.label)
                    + '<button data-key="' + c.key + '" data-val="' + escHtml(c.val) + '" aria-label="Retirer le filtre ' + escHtml(c.label) + '">'
                    + '<svg viewBox="0 0 8 8" fill="none" width="8" height="8"><path d="M1 1l6 6M7 1L1 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>'
                    + '</button>'
                    + '</span>';
            }).join('');
    }

    function removeFilter(key, val) {
        if (key === 'env') {
            activeEnvs = activeEnvs.filter(function (e) { return e !== val; });
            syncEnvPills();
        } else if (key === 'struct') {
            activeStructure = 'tous';
            structureSelect.value = 'tous';
        } else if (key === 'search') {
            searchQuery = '';
            searchInput.value = '';
        }
        applyFilters();
    }

    function syncEnvPills() {
        document.querySelectorAll('.ot-env-pill').forEach(function (pill) {
            var env = pill.dataset.env;
            if (env === 'tous') {
                pill.classList.toggle('is-active', activeEnvs.length === 0);
            } else {
                pill.classList.toggle('is-active', activeEnvs.indexOf(env) !== -1);
            }
        });
    }

    function resetAllFilters() {
        activeEnvs = [];
        activeStructure = 'tous';
        searchQuery = '';
        structureSelect.value = 'tous';
        searchInput.value = '';
        syncEnvPills();
        applyFilters();
    }

    /* ══════════════════════════════════════════════════════
       MODAL
       ══════════════════════════════════════════════════════ */

    function openModal(toolId) {
        var tool = TOOLS.find(function (t) { return t.id === toolId; });
        if (!tool) return;

        populateModal(tool);
        overlay.setAttribute('aria-hidden', 'false');
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';

        /* Focus premier élément focusable */
        requestAnimationFrame(function () {
            var btn = modal.querySelector('.ot-modal-close');
            if (btn) btn.focus();
        });
    }

    function closeModal() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function populateModal(tool) {
        /* Visual sidebar */
        modalVisual.style.background = 'linear-gradient(135deg,' + tool.g1 + ',' + tool.g2 + ')';
        modalIcon.innerHTML = ICONS[tool.icon] || '';

        var tags = [tool.category].concat(tool.envs.slice(0, 2)).concat(tool.extra_tags || []);
        modalTags.innerHTML = tags.map(function (t) {
            return '<span class="ot-modal-tag">' + escHtml(ucFirst(t)) + '</span>';
        }).join('');

        /* Difficulty */
        var diffCls = tool.difficulty <= 35 ? 'easy' : (tool.difficulty <= 65 ? 'med' : 'hard');
        var diffLbl = tool.difficulty <= 35 ? 'Facile' : (tool.difficulty <= 65 ? 'Modéré' : 'Avancé');

        /* Initiales avatar */
        var initials = tool.contact_name.split(' ').map(function (w) { return w[0]; }).join('').slice(0, 2).toUpperCase();

        /* Specs rows */
        var specsRows = (tool.specs || []).map(function (s) {
            return '<tr><th>' + escHtml(s[0]) + '</th><td>' + escHtml(s[1]) + '</td></tr>';
        }).join('');

        /* Stars HTML */
        var usageStars   = buildStars(tool.usage);
        var deployStars  = buildStars(tool.deploy);

        modalContent.innerHTML =
            '<h2 class="ot-modal-title" id="ot-modal-title">' + escHtml(tool.title) + '</h2>'
            + '<p class="ot-modal-meta">Partagé par ' + escHtml(tool.contact_org)
            + ' &middot; Mis à jour le ' + escHtml(tool.date) + '</p>'

            + '<div class="ot-modal-metrics">'
            +   '<div class="ot-modal-metric">'
            +     '<span class="ot-modal-metric-label">Fréquence d\'usage</span>'
            +     usageStars
            +     '<span style="font-size:.75rem;color:#94a3b8;margin-top:2px;">' + tool.usage.toFixed(1) + '/5</span>'
            +   '</div>'
            +   '<div class="ot-modal-metric">'
            +     '<span class="ot-modal-metric-label">Facilité déploiement</span>'
            +     deployStars
            +     '<span style="font-size:.75rem;color:#94a3b8;margin-top:2px;">' + tool.deploy.toFixed(1) + '/5</span>'
            +   '</div>'
            +   '<div class="ot-modal-metric">'
            +     '<span class="ot-modal-metric-label">Difficulté</span>'
            +     '<span class="ot-diff-badge ot-diff--' + diffCls + '" style="font-size:.85rem;margin-top:6px;">'
            +       diffLbl + ' <strong>' + tool.difficulty + '</strong>/100'
            +     '</span>'
            +   '</div>'
            + '</div>'

            + '<div class="ot-modal-section">'
            +   '<h3 class="ot-modal-section-title">📖 L\'Histoire</h3>'
            +   '<p class="ot-modal-story">' + escHtml(tool.story) + '</p>'
            + '</div>'

            + (specsRows
                ? '<div class="ot-modal-section">'
                +   '<h3 class="ot-modal-section-title">⚙️ Spécifications Techniques</h3>'
                +   '<table class="ot-modal-specs">' + specsRows + '</table>'
                + '</div>'
                : '')

            + '<div class="ot-modal-section">'
            +   '<h3 class="ot-modal-section-title">👤 Contact Structure</h3>'
            +   '<div class="ot-modal-contact">'
            +     '<div class="ot-modal-contact-info">'
            +       '<div class="ot-modal-avatar">' + initials + '</div>'
            +       '<div>'
            +         '<span class="ot-modal-contact-name">' + escHtml(tool.contact_name) + '</span>'
            +         '<span class="ot-modal-contact-role">' + escHtml(tool.contact_role) + ' — ' + escHtml(tool.contact_org) + '</span>'
            +       '</div>'
            +     '</div>'
            +     '<a href="' + homeUrl() + '/contact" class="ot-modal-contact-btn">'
            +       '<svg viewBox="0 0 16 16" fill="none" width="14" height="14"><path d="M2 4l6 5 6-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><rect x="1" y="3" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/></svg>'
            +       'Contacter'
            +     '</a>'
            +   '</div>'
            + '</div>';
    }

    function buildStars(score) {
        var html = '<span class="ot-stars" style="display:flex;gap:2px;justify-content:center;">';
        for (var i = 1; i <= 5; i++) {
            var pct = Math.max(0, Math.min(1, score - (i - 1)));
            var cls = pct >= 0.75 ? 'full' : (pct >= 0.25 ? 'half' : 'empty');
            html += '<svg viewBox="0 0 12 12" class="ot-star ot-star--' + cls + '" aria-hidden="true" width="14" height="14"><polygon points="6,1 7.5,4.5 11,5 8.5,7.5 9,11 6,9.5 3,11 3.5,7.5 1,5 4.5,4.5"/></svg>';
        }
        html += '</span>';
        return html;
    }

    /* ══════════════════════════════════════════════════════
       UTILS
       ══════════════════════════════════════════════════════ */

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function ucFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function homeUrl() {
        /* Fallback: origin */
        return window.location.origin;
    }

    /* ══════════════════════════════════════════════════════
       INIT
       ══════════════════════════════════════════════════════ */

    function init() {
        grid            = document.getElementById('ot-grid');
        noResultsEl     = document.getElementById('ot-no-results');
        activeFiltersEl = document.getElementById('ot-active-filters');
        overlay         = document.getElementById('ot-modal-overlay');
        modal           = document.getElementById('ot-modal');
        modalContent    = document.getElementById('ot-modal-content');
        modalVisual     = document.getElementById('ot-modal-visual');
        modalIcon       = document.getElementById('ot-modal-icon');
        modalTags       = document.getElementById('ot-modal-visual-tags');
        searchInput     = document.getElementById('ot-search');
        structureSelect = document.getElementById('ot-structure-select');

        if (!grid) return;

        cards = Array.from(grid.querySelectorAll('.ot-card'));

        /* Env pills */
        document.querySelectorAll('.ot-env-pill').forEach(function (pill) {
            pill.addEventListener('click', function () {
                var env = pill.dataset.env;
                if (env === 'tous') {
                    activeEnvs = [];
                } else {
                    var idx = activeEnvs.indexOf(env);
                    if (idx === -1) {
                        activeEnvs.push(env);
                    } else {
                        activeEnvs.splice(idx, 1);
                    }
                }
                syncEnvPills();
                applyFilters();
            });
        });

        /* Structure select */
        structureSelect.addEventListener('change', function () {
            activeStructure = this.value;
            applyFilters();
        });

        /* Search */
        var searchTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            var q = this.value.toLowerCase().trim();
            searchTimer = setTimeout(function () {
                searchQuery = q;
                applyFilters();
            }, 220);
        });

        /* Active filters chip clicks */
        activeFiltersEl.addEventListener('click', function (e) {
            var btn = e.target.closest('button[data-key]');
            if (btn) removeFilter(btn.dataset.key, btn.dataset.val);
        });

        /* Reset button */
        var resetBtn = document.getElementById('ot-reset-btn');
        if (resetBtn) resetBtn.addEventListener('click', resetAllFilters);

        /* Card → open modal */
        grid.addEventListener('click', function (e) {
            var card = e.target.closest('.ot-card');
            if (card) openModal(card.dataset.id);
        });

        grid.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                var card = e.target.closest('.ot-card');
                if (card) { e.preventDefault(); openModal(card.dataset.id); }
            }
        });

        /* Close modal */
        document.getElementById('ot-modal-close').addEventListener('click', closeModal);

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

        /* Trap focus in modal */
        modal.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            var focusable = modal.querySelectorAll(
                'a[href],button:not([disabled]),input,select,textarea,[tabindex]:not([tabindex="-1"])'
            );
            var first = focusable[0];
            var last  = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault(); last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault(); first.focus();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', init);

})();
