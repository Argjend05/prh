/* =======================================================
   FORMULAIRE OUTIL TERRAIN — tunnel 4 étapes
   ======================================================= */
(function () {
    'use strict';

    /* ── Refs ── */
    var form        = document.getElementById('fot-form');
    if (!form) return;

    var steps       = Array.from(form.querySelectorAll('.fot-step'));
    var progressDots = Array.from(document.querySelectorAll('.fot-progress-step'));
    var fill        = document.getElementById('fot-progress-fill');
    var currentStep = 1;
    var totalSteps  = steps.length; // 4

    /* ─────────────────────────────────────────────────────
       PROGRESS BAR
    ───────────────────────────────────────────────────── */
    function updateProgress(step) {
        var pct = ((step - 1) / (totalSteps - 1)) * 100;
        if (fill) fill.style.width = pct + '%';

        progressDots.forEach(function (dot, idx) {
            var s = idx + 1;
            dot.classList.toggle('is-active', s === step);
            dot.classList.toggle('is-done',   s < step);
            dot.classList.remove('is-active');
            if (s < step)  dot.classList.add('is-done');
            if (s === step) dot.classList.add('is-active');
        });
    }

    /* ─────────────────────────────────────────────────────
       SHOW / HIDE STEPS
    ───────────────────────────────────────────────────── */
    function goToStep(n) {
        steps.forEach(function (el) {
            var s = parseInt(el.dataset.step, 10);
            if (s === n) {
                el.removeAttribute('hidden');
                el.style.animation = 'none';
                void el.offsetWidth; // reflow to restart animation
                el.style.animation = '';
            } else {
                el.setAttribute('hidden', '');
            }
        });

        currentStep = n;
        updateProgress(n);

        // scroll to form section top
        var section = document.querySelector('.fot-form-section');
        if (section) {
            var top = section.getBoundingClientRect().top + window.scrollY - 90;
            window.scrollTo({ top: top, behavior: 'smooth' });
        }
    }

    /* ─────────────────────────────────────────────────────
       VALIDATION PER STEP
    ───────────────────────────────────────────────────── */
    function validateStep(n) {
        var stepEl  = document.getElementById('fot-step-' + n);
        if (!stepEl) return true;

        var valid = true;

        /* Clear previous errors */
        stepEl.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        var prev = stepEl.querySelector('.fot-inline-error');
        if (prev) prev.remove();

        function markInvalid(el, msg) {
            el.classList.add('is-invalid');
            el.setAttribute('aria-invalid', 'true');
            var err = document.createElement('span');
            err.className = 'fot-inline-error';
            err.style.cssText = 'display:block;font-size:.75rem;color:#dc2626;margin-top:4px;font-weight:600;';
            err.textContent = msg;
            el.parentNode.insertAdjacentElement('afterend', err);
            if (valid) el.focus();
            valid = false;
        }

        if (n === 1) {
            var sname   = stepEl.querySelector('#fot_struct_name');
            var stype   = stepEl.querySelector('#fot_struct_type');
            var sautre  = stepEl.querySelector('#fot_struct_type_autre');
            var spostal = stepEl.querySelector('#fot_struct_postal');
            var rname   = stepEl.querySelector('#fot_ref_name');
            var email   = stepEl.querySelector('#fot_ref_email');

            if (!sname.value.trim())   markInvalid(sname,   'Le nom de la structure est requis.');
            if (!stype.value)          markInvalid(stype,   'Sélectionnez un type de structure.');
            if (stype.value === 'autre' && sautre && !sautre.value.trim()) {
                markInvalid(sautre, 'Précisez le type de structure.');
            }
            if (!spostal.value.trim()) markInvalid(spostal, 'Le code postal est requis.');
            if (!rname.value.trim())   markInvalid(rname,   'Le nom du référent est requis.');
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                markInvalid(email, 'Adresse e-mail invalide.');
            }
        }

        if (n === 2) {
            var oname   = stepEl.querySelector('#fot_outil_name');
            var odesc   = stepEl.querySelector('#fot_outil_desc');
            var octx    = stepEl.querySelector('#fot_outil_context');
            var envs    = stepEl.querySelectorAll('input[name="fot_outil_envs[]"]:checked');

            if (!oname.value.trim() || oname.value.length < 3) markInvalid(oname, 'Le nom de l\'outil est requis (3 caractères min).');
            if (!odesc.value.trim() || odesc.value.length < 20) markInvalid(odesc, 'La description doit faire au moins 20 caractères.');
            if (odesc.value.length > 400) markInvalid(odesc, 'La description est trop longue (400 car. max).');
            if (!octx.value.trim()  || octx.value.length < 30)  markInvalid(octx, 'Le contexte d\'utilisation est requis (30 caractères min).');
            if (envs.length === 0) {
                var fieldset = stepEl.querySelector('.fot-env-pills');
                var err = document.createElement('span');
                err.className = 'fot-inline-error';
                err.style.cssText = 'display:block;font-size:.75rem;color:#dc2626;margin-top:6px;font-weight:600;';
                err.textContent = 'Sélectionnez au moins un environnement.';
                fieldset.insertAdjacentElement('afterend', err);
                if (valid) (stepEl.querySelector('input[name="fot_outil_envs[]"]') || fieldset).focus();
                valid = false;
            }
        }

        // Steps 3 (photos) and 4 (recap + consent) validate lightly
        if (n === 4) {
            var consent     = stepEl.querySelector('#fot_consent');
            var consentLbl  = document.getElementById('fot-consent-label');
            // Remove previous consent error
            var prevErr = stepEl.querySelector('.fot-consent-error');
            if (prevErr) prevErr.remove();
            if (consentLbl) consentLbl.classList.remove('is-invalid');

            if (!consent.checked) {
                if (consentLbl) {
                    consentLbl.classList.add('is-invalid');
                    var err2 = document.createElement('p');
                    err2.className = 'fot-consent-error';
                    err2.style.cssText = 'font-size:.75rem;color:#dc2626;margin:6px 0 0;font-weight:600;';
                    err2.textContent = 'Vous devez accepter les conditions avant de soumettre.';
                    consentLbl.insertAdjacentElement('afterend', err2);
                }
                if (valid) consent.focus();
                valid = false;
            }
        }

        return valid;
    }

    /* ─────────────────────────────────────────────────────
       NEXT / BACK BUTTONS
    ───────────────────────────────────────────────────── */
    form.addEventListener('click', function (e) {
        var nextBtn = e.target.closest('.fot-next-btn');
        var backBtn = e.target.closest('.fot-back-btn');

        if (nextBtn) {
            var next = parseInt(nextBtn.dataset.next, 10);
            if (validateStep(currentStep)) {
                if (next === 4) populateRecap();
                goToStep(next);
            }
        }

        if (backBtn) {
            var back = parseInt(backBtn.dataset.back, 10);
            goToStep(back);
        }
    });

    /* ─────────────────────────────────────────────────────
       FORM SUBMIT — valider le consentement côté client
       avant d'envoyer au PHP (évite un rechargement complet)
    ───────────────────────────────────────────────────── */
    form.addEventListener('submit', function (e) {
        if (!validateStep(4)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });

    /* ─────────────────────────────────────────────────────
       CHARACTER COUNT
    ───────────────────────────────────────────────────── */
    var descTA  = document.getElementById('fot_outil_desc');
    var descCnt = document.getElementById('fot-desc-count');
    var charSpan = descTA && descTA.closest('.fot-field') && descTA.closest('.fot-field').querySelector('.fot-charcount');

    if (descTA && descCnt) {
        function updateCount() {
            var len = descTA.value.length;
            descCnt.textContent = len;
            if (charSpan) {
                charSpan.classList.toggle('is-near', len >= 340 && len <= 400);
                charSpan.classList.toggle('is-over', len > 400);
            }
        }
        descTA.addEventListener('input', updateCount);
        updateCount();
    }

    /* ─────────────────────────────────────────────────────
       TYPE STRUCTURE "AUTRE" — champ conditionnel
    ───────────────────────────────────────────────────── */
    var structSelect = document.getElementById('fot_struct_type');
    var autreWrap    = document.getElementById('fot-struct-autre-wrap');

    function toggleAutreField() {
        if (!structSelect || !autreWrap) return;
        var show = structSelect.value === 'autre';
        if (show) {
            autreWrap.removeAttribute('hidden');
            autreWrap.querySelector('input').required = true;
        } else {
            autreWrap.setAttribute('hidden', '');
            autreWrap.querySelector('input').required = false;
            autreWrap.querySelector('input').value = '';
        }
    }

    if (structSelect) {
        structSelect.addEventListener('change', toggleAutreField);
        toggleAutreField();
    }

    /* ─────────────────────────────────────────────────────
       TAG INPUT
    ───────────────────────────────────────────────────── */
    var tagInput  = document.getElementById('fot-tag-input');
    var tagList   = document.getElementById('fot-tags-list');
    var tagHidden = document.getElementById('fot-tags-hidden');
    var tagAddBtn = document.getElementById('fot-tag-add');
    var tags      = [];

    function renderTags() {
        if (!tagList) return;
        tagList.innerHTML = '';
        tags.forEach(function (tag, idx) {
            var chip = document.createElement('span');
            chip.className = 'fot-tag-chip';
            chip.innerHTML = tag + '<button type="button" class="fot-tag-remove" data-idx="' + idx + '" aria-label="Supprimer ' + tag + '">×</button>';
            tagList.appendChild(chip);
        });
        if (tagHidden) tagHidden.value = tags.join(',');
    }

    function addTag(raw) {
        var val = raw.trim().replace(/,+/g, '').substring(0, 32);
        if (!val || tags.length >= 10 || tags.indexOf(val) !== -1) return;
        tags.push(val);
        renderTags();
    }

    if (tagInput) {
        tagInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag(tagInput.value);
                tagInput.value = '';
            }
            if (e.key === 'Backspace' && tagInput.value === '' && tags.length) {
                tags.pop();
                renderTags();
            }
        });
    }

    if (tagAddBtn) {
        tagAddBtn.addEventListener('click', function () {
            if (tagInput) {
                addTag(tagInput.value);
                tagInput.value = '';
                tagInput.focus();
            }
        });
    }

    if (tagList) {
        tagList.addEventListener('click', function (e) {
            var btn = e.target.closest('.fot-tag-remove');
            if (btn) {
                var idx = parseInt(btn.dataset.idx, 10);
                tags.splice(idx, 1);
                renderTags();
            }
        });
    }

    /* ─────────────────────────────────────────────────────
       DROPZONE + PHOTO PREVIEW
    ───────────────────────────────────────────────────── */
    var dropzone    = document.getElementById('fot-dropzone');
    var fileInput   = document.getElementById('fot_photos');
    var browseBtn   = document.getElementById('fot-browse-btn');
    var preview     = document.getElementById('fot-photo-preview');
    var photoFiles  = []; // DataTransfer-managed list

    /* Clic sur le bouton "Parcourir" */
    if (browseBtn && fileInput) {
        browseBtn.addEventListener('click', function (e) {
            e.stopPropagation(); // évite de déclencher aussi le handler dropzone
            fileInput.click();
        });
    }

    /* Clic n'importe où sur la zone de dépôt (hors bouton) */
    if (dropzone && fileInput) {
        dropzone.addEventListener('click', function (e) {
            if (e.target.closest('#fot-browse-btn')) return; // déjà géré ci-dessus
            fileInput.click();
        });
    }

    function addFiles(newFiles) {
        var added = 0;
        Array.from(newFiles).forEach(function (file) {
            if (photoFiles.length >= 5) return;
            if (!file.type.startsWith('image/')) return;
            if (file.size > 5 * 1024 * 1024) return; // 5MB
            photoFiles.push(file);
            added++;
        });
        if (added) syncFileInput();
        renderPreviews();
    }

    function syncFileInput() {
        if (!fileInput) return;
        try {
            var dt = new DataTransfer();
            photoFiles.forEach(function (f) { dt.items.add(f); });
            fileInput.files = dt.files;
        } catch (e) { /* Safari fallback: no removal */ }
    }

    function renderPreviews() {
        if (!preview) return;
        preview.innerHTML = '';
        photoFiles.forEach(function (file, idx) {
            var thumb = document.createElement('div');
            thumb.className = 'fot-preview-thumb';

            var url = URL.createObjectURL(file);
            var img = document.createElement('img');
            img.src = url;
            img.alt = file.name;
            img.onload = function () { URL.revokeObjectURL(url); };

            var rm = document.createElement('button');
            rm.type = 'button';
            rm.className = 'fot-preview-remove';
            rm.setAttribute('aria-label', 'Supprimer ' + file.name);
            rm.textContent = '×';
            rm.dataset.idx = idx;

            thumb.appendChild(img);
            thumb.appendChild(rm);
            preview.appendChild(thumb);
        });
    }

    if (preview) {
        preview.addEventListener('click', function (e) {
            var btn = e.target.closest('.fot-preview-remove');
            if (btn) {
                var idx = parseInt(btn.dataset.idx, 10);
                photoFiles.splice(idx, 1);
                syncFileInput();
                renderPreviews();
            }
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            addFiles(fileInput.files);
        });
    }

    if (dropzone) {
        ['dragenter', 'dragover'].forEach(function (ev) {
            dropzone.addEventListener(ev, function (e) {
                e.preventDefault();
                dropzone.classList.add('is-drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(function (ev) {
            dropzone.addEventListener(ev, function (e) {
                e.preventDefault();
                dropzone.classList.remove('is-drag-over');
                if (e.type === 'drop' && e.dataTransfer.files.length) {
                    addFiles(e.dataTransfer.files);
                }
            });
        });
    }

    /* ─────────────────────────────────────────────────────
       RECAP POPULATION (step 4)
    ───────────────────────────────────────────────────── */
    var envLabels = {
        'urbain': 'Urbain', 'rural': 'Rural', 'domicile': 'Domicile',
        'eaje': 'EAJE', 'rue': 'Rue', 'collectivite': 'Collectivité'
    };

    var ageLabels = {
        '0-3': '0-3 ans', '3-6': '3-6 ans',
        '6-12': '6-12 ans', '12-18': '12-18 ans'
    };

    function populateRecap() {
        setRecap('fot-recap-struct', val('#fot_struct_name') || '—');

        var typeLabel  = optionLabel('#fot_struct_type') || '';
        var autreLabel = val('#fot_struct_type_autre');
        if (typeLabel === 'Autre' && autreLabel) typeLabel = autreLabel;
        var postal = val('#fot_struct_postal');
        setRecap('fot-recap-type', [typeLabel, postal ? 'CP ' + postal : ''].filter(Boolean).join(' · ') || '—');

        setRecap('fot-recap-ref',    val('#fot_ref_name')    || '—');
        var role  = val('#fot_ref_role');
        var email = val('#fot_ref_email');
        setRecap('fot-recap-role', [role, email].filter(Boolean).join(' · ') || '—');

        setRecap('fot-recap-outil',  val('#fot_outil_name') || '—');

        // Photos
        var n = photoFiles.length;
        var photoTxt = n === 0 ? 'Aucune photo ajoutée'
                     : n === 1 ? '1 photo téléversée'
                     : n + ' photos téléversées';
        setRecap('fot-recap-photos', photoTxt);
        var subEl = document.getElementById('fot-recap-photos-sub');
        if (subEl) subEl.textContent = n > 0 ? 'JPG / PNG · En attente de modération' : '';

        // Environnements
        var envsEl = document.getElementById('fot-recap-envs');
        if (envsEl) {
            envsEl.innerHTML = '';
            var checkedEnvs = Array.from(form.querySelectorAll('input[name="fot_outil_envs[]"]:checked'));
            checkedEnvs.forEach(function (cb) {
                var chip = document.createElement('span');
                chip.className = 'fot-recap-tag';
                chip.textContent = envLabels[cb.value] || cb.value;
                envsEl.appendChild(chip);
            });
            if (!checkedEnvs.length) envsEl.textContent = '—';
        }

        // Tranches d'âge
        var agesEl = document.getElementById('fot-recap-ages');
        if (agesEl) {
            agesEl.innerHTML = '';
            var checkedAges = Array.from(form.querySelectorAll('input[name="fot_outil_ages[]"]:checked'));
            checkedAges.forEach(function (cb) {
                var chip = document.createElement('span');
                chip.className = 'fot-recap-tag';
                chip.textContent = ageLabels[cb.value] || cb.value;
                agesEl.appendChild(chip);
            });
        }
    }

    function val(sel) {
        var el = document.querySelector(sel);
        return el ? el.value.trim() : '';
    }

    function optionLabel(sel) {
        var el = document.querySelector(sel);
        if (!el) return '';
        var opt = el.options[el.selectedIndex];
        return opt ? opt.textContent.trim() : '';
    }

    function setRecap(id, text) {
        var el = document.getElementById(id);
        if (el) el.textContent = text;
    }

    /* ─────────────────────────────────────────────────────
       AUTOCOMPLETE — champ nom de structure
    ───────────────────────────────────────────────────── */
    var orgInput   = document.getElementById('fot_struct_name');
    var orgList    = document.getElementById('fot-org-suggestions');
    var orgData    = (window.fotOrgSuggestions && window.fotOrgSuggestions.length) ? window.fotOrgSuggestions : [];
    var orgActive  = -1; // index de l'item sélectionné au clavier

    function normalizeOrg(s) {
        return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9 ]/g, '').replace(/\s+/g, ' ').trim();
    }

    function showSuggestions(query) {
        if (!orgList || !orgData.length) return;
        var norm = normalizeOrg(query);
        if (!norm) { hideSuggestions(); return; }

        var matches = orgData.filter(function(s) {
            return normalizeOrg(s).indexOf(norm) !== -1;
        }).slice(0, 6);

        if (!matches.length) { hideSuggestions(); return; }

        orgList.innerHTML = matches.map(function(s, idx) {
            // Surligner la partie correspondante
            var re = new RegExp('(' + norm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
            var highlighted = escHtmlStr(s).replace(re, '<span class="fot-suggestion-match">$1</span>');
            return '<li class="fot-suggestion-item" role="option" data-value="' + escHtmlStr(s) + '" id="fot-sug-' + idx + '">' + highlighted + '</li>';
        }).join('');

        orgList.removeAttribute('hidden');
        orgInput.setAttribute('aria-expanded', 'true');
        orgActive = -1;
    }

    function hideSuggestions() {
        if (!orgList) return;
        orgList.setAttribute('hidden', '');
        orgList.innerHTML = '';
        if (orgInput) orgInput.setAttribute('aria-expanded', 'false');
        orgActive = -1;
    }

    function selectSuggestion(value) {
        if (orgInput) orgInput.value = value;
        hideSuggestions();
    }

    function escHtmlStr(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function updateActiveItem(items) {
        items.forEach(function(li, idx) {
            li.setAttribute('aria-selected', idx === orgActive ? 'true' : 'false');
        });
        if (orgActive >= 0 && items[orgActive]) {
            orgInput.setAttribute('aria-activedescendant', items[orgActive].id);
        } else {
            orgInput.removeAttribute('aria-activedescendant');
        }
    }

    if (orgInput) {
        orgInput.addEventListener('input', function() {
            showSuggestions(orgInput.value);
        });

        orgInput.addEventListener('keydown', function(e) {
            var items = orgList ? Array.from(orgList.querySelectorAll('.fot-suggestion-item')) : [];
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                orgActive = Math.min(orgActive + 1, items.length - 1);
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                orgActive = Math.max(orgActive - 1, -1);
                updateActiveItem(items);
            } else if (e.key === 'Enter' && orgActive >= 0) {
                e.preventDefault();
                selectSuggestion(items[orgActive].dataset.value);
            } else if (e.key === 'Escape') {
                hideSuggestions();
            }
        });

        orgInput.addEventListener('blur', function() {
            // Délai pour laisser le click sur un item se déclencher
            setTimeout(hideSuggestions, 150);
        });
    }

    if (orgList) {
        orgList.addEventListener('mousedown', function(e) {
            var item = e.target.closest('.fot-suggestion-item');
            if (item) selectSuggestion(item.dataset.value);
        });
    }

    /* ─────────────────────────────────────────────────────
       INIT PROGRESS
    ───────────────────────────────────────────────────── */
    updateProgress(1);

})();
