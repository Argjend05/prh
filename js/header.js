/* =======================================================
   Header PRH68 — burger menu mobile
   ======================================================= */
(function () {
    let burger = document.querySelector('.prh-burger');
    let mobileNav = document.getElementById('prh-mobile-nav');
    if (!burger || !mobileNav) return;

    burger.addEventListener('click', function () {
        let open = burger.getAttribute('aria-expanded') === 'true';
        burger.setAttribute('aria-expanded', String(!open));
        burger.classList.toggle('is-open', !open);
        mobileNav.classList.toggle('is-open', !open);
        mobileNav.setAttribute('aria-hidden', String(open));
    });

    /* Ferme au clic sur un lien mobile */
    mobileNav.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
            burger.setAttribute('aria-expanded', 'false');
            burger.classList.remove('is-open');
            mobileNav.classList.remove('is-open');
            mobileNav.setAttribute('aria-hidden', 'true');
        });
    });
})();
