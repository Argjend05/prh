(function () {
    'use strict';

    function init() {

        /* ────────────────────────────────────────────────────
           COMPTEUR ANIMÉ — stats hero
        ──────────────────────────────────────────────────── */
        function animateCounter(el, target, duration) {
            duration = duration || 1400;
            var startTime = null;
            function tick(ts) {
                if (!startTime) startTime = ts;
                var elapsed  = ts - startTime;
                var progress = Math.min(elapsed / duration, 1);
                var eased    = 1 - Math.pow(1 - progress, 3); // ease-out cubic
                el.textContent = Math.round(eased * target);
                if (progress < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        }

        var statNums = [...document.querySelectorAll('.tem-hero-stat-num[data-counter]')];
        if (statNums.length && 'IntersectionObserver' in window) {
            var statObs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    var el = entry.target;
                    animateCounter(el, parseInt(el.dataset.counter, 10) || 0, 1400);
                    statObs.unobserve(el);
                });
            }, { threshold: 0.7 });
            statNums.forEach(function (el) { statObs.observe(el); });
        } else {
            // Pas d'IntersectionObserver : affiche directement les valeurs
            statNums.forEach(function (el) {
                el.textContent = el.dataset.counter || '0';
            });
        }

        /* ────────────────────────────────────────────────────
           FILTRES + TRANSITIONS
        ──────────────────────────────────────────────────── */
        var filtersEl   = document.querySelector('.tem-filters');
        var cards       = [...document.querySelectorAll('.tem-card[data-cat]')];
        var featured    = document.querySelector('.tem-featured-wrap');
        var emptyMsg    = document.querySelector('.tem-empty-filtered');
        var resultNum   = document.querySelector('.tem-result-num');

        function updateResultCount(n) {
            if (!resultNum) return;
            resultNum.textContent = n;
        }

        function showCard(card) {
            card.classList.remove('is-hidden');
            // Force un reflow pour que la suppression de is-hiding déclenche la transition
            card.offsetHeight; // eslint-disable-line no-unused-expressions
            card.classList.remove('is-hiding');
            // Si la carte n'a pas encore été animée au scroll, on la rend visible directement
            if (!card.classList.contains('is-visible')) {
                card.classList.add('is-visible');
            }
        }

        function hideCard(card) {
            card.classList.add('is-hiding');
            setTimeout(function () {
                if (card.classList.contains('is-hiding')) {
                    card.classList.add('is-hidden');
                }
            }, 240);
        }

        if (filtersEl && cards.length) {
            var filterBtns = [...filtersEl.querySelectorAll('.tem-filter')];

            filterBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var target = btn.dataset.filter;

                    // Mise à jour des boutons actifs
                    filterBtns.forEach(function (b) {
                        b.classList.toggle('is-active', b === btn);
                        b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
                    });

                    var visible = 0;

                    cards.forEach(function (card) {
                        var match = target === 'all' || card.dataset.cat === target;
                        if (match) {
                            showCard(card);
                            visible++;
                        } else {
                            hideCard(card);
                        }
                    });

                    // Carte featured
                    if (featured) {
                        var fCard = featured.querySelector('.tem-card');
                        if (fCard) {
                            var fMatch = target === 'all' || fCard.dataset.cat === target;
                            featured.style.display = fMatch ? '' : 'none';
                            if (fMatch) visible++;
                        }
                    }

                    // Mise à jour du compteur résultat
                    updateResultCount(visible);

                    // Message vide
                    if (emptyMsg) emptyMsg.hidden = visible !== 0;
                });
            });
        }

        /* ────────────────────────────────────────────────────
           ANIMATIONS D'ENTRÉE AU SCROLL
        ──────────────────────────────────────────────────── */
        var allCards = [...document.querySelectorAll('.tem-card')];

        if (allCards.length && 'IntersectionObserver' in window) {
            // Désactive les transitions le temps d'appliquer l'état initial
            allCards.forEach(function (card) {
                card.style.transition = 'none';
            });

            // Applique l'état initial invisible
            // (la règle CSS .tem-card déjà fait opacity:0/translateY, mais on force ici
            //  pour les cartes qui auraient déjà is-visible d'un état précédent)
            allCards.forEach(function (card) {
                card.classList.remove('is-visible');
            });

            // Force reflow — à partir d'ici les cartes sont à opacity:0
            document.body.getBoundingClientRect();

            // Réactive les transitions
            allCards.forEach(function (card) {
                card.style.removeProperty('transition');
            });

            var cardObs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    var card  = entry.target;
                    var col   = parseInt(card.dataset.animCol || 0, 10);
                    var delay = col * 80; // stagger par colonne
                    setTimeout(function () {
                        card.classList.add('is-visible');
                    }, delay);
                    cardObs.unobserve(card);
                });
            }, { threshold: 0.06, rootMargin: '0px 0px -24px 0px' });

            allCards.forEach(function (card) { cardObs.observe(card); });
        } else {
            // Fallback sans observer : tout visible
            allCards.forEach(function (card) { card.classList.add('is-visible'); });
        }

        /* ────────────────────────────────────────────────────
           PLAYERS AUDIO CUSTOM
        ──────────────────────────────────────────────────── */
        var audioPlayers = [...document.querySelectorAll('.tem-audio[data-audio-player]')];

        function fmt(s) {
            if (!isFinite(s) || s < 0) return '0:00';
            s = Math.floor(s);
            var m = Math.floor(s / 60);
            var sec = s % 60;
            return m + ':' + (sec < 10 ? '0' : '') + sec;
        }

        var allAudios = [];

        /* ── Waveform réelle (Web Audio API) ── */
        var sharedCtx = null;
        function getAudioContext() {
            if (!sharedCtx) {
                var Ctx = window.AudioContext || window.webkitAudioContext;
                if (Ctx) sharedCtx = new Ctx();
            }
            return sharedCtx;
        }

        async function buildRealWaveform(audioEl, bars) {
            if (!bars.length) return;
            var url = audioEl.currentSrc || audioEl.src;
            if (!url) return;
            var ctx = getAudioContext();
            if (!ctx) return;
            try {
                var res = await fetch(url);
                if (!res.ok) return;
                var arrayBuffer = await res.arrayBuffer();
                var audioBuf    = await ctx.decodeAudioData(arrayBuffer);
                var channel     = audioBuf.getChannelData(0);
                var samples     = bars.length;
                var blockSize   = Math.floor(channel.length / samples) || 1;

                var peaks = new Array(samples).fill(0);
                for (var i = 0; i < samples; i++) {
                    var max   = 0;
                    var start = i * blockSize;
                    for (var j = 0; j < blockSize; j++) {
                        var v = Math.abs(channel[start + j] || 0);
                        if (v > max) max = v;
                    }
                    peaks[i] = max;
                }

                var maxPeak = Math.max.apply(null, peaks) || 1;
                bars.forEach(function (bar, idx) {
                    var ratio = peaks[idx] / maxPeak;
                    var h = Math.max(8, Math.round(Math.pow(ratio, 2.2) * 100));
                    bar.style.height = h + '%';
                });
            } catch (e) { /* CORS ou format non supporté — fallback conservé */ }
        }

        var wfObserver = ('IntersectionObserver' in window)
            ? new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) return;
                    var wrap  = entry.target;
                    var audio = wrap.querySelector('audio');
                    var bars  = [...wrap.querySelectorAll('.tem-audio-waveform span')];
                    if (audio) buildRealWaveform(audio, bars);
                    obs.unobserve(wrap);
                });
            }, { rootMargin: '300px' })
            : null;

        audioPlayers.forEach(function (wrap) {
            var audio       = wrap.querySelector('audio');
            var playBtn     = wrap.querySelector('.tem-audio-play');
            var volBtn      = wrap.querySelector('.tem-audio-volume');
            var bar         = wrap.querySelector('.tem-audio-bar');
            var fill        = wrap.querySelector('.tem-audio-bar-fill');
            var tCurrent    = wrap.querySelector('.tem-audio-time-current');
            var tTotal      = wrap.querySelector('.tem-audio-time-total');
            var waveBars    = [...wrap.querySelectorAll('.tem-audio-waveform span')];
            var waveformWrap = wrap.querySelector('.tem-audio-waveform');

            if (!audio) return;
            allAudios.push(audio);

            audio.addEventListener('loadedmetadata', function () {
                if (tTotal && (!tTotal.textContent || tTotal.textContent === '–:–')) {
                    tTotal.textContent = fmt(audio.duration);
                }
            });

            playBtn.addEventListener('click', function () {
                if (audio.paused) {
                    allAudios.forEach(function (a) { if (a !== audio && !a.paused) a.pause(); });
                    audio.play();
                } else {
                    audio.pause();
                }
            });

            audio.addEventListener('play',  function () { wrap.classList.add('is-playing'); });
            audio.addEventListener('pause', function () { wrap.classList.remove('is-playing'); });
            audio.addEventListener('ended', function () {
                wrap.classList.remove('is-playing');
                if (fill) fill.style.width = '0%';
                if (tCurrent) tCurrent.textContent = '0:00';
                waveBars.forEach(function (b) { b.classList.remove('is-played'); });
            });

            audio.addEventListener('timeupdate', function () {
                if (!audio.duration) return;
                var ratio = audio.currentTime / audio.duration;
                if (fill) fill.style.width = (ratio * 100) + '%';
                if (tCurrent) tCurrent.textContent = fmt(audio.currentTime);
                if (waveBars.length) {
                    var cutoff = Math.floor(ratio * waveBars.length);
                    waveBars.forEach(function (b, i) {
                        b.classList.toggle('is-played', i < cutoff);
                    });
                }
            });

            function seekFromEvent(e, target) {
                var rect  = target.getBoundingClientRect();
                var x     = (e.clientX || (e.touches && e.touches[0].clientX) || 0) - rect.left;
                var ratio = Math.max(0, Math.min(1, x / rect.width));
                if (audio.duration) audio.currentTime = ratio * audio.duration;
            }

            if (bar)         bar.addEventListener('click', function (e) { seekFromEvent(e, bar); });
            if (waveformWrap) waveformWrap.addEventListener('click', function (e) { seekFromEvent(e, waveformWrap); });

            if (volBtn) {
                volBtn.addEventListener('click', function () {
                    audio.muted = !audio.muted;
                    wrap.classList.toggle('is-muted', audio.muted);
                });
            }

            if (wfObserver) wfObserver.observe(wrap);
        });

        /* ────────────────────────────────────────────────────
           MODALE VIDÉO
        ──────────────────────────────────────────────────── */
        var modal     = document.getElementById('tem-video-modal');
        var modalSlot = document.getElementById('tem-video-modal-target');
        var triggers  = [...document.querySelectorAll('.tem-video-thumb[data-video-url]')];

        if (modal && modalSlot && triggers.length) {

            function buildEmbed(url) {
                var m;
                /* YouTube */
                m = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([^?&"'\s]+)/i);
                if (m) {
                    var yt = document.createElement('iframe');
                    yt.src = 'https://www.youtube.com/embed/' + m[1] + '?autoplay=1&rel=0';
                    yt.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
                    yt.allowFullscreen = true;
                    return yt;
                }
                /* Vimeo */
                m = url.match(/vimeo\.com\/(?:video\/)?(\d+)/i);
                if (m) {
                    var vm = document.createElement('iframe');
                    vm.src = 'https://player.vimeo.com/video/' + m[1] + '?autoplay=1';
                    vm.allow = 'autoplay; fullscreen; picture-in-picture';
                    vm.allowFullscreen = true;
                    return vm;
                }
                /* Fichier vidéo direct */
                if (/\.(mp4|webm|ogg)(\?.*)?$/i.test(url)) {
                    var vid = document.createElement('video');
                    vid.src = url;
                    vid.controls = true;
                    vid.autoplay  = true;
                    vid.playsInline = true;
                    return vid;
                }
                /* Fallback */
                var fb = document.createElement('iframe');
                fb.src = url;
                fb.allowFullscreen = true;
                return fb;
            }

            function openModal(url) {
                modalSlot.innerHTML = '';
                modalSlot.appendChild(buildEmbed(url));
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                modal.hidden = true;
                modalSlot.innerHTML = '';
                document.body.style.overflow = '';
            }

            triggers.forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    openModal(btn.dataset.videoUrl);
                });
            });

            modal.querySelectorAll('[data-close]').forEach(function (el) {
                el.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.hidden) closeModal();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
