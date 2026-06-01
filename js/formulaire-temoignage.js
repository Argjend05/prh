(function () {
    'use strict';

    function init() {
        var form = document.querySelector('.ftem-form');
        if (!form) return;

        /* ──────────────────────────────────────────────────
           CHAMPS CONDITIONNELS (audio / vidéo)
        ────────────────────────────────────────────────── */
        var typeRadios = form.querySelectorAll('input[name="ftem_type"]');
        var condBlocks = form.querySelectorAll('.ftem-cond[data-show-if-type]');

        function updateConditional() {
            var current = form.querySelector('input[name="ftem_type"]:checked');
            var val = current ? current.value : 'texte';
            condBlocks.forEach(function (block) {
                var match = block.dataset.showIfType === val;
                block.classList.toggle('is-active', match);
                block.querySelectorAll('input').forEach(function (inp) {
                    inp.disabled = !match;
                });
            });
        }
        typeRadios.forEach(function (r) {
            r.addEventListener('change', updateConditional);
        });
        updateConditional();

        /* ──────────────────────────────────────────────────
           COMPTEUR DE CARACTÈRES
        ────────────────────────────────────────────────── */
        var quote   = form.querySelector('#ftem_quote');
        var counter = form.querySelector('.ftem-counter-num');
        if (quote && counter) {
            function updateCounter() {
                var n = quote.value.length;
                counter.textContent = n;
                counter.classList.toggle('is-warn', n > 3600);
            }
            quote.addEventListener('input', updateCounter);
            updateCounter();
        }

        /* ──────────────────────────────────────────────────
           NOM DE FICHIER AUDIO
        ────────────────────────────────────────────────── */
        var fileInput = form.querySelector('#ftem_audio_file');
        var fileName  = form.querySelector('.ftem-file-name');
        if (fileInput && fileName) {
            fileInput.addEventListener('change', function () {
                if (fileInput.files && fileInput.files.length) {
                    var f      = fileInput.files[0];
                    var sizeMb = (f.size / (1024 * 1024)).toFixed(1);
                    fileName.textContent = f.name + ' (' + sizeMb + ' Mo)';
                    if (f.size > 25 * 1024 * 1024) {
                        fileName.textContent += ' — TROP LOURD';
                        fileName.style.color = 'var(--orange)';
                    } else {
                        fileName.style.color = '';
                    }
                } else {
                    fileName.textContent = '';
                }
            });
        }

        /* ──────────────────────────────────────────────────
           ERREURS / SUCCÈS
        ────────────────────────────────────────────────── */
        var errorZone    = document.querySelector('.ftem-errors');
        var successPanel = document.querySelector('.ftem-success');

        function showErrors(msgs) {
            if (!errorZone) return;
            var html = '<strong>Quelques points à corriger&nbsp;:</strong><ul>';
            msgs.forEach(function (m) { html += '<li>' + m + '</li>'; });
            html += '</ul>';
            errorZone.innerHTML = html;
            errorZone.hidden = false;
            errorZone.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideErrors() {
            if (errorZone) { errorZone.hidden = true; errorZone.innerHTML = ''; }
        }

        /* ──────────────────────────────────────────────────
           ENVOI AJAX
        ────────────────────────────────────────────────── */
        var submitting  = false;
        var submitBtn   = form.querySelector('.ftem-submit');
        var originalHtml = submitBtn ? submitBtn.innerHTML : '';

        function resetBtn() {
            if (!submitBtn) return;
            submitBtn.disabled   = false;
            submitBtn.style.opacity = '';
            submitBtn.style.cursor  = '';
            submitBtn.innerHTML  = originalHtml;
        }

        function lockBtn() {
            if (!submitBtn) return;
            submitBtn.disabled      = true;
            submitBtn.style.opacity = '.6';
            submitBtn.style.cursor  = 'wait';
            submitBtn.innerHTML = originalHtml.replace('Envoyer mon témoignage', 'Envoi en cours…');
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (submitting) return;
            submitting = true;
            hideErrors();
            lockBtn();

            var formData = new FormData(form);
            formData.append('action', 'ftem_submit');

            fetch('/wp-admin/admin-ajax.php', { method: 'POST', body: formData })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        form.hidden = true;
                        if (successPanel) {
                            successPanel.hidden = false;
                            successPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    } else {
                        var msgs = (data.data && data.data.errors)
                            ? data.data.errors
                            : [(data.data && data.data.message) ? data.data.message : "Une erreur est survenue."];
                        showErrors(msgs);
                        submitting = false;
                        resetBtn();
                    }
                })
                .catch(function () {
                    showErrors(["Erreur de connexion. Merci de réessayer."]);
                    submitting = false;
                    resetBtn();
                });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
