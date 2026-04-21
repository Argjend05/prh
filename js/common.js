/* =======================================================
   PRH68 — Utilitaires communs
   ======================================================= */

const PRH68 = window.PRH68 || {};

/**
 * Initialise les accordéons <details> avec animation WAAPI (Web Animations API).
 * Aucun reflow manuel — l'animation est gérée nativement par le navigateur.
 *
 * @param {string} cardSelector  Sélecteur des éléments <details>
 * @param {string} bodySelector  Sélecteur du corps animé à l'intérieur
 */
PRH68.initAccordions = (cardSelector, bodySelector) => {
    document.querySelectorAll(cardSelector).forEach(details => {
        const summary = details.querySelector('summary');
        const body    = details.querySelector(bodySelector);
        if (!summary || !body) return;

        let animation = null;

        /* État initial */
        body.style.overflow = 'hidden';
        if (!details.open) body.style.height = '0px';
        summary.setAttribute('aria-expanded', String(details.open));

        summary.addEventListener('click', e => {
            e.preventDefault();

            /* Annule l'animation en cours si l'utilisateur reclique rapidement */
            if (animation) { animation.cancel(); animation = null; }

            if (details.open) {
                /* ── Fermeture ── */
                const from = body.offsetHeight + 'px';
                animation  = body.animate(
                    [{ height: from }, { height: '0px' }],
                    { duration: 350, easing: 'ease' }
                );
                summary.setAttribute('aria-expanded', 'false');
                animation.onfinish = () => {
                    details.removeAttribute('open');
                    body.style.height = '0px';
                    animation = null;
                };

            } else {
                /* ── Ouverture ── */
                details.setAttribute('open', '');
                const to  = body.scrollHeight + 'px';
                animation = body.animate(
                    [{ height: '0px' }, { height: to }],
                    { duration: 350, easing: 'ease' }
                );
                summary.setAttribute('aria-expanded', 'true');
                animation.onfinish = () => {
                    body.style.height = 'auto'; /* permet au contenu de s'adapter librement */
                    animation = null;
                };
            }
        });
    });
};
