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

    <?php
    $prh_og_title = is_singular() ? get_the_title() : get_bloginfo( 'name' );
    $prh_og_url   = is_singular() ? get_permalink() : home_url( '/' );
    $uri = get_stylesheet_directory_uri();
    ?>
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="PRH68">
    <meta property="og:title"       content="<?php echo esc_attr( $prh_og_title ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( $prh_meta_description ) ); ?>">
    <meta property="og:url"         content="<?php echo esc_url( $prh_og_url ); ?>">
    <meta property="og:image"       content="<?php echo esc_url( $uri . '/logo-header.webp' ); ?>">
    <meta property="og:image:width"  content="350">
    <meta property="og:image:height" content="140">
    <meta name="twitter:card"        content="summary">
    <meta name="twitter:title"       content="<?php echo esc_attr( $prh_og_title ); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr( wp_strip_all_tags( $prh_meta_description ) ); ?>">

    <link rel="preload" href="<?php echo esc_url( $uri . '/fonts/montserrat-700-latin.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo esc_url( $uri . '/fonts/ubuntu-400-latin.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo esc_url( $uri . '/logo-header.webp' ); ?>" as="image" type="image/webp" fetchpriority="high">

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
            <?php $uri = get_stylesheet_directory_uri(); ?>
            <picture>
                <source srcset="<?php echo esc_url( $uri . '/logo-header.avif' ); ?>" type="image/avif">
                <source srcset="<?php echo esc_url( $uri . '/logo-header.webp' ); ?>" type="image/webp">
                <img src="<?php echo esc_url( $uri . '/logo-header.webp' ); ?>"
                     alt="PRH68 – Pôle Ressources Handicap" width="350" height="140"
                     fetchpriority="high" decoding="async">
            </picture>
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
