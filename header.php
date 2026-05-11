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
    <meta property="og:image"       content="<?php echo esc_url( $uri . '/assets/img/logo-header.webp' ); ?>">
    <meta property="og:image:width"  content="350">
    <meta property="og:image:height" content="140">
    <meta name="twitter:card"        content="summary">
    <meta name="twitter:title"       content="<?php echo esc_attr( $prh_og_title ); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr( wp_strip_all_tags( $prh_meta_description ) ); ?>">

    <link rel="preload" href="<?php echo esc_url( $uri . '/assets/fonts/ubuntu-400-latin.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo esc_url( $uri . '/assets/fonts/ubuntu-700-latin.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo esc_url( $uri . '/assets/fonts/montserrat-700-latin.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo esc_url( $uri . '/assets/fonts/montserrat-800-latin.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo esc_url( $uri . '/assets/img/logo-header.webp' ); ?>" as="image" type="image/webp" fetchpriority="high">
    <?php if ( is_page_template( 'page-accueil.php' ) ) : ?>
    <link rel="preload" href="<?php echo esc_url( $uri . '/assets/img/banniere-accueil.avif' ); ?>" as="image" type="image/avif" fetchpriority="high">
    <?php endif; ?>

    <!-- Styles critiques inline : masque le contenu de manière robuste -->
    <style>
        /* Couleur de fond par défaut (évite le fond du navigateur) */
        html { background-color: #fff; }
        
        /* Masque tout le contenu direct du body SAUF les loaders/scripts quand la classe prh-loading est là */
        html.prh-loading body > *:not(#page-loader):not(#page-curtain):not(script):not(style) {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        /* Styles de base du loader */
        #page-loader {
            position: fixed; inset: 0; z-index: 99999; 
            background: #fff; display: flex; align-items: center; justify-content: center;
        }

        /* --- En cas de navigation interne --- */
        html.prh-navigating { background-color: #16152b; } /* Couleur du rideau */
        html.prh-navigating #page-loader { display: none !important; }
        
        /* On force le rideau à couvrir tout de suite sans transition */
        html.prh-navigating #page-curtain {
            position: fixed; inset: 0; z-index: 100000; background: #16152b;
            transform: translateY(0) !important; transition: none !important;
        }
    </style>
    <script>
        (function(){
            document.documentElement.classList.add('prh-loading');
            if(sessionStorage.getItem('prh_nav')){
                document.documentElement.classList.add('prh-navigating');
            }
        }());
    </script>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Page loader -->
<div id="page-loader" aria-hidden="true">
    <div class="loader-inner">
        <div class="loader-glow"></div>
        <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/logo.svg' ); ?>"
             alt="" width="90" height="97" class="loader-logo">
        <p class="loader-brand">Pôle Ressources Handicap</p>
        <div class="loader-bar"><span class="loader-bar-fill"></span></div>
    </div>
</div>
<div id="page-curtain" aria-hidden="true">
    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/logo.svg' ); ?>"
         alt="" width="80" height="86" class="curtain-logo">
</div>
<script>setTimeout(function(){var l=document.getElementById('page-loader');if(l&&!l.classList.contains('loader-out')){l.style.transition='none';l.remove();}},5000);</script>

<!-- Header en dehors du .wrapper pour que position:sticky fonctionne -->
<header class="prh-header" id="prh-header">
    <a class="prh-skip-link show-on-focus" href="#content"><?php esc_html_e( 'Skip to content', 'prh68' ); ?></a>

    <div class="prh-header-inner">

        <!-- Logo -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="prh-header-logo" aria-label="Accueil">
            <?php $uri = get_stylesheet_directory_uri(); ?>
            <picture>
                <source srcset="<?php echo esc_url( $uri . '/assets/img/logo-header.avif' ); ?>" type="image/avif">
                <source srcset="<?php echo esc_url( $uri . '/assets/img/logo-header.webp' ); ?>" type="image/webp">
                <img src="<?php echo esc_url( $uri . '/assets/img/logo-header.webp' ); ?>"
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
            'depth'           => 2,
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
        'depth'          => 2,
    ] );
    ?>
</nav>

<div class="wrapper">
    <main id="content" class="neve-main">
