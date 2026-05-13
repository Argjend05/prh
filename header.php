<!DOCTYPE html>
<html <?php language_attributes(); ?> style="opacity:0">

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

    <!-- Styles critiques inline : anti-FOUC
         L'attribut style="opacity:0" sur <html> (balise PHP ci-dessus) est la seule
         garantie absolue : il est là avant tout paint, avant tout JS. -->
    <style data-no-optimize="1" data-no-minify="1" data-rocket-defer="false">
        html { background-color: #fff; }

        #page-loader {
            position: fixed; inset: 0; z-index: 99999;
            background: #fff; display: flex; align-items: center; justify-content: center;
        }

        /* Styles critiques pour le rideau de transition (doivent être inline pour éviter le FOUC) */
        .prh-curtain {
            position: fixed; inset: 0; z-index: 100000; pointer-events: none;
            display: flex; align-items: center; justify-content: center;
        }
        .prh-curtain-svg {
            position: absolute; top: 0; left: 0; width: 100vw; height: 100vh; fill: #16152b;
        }
        .prh-curtain-content {
            position: relative; z-index: 1; opacity: 0;
        }
    </style>
    <noscript>
        <style>
            html { opacity: 1 !important; }
            #page-loader { display: none !important; }
        </style>
    </noscript>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Curseur personnalisé GSAP -->
<div id="prh-cursor" class="prh-cursor">
    <div class="prh-cursor-inner"></div>
</div>

<!-- Rideau SVG Morphing pour navigation -->
<div id="page-curtain" class="prh-curtain" aria-hidden="true">
    <svg class="prh-curtain-svg" viewBox="0 0 100 100" preserveAspectRatio="none">
        <path class="prh-curtain-path" d="M 0 100 V 100 Q 50 100 100 100 V 100 z" />
    </svg>
    <div class="prh-curtain-content">
        <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/logo.svg' ); ?>" alt="" width="80" height="86" class="curtain-logo">
    </div>
</div>

<!-- Page loader (premier chargement uniquement) -->
<div id="page-loader" aria-hidden="true">
    <div class="loader-inner">
        <div class="loader-glow"></div>
        <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/logo.svg' ); ?>"
             alt="" width="90" height="97" class="loader-logo">
        <p class="loader-brand">Pôle Ressources Handicap</p>
        <div class="loader-bar"><span class="loader-bar-fill"></span></div>
    </div>
</div>
<!-- Révélation instantanée et synchronisée -->
<script data-no-optimize="1" data-no-minify="1" data-cfasync="false" data-rocket-delay-js="false">
    if (sessionStorage.getItem('prh_nav')) {
        var loader = document.getElementById('page-loader');
        if (loader) loader.style.display = 'none';
        
        var path = document.querySelector('.prh-curtain-path');
        if (path) path.setAttribute('d', 'M 0 100 V 0 Q 50 0 100 0 V 100 z');
        
        var content = document.querySelector('.prh-curtain-content');
        if (content) content.style.opacity = '1';
    }
    document.documentElement.style.opacity = '';
</script>
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
