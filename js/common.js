/* =======================================================
   PRH68 — Utilitaires communs
   ======================================================= */

var PRH68 = window.PRH68 || {};

/**
 * Initialise les accordéons <details> avec animation max-height fluide.
 *
 * @param {string} cardSelector  Sélecteur des éléments <details>
 * @param {string} bodySelector  Sélecteur du corps animé à l'intérieur
 */
PRH68.initAccordions = function ( cardSelector, bodySelector ) {
    document.querySelectorAll( cardSelector ).forEach( function ( details ) {
        var summary = details.querySelector( 'summary' );
        var body    = details.querySelector( bodySelector );

        if ( ! summary || ! body ) return;

        /* Garantit que le corps est visible même si Neve le masque */
        body.style.display = 'block';

        summary.addEventListener( 'click', function ( e ) {
            e.preventDefault();

            if ( details.open ) {
                /* ── Fermeture ── */
                body.style.maxHeight = body.scrollHeight + 'px';
                body.offsetHeight; /* force reflow pour déclencher la transition */
                body.style.maxHeight = '0';

                body.addEventListener( 'transitionend', function close( e ) {
                    if ( e.propertyName !== 'max-height' ) return;
                    details.removeAttribute( 'open' );
                    body.style.maxHeight = '';
                    body.removeEventListener( 'transitionend', close );
                } );

            } else {
                /* ── Ouverture ── */
                details.setAttribute( 'open', '' );
                body.style.maxHeight = '';
                var h = body.scrollHeight;
                body.style.maxHeight = '0';
                body.offsetHeight; /* force reflow */
                body.style.maxHeight = h + 'px';

                body.addEventListener( 'transitionend', function open( e ) {
                    if ( e.propertyName !== 'max-height' ) return;
                    body.style.maxHeight = 'none'; /* permet au contenu de s'adapter librement */
                    body.removeEventListener( 'transitionend', open );
                } );
            }
        } );
    } );
};
