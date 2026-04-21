/* =======================================================
   Page Accueil — interactions JS
   ======================================================= */

(function () {

    /* --- Fermeture barre d'annonce --- */
    let bar = document.getElementById('acc-announce');
    let btn = bar && bar.querySelector('.acc-announce-close');
    if (btn) {
        btn.addEventListener('click', function () {
            bar.style.transition = 'max-height .3s ease, padding .3s ease, opacity .3s ease';
            bar.style.maxHeight  = bar.offsetHeight + 'px';
            bar.style.overflow   = 'hidden';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    bar.style.maxHeight = '0';
                    bar.style.padding   = '0';
                    bar.style.opacity   = '0';
                });
            });
            bar.addEventListener('transitionend', function () { bar.remove(); }, { once: true });
        });
    }

    /* --- Tooltip définition "inclusion" --- */
    let defTrigger = document.querySelector('.acc-def-trigger');
    let defBox     = document.getElementById('acc-def-inclusion');
    if (defTrigger && defBox) {

        function positionDefBox() {
            let rect  = defTrigger.getBoundingClientRect();
            let boxW  = defBox.offsetWidth || 320;
            let boxH  = defBox.offsetHeight || 160;
            let viewW = window.innerWidth;
            let left  = rect.left + rect.width / 2 - boxW / 2;
            // Garde dans la fenêtre horizontalement
            left = Math.max(16, Math.min(left, viewW - boxW - 16));
            defBox.style.left = left + 'px';
            // Au-dessus par défaut, en dessous seulement si pas assez de place
            let spaceAbove = rect.top;
            if (spaceAbove >= boxH + 20) {
                defBox.style.top = (rect.top + window.scrollY - boxH - 12) + 'px';
                defBox.classList.remove('acc-def-below');
            } else {
                defBox.style.top = (rect.bottom + window.scrollY + 12) + 'px';
                defBox.classList.add('acc-def-below');
            }
            // Ajuste la flèche
            let arrowLeft = rect.left + rect.width / 2 - left;
            defBox.style.setProperty('--arrow-left', arrowLeft + 'px');
        }

        defTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            let isOpen = defBox.classList.contains('is-open');
            if (isOpen) {
                defBox.classList.remove('is-open');
                defTrigger.setAttribute('aria-expanded', 'false');
                defBox.setAttribute('aria-hidden', 'true');
            } else {
                positionDefBox();
                defBox.classList.add('is-open');
                defTrigger.setAttribute('aria-expanded', 'true');
                defBox.setAttribute('aria-hidden', 'false');
            }
        });

        defTrigger.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); defTrigger.click(); }
        });

        document.addEventListener('click', function () {
            defBox.classList.remove('is-open');
            defTrigger.setAttribute('aria-expanded', 'false');
            defBox.setAttribute('aria-hidden', 'true');
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                defBox.classList.remove('is-open');
                defTrigger.setAttribute('aria-expanded', 'false');
                defBox.setAttribute('aria-hidden', 'true');
            }
        });
    }

    /* --- Accordéons services (animation max-height) --- */
    if (typeof window.initAccordion === 'function') {
        window.initAccordion('.acc-service-card', '.acc-service-body');
    }

})();
