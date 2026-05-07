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
                // Désactive les inputs des blocs cachés pour éviter envoi
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
        var quote = form.querySelector('#ftem_quote');
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
        var fileName = form.querySelector('.ftem-file-name');
        if (fileInput && fileName) {
            fileInput.addEventListener('change', function () {
                if (fileInput.files && fileInput.files.length) {
                    var f = fileInput.files[0];
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
           PROTECTION DOUBLE SUBMIT
        ────────────────────────────────────────────────── */
        var submitBtn = form.querySelector('.ftem-submit');
        form.addEventListener('submit', function () {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '.6';
                submitBtn.style.cursor = 'wait';
                var original = submitBtn.innerHTML;
                submitBtn.innerHTML = original.replace('Envoyer mon témoignage', 'Envoi en cours…');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
