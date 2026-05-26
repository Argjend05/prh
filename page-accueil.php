<?php
/* Template Name: Accueil Custom */

$f = fn($key, $default = '') => get_field($key) ?: $default;

// Annonce & Hero
$announce = $f('prh68_acc_announce', 'Bienvenue sur le site du PRH68 – Pôle Ressources Handicap 68');
$hero_title = $f('prh68_acc_hero_title', 'Vous accompagnez des enfants de 0 à 17 ans et souhaitez favoriser l\'inclusion ?');
$hero_subtitle = $f('prh68_acc_hero_subtitle', 'Nous sommes à vos côtés pour construire un accueil inclusif.');
$hero_btn1 = $f('prh68_acc_hero_btn1', 'Découvrir nos missions');
$hero_btn2 = $f('prh68_acc_hero_btn2', 'Nous contacter');
$def_text = $f('prh68_acc_def_text', 'L\'inclusion désigne l\'accueil de tous les enfants, quels que soient leurs besoins, au sein des lieux d\'accueil de droit commun, avec les adaptations nécessaires pour garantir leur participation, leur bien-être et leur développement.');
$def_note = $f('prh68_acc_def_note', 'Cette définition est issue d\'une réflexion partagée et en lien avec les réalités du terrain.');

// Chiffres clés
$stat1_num = $f('prh68_acc_stat1_num', '3');
$stat1_lbl = $f('prh68_acc_stat1_lbl', 'Intervenants spécialisés');
$stat2_num = $f('prh68_acc_stat2_num', '7');
$stat2_lbl = $f('prh68_acc_stat2_lbl', "Types de lieux d'accueil concernés");
$stat3_num = $f('prh68_acc_stat3_num', '68');
$stat3_lbl = $f('prh68_acc_stat3_lbl', 'Haut-Rhin : notre territoire');

// Qui sommes-nous
$pres_title = $f('prh68_acc_pres_title', 'Qui sommes-nous ?');
$pres_p1 = $f('prh68_acc_pres_p1', 'Le Pôle Ressources Handicap du Haut-Rhin (PRH68) a officiellement ouvert ses portes le 1er janvier 2025. Il est le fruit d\'une collaboration entre trois associations engagées au service de l\'enfance et du handicap.');
$pres_p2 = $f('prh68_acc_pres_p2', 'Notre équipe, composée de 3 intervenants spécialistes de l\'enfance et du handicap, accompagne les professionnels de terrain dans leurs démarches d\'accueil inclusif.');

// Ce que nous faisons
$info_title = $f('prh68_acc_info_title', 'Ce que nous faisons');
$card1_title = $f('prh68_acc_card1_title', 'Mission');
$card1_text = $f('prh68_acc_card1_text', 'Soutenir et accompagner les professionnels dans l\'accueil inclusif des enfants de 0 à 17 ans en situation de handicap ou ayant des besoins spécifiques.');
$card2_title = $f('prh68_acc_card2_title', 'Objectif');
$card2_text = $f('prh68_acc_card2_text', 'Que chaque enfant puisse bénéficier d\'un accueil adapté au sein d\'un établissement de droit commun.');

// Professionnels
$pro_intro = $f('prh68_acc_pro_intro', 'Vous êtes professionnel dans une structure de droit commun :');
$pro_sub = $f('prh68_acc_pro_sub', 'Une équipe est disponible pour vous accompagner dans vos pratiques professionnelles.');
$pro_items = [
    $f('prh68_acc_pro_i1', 'EAJE'),
    $f('prh68_acc_pro_i2', 'RPE'),
    $f('prh68_acc_pro_i3', 'Assistantes maternelles'),
    $f('prh68_acc_pro_i4', 'ACM'),
    $f('prh68_acc_pro_i5', 'Service Jeunesse'),
    $f('prh68_acc_pro_i6', 'Collectivité territoriale'),
    $f('prh68_acc_pro_i7', 'Collectivité jeunesse'),
];
$pro_classes = [
    'acc-pro-icon--violet',
    'acc-pro-icon--turquoise',
    'acc-pro-icon--orange',
    'acc-pro-icon--violet',
    'acc-pro-icon--turquoise',
    'acc-pro-icon--orange',
    'acc-pro-icon--violet',
];
$pro_icons = [
    'eaje.svg',
    'relais-petite-enfance.svg',
    'assistante-maternelle.svg',
    'periscolaire.svg',
    'accueil-de-loisirs.svg',
    'accueil-jeunes.svg',
    'collectivite-territoriale.svg',
];

// Services
$srv_title = $f('prh68_acc_srv_title', 'Nous vous proposons');
$srv1_title = $f('prh68_acc_srv1_title', 'Solliciter l\'équipe pour un accompagnement');
$srv1_items = $f('prh68_acc_srv1_items', '');
$srv2_title = $f('prh68_acc_srv2_title', 'S\'appuyer sur un réseau de partenaires dans le Haut-Rhin');
$srv2_items = $f('prh68_acc_srv2_items', '');
$srv3_title = $f('prh68_acc_srv3_title', 'Partager vos besoins et questionnements professionnels');
$srv3_items = $f('prh68_acc_srv3_items', '');
$srv4_title = $f('prh68_acc_srv4_title', 'Puisez dans nos ressources conçues spécialement pour vous');
$srv4_items = $f('prh68_acc_srv4_items', '');
$srv4_btn = $f('prh68_acc_srv4_btn', 'Emprunter des ressources');

// CTA
$cta_title = $f('prh68_acc_cta_title', 'Besoin d\'un conseil ou d\'un accompagnement ?');
$cta_card_title = $f('prh68_acc_cta_card_title', 'Contactez-nous');
$cta_card_text = $f('prh68_acc_cta_card_text', 'Nous sommes à votre écoute pour vous accompagner dans vos projets d\'inclusion.');
$cta_phone = $f('prh68_acc_cta_phone', '03 89 32 81 50');
$cta_btn = $f('prh68_acc_cta_btn', 'Nous contacter');

// Helper défini dans functions.php

$srv1_defaults = [
    'Bénéficier d\'un espace de soutien et de réflexion pour prendre du recul sur les situations rencontrées.',
    'Renforcer vos compétences et valoriser vos pratiques professionnelles.',
    'Expérimenter des pistes concrètes d\'adaptation et d\'évolution de vos pratiques.',
    'Gagner en fluidité dans la communication et la co-construction avec les parents et les partenaires.',
];
$srv2_defaults = [
    'Participer à des temps interprofessionnels et initier de nouvelles rencontres.',
    'Favoriser l\'interconnaissance, le partage d\'expériences et la mutualisation des pratiques.',
    'Encourager la pair-aidance et l\'essaimage d\'initiatives inspirantes.',
    'Renforcer les dynamiques de réseau et les coopérations entre acteurs.',
];
$srv3_defaults = [
    'Mieux comprendre les besoins et les comportements des enfants.',
    'Adapter vos pratiques pour soutenir leur développement et leurs apprentissages.',
    'Renforcer la dynamique inclusive de votre collectif afin de répondre aux besoins de chaque enfant.',
    'Développer des adaptations bénéfiques et transférables au plus grand nombre d\'enfants.',
    'Être orienté vers les ressources et dispositifs du Haut-Rhin lorsque les situations le nécessitent.',
];
$srv4_defaults = [
    'S\'approprier l\'usage de matériel et d\'outils d\'adaptation à travers des actions de sensibilisation en lieu d\'accueil.',
    'Consultez et emprunter les ressources disponibles.',
];

get_header();
?>
<div class="acc-page">

    <!-- Barre d'annonce -->
    <div class="acc-announce-bar" id="acc-announce">
        <?php echo esc_html($announce); ?>
        <button class="acc-announce-close" aria-label="Fermer">&#x2715;</button>
    </div>

    <!-- ===================== HERO ===================== -->
    <section class="acc-hero">

        <div class="acc-hero-wrap">
            <div class="acc-hero-content">
                <h1 data-reveal="text-soft">Vous accompagnez des enfants de 0 à 17 ans et souhaitez favoriser <span class="acc-def-trigger"
                        tabindex="0" role="button" aria-expanded="false"
                        aria-controls="acc-def-inclusion">l'inclusion<sup class="acc-def-star">*</sup></span>&nbsp;?
                </h1>
                <p data-reveal="text-soft" data-reveal-delay="150"><?php echo esc_html($hero_subtitle); ?></p>
                <div class="acc-hero-btns">
                    <a href="#services" class="acc-btn-orange" data-magnetic>
                        <?php echo esc_html($hero_btn1); ?>
                    </a>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>"
                        class="acc-btn-hero-outline" data-magnetic>
                        <?php echo esc_html($hero_btn2); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Image bannière -->
        <div class="acc-hero-img" aria-hidden="true">
            <?php $uri = get_stylesheet_directory_uri(); ?>
            <picture>
                <source srcset="<?php echo esc_url( $uri . '/assets/img/banniere-accueil.avif' ); ?>" type="image/avif">
                <img src="<?php echo esc_url( $uri . '/assets/img/banniere-accueil.avif' ); ?>"
                     alt="" width="960" height="1080" fetchpriority="high" decoding="async">
            </picture>
        </div>

        <div class="acc-hero-wave">
            <svg viewBox="0 0 2880 70" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,35 C360,70 1080,0 1440,35 C1800,70 2520,0 2880,35 L2880,70 L0,70 Z" fill="#ffffff" />
            </svg>
        </div>
    </section>

    <!-- ===================== CHIFFRES CLÉS ===================== -->
    <section class="acc-stats">
        <div class="acc-container">
            <div class="acc-stats-grid">
                <div class="acc-stat" data-reveal="fade-up" data-reveal-delay="0">
                    <span class="acc-stat-number" data-counter="<?php echo esc_attr($stat1_num); ?>"><?php echo esc_html($stat1_num); ?></span>
                    <span class="acc-stat-label"><?php echo esc_html($stat1_lbl); ?></span>
                </div>
                <div class="acc-stat" data-reveal="fade-up" data-reveal-delay="100">
                    <span class="acc-stat-number" data-counter="<?php echo esc_attr($stat2_num); ?>"><?php echo esc_html($stat2_num); ?></span>
                    <span class="acc-stat-label"><?php echo esc_html($stat2_lbl); ?></span>
                </div>
                <div class="acc-stat" data-reveal="fade-up" data-reveal-delay="200">
                    <span class="acc-stat-number" data-counter="<?php echo esc_attr($stat3_num); ?>"><?php echo esc_html($stat3_num); ?></span>
                    <span class="acc-stat-label"><?php echo esc_html($stat3_lbl); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== PRESENTATION / PARTENAIRES ===================== -->
    <section class="acc-presentation" id="presentation">
        <div class="acc-container">
            <h2 class="acc-section-title" data-reveal="fade-up"><?php echo esc_html($pres_title); ?></h2>
            <div class="acc-presentation-content">
                <p data-reveal="fade-up" data-reveal-delay="100"><?php echo esc_html($pres_p1); ?></p>

                <?php $uri = get_stylesheet_directory_uri(); ?>
                <div class="acc-logos-partenaires">
                    <picture data-reveal="fade-up" data-reveal-delay="150" data-tilt>
                        <source srcset="<?php echo esc_url( $uri . '/assets/logos/sinclair.avif' ); ?>" type="image/avif">
                        <source srcset="<?php echo esc_url( $uri . '/assets/logos/sinclair.webp' ); ?>" type="image/webp">
                        <img src="<?php echo esc_url( $uri . '/assets/logos/sinclair.webp' ); ?>" alt="Marguerite Sinclair" width="192" height="160" loading="lazy">
                    </picture>
                    <picture data-reveal="fade-up" data-reveal-delay="250" data-tilt>
                        <source srcset="<?php echo esc_url( $uri . '/assets/logos/adapei.avif' ); ?>" type="image/avif">
                        <source srcset="<?php echo esc_url( $uri . '/assets/logos/adapei.webp' ); ?>" type="image/webp">
                        <img src="<?php echo esc_url( $uri . '/assets/logos/adapei.webp' ); ?>" alt="ADAPEI Papillons Blancs d'Alsace" width="305" height="160" loading="lazy">
                    </picture>
                    <picture data-reveal="fade-up" data-reveal-delay="350" data-tilt>
                        <source srcset="<?php echo esc_url( $uri . '/assets/logos/au-fil-de-la-vie.avif' ); ?>" type="image/avif">
                        <source srcset="<?php echo esc_url( $uri . '/assets/logos/au-fil-de-la-vie.webp' ); ?>" type="image/webp">
                        <img src="<?php echo esc_url( $uri . '/assets/logos/au-fil-de-la-vie.webp' ); ?>" alt="Au Fil de la Vie" width="200" height="159" loading="lazy">
                    </picture>
                </div>

                <p data-reveal="fade-up" data-reveal-delay="200"><?php echo esc_html($pres_p2); ?></p>
            </div>
        </div>
    </section>

    <!-- ===================== MISSION / OBJECTIF ===================== -->
    <section class="acc-info">
        <div class="acc-container">
            <h2 class="acc-section-title" data-reveal="fade-up"><?php echo esc_html($info_title); ?></h2>
            <div class="acc-info-grid">

                <div class="acc-info-card acc-card-violet" data-reveal="fade-up" data-reveal-delay="100" data-tilt>
                    <div class="acc-info-card-header">
                        <div class="acc-info-icon acc-icon-violet">
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/icons/mission.svg'); ?>"
                                alt="" width="26" height="26">
                        </div>
                        <h3 class="acc-color-violet"><?php echo esc_html($card1_title); ?></h3>
                    </div>
                    <p><?php echo esc_html($card1_text); ?></p>
                </div>

                <div class="acc-info-card acc-card-turquoise" data-reveal="fade-up" data-reveal-delay="200" data-tilt>
                    <div class="acc-info-card-header">
                        <div class="acc-info-icon acc-icon-turquoise">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="26"
                                height="26">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-12.5c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 5.5c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z" />
                            </svg>
                        </div>
                        <h3 class="acc-color-turquoise"><?php echo esc_html($card2_title); ?></h3>
                    </div>
                    <p><?php echo esc_html($card2_text); ?></p>
                </div>

            </div>
        </div>
    </section>

    <!-- ===================== PROFESSIONNELS ===================== -->
    <section class="acc-pro">
        <div class="acc-container">
            <p class="acc-pro-title" data-reveal="fade-up"><?php echo esc_html($pro_intro); ?></p>
            <div class="acc-pro-items">
                <?php
                $pro_url = esc_url(get_permalink(get_page_by_path('professionnel')));
                $icons_base = get_stylesheet_directory_uri() . '/assets/icons/';
                foreach ($pro_items as $i => $label):
                    $icon = $pro_icons[$i];
                    ?>
                    <a href="<?php echo $pro_url; ?>" class="acc-pro-item" data-reveal="fade-up" data-reveal-delay="<?php echo $i * 50; ?>" data-tilt>
                        <div class="acc-pro-icon <?php echo $pro_classes[$i]; ?>">
                            <img src="<?php echo esc_url($icons_base . $icon); ?>" alt="" width="26" height="26">
                        </div>
                        <span><?php echo esc_html($label); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <p class="acc-pro-subtitle" data-reveal="fade-up" data-reveal-delay="200"><?php echo esc_html($pro_sub); ?></p>
        </div>
    </section>

    <!-- ===================== SERVICES ===================== -->
    <section class="acc-services" id="services">
        <div class="acc-container">
            <h2 class="acc-section-title" data-reveal="fade-up"><?php echo esc_html($srv_title); ?></h2>
            <div class="acc-services-grid">

                <!-- Colonne gauche -->
                <div class="acc-services-col">
                    <details class="acc-service-card acc-border-orange" data-reveal="fade-up" data-reveal-delay="100" data-tilt>
                        <summary>
                            <div class="acc-service-icon acc-service-icon--orange">
                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/icons/professionnels.svg'); ?>"
                                    alt="" width="22" height="22">
                            </div>
                            <h3><?php echo esc_html($srv1_title); ?></h3>
                            <svg class="acc-service-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="acc-service-body">
                            <ul class="acc-service-list">
                                <?php prh68_acc_items($srv1_items, $srv1_defaults); ?>
                            </ul>
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('professionnel'))); ?>"
                                class="acc-btn-service acc-btn-service--orange" data-magnetic>
                                En savoir plus
                            </a>
                        </div>
                    </details>

                    <details class="acc-service-card acc-border-orange" data-reveal="fade-up" data-reveal-delay="200" data-tilt>
                        <summary>
                            <div class="acc-service-icon acc-service-icon--orange">
                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . "/assets/icons/reseau.svg"); ?>"
                                    alt="" width="22" height="22">
                            </div>
                            <h3><?php echo esc_html($srv2_title); ?></h3>
                            <svg class="acc-service-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="acc-service-body">
                            <ul class="acc-service-list">
                                <?php prh68_acc_items($srv2_items, $srv2_defaults); ?>
                            </ul>
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('evenements-prh68'))); ?>"
                                class="acc-btn-service acc-btn-service--orange" data-magnetic>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="16" height="16">
                                    <path
                                        d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
                                </svg>
                                Voir les événements
                            </a>
                        </div>
                    </details>
                </div>

                <!-- Colonne droite -->
                <div class="acc-services-col">
                    <details class="acc-service-card acc-border-violet" data-reveal="fade-up" data-reveal-delay="150" data-tilt>
                        <summary>
                            <div class="acc-service-icon acc-service-icon--violet">
                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . "/assets/icons/partagez.svg"); ?>"
                                    alt="" width="22" height="22">
                            </div>
                            <h3><?php echo esc_html($srv3_title); ?></h3>
                            <svg class="acc-service-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="acc-service-body">
                            <ul class="acc-service-list">
                                <?php prh68_acc_items($srv3_items, $srv3_defaults); ?>
                            </ul>
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('professionnel'))); ?>"
                                class="acc-btn-service acc-btn-service--violet" data-magnetic>
                                En savoir plus
                            </a>
                        </div>
                    </details>

                    <details class="acc-service-card acc-border-turquoise" data-reveal="fade-up" data-reveal-delay="250" data-tilt>
                        <summary>
                            <div class="acc-service-icon acc-service-icon--turquoise">
                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/icons/ressources.svg'); ?>"
                                    alt="" width="22" height="22">
                            </div>
                            <h3><?php echo esc_html($srv4_title); ?></h3>
                            <svg class="acc-service-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="acc-service-body">
                            <ul class="acc-service-list">
                                <?php prh68_acc_items($srv4_items, $srv4_defaults); ?>
                            </ul>
                            <a href="<?php echo esc_url(get_permalink(get_page_by_path('malettes-pedagogiques'))); ?>"
                                class="acc-btn-ressources" data-magnetic>
                                <?php echo esc_html($srv4_btn); ?>
                            </a>
                        </div>
                    </details>
                </div>

            </div>
        </div>
    </section>

    <!-- ===================== CTA ===================== -->
    <section class="acc-cta">
        <div class="acc-container">
            <h2 data-reveal="fade-up"><?php echo esc_html($cta_title); ?></h2>
            <div class="acc-cta-card" data-reveal="fade-up" data-reveal-delay="100" data-tilt>
                <h3><?php echo esc_html($cta_card_title); ?></h3>
                <p><?php echo esc_html($cta_card_text); ?></p>
                <div class="acc-cta-phone">
                    <div class="acc-cta-phone-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20"
                            height="20">
                            <path
                                d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                        </svg>
                    </div>
                    <span><?php echo esc_html($cta_phone); ?></span>
                </div>
                <div class="acc-cta-btns">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>"
                        class="acc-btn-dark" data-magnetic><?php echo esc_html($cta_btn); ?></a>
                    <a href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/docs/flyer-prh68.pdf' ); ?>"
                        class="acc-btn-flyer" download target="_blank" rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15" height="15" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 7V3.5L18.5 9H13zM12 17l-4-4h2.5v-3h3v3H16l-4 4z"/>
                        </svg>
                        Télécharger le flyer
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- Tooltip définition "inclusion" -->
<div class="acc-def-box" id="acc-def-inclusion" role="tooltip" aria-hidden="true">
    <p><?php echo esc_html($def_text); ?></p>
    <?php if ($def_note): ?>
        <p class="acc-def-note"><?php echo esc_html($def_note); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>