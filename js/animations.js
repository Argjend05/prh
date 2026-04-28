
document.addEventListener('DOMContentLoaded', () => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* =======================================================
       5. BOUTON RETOUR EN HAUT
       ======================================================= */
    const btn = document.createElement('button');
    btn.id = 'prh-back-to-top';
    btn.setAttribute('aria-label', 'Retour en haut');
    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><polyline points="18 15 12 9 6 15"/></svg>';
    document.body.appendChild(btn);
    window.addEventListener('scroll', () => {
        btn.classList.toggle('is-visible', window.scrollY > 400);
    }, { passive: true });
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: prefersReducedMotion ? 'auto' : 'smooth' }));

    /* =======================================================
       6. BARRE DE PROGRESSION
       ======================================================= */
    const bar = document.createElement('div');
    bar.id = 'prh-progress-bar';
    document.body.prepend(bar);
    window.addEventListener('scroll', () => {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
    }, { passive: true });

    /* =======================================================
       7. SCROLL SPY (liens d'ancrage dans la nav)
       ======================================================= */
    const navAnchors = document.querySelectorAll('.prh-nav-list a[href^="#"], .prh-mobile-nav-list a[href^="#"]');
    if (navAnchors.length) {
        const spySections = [];
        navAnchors.forEach(a => {
            const id = a.getAttribute('href').slice(1);
            const sec = document.getElementById(id);
            if (sec) spySections.push({ a, sec });
        });
        if (spySections.length) {
            const spyObserver = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    spySections.forEach(({ a }) => a.classList.remove('spy-active'));
                    const match = spySections.find(s => s.sec === entry.target);
                    if (match) match.a.classList.add('spy-active');
                });
            }, { rootMargin: '-35% 0px -60% 0px' });
            spySections.forEach(({ sec }) => spyObserver.observe(sec));
        }
    }

    if (prefersReducedMotion) return;

    /* =======================================================
       1. APPARITION AU DEFILEMENT
       ======================================================= */
    const revealElements = document.querySelectorAll('[data-reveal]');
    
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const delay = el.getAttribute('data-reveal-delay') || 0;
                
                setTimeout(() => {
                    el.classList.add('reveal-visible');
                }, delay);
                
                observer.unobserve(el);
            }
        });
    }, {
        root: null,
        rootMargin: '0px 0px -10% 0px',
        threshold: 0.1
    });

    revealElements.forEach(el => revealObserver.observe(el));

    /* =======================================================
       2. COMPTEURS DYNAMIQUES
       ======================================================= */
    const counterElements = document.querySelectorAll('[data-counter]');
    
    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-counter'), 10);
                const duration = 2000;
                let startTimestamp = null;
                
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const easeProgress = 1 - Math.pow(1 - progress, 4);
                    el.innerText = Math.floor(easeProgress * target);
                    
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        el.innerText = target;
                    }
                };
                
                window.requestAnimationFrame(step);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    
    counterElements.forEach(el => counterObserver.observe(el));

    /* =======================================================
       3. SURVOL 3D ET BOUTONS MAGNETIQUES
       ======================================================= */
    if (window.matchMedia("(pointer: fine)").matches) {
        
        const tiltElements = document.querySelectorAll('[data-tilt]');
        
        tiltElements.forEach(el => {
            let rect, centerX, centerY, rafId;

            el.addEventListener('mouseenter', () => {
                rect = el.getBoundingClientRect();
                centerX = rect.width / 2;
                centerY = rect.height / 2;
                el.style.transition = 'transform 0.1s ease-out';
            });

            el.addEventListener('mousemove', (e) => {
                if (!rect) return;
                if (rafId) return;
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const isOpen = el.open || el.closest('details[open]');
                const maxTilt = isOpen ? 1.5 : 8;
                const tiltX = ((y - centerY) / centerY) * -maxTilt;
                const tiltY = ((x - centerX) / centerX) * maxTilt;

                rafId = window.requestAnimationFrame(() => {
                    el.style.transform = `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale3d(1.02, 1.02, 1.02)`;
                    rafId = null;
                });
            });

            el.addEventListener('mouseleave', () => {
                rect = null;
                el.style.transition = 'transform 0.5s ease';
                window.requestAnimationFrame(() => {
                    el.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
                });
            });
        });

        const magneticElements = document.querySelectorAll('[data-magnetic]');
        
        magneticElements.forEach(el => {
            let rect, rafId;

            el.addEventListener('mouseenter', () => {
                rect = el.getBoundingClientRect();
                el.style.transition = 'transform 0.1s ease-out';
            });

            el.addEventListener('mousemove', (e) => {
                if (!rect || rafId) return;
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;

                rafId = window.requestAnimationFrame(() => {
                    el.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
                    rafId = null;
                });
            });

            el.addEventListener('mouseleave', () => {
                rect = null;
                el.style.transition = 'transform 0.5s ease-out';
                window.requestAnimationFrame(() => {
                    el.style.transform = 'translate(0px, 0px)';
                });
            });
        });
    }

    /* =======================================================
       4. EFFET PARALLAXE
       ======================================================= */
    const parallaxElements = document.querySelectorAll('[data-parallax]');
    
    if (parallaxElements.length > 0) {
        let ticking = false;
        let lastScrollY = window.scrollY;
        
        parallaxElements.forEach(el => {
            const speed = parseFloat(el.getAttribute('data-parallax')) || 0.2;
            el.style.transform = `translateY(${lastScrollY * speed}px)`;
        });
        
        window.addEventListener('scroll', () => {
            lastScrollY = window.scrollY;
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    parallaxElements.forEach(el => {
                        const speed = parseFloat(el.getAttribute('data-parallax')) || 0.2;
                        el.style.transform = `translateY(${lastScrollY * speed}px)`;
                    });
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }
});
