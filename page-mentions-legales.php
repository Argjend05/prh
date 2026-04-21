<?php
/* Template Name: Mentions Légales */

$f = fn($key, $default = '') => get_field($key) ?: $default;

// Section 1 — Éditeur
$s1_title = $f('prh68_ml_s1_title', 'Éditeur du site');
$denom = $f('prh68_ml_denom', 'Association Marguerite Sinclair / PRH 68');
$forme = $f('prh68_ml_forme', 'Association de droit local (Bas-Rhin, Haut-Rhin et Moselle)');
$siege = $f('prh68_ml_siege', "2 avenue du Maréchal Joffre\nCS 11035, 68050 MULHOUSE Cedex 1");
$siret = $f('prh68_ml_siret', '77892930700032');
$dir_name = $f('prh68_ml_dir_name', 'François GILLET');
$dir_role = $f('prh68_ml_dir_role', 'Directeur Général');
$email = $f('prh68_ml_email', 'contact@prh68.fr');
$phone = $f('prh68_ml_phone', '03 89 32 81 50');

// Section 2 — Hébergement
$s2_title = $f('prh68_ml_s2_title', 'Hébergement');
$host_name = $f('prh68_ml_host_name', 'SAS DIGITALYZ');
$host_addr = $f('prh68_ml_host_addr', "255 route du Ségala\n48500 Massegros Causses Gorges");
$host_siret = $f('prh68_ml_host_siret', '849 858 261 RCS Mende');
$host_email = $f('prh68_ml_host_email', 'contact@digitalyz.fr');
$host_phone = $f('prh68_ml_host_phone', '09 72 16 91 58');

// Section 3 — Propriété intellectuelle
$s3_title = $f('prh68_ml_s3_title', 'Propriété intellectuelle');
$pi_text1 = $f('prh68_ml_pi_text1', "L'ensemble des contenus présents sur ce site (textes, images, graphismes, logos, vidéos, etc.) est la propriété exclusive de l'association Marguerite Sinclair.");
$pi_text2 = $f('prh68_ml_pi_text2', "Les éléments Canva utilisés sur ce site sont exploités sous licences conformément aux conditions d'utilisation de la plateforme Canva.");
$pi_warning = $f('prh68_ml_pi_warning', "Toute reproduction est interdite sans autorisation préalable écrite de l'association.");

// Section 4 — Responsabilité & Cookies
$s4_title = $f('prh68_ml_s4_title', 'Responsabilité & Cookies');
$resp_title = $f('prh68_ml_resp_title', 'Responsabilité');
$resp_text = $f('prh68_ml_resp_text', "L'association s'efforce de fournir des informations précises et à jour, mais décline toute responsabilité pour d'éventuelles inexactitudes ou omissions présentes sur le site.");
$cookies_title = $f('prh68_ml_cookies_title', 'Cookies');
$cookies_text = $f('prh68_ml_cookies_text', "Le site ne collecte ni n'utilise de cookies. Aucune donnée de navigation n'est enregistrée ou transmise à des tiers.");

// Section 5 — Droit applicable
$s5_title = $f('prh68_ml_s5_title', 'Droit applicable');
$droit_text = $f('prh68_ml_droit_text', "Le présent site et les présentes mentions légales sont soumis au droit français. En cas de litige, et à défaut de résolution amiable, la compétence exclusive est attribuée aux tribunaux français.");
$update_date = $f('prh68_ml_update_date', 'Juin 2025');

// Helpers définis dans functions.php

get_header();
?>

<div class="ml-page">

    <!-- ===================== HERO ===================== -->
    <section class="ml-hero">
        <div class="ml-container">
            <p class="ml-breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a>
                <span aria-hidden="true">›</span>
                Mentions Légales
            </p>
            <h1>Mentions Légales</h1>
            <div class="ml-hero-bar" aria-hidden="true"></div>
        </div>
    </section>

    <!-- ===================== CONTENU ===================== -->
    <main class="ml-main">
        <div class="ml-container">

            <!-- 1. Éditeur du site -->
            <section class="ml-section">
                <div class="ml-section-header">
                    <span class="ml-badge">1</span>
                    <h2><?php echo esc_html($s1_title); ?></h2>
                </div>
                <div class="ml-card ml-card-grid">
                    <dl class="ml-field">
                        <dt>Dénomination sociale</dt>
                        <dd><?php echo esc_html($denom); ?></dd>
                    </dl>
                    <dl class="ml-field">
                        <dt>Forme juridique</dt>
                        <dd><?php echo ml_nl2br($forme); ?></dd>
                    </dl>
                    <dl class="ml-field">
                        <dt>Siège social</dt>
                        <dd><?php echo ml_nl2br($siege); ?></dd>
                    </dl>
                    <dl class="ml-field">
                        <dt>SIRET</dt>
                        <dd><?php echo esc_html($siret); ?></dd>
                    </dl>
                    <dl class="ml-field">
                        <dt>Directeur de la publication</dt>
                        <dd><?php echo esc_html($dir_name); ?><span
                                class="ml-sub"><?php echo esc_html($dir_role); ?></span></dd>
                    </dl>
                    <dl class="ml-field">
                        <dt>Contact</dt>
                        <dd>
                            <a href="mailto:<?php echo esc_attr($email); ?>" class="ml-contact-link">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="14" height="14">
                                    <path
                                        d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                                <?php echo esc_html($email); ?>
                            </a>
                            <a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $phone)); ?>"
                                class="ml-contact-link">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="14" height="14">
                                    <path
                                        d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                </svg>
                                <?php echo esc_html($phone); ?>
                            </a>
                        </dd>
                    </dl>
                </div>
            </section>

            <!-- 2. Hébergement -->
            <section class="ml-section">
                <div class="ml-section-header">
                    <span class="ml-badge">2</span>
                    <h2><?php echo esc_html($s2_title); ?></h2>
                </div>
                <div class="ml-card ml-card-grid">
                    <dl class="ml-field">
                        <dt>Hébergeur</dt>
                        <dd><?php echo esc_html($host_name); ?></dd>
                    </dl>
                    <dl class="ml-field">
                        <dt>Adresse</dt>
                        <dd><?php echo ml_nl2br($host_addr); ?></dd>
                    </dl>
                    <dl class="ml-field">
                        <dt>SIRET / RCS</dt>
                        <dd><?php echo esc_html($host_siret); ?></dd>
                    </dl>
                    <dl class="ml-field">
                        <dt>Contact</dt>
                        <dd>
                            <a href="mailto:<?php echo esc_attr($host_email); ?>" class="ml-contact-link">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="14" height="14">
                                    <path
                                        d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                                <?php echo esc_html($host_email); ?>
                            </a>
                            <a href="tel:<?php echo esc_attr(preg_replace('/\s/', '', $host_phone)); ?>"
                                class="ml-contact-link">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="14" height="14">
                                    <path
                                        d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                </svg>
                                <?php echo esc_html($host_phone); ?>
                            </a>
                        </dd>
                    </dl>
                </div>
            </section>

            <!-- 3. Propriété intellectuelle -->
            <section class="ml-section">
                <div class="ml-section-header">
                    <span class="ml-badge">3</span>
                    <h2><?php echo esc_html($s3_title); ?></h2>
                </div>
                <div class="ml-card ml-card-text">
                    <p class="ml-text-icon">
                        <span class="ml-icon-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16"
                                height="16">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                            </svg>
                        </span>
                        <span><?php echo esc_html($pi_text1); ?></span>
                    </p>
                    <p><?php echo esc_html($pi_text2); ?></p>
                    <div class="ml-warning-box">
                        <span class="ml-icon-circle ml-icon-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16"
                                height="16">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM4 12c0-4.42 3.58-8 8-8 1.85 0 3.55.63 4.9 1.69L5.69 16.9C4.63 15.55 4 13.85 4 12zm8 8c-1.85 0-3.55-.63-4.9-1.69l11.21-11.21C19.37 8.45 20 10.15 20 12c0 4.42-3.58 8-8 8z" />
                            </svg>
                        </span>
                        <span><?php echo esc_html($pi_warning); ?></span>
                    </div>
                </div>
            </section>

            <!-- 4. Responsabilité & Cookies -->
            <section class="ml-section">
                <div class="ml-section-header">
                    <span class="ml-badge">4</span>
                    <h2><?php echo esc_html($s4_title); ?></h2>
                </div>
                <div class="ml-two-col">
                    <div class="ml-card ml-card-text">
                        <div class="ml-card-icon-title">
                            <span class="ml-icon-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="16" height="16">
                                    <path
                                        d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l6 2.67V11c0 3.72-2.56 7.2-6 8.27-3.44-1.07-6-4.55-6-8.27V7.67L12 5z" />
                                </svg>
                            </span>
                            <h3><?php echo esc_html($resp_title); ?></h3>
                        </div>
                        <p><?php echo esc_html($resp_text); ?></p>
                    </div>
                    <div class="ml-card ml-card-text">
                        <div class="ml-card-icon-title">
                            <span class="ml-icon-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    width="16" height="16">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </span>
                            <h3><?php echo esc_html($cookies_title); ?></h3>
                        </div>
                        <p><?php echo esc_html($cookies_text); ?></p>
                    </div>
                </div>
            </section>

            <!-- 5. Droit applicable -->
            <section class="ml-section">
                <div class="ml-section-header">
                    <span class="ml-badge">5</span>
                    <h2><?php echo esc_html($s5_title); ?></h2>
                </div>
                <div class="ml-card ml-card-text">
                    <p class="ml-text-icon">
                        <span class="ml-icon-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16"
                                height="16">
                                <path
                                    d="M13 7.83c.85-.3 1.53-.98 1.83-1.83H18l-3 7c0 1.66 1.57 3 3.5 3s3.5-1.34 3.5-3l-3-7h2V4h-6.17C14.42 2.83 13.3 2 12 2s-2.42.83-2.83 2H3v2h2l-3 7c0 1.66 1.57 3 3.5 3s3.5-1.34 3.5-3L6 6h3.17c.3.85.98 1.53 1.83 1.83V19H2v2h20v-2h-9V7.83zM20.5 16h-5l2.5-5.8 2.5 5.8zM8.5 16h-5l2.5-5.8 2.5 5.8zM12 6c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z" />
                            </svg>
                        </span>
                        <span><?php echo esc_html($droit_text); ?></span>
                    </p>
                </div>
            </section>

            <p class="ml-update-date">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                    <path
                        d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
                </svg>
                Dernière mise à jour : <?php echo esc_html($update_date); ?>
            </p>

        </div>
    </main>

</div>

<?php get_footer(); ?>