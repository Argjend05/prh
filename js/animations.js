document.addEventListener('DOMContentLoaded', () => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
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
            let rect, centerX, centerY;
            
            el.addEventListener('mouseenter', () => {
                rect = el.getBoundingClientRect();
                centerX = rect.width / 2;
                centerY = rect.height / 2;
            });

            el.addEventListener('mousemove', (e) => {
                if (!rect) return;
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const maxTilt = 8;
                const tiltX = ((y - centerY) / centerY) * -maxTilt;
                const tiltY = ((x - centerX) / centerX) * maxTilt;
                
                window.requestAnimationFrame(() => {
                    el.style.transform = `perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale3d(1.02, 1.02, 1.02)`;
                    el.style.transition = 'transform 0.1s ease-out';
                });
            });
            
            el.addEventListener('mouseleave', () => {
                rect = null;
                window.requestAnimationFrame(() => {
                    el.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
                    el.style.transition = 'transform 0.5s ease';
                });
            });
        });

        const magneticElements = document.querySelectorAll('[data-magnetic]');
        
        magneticElements.forEach(el => {
            let rect;
            
            el.addEventListener('mouseenter', () => {
                rect = el.getBoundingClientRect();
            });

            el.addEventListener('mousemove', (e) => {
                if (!rect) return;
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                window.requestAnimationFrame(() => {
                    el.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
                    el.style.transition = 'transform 0.1s ease-out';
                });
            });
            
            el.addEventListener('mouseleave', () => {
                rect = null;
                window.requestAnimationFrame(() => {
                    el.style.transform = 'translate(0px, 0px)';
                    el.style.transition = 'transform 0.5s ease-out';
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
