<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <meta name="google-site-verification" content="bMPN8fsSVj0T_6AgGBnv_fZ5vfZJkiFBMrzjemMfNOc" />
    <?php
    $prh_meta_description = '';
    if ( is_singular() ) {
        $prh_meta_description = get_the_excerpt();
    }
    if ( empty( $prh_meta_description ) ) {
        $prh_meta_description = get_bloginfo( 'description' );
    }
    if ( empty( $prh_meta_description ) ) {
        $prh_meta_description = 'Le PRH 68 (Pôle Ressources Handicap du Haut-Rhin) vous accompagne pour l\'inclusion des enfants et jeunes en situation de handicap. Accueil, écoute, conseil et orientation.';
    }
    ?>
    <meta name="description" content="<?php echo esc_attr( wp_strip_all_tags( $prh_meta_description ) ); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Header en dehors du .wrapper pour que position:sticky fonctionne -->
<header class="prh-header" id="prh-header">
    <a class="prh-skip-link show-on-focus" href="#content"><?php esc_html_e( 'Skip to content', 'prh68' ); ?></a>

    <div class="prh-header-inner">

        <!-- Logo -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="prh-header-logo" aria-label="Accueil">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/logo-header.png"
                 alt="PRH68 – Pôle Ressources Handicap">
        </a>

        <!-- Nav desktop -->
        <?php
        wp_nav_menu( [
            'theme_location' => 'primary',
            'container'       => 'nav',
            'container_class' => 'prh-nav',
            'menu_class'      => 'prh-nav-list',
            'fallback_cb'     => false,
            'depth'           => 1,
            'walker'          => null,
        ] );
        ?>

        <!-- Burger mobile -->
        <button class="prh-burger" aria-label="Menu" aria-expanded="false" aria-controls="prh-mobile-nav">
            <span></span><span></span><span></span>
        </button>

    </div>

    <!-- Barre décorative -->
    <div class="prh-header-bar"></div>
</header>

<!-- Nav mobile (overlay) -->
<nav id="prh-mobile-nav" class="prh-mobile-nav" aria-hidden="true">
    <?php
    wp_nav_menu( [
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'prh-mobile-nav-list',
        'fallback_cb'    => false,
        'depth'          => 1,
    ] );
    ?>
</nav>

<div class="wrapper">
    <main id="content" class="neve-main">
