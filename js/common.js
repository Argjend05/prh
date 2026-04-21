/* =======================================================
   Scripts Communs
   ======================================================= */

(function () {

    /**
     * Initialise un système d'accordéons pour des éléments <details>
     * @param {string} cardSelector Sélecteur pour les <details>
     * @param {string} bodySelector Sélecteur pour le contenu rétractable
     */
    window.initAccordion = function(cardSelector, bodySelector) {
        document.querySelectorAll(cardSelector).forEach(function (details) {
            let summary = details.querySelector('summary');
            let body    = details.querySelector(bodySelector);

            if ( ! summary || ! body ) return;

            /* Forcer display:block au cas où CSS masquerait le contenu */
            body.style.display = 'block';

            summary.addEventListener('click', function (e) {
                e.preventDefault();

                if (details.open) {
                    /* Fermeture */
                    body.style.maxHeight = body.scrollHeight + 'px';
                    body.offsetHeight; // Forcer le reflow du navigateur pour l'animation
                    body.style.maxHeight = '0';
                    
                    body.addEventListener('transitionend', function close(e) {
                        if (e.propertyName === 'max-height') {
                            details.removeAttribute('open');
                            body.style.maxHeight = '';
                            body.removeEventListener('transitionend', close);
                        }
                    });
                } else {
                    /* Ouverture */
                    details.setAttribute('open', '');
                    body.style.maxHeight = ''; // reset éventuel
                    let h = body.scrollHeight;
                    body.style.maxHeight = '0';
                    body.offsetHeight; // Forcer le reflow du navigateur
                    body.style.maxHeight = h + 'px';
                    
                    body.addEventListener('transitionend', function open(e) {
                        if (e.propertyName === 'max-height') {
                            body.style.maxHeight = 'none'; // Permet au texte de changer de taille ou s'adapter à l'écran
                            body.removeEventListener('transitionend', open);
                        }
                    });
                }
            });
        });
    };

})();
