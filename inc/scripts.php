<?php
/* =======================================================
   STYLES & SCRIPTS
   ======================================================= */

add_action( 'wp_enqueue_scripts', 'prh68_enqueue_styles' );
function prh68_enqueue_styles() {
    $ver = wp_get_theme()->get( 'Version' );
    $uri = get_stylesheet_directory_uri();

    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',  get_stylesheet_uri(), [ 'parent-style' ], $ver );
    wp_enqueue_style( 'local-fonts',  $uri . '/css/fonts.css', [ 'parent-style' ], $ver );

    wp_enqueue_script( 'header-script', $uri . '/js/header.js', [],                  $ver, true );
    wp_enqueue_script( 'common-script', $uri . '/js/common.js', [ 'header-script' ], $ver, true );

    if ( is_page_template( 'page-accueil.php' ) ) {
        wp_enqueue_style(  'accueil-style',  $uri . '/css/style-accueil.css',   [ 'child-style' ],    $ver );
        wp_enqueue_script( 'accueil-script', $uri . '/js/accueil.js',           [ 'common-script' ], $ver, true );
    }

    if ( is_page_template( 'page-mentions-legales.php' ) ) {
        wp_enqueue_style( 'mentions-style', $uri . '/css/style-mentions-legales.css', [ 'child-style' ], $ver );
    }

    if ( is_page_template( 'page-professionnels.php' ) ) {
        wp_enqueue_style(  'pro-style',  $uri . '/css/style-professionnels.css', [ 'child-style' ],    $ver );
        wp_enqueue_script( 'pro-script', $uri . '/js/professionnels.js',         [ 'common-script' ], $ver, true );
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
            echo '<li>' . esc_html( $line ) . '</li>';
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

add_action( 'wp_head', function () {
    $url = esc_url( get_stylesheet_directory_uri() . '/logo.svg' );
    echo '<link rel="icon" type="image/svg+xml" href="' . $url . '">' . "\n";
} );
