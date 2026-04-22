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

    /* --- Accordéons services --- */
    PRH68.initAccordions( '.acc-service-card', '.acc-service-body' );

})();
