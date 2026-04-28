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

    wp_enqueue_script( 'header-script', $uri . '/js/header.js', [],                  $ver, true );
    wp_enqueue_script( 'common-script', $uri . '/js/common.js', [ 'header-script' ], $ver, true );
    wp_enqueue_script( 'animations-script', $uri . '/js/animations.js', [ 'common-script' ], $ver, true );

    if ( is_page_template( 'page-accueil.php' ) ) {
        wp_enqueue_style(  'accueil-style',  $uri . '/css/style-accueil.css',   [ 'prh68-style' ],   $ver );
        wp_enqueue_script( 'accueil-script', $uri . '/js/accueil.js',           [ 'common-script' ], $ver, true );
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
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => 'PRH68',
        'alternateName' => 'Pôle Ressources Handicap du Haut-Rhin',
        'url'      => home_url( '/' ),
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 1 );

/* =======================================================
   FAVICON
   ======================================================= */

// Empêche WordPress d'afficher son propre favicon
remove_action( 'wp_head', 'wp_site_icon' );

add_action( 'wp_head', function () {
    $uri = get_stylesheet_directory_uri();
    $png = esc_url( $uri . '/logos/favicon.png' );
    $svg = esc_url( $uri . '/logo.svg' );
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . $png . '">' . "\n";
    echo '<link rel="icon" type="image/svg+xml" href="' . $svg . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . $png . '">' . "\n";
}, 1 );

/* =======================================================
   OPTIMISATIONS
   ======================================================= */

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
