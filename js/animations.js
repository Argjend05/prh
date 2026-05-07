(function () {
    function init() {

        /* ── RETOUR EN HAUT ───────────────────────────────────────────────── */
        const topBtn = document.createElement('button');
        topBtn.id = 'prh-back-to-top';
        topBtn.setAttribute('aria-label', 'Retour en haut');
        topBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><polyline points="18 15 12 9 6 15"/></svg>';
        document.body.appendChild(topBtn);
        window.addEventListener('scroll', () => {
            topBtn.classList.toggle('is-visible', window.scrollY > 400);
        }, { passive: true });
        topBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        /* ── BARRE DE PROGRESSION ─────────────────────────────────────────── */
        const progressBar = document.createElement('div');
        progressBar.id = 'prh-progress-bar';
        document.body.prepend(progressBar);
        window.addEventListener('scroll', () => {
            const max = document.documentElement.scrollHeight - window.innerHeight;
            progressBar.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
        }, { passive: true });

        /* ── SCROLL SPY ───────────────────────────────────────────────────── */
        const navAnchors = document.querySelectorAll(
            '.prh-nav-list a[href^="#"], .prh-mobile-nav-list a[href^="#"]'
        );
        if (navAnchors.length) {
            const spySections = [];
            navAnchors.forEach(a => {
                const sec = document.getElementById(a.getAttribute('href').slice(1));
                if (sec) spySections.push({ a, sec });
            });
            if (spySections.length) {
                const spyObs = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting) return;
                        spySections.forEach(({ a }) => a.classList.remove('spy-active'));
                        const match = spySections.find(s => s.sec === entry.target);
                        if (match) match.a.classList.add('spy-active');
                    });
                }, { rootMargin: '-35% 0px -60% 0px' });
                spySections.forEach(({ sec }) => spyObs.observe(sec));
            }
        }

        /* ── GSAP ─────────────────────────────────────────────────────────── */
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (typeof gsap === 'undefined') return;

        gsap.registerPlugin(ScrollTrigger);

        /* ── CSS utilitaire pour le split de mots ─────────────────────────── */
        const splitStyle = document.createElement('style');
        splitStyle.textContent =
            '.prh-word{display:inline-block;overflow:hidden;vertical-align:bottom;line-height:1.15;}' +
            '.prh-word-inner{display:inline-block;}';
        document.head.appendChild(splitStyle);

        /* ── Helper : découpe un élément texte mot par mot ─────────────────── */
        function wordSplit(el) {
            if (!el || el.children.length) return null;
            const words = el.textContent.trim().split(/\s+/);
            el.innerHTML = words.map(w =>
                `<span class="prh-word"><span class="prh-word-inner">${w}</span></span>`
            ).join(' ');
            return [...el.querySelectorAll('.prh-word-inner')];
        }

        const ST = { start: 'top 88%', once: true };

        /* Éléments gérés collectivement — pas d'animation individuelle */
        const managed = new Set();

        /* ── 1. TITRES DE SECTION — trait qui se dessine, mots qui surgissent ─ */
        function animateSectionTitle(titleEl) {
            managed.add(titleEl);

            /* Subtitle éventuel juste après */
            const next = titleEl.nextElementSibling;
            const subtitle = (next && (next.tagName === 'P' || next.dataset.reveal)) ? next : null;
            if (subtitle) managed.add(subtitle);

            const wordSpans = wordSplit(titleEl);

            const tl = gsap.timeline({
                scrollTrigger: { trigger: titleEl, start: 'top 85%', once: true },
            });

            /* Le trait part du centre vers les deux bords, avec rebond élastique */
            tl.to(titleEl, {
                '--prh-line': 1,
                duration: 0.5,
                ease: 'back.out(2)',
            });

            /* Les mots émergent depuis en-dessous le trait */
            if (wordSpans) {
                tl.from(wordSpans, {
                    y: '120%',
                    duration: 0.65,
                    stagger: 0.055,
                    ease: 'power3.out',
                    clearProps: 'all',
                }, '-=0.25');
            } else {
                tl.from(titleEl, {
                    opacity: 0, y: 32,
                    duration: 0.7, ease: 'power3.out', clearProps: 'all',
                }, '-=0.25');
            }

            /* Subtitle glisse doucement après */
            if (subtitle) {
                tl.from(subtitle, {
                    opacity: 0, y: 18,
                    duration: 0.6, ease: 'power2.out', clearProps: 'all',
                }, '-=0.15');
            }
        }

        document.querySelectorAll('.acc-section-title, .obs-section-title, .pro-section-title')
            .forEach(animateSectionTitle);

        /* ── 3. ENTRÉE HERO ───────────────────────────────────────────────── */
        const heroContent = document.querySelector(
            '.acc-hero-content, .obs-hero-content, .pro-hero-content'
        );
        if (heroContent) {
            [...heroContent.querySelectorAll('[data-reveal]')].forEach(el => managed.add(el));

            const tl = gsap.timeline({ defaults: { ease: 'power4.out', clearProps: 'all' } });

            const h1 = heroContent.querySelector('h1');
            const wordSpans = wordSplit(h1);
            if (wordSpans) {
                tl.from(wordSpans, { y: '115%', duration: 0.7, stagger: 0.06 });
            } else if (h1) {
                tl.from(h1, { opacity: 0, y: 40, duration: 0.8 });
            }

            const subtitle = heroContent.querySelector('p');
            if (subtitle) {
                tl.from(subtitle, { opacity: 0, y: 24, duration: 0.7, ease: 'power3.out' }, '-=0.35');
            }

            const btns = [...heroContent.querySelectorAll('a, button')];
            if (btns.length) {
                tl.from(btns, {
                    opacity: 0, scale: 0.75, y: 12,
                    duration: 0.55, stagger: 0.08,
                    ease: 'back.out(1.8)',
                }, '-=0.25');
            }

            /* Image hero — s'ouvre depuis le coin supérieur droit */
            const heroImg = document.querySelector('.acc-hero-img');
            if (heroImg) {
                gsap.set(heroImg, { clipPath: 'circle(0% at 100% 0%)' });
                tl.to(heroImg, {
                    clipPath: 'circle(150% at 100% 0%)',
                    opacity: 1,
                    duration: 1.5,
                    ease: 'power2.out',
                    clearProps: 'clipPath',
                }, 0);
            }
        }

        /* ── 2. PARALLAXE HERO MULTI-COUCHES ──────────────────────────────── */
        const heroSection = document.querySelector('.acc-hero, .obs-hero, .pro-hero');
        if (heroSection) {
            const parallaxMap = [
                ['.acc-blob-1, .pro-blob-1',  -22, 15],
                ['.acc-blob-2, .pro-blob-2',  -12, 0],
                ['.acc-blob-3',               -30, -10],
                ['.acc-ring-1',               -18, 20],
                ['.acc-ring-2',               -30, -12],
                ['.acc-ring-3',               -10, 8],
                ['.acc-fcard-1',              -25, 0],
                ['.acc-fcard-2',              -15, 0],
                ['.acc-fcard-3',              -35, 0],
                ['.acc-fpill-1',              -20, 0],
                ['.acc-fpill-2',              -28, 0],
            ];
            parallaxMap.forEach(([sel, yPct, rotate]) => {
                const els = heroSection.querySelectorAll(sel);
                if (!els.length) return;
                gsap.to(els, {
                    yPercent: yPct,
                    rotation: rotate || 0,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: heroSection,
                        start: 'top top',
                        end: 'bottom top',
                        scrub: 2,
                    },
                });
            });
        }

        /* ── Helper : init immédiate + animation vers état naturel ──────────── */
        function batchReveal(items, fromVars, batchVars = {}) {
            gsap.set(items, fromVars);
            ScrollTrigger.batch(items, {
                onEnter: batch => gsap.to(batch, {
                    opacity: 1, y: 0, x: 0, scale: 1, rotation: 0,
                    clearProps: 'all',
                    ...batchVars,
                }),
                ...ST,
            });
        }

        /* ── 3. CHIFFRES CLÉS — cascade depuis le centre + rebond ─────────── */
        const stats = [...document.querySelectorAll('.acc-stats-grid .acc-stat')];
        if (stats.length) {
            stats.forEach(el => managed.add(el));
            gsap.set(stats, { opacity: 0, y: 55, scale: 0.7 });
            ScrollTrigger.batch(stats, {
                onEnter: batch => gsap.to(batch, {
                    opacity: 1, y: 0, scale: 1, rotation: 0,
                    duration: 0.7,
                    stagger: { each: 0.15, from: 'center' },
                    ease: 'back.out(1.8)',
                    clearProps: 'all',
                }),
                ...ST,
            });
        }

        /* ── 4. ICÔNES PROFESSIONNELS — pop en vague ──────────────────────── */
        const proItems = [...document.querySelectorAll('.acc-pro-items .acc-pro-item')];
        if (proItems.length) {
            proItems.forEach(el => managed.add(el));
            gsap.set(proItems, { opacity: 0, scale: 0, rotation: -20, y: 30 });
            ScrollTrigger.batch(proItems, {
                onEnter: batch => gsap.to(batch, {
                    opacity: 1, scale: 1, rotation: 0, y: 0,
                    duration: 0.5,
                    stagger: { each: 0.07, from: 'start' },
                    ease: 'back.out(2.5)',
                    clearProps: 'all',
                }),
                ...ST,
            });
        }

        /* ── 5. CARTES INFO / MISSION — fan qui se referme ───────────────── */
        [
            ['.acc-info-grid',       '.acc-info-card'],
            ['.obs-mission-grid',    '.obs-mission-card'],
            ['.obs-participez-grid', '.obs-participez-card'],
            ['.pro-why-grid',        '.pro-why-card'],
        ].forEach(([gridSel, itemSel]) => {
            const items = [...document.querySelectorAll(`${gridSel} ${itemSel}`)];
            if (!items.length) return;
            items.forEach(el => managed.add(el));
            /* Rotations initiales alternées : fan qui se referme */
            items.forEach((el, i) => {
                gsap.set(el, { opacity: 0, y: 70, scale: 0.88, rotation: [-4, 0, 4, -2][i % 4] });
            });
            ScrollTrigger.batch(items, {
                onEnter: batch => gsap.to(batch, {
                    opacity: 1, y: 0, scale: 1, rotation: 0,
                    duration: 0.8,
                    stagger: 0.12,
                    ease: 'power3.out',
                    clearProps: 'all',
                }),
                ...ST,
            });
        });

        /* ── 6. CYCLE OBSERVATOIRE — séquentiel gauche→droite ────────────── */
        const cycleCards = [...document.querySelectorAll('.obs-cycle-grid .obs-cycle-card')];
        if (cycleCards.length) {
            cycleCards.forEach(el => managed.add(el));
            gsap.set(cycleCards, { opacity: 0, x: -50, scale: 0.9 });
            ScrollTrigger.batch(cycleCards, {
                onEnter: batch => gsap.to(batch, {
                    opacity: 1, x: 0, scale: 1,
                    duration: 0.65,
                    stagger: 0.18,
                    ease: 'power2.out',
                    clearProps: 'all',
                }),
                ...ST,
            });
            const arrows = [...document.querySelectorAll('.obs-cycle-arrow, .obs-cycle-sep')];
            if (arrows.length) {
                gsap.set(arrows, { opacity: 0, scaleX: 0 });
                ScrollTrigger.batch(arrows, {
                    onEnter: batch => gsap.to(batch, {
                        opacity: 1, scaleX: 1,
                        duration: 0.4, stagger: 0.2,
                        ease: 'power2.out', clearProps: 'all',
                    }),
                    ...ST,
                });
            }
        }

        /* ── 7. ACCORDÉONS — colonnes en sens opposés ─────────────────────── */
        const leftCol  = document.querySelector('.acc-services-col:first-child, .pro-nvp-col:first-child');
        const rightCol = document.querySelector('.acc-services-col:last-child,  .pro-nvp-col:last-child');

        if (leftCol && rightCol) {
            const leftCards  = [...leftCol.querySelectorAll('.acc-service-card, details.pro-acc-card')];
            const rightCards = [...rightCol.querySelectorAll('.acc-service-card, details.pro-acc-card')];
            [...leftCards, ...rightCards].forEach(el => managed.add(el));

            if (leftCards.length) {
                gsap.set(leftCards, { opacity: 0, x: -70 });
                gsap.to(leftCards, {
                    opacity: 1, x: 0, duration: 0.7, stagger: 0.15, ease: 'power2.out', clearProps: 'all',
                    scrollTrigger: { trigger: leftCol, ...ST },
                });
            }
            if (rightCards.length) {
                gsap.set(rightCards, { opacity: 0, x: 70 });
                gsap.to(rightCards, {
                    opacity: 1, x: 0, duration: 0.7, stagger: 0.15, ease: 'power2.out', clearProps: 'all',
                    scrollTrigger: { trigger: rightCol, ...ST },
                });
            }
        } else {
            const allAcc = [...document.querySelectorAll('.acc-service-card, details.pro-acc-card')];
            if (allAcc.length) {
                allAcc.forEach(el => managed.add(el));
                gsap.set(allAcc, { opacity: 0, y: 50 });
                ScrollTrigger.batch(allAcc, {
                    onEnter: batch => gsap.to(batch, {
                        opacity: 1, y: 0, duration: 0.65, stagger: 0.12, ease: 'power2.out', clearProps: 'all',
                    }),
                    ...ST,
                });
            }
        }

        /* ── 8a. PAGE ÉVÉNEMENTS ─────────────────────────────────────────── */
        const agendaHero = document.querySelector('.prh68-agenda-hero');
        if (agendaHero) {
            const h1    = agendaHero.querySelector('h1');
            const heroP = agendaHero.querySelector('p');
            const tl    = gsap.timeline({ defaults: { ease: 'power4.out', clearProps: 'all' } });

            const wordSpans = wordSplit(h1);
            if (wordSpans) {
                tl.from(wordSpans, { y: '115%', duration: 0.7, stagger: 0.06 });
            } else if (h1) {
                tl.from(h1, { opacity: 0, y: 40, duration: 0.8 });
            }
            if (heroP) {
                tl.from(heroP, { opacity: 0, y: 22, duration: 0.65, ease: 'power3.out' }, '-=0.35');
            }

            /* Tabs — glissent depuis le bas en décalé */
            const tabs = [...document.querySelectorAll('.prh68-tab-btn')];
            if (tabs.length) {
                gsap.set(tabs, { opacity: 0, y: 30, scale: 0.92 });
                gsap.to(tabs, {
                    opacity: 1, y: 0, scale: 1,
                    duration: 0.55, stagger: 0.12,
                    ease: 'back.out(1.8)', clearProps: 'all',
                    scrollTrigger: { trigger: tabs[0], ...ST },
                });
            }
        }

        /* Event cards — pop décalé à l'entrée dans le viewport */
        const eventCards = [...document.querySelectorAll('.prh68-event-card')];
        if (eventCards.length) {
            eventCards.forEach(el => managed.add(el));
            gsap.set(eventCards, { opacity: 0, y: 60, scale: 0.92 });
            ScrollTrigger.batch(eventCards, {
                onEnter: batch => gsap.to(batch, {
                    opacity: 1, y: 0, scale: 1,
                    duration: 0.65,
                    stagger: { each: 0.1, from: 'start' },
                    ease: 'power3.out', clearProps: 'all',
                }),
                ...ST,
            });
        }

        /* ── 8b. PAGE SACS PÉDAGOGIQUES ──────────────────────────────────── */
        const kitsContainer = document.querySelector('.prh68-kits-container');
        if (kitsContainer) {
            const kitsTitle    = kitsContainer.querySelector('.prh68-title');
            const kitsSubtitle = kitsContainer.querySelector('.prh68-subtitle');
            const kitsSearch   = kitsContainer.querySelector('.prh68-search-container');

            /* Titre — word split + glisse depuis le bas */
            const tlKits = gsap.timeline({ defaults: { ease: 'power4.out', clearProps: 'all' } });
            const kitsWords = wordSplit(kitsTitle);
            if (kitsWords) {
                tlKits.from(kitsWords, { y: '115%', duration: 0.7, stagger: 0.06 });
            } else if (kitsTitle) {
                tlKits.from(kitsTitle, { opacity: 0, y: 36, duration: 0.75 });
            }
            if (kitsSubtitle) {
                tlKits.from(kitsSubtitle, { opacity: 0, y: 20, duration: 0.6, ease: 'power3.out' }, '-=0.3');
            }
            if (kitsSearch) {
                tlKits.from(kitsSearch, { opacity: 0, y: 18, duration: 0.5, ease: 'power2.out' }, '-=0.2');
            }

            /* Kit cards — vague de pop depuis la gauche */
            const kitCards = [...kitsContainer.querySelectorAll('.prh68-kit-card')];
            if (kitCards.length) {
                kitCards.forEach(el => managed.add(el));
                kitCards.forEach((el, i) => {
                    gsap.set(el, { opacity: 0, y: 55, scale: 0.88, rotation: [-3, 0, 3, -1][i % 4] });
                });
                ScrollTrigger.batch(kitCards, {
                    onEnter: batch => gsap.to(batch, {
                        opacity: 1, y: 0, scale: 1, rotation: 0,
                        duration: 0.7,
                        stagger: { each: 0.08, from: 'start' },
                        ease: 'back.out(1.6)', clearProps: 'all',
                    }),
                    ...ST,
                });
            }
        }

        /* ── 8. RÉVÉLATION INDIVIDUELLE — titres avec atterrissage skewé ──── */
        const REVEAL_FROM = {
            'fade-up':    (el) => ({ opacity: 0, y: 50, skewX: 2 }),
            'text-soft':  (el) => ({ opacity: 0, y: 22 }),
            'fade-left':  (el) => ({ opacity: 0, x: 55, skewY: -1 }),
            'fade-right': (el) => ({ opacity: 0, x: -55, skewY: 1 }),
        };

        document.querySelectorAll('[data-reveal]').forEach(el => {
            if (managed.has(el)) return;
            const fromVars = REVEAL_FROM[el.dataset.reveal] ?? REVEAL_FROM['fade-up'];
            const toVars   = { opacity: 1, y: 0, x: 0, skewX: 0, skewY: 0 };
            const delay    = parseFloat(el.dataset.revealDelay || 0) / 1000;
            gsap.set(el, fromVars(el));
            gsap.to(el, {
                ...toVars,
                duration: 0.8,
                delay,
                ease: 'power3.out',
                clearProps: 'all',
                scrollTrigger: { trigger: el, ...ST },
            });
        });

        /* ── 9. COMPTEURS ─────────────────────────────────────────────────── */
        document.querySelectorAll('[data-counter]').forEach(el => {
            const target = parseInt(el.dataset.counter, 10);
            if (isNaN(target)) return;
            el.textContent = '0'; /* évite le flash valeur réelle → 0 au démarrage */
            const obj = { n: 0 };
            gsap.to(obj, {
                n: target,
                duration: 2.2,
                ease: 'power2.out',
                onUpdate() { el.textContent = Math.round(obj.n); },
                scrollTrigger: { trigger: el, ...ST },
            });
        });

        /* ── 10. DATA-PARALLAX (éléments individuels) ─────────────────────── */
        document.querySelectorAll('[data-parallax]').forEach(el => {
            const speed = parseFloat(el.dataset.parallax) || 0.2;
            gsap.to(el, {
                y: () => -(window.innerHeight * speed * 0.4),
                ease: 'none',
                scrollTrigger: {
                    trigger: el.closest('section') || el.parentElement,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: 1.5,
                    invalidateOnRefresh: true,
                },
            });
        });

        /* ── 11. TILT 3D via quickTo (pointer: fine) ─────────────────────── */
        if (window.matchMedia('(pointer: fine)').matches) {

            document.querySelectorAll('[data-tilt]').forEach(el => {
                gsap.set(el, { transformPerspective: 1000 });
                let rect;
                const rxTo = gsap.quickTo(el, 'rotationX', { duration: 0.2, ease: 'power2.out' });
                const ryTo = gsap.quickTo(el, 'rotationY', { duration: 0.2, ease: 'power2.out' });
                const scTo = gsap.quickTo(el, 'scale',     { duration: 0.2, ease: 'power2.out' });

                el.addEventListener('mouseenter', () => { rect = el.getBoundingClientRect(); });
                el.addEventListener('mousemove', e => {
                    if (!rect) return;
                    const isOpen = el.open || !!el.closest('details[open]');
                    const max    = isOpen ? 1.5 : 8;
                    const nx = (e.clientX - rect.left   - rect.width  / 2) / (rect.width  / 2);
                    const ny = (e.clientY - rect.top    - rect.height / 2) / (rect.height / 2);
                    ryTo(nx *  max);
                    rxTo(ny * -max);
                    scTo(1.02);
                });
                el.addEventListener('mouseleave', () => {
                    rect = null;
                    rxTo(0); ryTo(0); scTo(1);
                });
            });

            /* ── 12. BOUTONS MAGNÉTIQUES avec rebond élastique ───────────── */
            document.querySelectorAll('[data-magnetic]').forEach(el => {
                const xTo = gsap.quickTo(el, 'x', { duration: 0.4, ease: 'power2.out' });
                const yTo = gsap.quickTo(el, 'y', { duration: 0.4, ease: 'power2.out' });

                el.addEventListener('mousemove', e => {
                    const r = el.getBoundingClientRect();
                    xTo((e.clientX - r.left - r.width  / 2) * 0.3);
                    yTo((e.clientY - r.top  - r.height / 2) * 0.3);
                });
                el.addEventListener('mouseleave', () => {
                    gsap.to(el, { x: 0, y: 0, duration: 0.7, ease: 'elastic.out(1, 0.4)' });
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    /* Refresh ScrollTrigger après chargement complet des polices + images
       pour corriger les positions calculées avec des fontes de fallback */
    Promise.all([
        document.fonts ? document.fonts.ready : Promise.resolve(),
        new Promise(resolve => {
            if (document.readyState === 'complete') resolve();
            else window.addEventListener('load', resolve, { once: true });
        }),
    ]).then(function () {
        if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
    });
})();
