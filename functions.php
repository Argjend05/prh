<?php
require_once get_stylesheet_directory() . '/inc/acf-fields.php';
require_once get_stylesheet_directory() . '/inc/scripts.php';

// Nettoyage unique du cache Elementor 
if ( ! get_option( 'prh68_elementor_cleanup_done' ) ) {
    add_action( 'init', function () {
        $dir = WP_CONTENT_DIR . '/uploads/elementor';
        if ( is_dir( $dir ) ) {
            foreach ( new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::CHILD_FIRST
            ) as $file ) {
                $file->isDir() ? rmdir( $file->getRealPath() ) : unlink( $file->getRealPath() );
            }
            rmdir( $dir );
        }
        update_option( 'prh68_elementor_cleanup_done', '1' );
    } );
}

/* Support thème */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    register_nav_menus( [
        'primary' => __( 'Menu principal', 'prh68' ),
    ] );
} );
