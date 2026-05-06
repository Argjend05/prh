<?php
require_once get_stylesheet_directory() . '/inc/acf-fields.php';
require_once get_stylesheet_directory() . '/inc/scripts.php';
require_once get_stylesheet_directory() . '/inc/cpt.php';

/* ── Supprime les items de menu dropdown sans enfants ─── */
add_filter( 'wp_nav_menu_objects', function ( $items, $args ) {
    // Collecte les IDs des items qui ont au moins un enfant
    $parents_with_children = [];
    foreach ( $items as $item ) {
        $pid = (int) $item->menu_item_parent;
        if ( $pid > 0 ) {
            $parents_with_children[ $pid ] = true;
        }
    }

    return array_values( array_filter( $items, function ( $item ) use ( $parents_with_children ) {
        // Garde tous les enfants tels quels
        if ( (int) $item->menu_item_parent !== 0 ) return true;

        // Pour les top-level : supprime si URL vide/# ET aucun enfant
        $no_real_url = in_array( trim( $item->url ), [ '#', '', 'javascript:void(0)' ], true );
        $no_children = ! isset( $parents_with_children[ (int) $item->ID ] );

        return ! ( $no_real_url && $no_children );
    } ) );
}, 10, 2 );

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
