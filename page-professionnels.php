<?php
/* Template Name: Professionnels Custom */

get_header();

/* ── ACF helpers ──────────────────────────────────────── */
$f = fn($key, $default) => get_field($key) ?: $default;

/* ── Hero ─────────────────────────────────────────────── */
$hero_title = $f('prh68_pro_hero_title', 'Espace Professionnels : Co-construire l\'inclusion');
$hero_p1 = $f('prh68_pro_hero_p1', 'Vos compétences, votre expérience et votre engagement sont au cœur de cette mission.');

/* ── Pourquoi ─────────────────────────────────────────── */
$why_title = $f('prh68_pro_why_title', 'Pourquoi nous solliciter ?');
$why_c1 = $f('prh68_pro_why_c1', 'Vous souhaitez approfondir la question de l\'accueil inclusif au sein de votre lieu d\'accueil');
$why_c2 = $f('prh68_pro_why_c2', 'Vous vous questionnez ou rencontrez des difficultés dans l\'accueil d\'enfants ayant des besoins spécifiques');
$why_c3 = $f('prh68_pro_why_c3', 'Vous cherchez des pistes éducatives et des outils concrets pour votre pratique au quotidien');
$why_c4 = $f('prh68_pro_why_c4', 'Vous avez besoin d\'outils, pistes et idées concrètes pour l\'inclusion');

/* ── Nous vous proposons ──────────────────────────────── */
$nvp_title = $f('prh68_pro_nvp_title', 'Nous vous proposons');
$nvp_sub = $f('prh68_pro_nvp_sub', 'Six types d\'interventions co-construites avec vous à partir de vos besoins et de votre réalité de terrain');
$nvp_acc1_ttl = $f('prh68_pro_acc1_title', 'Appui téléphonique');
$nvp_acc2_ttl = $f('prh68_pro_acc2_title', 'Appui sur votre lieu d\'accueil');
$nvp_acc3_ttl = $f('prh68_pro_acc3_title', 'Sensibilisation collective');
$nvp_acc4_ttl = $f('prh68_pro_acc4_title', 'Immersion dans des lieux inclusifs');
$nvp_acc5_ttl = $f('prh68_pro_acc5_title', 'Sacs pédagogiques');
$nvp_acc6_ttl = $f('prh68_pro_acc6_title', 'Projets / Partenariats');

/* ── CTA ──────────────────────────────────────────────── */
$cta_title = $f('prh68_pro_cta_title', 'Prêt à développer l\'inclusion dans votre structure ?');
$cta_sub = $f('prh68_pro_cta_sub', 'Contactez-nous dès aujourd\'hui pour échanger sur vos besoins et construire ensemble un accompagnement sur mesure');
$cta_btn = $f('prh68_pro_cta_btn', 'Contactez le Pôle Ressources');
$cta_phone = $f('prh68_pro_cta_phone', '03 89 32 81 50');
$cta_email = $f('prh68_pro_cta_email', 'contact@prh68.fr');

/* ── Chemin icônes ────────────────────────────────────── */
$icons_base = get_stylesheet_directory_uri() . '/icons/';

/* ── Contenu accordéons (hardcodé) ───────────────────── */
$acc1_items = [
    ['Écoute bienveillante', 'Soyez libre de vous exprimer, de partager vos ressentis, vos questions ou vos difficultés, sans jugement'],
    ['Soutien', 'Appelez le PRH68 si vous avez besoin d\'être conforté et/ou rassuré dans votre pratique professionnelle'],
    ['Conseils personnalisés', 'Le PRH68 vous propose des pistes d\'actions concrètes, en lien avec les besoins des enfants et votre réalité de terrain'],
    ['Orientation', 'Si nécessaire, le PRH68 vous oriente vers les partenaires du réseau'],
];
$acc2_items = [
    ['Observations', 'Le PRH68 observe les besoins des enfants, l\'aménagement de l\'espace, la structuration du temps et les réponses éducatives afin de croiser son regard avec le vôtre'],
    ['Temps réflexifs', 'Lors de réunions d\'équipe, échangez autour de vos questionnements et des situations rencontrées pour faire évoluer les pratiques professionnelles'],
    ['Préconisations', 'Le PRH68 vous transmet un écrit avec des pistes de réflexion et des outils concrets adaptés aux besoins des enfants'],
    ['Mise en place d\'outils', 'Le PRH68 vous accompagne dans la mise en œuvre d\'outils et d\'adaptations au quotidien, en lien avec votre réalité de terrain'],
    ['Dialogue avec les partenaires', 'Si besoin, le PRH68 vous accompagne dans vos échanges avec les parents et les partenaires'],
];
$acc3_items = [
    ['Sessions thématiques', 'Partagez vos questions, vos observations et votre réalité de terrain afin de co-construire des temps de sensibilisation adaptés à vos besoins'],
    ['Apports de connaissances', 'Selon vos besoins, des contenus peuvent être proposés sous différentes formes (supports visuels, vidéos, affiches…)'],
    ['Formats interactifs', 'Selon les objectifs, des outils ludiques, participatifs ou immersifs peuvent être proposés'],
    ['Temps d\'échange', 'Ces temps sont conçus comme des espaces ouverts à la discussion, favorisant les échanges et le partage d\'expériences'],
];
$acc4_items = [
    ['Prendre du recul', 'Si vous le souhaitez, changez de regard sur votre quotidien professionnel pour ouvrir de nouvelles pistes d\'action'],
    ['Découvrir d\'autres pratiques', 'Observez d\'autres lieux d\'accueil, sources d\'inspiration pour enrichir vos pratiques'],
    ['S\'inspirer de situations concrètes', 'Repérez des aménagements, des outils et des postures liés à l\'accueil inclusif'],
    ['Échanger entre professionnels', 'Rencontrez d\'autres équipes et partagez vos expériences'],
];
$acc5_items = [
    ['Prêt de matériel', 'Le PRH68 met à votre disposition du matériel éducatif pour vous permettre de le tester avant un éventuel investissement'],
    ['Mise en pratique', 'Le PRH68 vous accompagne dans la compréhension et l\'appropriation du matériel, en lien avec les besoins des enfants et votre réalité de terrain'],
];
$acc6_items = [
    ['Dynamique inclusive', 'Favoriser l\'échange et l\'implication de chacun.'],
    ['Conseil aux structures', 'Accompagner la mise en place de projets spécifiques d\'inclusion.'],
];
?>

<div class="pro-page">

    <!-- HERO -->
    <section class="pro-hero">
        <div class="pro-container">
            <div class="pro-hero-content">
                <h1><?php
                    $parts = explode( "l'inclusion", $hero_title, 2 );
                    if ( count( $parts ) === 2 ) {
                        echo esc_html( $parts[0] );
                        ?><span class="acc-def-trigger" tabindex="0" role="button" aria-expanded="false" aria-controls="acc-def-inclusion">l'inclusion<sup class="acc-def-star">*</sup></span><?php
                        echo esc_html( $parts[1] );
                    } else {
                        echo esc_html( $hero_title );
                    }
                ?></h1>
                <p><?= esc_html($hero_p1) ?></p>
                <a href="#nos-accompagnements" class="pro-hero-btn">
                    Voir les accompagnements
                    <img src="<?= esc_url($icons_base . 'fleche.svg') ?>" alt="" width="16" height="13"
                        class="pro-hero-btn-arrow">
                </a>
            </div>
        </div>
        <div class="pro-hero-wave">
            <svg viewBox="0 0 2880 70" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,35 C360,70 1080,0 1440,35 C1800,70 2520,0 2880,35 L2880,70 L0,70 Z" fill="#ffffff" />
            </svg>
        </div>
    </section>

    <!-- POURQUOI -->
    <section class="pro-why">
        <div class="pro-container">
            <h2 class="pro-section-title"><?= esc_html($why_title) ?></h2>
            <div class="pro-why-grid">

                <div class="pro-why-card pro-why-violet">
                    <div class="pro-why-icon">
                        <img src="<?= esc_url($icons_base . 'Engrenage-pro.svg') ?>" alt="" width="28" height="28">
                    </div>
                    <p><?= esc_html($why_c1) ?></p>
                </div>

                <div class="pro-why-card pro-why-turquoise">
                    <div class="pro-why-icon">
                        <img src="<?= esc_url($icons_base . 'Question-pro.svg') ?>" alt="" width="28" height="28">
                    </div>
                    <p><?= esc_html($why_c2) ?></p>
                </div>

                <div class="pro-why-card pro-why-orange">
                    <div class="pro-why-icon">
                        <img src="<?= esc_url($icons_base . 'Ampoule-pro.svg') ?>" alt="" width="28" height="28">
                    </div>
                    <p><?= esc_html($why_c3) ?></p>
                </div>

                <div class="pro-why-card pro-why-dark">
                    <div class="pro-why-icon">
                        <img src="<?= esc_url($icons_base . 'Ressources.svg') ?>" alt="" width="28" height="28">
                    </div>
                    <p><?= esc_html($why_c4) ?></p>
                </div>

            </div>
        </div>
    </section>

    <!-- NOUS VOUS PROPOSONS -->
    <section class="pro-nvp" id="nos-accompagnements">
        <div class="pro-container">
            <h2 class="pro-section-title"><?= esc_html($nvp_title) ?></h2>
            <p class="pro-section-sub"><?= esc_html($nvp_sub) ?></p>
            <div class="pro-nvp-grid">

                <!-- Colonne gauche : acc1, acc3, acc5 -->
                <div class="pro-nvp-col">

                    <details class="pro-acc-card">
                        <summary>
                            <div class="pro-acc-icon pro-icon-turquoise">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="22" height="22">
                                    <path
                                        d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                </svg>
                            </div>
                            <h3><?= esc_html($nvp_acc1_ttl) ?></h3>
                            <svg class="pro-acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="pro-acc-body">
                            <?php foreach ($acc1_items as $item): ?>
                                <div class="pro-item">
                                    <p class="pro-item-title"><?= esc_html($item[0]) ?></p>
                                    <p class="pro-item-desc"><?= esc_html($item[1]) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>

                    <details class="pro-acc-card">
                        <summary>
                            <div class="pro-acc-icon pro-icon-violet">
                                <img src="<?= esc_url($icons_base . 'Sensibilisation-pro.svg') ?>" alt="" width="22"
                                    height="22">
                            </div>
                            <h3><?= esc_html($nvp_acc3_ttl) ?></h3>
                            <svg class="pro-acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="pro-acc-body">
                            <?php foreach ($acc3_items as $item): ?>
                                <div class="pro-item">
                                    <p class="pro-item-title"><?= esc_html($item[0]) ?></p>
                                    <p class="pro-item-desc"><?= esc_html($item[1]) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>

                    <details class="pro-acc-card">
                        <summary>
                            <div class="pro-acc-icon pro-icon-orange">
                                <img src="<?= esc_url($icons_base . 'Sacs-pro.svg') ?>" alt="" width="22" height="22">
                            </div>
                            <h3><?= esc_html($nvp_acc5_ttl) ?></h3>
                            <svg class="pro-acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="pro-acc-body">
                            <?php foreach ($acc5_items as $item): ?>
                                <div class="pro-item">
                                    <p class="pro-item-title"><?= esc_html($item[0]) ?></p>
                                    <p class="pro-item-desc"><?= esc_html($item[1]) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>

                </div><!-- /col gauche -->

                <!-- Colonne droite : acc2, acc4, acc6 -->
                <div class="pro-nvp-col">

                    <details class="pro-acc-card">
                        <summary>
                            <div class="pro-acc-icon pro-icon-violet">
                                <img src="<?= esc_url($icons_base . 'Appui-pro.svg') ?>" alt="" width="22"
                                    height="22">
                            </div>
                            <h3><?= esc_html($nvp_acc2_ttl) ?></h3>
                            <svg class="pro-acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="pro-acc-body">
                            <?php foreach ($acc2_items as $item): ?>
                                <div class="pro-item">
                                    <p class="pro-item-title"><?= esc_html($item[0]) ?></p>
                                    <p class="pro-item-desc"><?= esc_html($item[1]) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>

                    <details class="pro-acc-card">
                        <summary>
                            <div class="pro-acc-icon pro-icon-orange">
                                <img src="<?= esc_url($icons_base . 'Accueil-jeunes-pro.svg') ?>" alt="" width="22"
                                    height="22">
                            </div>
                            <h3><?= esc_html($nvp_acc4_ttl) ?></h3>
                            <svg class="pro-acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="pro-acc-body">
                            <?php foreach ($acc4_items as $item): ?>
                                <div class="pro-item">
                                    <p class="pro-item-title"><?= esc_html($item[0]) ?></p>
                                    <p class="pro-item-desc"><?= esc_html($item[1]) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>

                    <details class="pro-acc-card">
                        <summary>
                            <div class="pro-acc-icon pro-icon-turquoise">
                                <img src="<?= esc_url($icons_base . 'Projets-pro.svg') ?>" alt="" width="22"
                                    height="22">
                            </div>
                            <h3><?= esc_html($nvp_acc6_ttl) ?></h3>
                            <svg class="pro-acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </summary>
                        <div class="pro-acc-body">
                            <?php foreach ($acc6_items as $item): ?>
                                <div class="pro-item">
                                    <p class="pro-item-title"><?= esc_html($item[0]) ?></p>
                                    <p class="pro-item-desc"><?= esc_html($item[1]) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>

                </div><!-- /col droite -->

            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="pro-cta">
        <div class="pro-container">
            <h2><?= esc_html($cta_title) ?></h2>
            <p><?= esc_html($cta_sub) ?></p>
            <a href="<?= esc_url(get_permalink(get_page_by_path('formulaire-contact'))) ?>" class="pro-cta-btn">
                <span><?= esc_html($cta_btn) ?></span>
                <img src="<?= esc_url(get_stylesheet_directory_uri() . '/icons/fleche.svg') ?>" alt="" width="18"
                    height="15" class="pro-cta-arrow">
            </a>
            <div class="pro-cta-contact">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24"
                        height="24">
                        <path
                            d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                    </svg>
                    <?= esc_html($cta_phone) ?>
                </span>

            </div>
        </div>
    </section>

</div><!-- /.pro-page -->

<?php
$def_text = $f('prh68_pro_def_text', "L'inclusion désigne l'accueil de tous les enfants, quels que soient leurs besoins, au sein des lieux d'accueil de droit commun, avec les adaptations nécessaires pour garantir leur participation, leur bien-être et leur développement.");
$def_note = $f('prh68_pro_def_note', "Cette définition est issue d'une réflexion partagée et en lien avec les réalités du terrain.");
?>
<!-- Tooltip définition "inclusion" -->
<div class="acc-def-box" id="acc-def-inclusion" role="tooltip" aria-hidden="true">
    <p><?php echo esc_html($def_text); ?></p>
    <?php if ($def_note): ?>
        <p class="acc-def-note"><?php echo esc_html($def_note); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>