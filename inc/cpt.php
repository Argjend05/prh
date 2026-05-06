<?php
/* =======================================================
   CUSTOM POST TYPES
   ======================================================= */

/* ── Témoignages ──────────────────────────────────────── */
add_action( 'init', function () {
    register_post_type( 'temoignage', [
        'labels' => [
            'name'               => 'Témoignages',
            'singular_name'      => 'Témoignage',
            'menu_name'          => 'Témoignages',
            'add_new'            => 'Ajouter',
            'add_new_item'       => 'Ajouter un témoignage',
            'edit_item'          => 'Modifier le témoignage',
            'new_item'           => 'Nouveau témoignage',
            'view_item'          => 'Voir le témoignage',
            'search_items'       => 'Rechercher un témoignage',
            'not_found'          => 'Aucun témoignage trouvé',
            'not_found_in_trash' => 'Aucun témoignage dans la corbeille',
            'all_items'          => 'Tous les témoignages',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-format-quote',
        'menu_position' => 21,
        'supports'     => [ 'title' ],
        'has_archive'  => false,
        'rewrite'      => false,
    ] );
} );

/* ── Colonnes admin custom pour Témoignages ──────────── */
add_filter( 'manage_temoignage_posts_columns', function ( $cols ) {
    $new = [];
    $new['cb']        = $cols['cb'];
    $new['title']     = 'Personne';
    $new['categorie'] = 'Catégorie';
    $new['type']      = 'Type';
    $new['featured']  = 'Mis en avant';
    $new['date']      = $cols['date'];
    return $new;
} );

add_action( 'manage_temoignage_posts_custom_column', function ( $col, $post_id ) {
    if ( ! function_exists( 'get_field' ) ) return;

    if ( $col === 'categorie' ) {
        $cat = get_field( 'temoig_category', $post_id );
        $labels = [
            'parent'              => '👨‍👩‍👧 Parent',
            'professionnel'       => '💼 Professionnel',
            'personne_accompagnee' => '🤝 Personne accompagnée',
        ];
        echo esc_html( $labels[ $cat ] ?? '—' );
    } elseif ( $col === 'type' ) {
        $type = get_field( 'temoig_type', $post_id );
        $labels = [
            'texte'       => '💬 Texte',
            'video'       => '🎥 Vidéo',
            'audio'       => '🎙 Audio',
            'video_audio' => '🎥 + 🎙 Vidéo + Audio',
        ];
        echo esc_html( $labels[ $type ] ?? '💬 Texte' );
    } elseif ( $col === 'featured' ) {
        echo get_field( 'temoig_featured', $post_id ) ? '⭐ Oui' : '—';
    }
}, 10, 2 );

/* Trier par "featured" puis date */
add_filter( 'manage_edit-temoignage_sortable_columns', function ( $cols ) {
    $cols['categorie'] = 'categorie';
    $cols['featured']  = 'featured';
    return $cols;
} );
