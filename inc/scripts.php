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

    wp_enqueue_script( 'header-script', $uri . '/js/header.js', [],                  $ver, true );
    wp_enqueue_script( 'common-script', $uri . '/js/common.js', [ 'header-script' ], $ver, true );

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
   FAVICON
   ======================================================= */

// Empêche WordPress d'afficher son propre favicon (logo WP par défaut)
remove_action( 'wp_head', 'wp_site_icon' );

add_action( 'wp_head', function () {
    $svg = esc_url( get_stylesheet_directory_uri() . '/logo.svg' );
    echo '<link rel="icon" type="image/svg+xml" href="' . $svg . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . $svg . '">' . "\n";
}, 1 );
