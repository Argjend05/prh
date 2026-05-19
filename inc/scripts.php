<?php
/* =======================================================
   STYLES & SCRIPTS
   ======================================================= */

add_action( 'wp_enqueue_scripts', 'prh68_enqueue_styles' );
function prh68_enqueue_styles() {
    $ver = wp_get_theme()->get( 'Version' );
    $uri = get_stylesheet_directory_uri();

    wp_enqueue_style( 'prh68-style', get_stylesheet_uri(), [], $ver );
    wp_enqueue_style( 'local-fonts', $uri . '/css/fonts.css', [ 'prh68-style' ], $ver );
    wp_enqueue_style( 'animations-style', $uri . '/css/animations.css', [ 'prh68-style' ], $ver );

    /* GSAP chargé sur tous les supports pour les transitions et animations de base */
    wp_enqueue_script( 'gsap',    'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',          [],        null, true );
    wp_enqueue_script( 'gsap-st', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js', [ 'gsap' ], null, true );
    
    /* Lenis : seulement sur desktop (pas utile sur mobile, conflit avec le scroll tactile natif) */
    if ( ! wp_is_mobile() ) {
        wp_enqueue_script( 'lenis',   'https://unpkg.com/lenis@1.1.20/dist/lenis.min.js',                   [],        null, true );
    }

    wp_enqueue_script( 'header-script',     $uri . '/js/header.js',     [],                  $ver, true );
    wp_enqueue_script( 'common-script',     $uri . '/js/common.js',     [ 'header-script' ], $ver, true );

    $anim_deps = [ 'common-script', 'gsap-st' ];
    if ( ! wp_is_mobile() ) {
        $anim_deps[] = 'lenis';
    }
    wp_enqueue_script( 'animations-script', $uri . '/js/animations.js', $anim_deps, $ver, true );

    if ( is_page_template( 'page-accueil.php' ) ) {
        wp_enqueue_style(  'accueil-style',  $uri . '/css/style-accueil.css', [ 'prh68-style' ],   $ver );
        wp_enqueue_script( 'accueil-script', $uri . '/js/accueil.js',         [ 'common-script' ], $ver, true );
    }

    if ( is_page_template( 'page-mentions-legales.php' ) ) {
        wp_enqueue_style( 'mentions-style', $uri . '/css/style-mentions-legales.css', [ 'prh68-style' ], $ver );
    }

    if ( is_page_template( 'page-professionnels.php' ) ) {
        wp_enqueue_style(  'pro-style',  $uri . '/css/style-professionnels.css', [ 'prh68-style' ],   $ver );
        wp_enqueue_script( 'pro-script', $uri . '/js/professionnels.js',         [ 'common-script' ], $ver, true );
    }

    if ( is_page_template( 'page-politique-confidentialite.php' ) ) {
        wp_enqueue_style( 'pc-style', $uri . '/css/style-politique-confidentialite.css', [ 'prh68-style' ], $ver );
    }

    if ( is_page_template( 'page-temoignages.php' ) ) {
        wp_enqueue_style(  'tem-style',  $uri . '/css/style-temoignages.css', [ 'prh68-style' ],   $ver );
        wp_enqueue_script( 'tem-script', $uri . '/js/temoignages.js',         [ 'common-script' ], $ver, true );
    }

    if ( is_page_template( 'page-outils-terrain.php' ) ) {
        wp_enqueue_style(  'ot-style',  $uri . '/css/style-outils-terrain.css', [ 'prh68-style' ],   $ver );
        wp_enqueue_script( 'ot-script', $uri . '/js/outils-terrain.js',         [ 'common-script' ], $ver, true );
    }

    if ( is_page_template( 'page-formulaire-temoignage.php' ) ) {
        wp_enqueue_style(  'ftem-style',  $uri . '/css/style-formulaire-temoignage.css', [ 'prh68-style' ],   $ver );
        wp_enqueue_script( 'ftem-script', $uri . '/js/formulaire-temoignage.js',         [ 'common-script' ], $ver, true );
    }

    if ( is_page_template( 'page-formulaire-outil.php' ) ) {
        wp_enqueue_style(  'fot-style',  $uri . '/css/style-formulaire-outil.css', [ 'prh68-style' ],   $ver );
        wp_enqueue_script( 'fot-script', $uri . '/js/formulaire-outil.js',         [ 'common-script' ], $ver, true );
    }
}

/* =======================================================
   HELPERS TEMPLATES
   ======================================================= */

if ( ! function_exists( 'prh68_acc_items' ) ) {
    function prh68_acc_items( $text, $defaults = [] ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", $text ) ) );
        $lines = ! empty( $lines ) ? $lines : $defaults;
        foreach ( $lines as $line ) {
            echo '<li>' . wp_kses_post( $line ) . '</li>';
        }
    }
}

if ( ! function_exists( 'ml_nl2br' ) ) {
    function ml_nl2br( $text ) {
        return implode( '<br>', array_map( 'esc_html', explode( "\n", $text ) ) );
    }
}

/* =======================================================
   STRUCTURED DATA (site name pour Google)
   ======================================================= */

add_action( 'wp_head', function () {
    $logo = get_stylesheet_directory_uri() . '/assets/img/logo.svg';

    /* Organization + WebSite : Organization.logo est LE signal que
       Google utilise pour l'image représentative du site (vignette
       SERP). Sans ça, Google scrape une image de la page — ici le
       logo Adapei — ce qui laisse croire à une dépendance. */
    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type' => 'Organization',
                '@id'   => home_url( '/#organization' ),
                'name'  => 'PRH68 – Pôle Ressources Handicap du Haut-Rhin',
                'url'   => home_url( '/' ),
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => $logo,
                ],
                'image' => $logo,
            ],
            [
                '@type'         => 'WebSite',
                '@id'           => home_url( '/#website' ),
                'name'          => 'PRH68',
                'alternateName' => 'Pôle Ressources Handicap du Haut-Rhin',
                'url'           => home_url( '/' ),
                'publisher'     => [ '@id' => home_url( '/#organization' ) ],
            ],
        ],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 1 );

/* =======================================================
   FAVICON
   ======================================================= */

/* Si une "Icône du site" est définie dans Customizer › Identité du
   site, on laisse WordPress générer le markup standard (toutes les
   tailles), c'est ce que Google reconnaît le mieux. Sinon, fallback
   maison CORRIGÉ : taille déclarée = taille réelle (192px, multiple
   de 48 → conforme aux exigences favicon de Google). */
if ( ! has_site_icon() ) {
    remove_action( 'wp_head', 'wp_site_icon' );

    add_action( 'wp_head', function () {
        $uri  = get_stylesheet_directory_uri();
        $png  = esc_url( $uri . '/assets/img/favicon-192.png' );
        $svg  = esc_url( $uri . '/assets/img/logo.svg' );
        echo '<link rel="icon" type="image/png" sizes="192x192" href="' . $png . '">' . "\n";
        echo '<link rel="icon" type="image/svg+xml" href="' . $svg . '">' . "\n";
        echo '<link rel="apple-touch-icon" sizes="192x192" href="' . $png . '">' . "\n";
    }, 1 );
}

/* =======================================================
   OPTIMISATIONS
   ======================================================= */

// 0. Defer les scripts lourds de plugins tiers (non bloquants pour le rendu)
add_filter( 'script_loader_tag', function( $tag, $handle ) {
    $defer = [
        'accessibility-onetap',   // 200 Ko — widget accessibilité
        'onetap-hotkeys-library', // dépendance du widget
    ];
    if ( in_array( $handle, $defer, true ) ) {
        // Remplace <script src="..."> par <script defer src="...">
        return str_replace( ' src=', ' defer src=', $tag );
    }
    return $tag;
}, 10, 2 );

// 1. Supprimer le CSS inutilisé
add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'classic-theme-styles' );
}, 100 );

// 2. Désactiver les Emojis natifs WP
add_action( 'init', function() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
} );


// 4. Supprimer TOUTES les requêtes Google Fonts
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
add_filter( 'neve_google_fonts_url', '__return_empty_string' );
add_filter( 'neve_get_fonts_url', '__return_empty_string' );

add_filter( 'style_loader_src', function ( $href ) {
    if ( strpos( $href, 'fonts.googleapis.com' ) !== false || strpos( $href, 'fonts.gstatic.com' ) !== false ) {
        return false;
    }
    return $href;
} );

// Désactiver Google Fonts dans Elementor via l'option DB (évite le @import dans le CSS généré)
add_filter( 'pre_option_elementor_google_font', '__return_zero' );

// Filet de sécurité : supprime les balises ET @import Google Fonts qui auraient échappé aux filtres
add_action( 'template_redirect', function () {
    if ( is_admin() ) return;
    ob_start( function ( $html ) {
        if ( ! is_string( $html ) ) return '';
        // Supprime les <link> Google Fonts
        $r = preg_replace( '/<link[^>]+href=["\'][^"\']*fonts\.googleapis\.com[^"\']*["\'][^>]*>/i', '', $html );
        if ( is_string( $r ) ) $html = $r;
        // Supprime uniquement la ligne @import Google Fonts (pas le bloc <style> entier)
        $r = preg_replace( '/@import\s+url\([\'"]?[^\'")]*fonts\.googleapis\.com[^\'")]*[\'"]?\)\s*;?/i', '', $html );
        if ( is_string( $r ) ) $html = $r;
        return $html;
    } );
} );
