<?php
require_once get_stylesheet_directory() . '/inc/acf-fields.php';
require_once get_stylesheet_directory() . '/inc/scripts.php';
require_once get_stylesheet_directory() . '/inc/cpt.php';
require_once get_stylesheet_directory() . '/inc/ajax-handlers.php';

/* ── Filtrage des items de menu (dropdowns vides + témoignages) ─── */
add_filter( 'wp_nav_menu_objects', function ( $items, $args ) {
    // 1. Map des parents qui ont au moins un enfant
    $parents_with_children = [];
    foreach ( $items as $item ) {
        $pid = (int) $item->menu_item_parent;
        if ( $pid > 0 ) {
            $parents_with_children[ $pid ] = true;
        }
    }

    // 2. Check : existe-t-il au moins un témoignage publié ?
    $has_temoignages = (bool) get_posts( [
        'post_type'      => 'temoignage',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'post_status'    => 'publish',
    ] );

    // 2b. Check : existe-t-il au moins un outil terrain publié ?
    $has_outils = (bool) get_posts( [
        'post_type'      => 'outil_terrain',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'post_status'    => 'publish',
    ] );

    // 3. Premier passage : on supprime les liens si contenu vide
    $slugs_to_hide = [];
    if ( ! $has_temoignages ) $slugs_to_hide[] = 'temoignages';
    if ( ! $has_outils )      $slugs_to_hide[] = 'outils-de-terrain';

    if ( ! empty( $slugs_to_hide ) ) {
        $items = array_filter( $items, function ( $item ) use ( $slugs_to_hide ) {
            $url   = trim( $item->url );
            $title = strtolower( trim( wp_strip_all_tags( $item->title ) ) );

            if ( $item->object === 'page' ) {
                $slug = get_post_field( 'post_name', (int) $item->object_id );
                if ( in_array( $slug, $slugs_to_hide, true ) ) return false;
            }
            foreach ( $slugs_to_hide as $s ) {
                if ( preg_match( '#/' . preg_quote( $s, '#' ) . '/?$#i', $url ) ) return false;
            }
            // Titres lisibles correspondants
            $titles_map = [
                'temoignages'      => [ 'témoignages', 'temoignages' ],
                'outils-de-terrain' => [ 'outils de terrain', 'outils-de-terrain' ],
            ];
            foreach ( $slugs_to_hide as $s ) {
                if ( isset( $titles_map[ $s ] ) && in_array( $title, $titles_map[ $s ], true ) ) return false;
            }

            return true;
        } );
    }

    // 4. Recalcule les parents avec enfants après le filtre témoignages
    //    (au cas où un témoignage retiré laisserait un parent vide)
    $parents_with_children = [];
    foreach ( $items as $item ) {
        $pid = (int) $item->menu_item_parent;
        if ( $pid > 0 ) {
            $parents_with_children[ $pid ] = true;
        }
    }

    // 5. Deuxième passage : supprime les top-level dropdowns vides
    return array_values( array_filter( $items, function ( $item ) use ( $parents_with_children ) {
        // Garde tous les enfants
        if ( (int) $item->menu_item_parent !== 0 ) return true;

        // Top-level : supprime si AUCUN enfant (peu importe l'URL)
        $has_children = isset( $parents_with_children[ (int) $item->ID ] );
        $is_dropdown_label = in_array( trim( $item->url ), [ '#', '', 'javascript:void(0)' ], true );

        // Si c'est un libellé de dropdown (URL = #) sans enfants → supprime
        if ( $is_dropdown_label && ! $has_children ) return false;

        return true;
    } ) );
}, 10, 2 );

/* ── Rappel (non bloquant) sur le poids des vidéos ─────
   Plus de blocage dur : seuls les admins uploadent des vidéos.
   On affiche juste un message d'information dans la médiathèque
   pour les sensibiliser au poids des fichiers. */
add_action( 'admin_notices', function () {
    $screen = get_current_screen();
    if ( ! $screen || ! in_array( $screen->base, [ 'upload', 'media' ], true ) ) {
        return;
    }
    echo '<div class="notice notice-info">
        <p><strong>📹 Vidéos :</strong> privilégiez un lien YouTube ou Vimeo plutôt qu\'un fichier hébergé.
        Si vous devez uploader une vidéo, gardez un poids raisonnable (idéalement &lt; 500 Mo) :
        des fichiers trop lourds ralentissent le site et saturent l\'espace de stockage.</p>
    </div>';
} );

/* ── Auto-création de la page "Partager témoignage" ──── */
add_action( 'after_switch_theme', 'prh68_create_temoignage_form_page' );
add_action( 'admin_init',          'prh68_create_temoignage_form_page' );
function prh68_create_temoignage_form_page() {
    // Si une page utilise déjà ce template (peu importe son slug) → on ne fait rien
    $existing = get_posts( [
        'post_type'      => 'page',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'page-formulaire-temoignage.php',
        'post_status'    => 'any',
    ] );
    if ( ! empty( $existing ) ) return;

    // Si une page avec un de ces slugs existe déjà mais sans le template, on l'ignore aussi
    if ( get_page_by_path( 'partager-temoignage' ) || get_page_by_path( 'formulaire-temoignage' ) ) return;

    $page_id = wp_insert_post( [
        'post_title'     => 'Partager mon témoignage',
        'post_name'      => 'partager-temoignage',
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_content'   => '',
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
    ] );

    if ( $page_id && ! is_wp_error( $page_id ) ) {
        update_post_meta( $page_id, '_wp_page_template', 'page-formulaire-temoignage.php' );
    }
}

/* ── Auto-création de la page "Partager un outil de terrain" ── */
add_action( 'after_switch_theme', 'prh68_create_outil_form_page' );
add_action( 'admin_init',          'prh68_create_outil_form_page' );
function prh68_create_outil_form_page() {
    // Si une page utilise déjà ce template → rien à faire
    $existing = get_posts( [
        'post_type'      => 'page',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'page-formulaire-outil.php',
        'post_status'    => 'any',
    ] );
    if ( ! empty( $existing ) ) return;

    if ( get_page_by_path( 'partager-un-outil' ) || get_page_by_path( 'formulaire-outil' ) ) return;

    $page_id = wp_insert_post( [
        'post_title'     => 'Partager un outil de terrain',
        'post_name'      => 'partager-un-outil',
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_content'   => '',
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
    ] );

    if ( $page_id && ! is_wp_error( $page_id ) ) {
        update_post_meta( $page_id, '_wp_page_template', 'page-formulaire-outil.php' );
    }
}

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

/* Support thème */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    register_nav_menus( [
        'primary' => __( 'Menu principal', 'prh68' ),
    ] );
} );
