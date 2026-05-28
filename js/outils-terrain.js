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
    var activeStructure = 'tous';
    var searchQuery     = '';

    /* ── DOM refs ───────────────────────────────────────── */
    var grid, cards, noResultsEl, activeFiltersEl, resultCountEl,
        overlay, modal, modalContent, modalVisual, modalIcon, modalTags, modalVisualOrg,
        searchInput;

    /* ══════════════════════════════════════════════════════
       FILTRES
       ══════════════════════════════════════════════════════ */

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
            var show = matchesStructure(card) && matchesSearch(card);
            card.hidden = !show;
            if (show) visible++;
        });
        noResultsEl.hidden = visible > 0;
        applyGridCols(visible);
        renderActiveFilters();
        updateResultCount(visible);
    }

    /* Colonnes adaptatives : aligné sur le PHP (1 → 1 col,
       2-4 → 2 cols, 5+ → 3 cols). Évite les colonnes vides à droite. */
    function applyGridCols(n) {
        if (!grid) return;
        var c = n <= 1 ? 1 : (n <= 4 ? 2 : 3);
        grid.classList.remove('ot-grid--c1', 'ot-grid--c2', 'ot-grid--c3');
        grid.classList.add('ot-grid--c' + c);
    }

    function updateResultCount(n) {
        if (!resultCountEl) return;
        var total = cards.length;
        if (n === total) {
            resultCountEl.hidden = true;
            resultCountEl.textContent = '';
        } else {
            resultCountEl.hidden = false;
            resultCountEl.textContent = n + ' outil' + (n > 1 ? 's' : '') + ' affiché' + (n > 1 ? 's' : '') + ' sur ' + total;
        }
    }

    /* ── Chips filtres actifs ───────────────────────────── */
    function renderActiveFilters() {
        var chips = [];

        if (activeStructure !== 'tous') {
            var structLabels = {
                'eaje': 'EAJE', 'assistante_maternelle': 'Ass. maternelle',
                'rpe': 'RPE', 'acm': 'ACM', 'autre': 'Autre'
            };
            chips.push({ label: structLabels[activeStructure] || activeStructure, key: 'struct', val: activeStructure });
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

    function syncStructPills() {
        document.querySelectorAll('.ot-struct-pill').forEach(function (pill) {
            pill.classList.toggle('is-active', pill.dataset.struct === activeStructure);
        });
    }

    function removeFilter(key, val) {
        if (key === 'struct') {
            activeStructure = 'tous';
            syncStructPills();
        } else if (key === 'search') {
            searchQuery = '';
            searchInput.value = '';
        }
        applyFilters();
    }

    function resetAllFilters() {
        activeStructure = 'tous';
        searchQuery = '';
        syncStructPills();
        searchInput.value = '';
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
        document.documentElement.classList.add('ot-modal-open');
        if (window._prhLenis) window._prhLenis.stop();

        /* Focus premier élément focusable */
        requestAnimationFrame(function () {
            var btn = modal.querySelector('.ot-modal-close');
            if (btn) btn.focus();
        });
    }

    function closeModal() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('ot-modal-open');
        if (window._prhLenis) window._prhLenis.start();
    }

    /* Libellés des tranches d'âge */
    var AGE_LABELS = {
        '0-3': '0-3 ans', '3-6': '3-6 ans',
        '6-12': '6-12 ans', '12-18': '12-18 ans'
    };

    /* Libellés type de lieu d'accueil */
    var LIEU_LABELS = {
        'eaje':                  'EAJE',
        'assistante_maternelle': 'Assistante maternelle',
        'rpe':                   'RPE',
        'acm':                   'ACM',
        'autre':                 'Autre',
        'tous':                  'Tous types',
    };

    function populateModal(tool) {
        /* Visual sidebar */
        modalVisual.style.background = 'linear-gradient(135deg,' + tool.g1 + ',' + tool.g2 + ')';
        modalIcon.innerHTML = ICONS[tool.icon] || '';

        /* Sidebar : lieu d'accueil + tranches d'âge */
        var lieuTagHtml = '';
        var lieuLabel = LIEU_LABELS[tool.structure] || null;
        if (lieuLabel) {
            lieuTagHtml = '<div class="ot-modal-sidebar-group">'
                + '<span class="ot-modal-sidebar-label">Lieu d\'accueil</span>'
                + '<span class="ot-modal-tag ot-modal-tag--lieu">' + escHtml(lieuLabel) + '</span>'
                + '</div>';
        }
        var ageTagsHtml = '';
        if (tool.age_ranges && tool.age_ranges.length) {
            ageTagsHtml = '<div class="ot-modal-sidebar-group">'
                + '<span class="ot-modal-sidebar-label">Tranches d\'âge</span>'
                + '<div class="ot-modal-sidebar-chips">'
                + tool.age_ranges.map(function (a) {
                    return '<span class="ot-modal-tag ot-modal-tag--age">' + escHtml(AGE_LABELS[a] || a) + '</span>';
                }).join('')
                + '</div>'
                + '</div>';
        }
        modalTags.innerHTML = lieuTagHtml + ageTagsHtml;

        /* Sidebar org (en bas) */
        if (modalVisualOrg) {
            var initials = tool.contact_name
                ? tool.contact_name.split(' ').map(function (w) { return w[0] || ''; }).join('').slice(0, 2).toUpperCase()
                : '?';
            modalVisualOrg.innerHTML = tool.contact_org
                ? '<div class="ot-modal-visual-org-inner">'
                +   '<div class="ot-modal-avatar">' + escHtml(initials) + '</div>'
                +   '<span class="ot-modal-visual-org-name">' + escHtml(tool.contact_org) + '</span>'
                + '</div>'
                : '';
        }

        /* Specs rows */
        var specsRows = (tool.specs || []).map(function (s) {
            return '<tr><th>' + escHtml(s[0]) + '</th><td>' + escHtml(s[1]) + '</td></tr>';
        }).join('');


        /* Galerie photos */
        var photosHtml = '';
        if (tool.photos && tool.photos.length) {
            var isSingle = tool.photos.length === 1;
            photosHtml = '<div class="ot-modal-gallery' + (isSingle ? ' ot-modal-gallery--single' : '') + '">'
                + tool.photos.map(function (url, i) {
                    return '<img class="ot-modal-gallery-img" src="' + escHtml(url) + '" alt="" loading="lazy"'
                        + ' data-lightbox-idx="' + i + '" role="button" tabindex="0"'
                        + ' aria-label="Agrandir la photo ' + (i + 1) + '">';
                }).join('')
                + '</div>';
        }

        modalContent.innerHTML =
            '<h2 class="ot-modal-title" id="ot-modal-title">' + escHtml(tool.title) + '</h2>'
            + '<p class="ot-modal-meta">Mis à jour le ' + escHtml(tool.date) + '</p>'

            + (tool.description
                ? '<div class="ot-modal-section">'
                +   '<h3 class="ot-modal-section-title">'
                +     '<svg class="ot-modal-section-icon" viewBox="0 0 16 16" fill="none" width="14" height="14" aria-hidden="true"><path d="M2 3h12M2 7h9M2 11h11M2 15h7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>'
                +     'Description'
                +   '</h3>'
                +   '<p class="ot-modal-description">' + escHtml(tool.description) + '</p>'
                + '</div>'
                : '')

            + photosHtml

            + (tool.story
                ? '<div class="ot-modal-section">'
                +   '<h3 class="ot-modal-section-title">'
                +     '<svg class="ot-modal-section-icon" viewBox="0 0 16 16" fill="none" width="14" height="14" aria-hidden="true"><path d="M2 3h12v8H9l-3 3V11H2V3z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>'
                +     'Contexte d\'utilisation'
                +   '</h3>'
                +   '<p class="ot-modal-story">' + escHtml(tool.story) + '</p>'
                + '</div>'
                : '')

            + (specsRows
                ? '<div class="ot-modal-section">'
                +   '<h3 class="ot-modal-section-title">'
                +     '<svg class="ot-modal-section-icon" viewBox="0 0 16 16" fill="none" width="14" height="14" aria-hidden="true"><rect x="2" y="2" width="12" height="2" rx=".5" fill="currentColor" opacity=".6"/><rect x="2" y="7" width="8" height="2" rx=".5" fill="currentColor" opacity=".6"/><rect x="2" y="12" width="10" height="2" rx=".5" fill="currentColor" opacity=".6"/></svg>'
                +     'Spécifications'
                +   '</h3>'
                +   '<table class="ot-modal-specs">' + specsRows + '</table>'
                + '</div>'
                : '')

            + '';
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

    function contactUrl() {
        return window.prhOutilsContactUrl || (window.location.origin + '/formulaire-contact/');
    }

    /* ══════════════════════════════════════════════════════
       INIT
       ══════════════════════════════════════════════════════ */

    function init() {
        grid            = document.getElementById('ot-grid');
        noResultsEl     = document.getElementById('ot-no-results');
        activeFiltersEl = document.getElementById('ot-active-filters');
        resultCountEl   = document.getElementById('ot-result-count');
        overlay         = document.getElementById('ot-modal-overlay');
        modal           = document.getElementById('ot-modal');
        modalContent    = document.getElementById('ot-modal-content');
        modalVisual     = document.getElementById('ot-modal-visual');
        modalIcon       = document.getElementById('ot-modal-icon');
        modalTags       = document.getElementById('ot-modal-visual-tags');
        modalVisualOrg  = document.getElementById('ot-modal-visual-org');
        searchInput     = document.getElementById('ot-search');

        if (!grid) return;

        cards = Array.from(grid.querySelectorAll('.ot-card'));

        /* Structure pills */
        document.querySelectorAll('.ot-struct-pill').forEach(function (pill) {
            pill.addEventListener('click', function () {
                activeStructure = pill.dataset.struct;
                syncStructPills();
                applyFilters();
            });
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
            if (e.key === 'Escape') {
                if (lightbox && !lightbox.hasAttribute('hidden')) {
                    closeLightbox();
                } else {
                    closeModal();
                }
            }
            if (e.key === 'ArrowRight' && lightbox && !lightbox.hasAttribute('hidden')) lightboxNav(1);
            if (e.key === 'ArrowLeft'  && lightbox && !lightbox.hasAttribute('hidden')) lightboxNav(-1);
        });

        /* Lightbox — clic sur une image de la galerie */
        modal.addEventListener('click', function (e) {
            var img = e.target.closest('.ot-modal-gallery-img');
            if (img) openLightbox(parseInt(img.dataset.lightboxIdx, 10));
        });
        modal.addEventListener('keydown', function (e) {
            if ((e.key === 'Enter' || e.key === ' ') && e.target.classList.contains('ot-modal-gallery-img')) {
                e.preventDefault();
                openLightbox(parseInt(e.target.dataset.lightboxIdx, 10));
            }
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

    /* ─────────────────────────────────────────────────────
       LIGHTBOX
    ───────────────────────────────────────────────────── */
    var lightbox     = null;
    var lbImg        = null;
    var lbPrev       = null;
    var lbNext       = null;
    var lbCounter    = null;
    var lbUrls       = [];
    var lbIndex      = 0;

    function buildLightbox() {
        if (lightbox) return;
        lightbox = document.createElement('div');
        lightbox.className = 'ot-lightbox';
        lightbox.setAttribute('role', 'dialog');
        lightbox.setAttribute('aria-modal', 'true');
        lightbox.setAttribute('aria-label', 'Photo agrandie');
        lightbox.setAttribute('hidden', '');

        lightbox.innerHTML =
            '<button class="ot-lb-close" aria-label="Fermer">'
            + '<svg viewBox="0 0 24 24" fill="none" width="24" height="24"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
            + '</button>'
            + '<button class="ot-lb-nav ot-lb-prev" aria-label="Photo précédente">'
            + '<svg viewBox="0 0 24 24" fill="none" width="20" height="20"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            + '</button>'
            + '<div class="ot-lb-img-wrap"><img class="ot-lb-img" src="" alt=""></div>'
            + '<button class="ot-lb-nav ot-lb-next" aria-label="Photo suivante">'
            + '<svg viewBox="0 0 24 24" fill="none" width="20" height="20"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            + '</button>'
            + '<span class="ot-lb-counter"></span>';

        document.body.appendChild(lightbox);

        lbImg     = lightbox.querySelector('.ot-lb-img');
        lbPrev    = lightbox.querySelector('.ot-lb-prev');
        lbNext    = lightbox.querySelector('.ot-lb-next');
        lbCounter = lightbox.querySelector('.ot-lb-counter');

        lightbox.querySelector('.ot-lb-close').addEventListener('click', closeLightbox);
        lbPrev.addEventListener('click', function () { lightboxNav(-1); });
        lbNext.addEventListener('click', function () { lightboxNav(1); });

        /* Clic sur le fond → fermer */
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox || e.target.classList.contains('ot-lb-img-wrap')) closeLightbox();
        });
    }

    function openLightbox(idx) {
        var imgs = modal.querySelectorAll('.ot-modal-gallery-img');
        lbUrls = Array.from(imgs).map(function (i) { return i.src; });
        if (!lbUrls.length) return;

        buildLightbox();
        lbIndex = idx || 0;
        lightboxShow();

        lightbox.removeAttribute('hidden');
        /* scroll déjà verrouillé par la modal — pas besoin de retoucher */
        lightbox.querySelector('.ot-lb-close').focus();
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.setAttribute('hidden', '');
        /* ne pas toucher au scroll — la modal est encore ouverte */
    }

    function lightboxShow() {
        if (!lbImg) return;
        lbImg.src = lbUrls[lbIndex];
        lbImg.alt = 'Photo ' + (lbIndex + 1) + ' sur ' + lbUrls.length;

        var multi = lbUrls.length > 1;
        lbPrev.hidden = !multi;
        lbNext.hidden = !multi;
        lbCounter.hidden = !multi;
        if (multi) lbCounter.textContent = (lbIndex + 1) + ' / ' + lbUrls.length;

        lbPrev.disabled = lbIndex === 0;
        lbNext.disabled = lbIndex === lbUrls.length - 1;
    }

    function lightboxNav(dir) {
        var next = lbIndex + dir;
        if (next < 0 || next >= lbUrls.length) return;
        lbIndex = next;
        lightboxShow();
    }

    document.addEventListener('DOMContentLoaded', init);

})();
