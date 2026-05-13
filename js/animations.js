(function () {
    function init() {

        /* ── NAVIGATION INTERNE (SVG MORPHING) ────────────────────────────── */
        window._prhIsNavigation = sessionStorage.getItem('prh_nav') === '1';
        if (window._prhIsNavigation) sessionStorage.removeItem('prh_nav');

        const curtainPath = document.querySelector('.prh-curtain-path');
        const curtainContent = document.querySelector('.prh-curtain-content');
        const curtainEl = document.getElementById('page-curtain');
        
        // svg paths pour morphing
        const paths = {
            step1: 'M 0 100 V 50 Q 50 0 100 50 V 100 z', // Vague qui monte
            step2: 'M 0 100 V 0 Q 50 0 100 0 V 100 z',   // Écran plein
            step3: 'M 0 0 V 50 Q 50 100 100 50 V 0 z',   // Vague qui descend
            step4: 'M 0 0 V 0 Q 50 0 100 0 V 0 z',       // Vide en haut
            start: 'M 0 100 V 100 Q 50 100 100 100 V 100 z' // Vide en bas
        };

        document.addEventListener('click', function (e) {
            var a = e.target.closest('a[href]');
            if (!a) return;
            try {
                var url = new URL(a.href);
                if (url.hostname !== window.location.hostname) return;
                if (a.target === '_blank') return;
                if (a.getAttribute('download') !== null) return;
                if (url.pathname === window.location.pathname && url.hash) return;
                
                e.preventDefault();
                sessionStorage.setItem('prh_nav', '1');
                
                curtainEl.style.pointerEvents = 'auto';
                
                const tl = gsap.timeline({
                    onComplete: () => { window.location.href = a.href; }
                });
                
                tl.set(curtainPath, { attr: { d: paths.start } })
                  .to(curtainPath, { attr: { d: paths.step1 }, duration: 0.4, ease: 'power2.in' })
                  .to(curtainPath, { attr: { d: paths.step2 }, duration: 0.4, ease: 'power2.out' })
                  .to(curtainContent, { opacity: 1, duration: 0.2 }, '-=0.2');

            } catch (err) {}
        }, true);

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

        /* ── SCROLL SPY (GSAP) ────────────────────────────────────────────── */
        const navAnchors = document.querySelectorAll('.prh-nav-list a[href^="#"], .prh-mobile-nav-list a[href^="#"]');
        if (navAnchors.length && typeof ScrollTrigger !== 'undefined') {
            navAnchors.forEach(a => {
                const secId = a.getAttribute('href').slice(1);
                const sec = document.getElementById(secId);
                if (sec) {
                    ScrollTrigger.create({
                        trigger: sec,
                        start: 'top center',
                        end: 'bottom center',
                        onToggle: self => {
                            if (self.isActive) {
                                document.querySelectorAll('.spy-active').forEach(el => el.classList.remove('spy-active'));
                                a.classList.add('spy-active');
                            }
                        }
                    });
                }
            });
        }

        /* Navigation interne (Arrivée sur la nouvelle page) */
        if (window._prhIsNavigation) {
            var loader = document.getElementById('page-loader');
            if (loader) loader.remove();
            
            // Jouer le SVG morphing d'ouverture
            if (curtainPath && curtainContent) {
                gsap.set(curtainPath, { attr: { d: paths.step2 } });
                gsap.set(curtainContent, { opacity: 1 });
                curtainEl.style.pointerEvents = 'auto';
                
                const tl = gsap.timeline();
                tl.to(curtainContent, { opacity: 0, duration: 0.2 })
                  .to(curtainPath, { attr: { d: paths.step3 }, duration: 0.4, ease: 'power2.in' })
                  .to(curtainPath, { attr: { d: paths.step4 }, duration: 0.4, ease: 'power2.out' })
                  .set(curtainEl, { pointerEvents: 'none' });
            }
        }

        /* ── GSAP ─────────────────────────────────────────────────────────── */
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (typeof gsap === 'undefined') return;
        /* Pas d'animations GSAP sur petits écrans : économise CPU et évite le CLS
           (word-split innerHTML + gsap-ready opacity:0 → shifts perceptibles) */
        if (window.innerWidth < 769) return;

        /* Signale que GSAP est opérationnel — les règles CSS .gsap-ready
           cachent les éléments hero que la timeline va animer. */
        document.body.classList.add('gsap-ready');

        gsap.registerPlugin(ScrollTrigger);

        /* ── LENIS SMOOTH SCROLL ──────────────────────────────────────────── */
        if (typeof Lenis !== 'undefined') {
            const lenis = new Lenis({
                duration: 1.2,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                smoothWheel: true,
                touchMultiplier: 2
            });
            lenis.on('scroll', ScrollTrigger.update);
            gsap.ticker.add((time) => { lenis.raf(time * 1000); });
            gsap.ticker.lagSmoothing(0);
            window._prhLenis = lenis;
            
            // Remplacer scrollTo par Lenis
            const topBtn = document.getElementById('prh-back-to-top');
            if (topBtn) {
                topBtn.replaceWith(topBtn.cloneNode(true));
                document.getElementById('prh-back-to-top').addEventListener('click', () => {
                    lenis.scrollTo(0, { duration: 1.5 });
                });
            }
            
            document.querySelectorAll('a[href^="#"]').forEach(a => {
                a.addEventListener('click', function(e) {
                    const id = this.getAttribute('href');
                    if(id !== '#') {
                        const target = document.querySelector(id);
                        if(target) {
                            e.preventDefault();
                            lenis.scrollTo(target, { offset: -90 });
                        }
                    }
                });
            });
        }

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

            /* Timeline en pause — lancée quand le loader sort (voir Promise.all). */
            const tl = gsap.timeline({ defaults: { ease: 'power4.out' }, paused: true });

            const h1 = heroContent.querySelector('h1');
            const wordSpans = wordSplit(h1);
            if (wordSpans) {
                /* Spans créés dynamiquement — pas de CSS opacity:0, clearProps ok */
                tl.from(wordSpans, { y: '115%', duration: 0.7, stagger: 0.06, clearProps: 'all' });
            } else if (h1) {
                /* fromTo explicite : go to opacity:1 sans dépendre du CSS */
                tl.fromTo(h1,
                    { opacity: 0, y: 40 },
                    { opacity: 1, y: 0, duration: 0.8, clearProps: 'y' }
                );
            }

            const subtitle = heroContent.querySelector('p');
            if (subtitle) {
                tl.fromTo(subtitle,
                    { opacity: 0, y: 24 },
                    { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out', clearProps: 'y' },
                    '-=0.35'
                );
            }

            /* Accueil : animer le conteneur .acc-hero-btns (CSS opacity:0 via .gsap-ready).
               Autres pages (pro, obs) : animer les liens individuels. */
            const btnsContainer = heroContent.querySelector('.acc-hero-btns');
            if (btnsContainer) {
                tl.fromTo(btnsContainer,
                    { opacity: 0, y: 12 },
                    { opacity: 1, y: 0, duration: 0.55, ease: 'back.out(1.8)', clearProps: 'y' },
                    '-=0.25'
                );
            } else {
                const btns = [...heroContent.querySelectorAll('a, button')];
                if (btns.length) {
                    tl.from(btns, {
                        opacity: 0, scale: 0.75, y: 12,
                        duration: 0.55, stagger: 0.08,
                        ease: 'back.out(1.8)', clearProps: 'all',
                    }, '-=0.25');
                }
            }

            /* Image hero — s'ouvre depuis le coin supérieur droit.
               clearProps:'clipPath' seulement (opacity:1 reste inline
               pour overrider la règle CSS .gsap-ready). */
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
            window._prhHeroTl = tl;

            /* Sur navigation interne, les fonts/images sont déjà en cache :
               on joue immédiatement sans attendre Promise.all (window.load).
               Sans ça, les éléments resteraient opacity:0 pendant que
               window.load résout les nouvelles images de la page. */
            if (window._prhIsNavigation) {
                requestAnimationFrame(function () { tl.play(); });
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

        /* ── 8. RÉVÉLATION INDIVIDUELLE — incluant mask reveal ────────────── */
        const REVEAL_FROM = {
            'fade-up':    (el) => ({ opacity: 0, y: 50, skewX: 2 }),
            'text-soft':  (el) => ({ opacity: 0, y: 22 }),
            'fade-left':  (el) => ({ opacity: 0, x: 55, skewY: -1 }),
            'fade-right': (el) => ({ opacity: 0, x: -55, skewY: 1 }),
            'mask':       (el) => ({ opacity: 1 }), // Mask a une logique spécifique
        };

        document.querySelectorAll('[data-reveal]').forEach(el => {
            if (managed.has(el)) return;
            
            const type = el.dataset.reveal;
            const delay = parseFloat(el.dataset.revealDelay || 0) / 1000;
            
            if (type === 'mask') {
                // Wrapper pour le mask si ce n'est pas déjà fait
                const wrap = document.createElement('div');
                wrap.style.overflow = 'hidden';
                wrap.style.display = 'inline-block';
                wrap.style.width = '100%';
                wrap.style.height = '100%';
                el.parentNode.insertBefore(wrap, el);
                wrap.appendChild(el);
                
                gsap.fromTo(el, 
                    { scale: 1.2, y: '20%' },
                    { 
                        scale: 1, y: '0%', duration: 1.2, delay: delay, ease: 'power3.out',
                        scrollTrigger: { trigger: wrap, ...ST }
                    }
                );
            } else {
                const fromVars = REVEAL_FROM[type] ?? REVEAL_FROM['fade-up'];
                gsap.set(el, fromVars(el));
                gsap.to(el, {
                    opacity: 1, y: 0, x: 0, skewX: 0, skewY: 0,
                    duration: 0.8, delay: delay, ease: 'power3.out', clearProps: 'all',
                    scrollTrigger: { trigger: el, ...ST },
                });
            }
        });

        /* ── 9. COMPTEURS ─────────────────────────────────────────────────── */
        document.querySelectorAll('[data-counter]').forEach(el => {
            const target = parseInt(el.dataset.counter, 10);
            if (isNaN(target)) return;
            /* La valeur réelle est dans le HTML (visible sans GSAP / mobile).
               GSAP repart de 0 et restaure l'opacity (masquée par
               .gsap-ready .acc-stat-number dans le CSS). */
            el.textContent = '0';
            const obj = { n: 0 };
            gsap.to(obj, {
                n: target,
                duration: 2.2,
                ease: 'power2.out',
                onStart()  { gsap.to(el, { opacity: 1, duration: 0.3 }); },
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

        /* ── 11. CURSEUR GSAP + TILT + BOUTONS MAGNÉTIQUES ────────────── */
        if (window.matchMedia('(pointer: fine)').matches) {
            
            /* Curseur */
            const cursor = document.getElementById('prh-cursor');
            if (cursor) {
                const cxTo = gsap.quickTo(cursor, 'x', { duration: 0.15, ease: 'power3' });
                const cyTo = gsap.quickTo(cursor, 'y', { duration: 0.15, ease: 'power3' });
                window.addEventListener('mousemove', e => {
                    cxTo(e.clientX);
                    cyTo(e.clientY);
                });
                
                document.querySelectorAll('a, button, [data-magnetic], .acc-def-trigger').forEach(el => {
                    el.addEventListener('mouseenter', () => cursor.classList.add('is-hover'));
                    el.addEventListener('mouseleave', () => cursor.classList.remove('is-hover'));
                });
            }

            /* Tilt 3D */
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

            /* Boutons magnétiques double-couche */
            document.querySelectorAll('[data-magnetic]').forEach(el => {
                // Wrapper interne pour l'effet double
                if (!el.querySelector('.magnetic-inner')) {
                    const inner = document.createElement('span');
                    inner.className = 'magnetic-inner';
                    inner.style.display = 'inline-block';
                    inner.style.pointerEvents = 'none';
                    while(el.firstChild) {
                        inner.appendChild(el.firstChild);
                    }
                    el.appendChild(inner);
                }
                const innerEl = el.querySelector('.magnetic-inner');
                
                const xTo = gsap.quickTo(el, 'x', { duration: 0.4, ease: 'power2.out' });
                const yTo = gsap.quickTo(el, 'y', { duration: 0.4, ease: 'power2.out' });
                const innerXTo = gsap.quickTo(innerEl, 'x', { duration: 0.5, ease: 'power2.out' });
                const innerYTo = gsap.quickTo(innerEl, 'y', { duration: 0.5, ease: 'power2.out' });

                el.addEventListener('mousemove', e => {
                    const r = el.getBoundingClientRect();
                    const moveX = e.clientX - r.left - r.width / 2;
                    const moveY = e.clientY - r.top - r.height / 2;
                    xTo(moveX * 0.4);
                    yTo(moveY * 0.4);
                    innerXTo(moveX * 0.2);
                    innerYTo(moveY * 0.2);
                });
                el.addEventListener('mouseleave', () => {
                    gsap.to([el, innerEl], { x: 0, y: 0, duration: 0.7, ease: 'elastic.out(1, 0.4)' });
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    /* ── RETOUR ARRIÈRE (bfcache) ─────────────────────────────────────────
       Sur ←, Chrome restaure la page depuis le bfcache. On nettoie
       le sessionStorage et on s'assure que le loader ne bloque pas l'écran. */
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        sessionStorage.removeItem('prh_nav');
        document.documentElement.style.opacity = '';
        var loader = document.getElementById('page-loader');
        if (loader) loader.remove();
    });

    /* Refresh ScrollTrigger + dismiss du loader après polices + images chargés.
       Le timer de 900ms garantit que l'animation d'entrée du loader est visible
       même sur connexions très rapides. */
    Promise.all([
        document.fonts ? document.fonts.ready : Promise.resolve(),
        new Promise(resolve => {
            if (document.readyState === 'complete') resolve();
            else window.addEventListener('load', resolve, { once: true });
        }),
        /* Pas de délai minimum sur navigation interne (tout est en cache).
           Sur mobile : 0ms — on affiche le contenu dès que le DOM est prêt.
           Sur desktop : 900ms pour laisser l'animation du loader se dérouler. */
        new Promise(resolve => setTimeout(resolve, window._prhIsNavigation ? 0 : (window.innerWidth < 769 ? 0 : 900))),
    ]).then(function () {
        if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();

        /* Loader de premier chargement : on le fait sortir par le haut,
           puis on lance l'animation hero. Sur navigation interne le loader
           a déjà été supprimé dans init(). */
        var loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('loader-out');
            loader.addEventListener('transitionend', function () { loader.remove(); }, { once: true });
            if (typeof gsap !== 'undefined' && window._prhHeroTl) {
                gsap.delayedCall(0.3, function () { window._prhHeroTl.play(); });
            }
        } else if (typeof gsap !== 'undefined' && window._prhHeroTl) {
            window._prhHeroTl.play();
        }
    });
})();
