<?php
require_once get_stylesheet_directory() . '/inc/acf-fields.php';
require_once get_stylesheet_directory() . '/inc/scripts.php';

/* Support thème */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    register_nav_menus( [
        'primary' => __( 'Menu principal', 'prh68' ),
    ] );
} );
