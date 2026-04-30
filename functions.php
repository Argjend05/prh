<?php
require_once get_stylesheet_directory() . '/inc/acf-fields.php';
require_once get_stylesheet_directory() . '/inc/scripts.php';


/* CPT — Résultats enquête observatoire */
add_action( 'init', function () {
    register_post_type( 'resultats_enquete', [
        'labels' => [
            'name'               => 'Résultats enquête',
            'singular_name'      => 'Résultat enquête',
            'add_new'            => 'Ajouter',
            'add_new_item'       => 'Ajouter un résultat',
            'edit_item'          => 'Modifier le résultat',
            'new_item'           => 'Nouveau résultat',
            'view_item'          => 'Voir le résultat',
            'search_items'       => 'Rechercher',
            'not_found'          => 'Aucun résultat trouvé',
            'not_found_in_trash' => 'Aucun résultat dans la corbeille',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-chart-bar',
        'supports'     => [ 'title' ],
        'has_archive'  => false,
        'rewrite'      => false,
    ] );
} );

add_action( 'phpmailer_init', function ( $phpmailer ) {
    $phpmailer->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ];
} );

/* Support thème */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    register_nav_menus( [
        'primary' => __( 'Menu principal', 'prh68' ),
    ] );
} );
