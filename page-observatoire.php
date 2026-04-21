<?php
/* Template Name: Observatoire Custom */

// Récupère un champ ACF avec valeur par défaut
$m = fn($key, $default = '') => get_field($key) ?: $default;

// Affiche un texte multi-lignes sous forme de liste <ul>
function prh68_bullet_list($text)
{
    $items = array_filter(array_map('trim', explode("\n", $text)));
    if (empty($items))
        return;
    echo '<ul class="obs-mission-bullets">';
    foreach ($items as $item) {
        echo '<li>' . esc_html($item) . '</li>';
    }
    echo '</ul>';
}

// Hero
$hero_title = $m('prh68_hero_title', "Observatoire départemental de l'inclusion");
$hero_subtitle = $m('prh68_hero_subtitle', "Améliorer l'accueil de tous les enfants au sein des lieux collectifs.");
$hero_btn = $m('prh68_hero_btn', 'Voir les questionnaires');

// Mission
$mission_title = $m('prh68_mission_title', 'Notre Ambition');
$mission_subtitle = $m('prh68_mission_subtitle', "Partir des besoins concrets du terrain – Enfants, Parents, Professionnels – pour éclairer et faire évoluer les pratiques au regard des réalités du quotidien");
$m1_title = $m('prh68_m1_title', 'Enfants');
$m1_desc = $m('prh68_m1_desc', "Prendre en compte leurs besoins\nDonner et valoriser leur parole\nMettre en lumière ce qui facilite leur inclusion");
$m2_title = $m('prh68_m2_title', 'Parents');
$m2_desc = $m('prh68_m2_desc', "Recueillir leurs besoins\nIdentifier les freins rencontrés\nMettre en évidence leurs attentes");
$m3_title = $m('prh68_m3_title', 'Professionnels');
$m3_desc = $m('prh68_m3_desc', "Repérer les enjeux\nIdentifier les freins à l'inclusion\nValoriser les leviers et les initiatives réussies");

// Participez
$part_title = $m('prh68_part_title', 'Participez à notre démarche');
$part_subtitle = $m('prh68_part_subtitle', "Votre voix compte ! Partagez votre expérience et contribuez à améliorer l'accueil inclusif dans notre département.");
$pro_title = $m('prh68_part_pro_title', 'Professionnels');
$pro_desc = $m('prh68_part_pro_desc', "Responsables d'accueils collectifs 0 - 17 ans et assistantes maternelles");
$par_title = $m('prh68_part_par_title', 'Parents');
$par_desc = $m('prh68_part_par_desc', "Vous êtes parent d'enfants âgés de 0 à 17 ans ? Faites-nous part de vos besoins et attentes");
$url_pro = $m('prh68_url_professionnels', '#');
$url_parents = $m('prh68_url_parents', '#');

// Cycle
$cycle_title = $m('prh68_cycle_title', 'Les étapes clés');
$cycle_subtitle = $m('prh68_cycle_subtitle', 'Cette enquête auprès des familles et des professionnels sera reconduite chaque année afin de pouvoir co-construire des réponses adaptées et durables.');
$c = [];
$defaults = [
    1 => ['AVRIL - MAI', 'Envoi des questionnaires aux parents', '', 'Envoi des questionnaires aux lieux d\'accueil', '', 'Remplissage des questionnaires', ''],
    2 => ['JUILLET - NOVEMBRE', 'Traitement des données récoltées', '', 'Analyse territoriale (avec les acteurs locaux)', '', 'Analyse départementale', ''],
    3 => ['FEVRIER', 'Restitution public', '', 'Vision territoriale (besoin, freins, réussites)', '', 'Mise en lien des actions & initiatives inclusives', ''],
];
for ($n = 1; $n <= 3; $n++) {
    $d = $defaults[$n];
    $c[$n] = [
        'title' => $m("prh68_c{$n}_title", $d[0]),
        'i1_title' => $m("prh68_c{$n}_i1_title", $d[1]),
        'i1_desc' => $m("prh68_c{$n}_i1_desc", $d[2]),
        'i2_title' => $m("prh68_c{$n}_i2_title", $d[3]),
        'i2_desc' => $m("prh68_c{$n}_i2_desc", $d[4]),
        'i3_title' => $m("prh68_c{$n}_i3_title", $d[5]),
        'i3_desc' => $m("prh68_c{$n}_i3_desc", $d[6]),
    ];
}

get_header();
?>

<div class="obs-wrapper">

    <!-- ===================== HERO ===================== -->
    <section class="obs-hero">

        <!-- Particules flottantes -->
        <div class="obs-hero-particles" aria-hidden="true">
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
        </div>

        <div class="obs-container obs-hero-inner">
            <div class="obs-hero-content">
                <h1><?php echo esc_html($hero_title); ?></h1>
                <p><?php echo esc_html($hero_subtitle); ?></p>
                <a href="#questionnaires" class="obs-btn-violet">
                    <span><?php echo esc_html($hero_btn); ?></span>
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/icons/fleche.svg'); ?>" alt=""
                        width="18" height="15" class="obs-btn-arrow">
                </a>
            </div>
            <div class="obs-hero-deco">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/Carte.svg" alt="Carte du Haut-Rhin"
                    class="obs-hero-carte">
            </div>
        </div>

        <div class="obs-hero-wave">
            <svg viewBox="0 0 2880 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,40 C360,80 1080,0 1440,40 C1800,80 2520,0 2880,40 L2880,80 L0,80 Z" fill="#ffffff" />
            </svg>
        </div>
    </section>

    <!-- ===================== MISSION ===================== -->
    <section class="obs-mission" id="mission">
        <div class="obs-container">
            <h2 class="obs-section-title"><?php echo esc_html($mission_title); ?></h2>
            <p class="obs-section-subtitle"><?php echo esc_html($mission_subtitle); ?></p>

            <div class="obs-mission-grid">

                <div class="obs-mission-card bg-violet">
                    <div class="obs-mission-icon-wrap">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/Enfant.svg" alt="Enfants"
                            width="36" height="36">
                    </div>
                    <h3><?php echo esc_html($m1_title); ?></h3>
                    <?php prh68_bullet_list($m1_desc); ?>
                </div>

                <div class="obs-mission-card bg-turquoise">
                    <div class="obs-mission-icon-wrap">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/Parents.svg" alt="Parents"
                            width="36" height="36">
                    </div>
                    <h3><?php echo esc_html($m2_title); ?></h3>
                    <?php prh68_bullet_list($m2_desc); ?>
                </div>

                <div class="obs-mission-card bg-orange">
                    <div class="obs-mission-icon-wrap">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/Professionnels.svg"
                            alt="Professionnels" width="36" height="36">
                    </div>
                    <h3><?php echo esc_html($m3_title); ?></h3>
                    <?php prh68_bullet_list($m3_desc); ?>
                </div>

            </div>
    </section>

    <!-- ===================== PARTICIPEZ ===================== -->
    <section class="obs-participez" id="questionnaires">
        <div class="obs-container">
            <h2 class="obs-section-title"><?php echo esc_html($part_title); ?></h2>
            <p class="obs-section-subtitle"><?php echo esc_html($part_subtitle); ?></p>

            <div class="obs-participez-grid">

                <div class="obs-participez-card border-orange">
                    <div class="obs-part-icon-wrap bg-orange">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/Professionnels.svg"
                            alt="Professionnels" width="32" height="32">
                    </div>
                    <h3><?php echo esc_html($pro_title); ?></h3>
                    <p><?php echo esc_html($pro_desc); ?></p>
                    <ul class="obs-part-list">
                        <li><span class="check-orange">✓</span> Durée : 5 - 7 minutes</li>
                        <li><span class="check-orange">✓</span> Anonyme et confidentiel</li>
                        <li><span class="check-orange">✓</span> Accessible 24/7</li>
                    </ul>
                    <a href="<?php echo esc_url($url_pro); ?>" class="obs-btn-orange">
                        <span>Répondre au Questionnaire</span>
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/icons/fleche.svg'); ?>" alt=""
                            width="18" height="15" class="obs-btn-arrow-right">
                    </a>
                </div>

                <div class="obs-participez-card border-turquoise">
                    <div class="obs-part-icon-wrap bg-turquoise">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/icons/Parents.svg" alt="Parents"
                            width="32" height="32">
                    </div>
                    <h3><?php echo esc_html($par_title); ?></h3>
                    <p><?php echo esc_html($par_desc); ?></p>
                    <ul class="obs-part-list">
                        <li><span class="check-turquoise">✓</span> Durée : 3 - 5 minutes</li>
                        <li><span class="check-turquoise">✓</span> Anonyme et confidentiel</li>
                        <li><span class="check-turquoise">✓</span> Accessible 24/7</li>
                    </ul>
                    <a href="<?php echo esc_url($url_parents); ?>" class="obs-btn-turquoise">
                        <span>Répondre au Questionnaire</span>
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/icons/fleche.svg'); ?>" alt=""
                            width="18" height="15" class="obs-btn-arrow-right">
                    </a>
                </div>

            </div>

            <div class="obs-security-notice">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#4b4b8b" width="18" height="18">
                    <path
                        d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
                </svg>
                Vos données sont protégées et utilisées uniquement dans le cadre de cette étude
            </div>
        </div>
    </section>

    <!-- ===================== CYCLE ===================== -->
    <section class="obs-cycle-section" id="cycle">
        <div class="obs-container">
            <h2 class="obs-section-title"><?php echo esc_html($cycle_title); ?></h2>
            <p class="obs-section-subtitle"><?php echo esc_html($cycle_subtitle); ?></p>

            <?php
            $icons_url = get_stylesheet_directory_uri() . '/icons/';
            $cycle_cfg = [
                1 => ['color' => 'orange', 'icon' => $icons_url . 'Questionnaire.svg', 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v1h16v-1c0-2.66-5.33-4-8-4z"/></svg><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>'],
                2 => ['color' => 'turquoise', 'icon' => $icons_url . 'Engrenage.svg', 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><circle cx="12" cy="4" r="2"/><path d="M19 13v-2c-1.54.02-3.09-.75-4.07-1.83l-1.29-1.43c-.17-.19-.38-.34-.61-.45H13c-.35-.2-.75-.3-1.19-.26C10.76 7.11 10 8.04 10 9.09V15c0 1.1.9 2 2 2h5v5h2v-5.5c0-1.1-.9-2-2-2h-3v-3.45c1.29 1.07 3.25 1.94 5 1.95zm-9 7c-1.1 0-2-.9-2-2v-6H6v6c0 2.21 1.79 4 4 4h4v-2h-4z"/></svg>'],
                3 => ['color' => 'violet', 'icon' => $icons_url . 'Analyse.svg', 'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M20.5 3l-.16.03L15 5.1 9 3 3.36 4.9c-.21.07-.36.25-.36.48V20.5c0 .28.22.5.5.5l.16-.03L9 18.9l6 2.1 5.64-1.9c.21-.07.36-.25.36-.48V3.5c0-.28-.22-.5-.5-.5zM15 19l-6-2.11V5l6 2.11V19z"/></svg><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-12.5c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 5.5c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>'],
            ];
            ?>

            <div class="obs-cycle-grid">
                <?php foreach ($cycle_cfg as $n => $cfg):
                    $col = $cfg['color'];
                    $icons = array_values(array_filter(explode('</svg>', $cfg['svg']), 'strlen'));
                    if ($n > 1): ?>
                        <div class="obs-cycle-arrow">→</div><?php endif; ?>
                    <div class="obs-cycle-card cycle-card-<?php echo $col; ?>">
                        <div class="obs-cycle-num bg-<?php echo $col; ?>">
                            <img src="<?php echo esc_url($cfg['icon']); ?>" alt="" width="26" height="26">
                        </div>
                        <h3 class="color-<?php echo $col; ?>"><?php echo esc_html($c[$n]['title']); ?></h3>
                        <ul class="obs-cycle-list">
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <li>
                                    <span class="cycle-icon-bg cicbg-<?php echo $col; ?>">
                                        <?php echo ($icons[$i - 1] ?? '') . '</svg>'; ?>
                                    </span>
                                    <div>
                                        <strong><?php echo esc_html($c[$n]["i{$i}_title"]); ?></strong>
                                        <p><?php echo esc_html($c[$n]["i{$i}_desc"]); ?></p>
                                    </div>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

</div><!-- .obs-wrapper -->

<?php get_footer(); ?>