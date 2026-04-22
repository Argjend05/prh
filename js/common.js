/* =======================================================
   PRH68 — Utilitaires communs
   ======================================================= */

const PRH68 = window.PRH68 || {};

/**
 * Initialise les accordéons <details> avec animation WAAPI (Web Animations API).
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

            if (animation) { animation.cancel(); animation = null; }

            if (details.open) {
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
                details.setAttribute('open', '');
                const to  = body.scrollHeight + 'px';
                animation = body.animate(
                    [{ height: '0px' }, { height: to }],
                    { duration: 350, easing: 'ease' }
                );
                summary.setAttribute('aria-expanded', 'true');
                animation.onfinish = () => {
                    body.style.height = 'auto';
                    animation = null;
                };
            }
        });
    });
};

/**
 * Initialise le tooltip de définition "inclusion".
 * Fonctionne sur toutes les pages qui ont .acc-def-trigger + #acc-def-inclusion.
 */
PRH68.initTooltip = () => {
    const defTrigger = document.querySelector('.acc-def-trigger');
    const defBox     = document.getElementById('acc-def-inclusion');
    if (!defTrigger || !defBox) return;

    function positionDefBox() {
        const rect  = defTrigger.getBoundingClientRect();
        const boxW  = defBox.offsetWidth || 320;
        const boxH  = defBox.offsetHeight || 160;
        const viewW = window.innerWidth;
        let   left  = rect.left + rect.width / 2 - boxW / 2;
        left = Math.max(16, Math.min(left, viewW - boxW - 16));
        defBox.style.left = left + 'px';
        if (rect.top >= boxH + 20) {
            defBox.style.top = (rect.top + window.scrollY - boxH - 12) + 'px';
            defBox.classList.remove('acc-def-below');
        } else {
            defBox.style.top = (rect.bottom + window.scrollY + 12) + 'px';
            defBox.classList.add('acc-def-below');
        }
        defBox.style.setProperty('--arrow-left', (rect.left + rect.width / 2 - left) + 'px');
    }

    defTrigger.addEventListener('click', e => {
        e.stopPropagation();
        const isOpen = defBox.classList.contains('is-open');
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

    defTrigger.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); defTrigger.click(); }
    });

    document.addEventListener('click', () => {
        defBox.classList.remove('is-open');
        defTrigger.setAttribute('aria-expanded', 'false');
        defBox.setAttribute('aria-hidden', 'true');
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            defBox.classList.remove('is-open');
            defTrigger.setAttribute('aria-expanded', 'false');
            defBox.setAttribute('aria-hidden', 'true');
        }
    });
};

PRH68.initTooltip();
