/* =======================================================
   Header PRH68 — burger menu mobile + floating header
   ======================================================= */
(function () {

    /* ── Floating header ────────────────────────────────── */
    var header    = document.getElementById('prh-header');
    var adminBar  = document.getElementById('wpadminbar');
    var threshold = 80;
    if (header) {
        function updateFloat() {
            header.classList.toggle('is-floating', window.scrollY > threshold);
        }
        window.addEventListener('scroll', updateFloat, { passive: true });
        updateFloat();
    }

    /* ── Burger menu mobile ─────────────────────────────── */
    var burger    = document.querySelector('.prh-burger');
    var mobileNav = document.getElementById('prh-mobile-nav');
    if (!burger || !mobileNav) return;

    burger.addEventListener('click', function () {
        var open = burger.getAttribute('aria-expanded') === 'true';
        burger.setAttribute('aria-expanded', String(!open));
        burger.classList.toggle('is-open', !open);
        mobileNav.classList.toggle('is-open', !open);
        mobileNav.setAttribute('aria-hidden', String(open));
    });

    mobileNav.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
            burger.setAttribute('aria-expanded', 'false');
            burger.classList.remove('is-open');
            mobileNav.classList.remove('is-open');
            mobileNav.setAttribute('aria-hidden', 'true');
        });
    });

})();
