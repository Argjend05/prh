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

/* ── Meta box : infos de contact internes (témoignage) ── */
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'ftem_contact_box',
        '🔒 Infos de contact (internes)',
        function ( $post ) {
            $email     = get_post_meta( $post->ID, '_ftem_submit_email',     true );
            $phone     = get_post_meta( $post->ID, '_ftem_submit_phone',     true );
            $real_name = get_post_meta( $post->ID, '_ftem_submit_real_name', true );
            $date      = get_post_meta( $post->ID, '_ftem_submit_date',      true );

            if ( ! $email && ! $phone ) {
                echo '<p style="color:#888;font-size:13px;">Aucune info de contact.</p>';
                return;
            }

            echo '<table style="width:100%;border-collapse:collapse;font-size:13px;">';
            if ( $real_name ) {
                echo '<tr><td style="padding:5px 0;color:#666;width:130px;">👤 Vrai prénom</td>';
                echo '<td style="padding:5px 0;color:#222;font-weight:600;">' . esc_html( $real_name ) . ' <span style="color:#e05050;font-size:.8em;">(anonyme publiquement)</span></td></tr>';
            }
            if ( $email ) {
                echo '<tr><td style="padding:5px 0;color:#666;">📧 Email</td>';
                echo '<td style="padding:5px 0;"><a href="mailto:' . esc_attr( $email ) . '" style="color:#2271b1;">' . esc_html( $email ) . '</a></td></tr>';
            }
            if ( $phone ) {
                echo '<tr><td style="padding:5px 0;color:#666;">📞 Téléphone</td>';
                echo '<td style="padding:5px 0;color:#222;">' . esc_html( $phone ) . '</td></tr>';
            }
            if ( $date ) {
                echo '<tr><td style="padding:5px 0;color:#666;">📅 Soumis le</td>';
                echo '<td style="padding:5px 0;color:#888;">' . esc_html( $date ) . '</td></tr>';
            }
            echo '</table>';
        },
        'temoignage',
        'side',
        'default'
    );
} );

/* ── Outils de Terrain ────────────────────────────────── */
add_action( 'init', function () {
    register_post_type( 'outil_terrain', [
        'labels' => [
            'name'               => 'Outils de Terrain',
            'singular_name'      => 'Outil de Terrain',
            'menu_name'          => 'Outils de Terrain',
            'add_new'            => 'Ajouter',
            'add_new_item'       => 'Ajouter un outil',
            'edit_item'          => "Modifier l'outil",
            'new_item'           => 'Nouvel outil',
            'view_item'          => "Voir l'outil",
            'search_items'       => 'Rechercher un outil',
            'not_found'          => 'Aucun outil trouvé',
            'not_found_in_trash' => 'Aucun outil dans la corbeille',
            'all_items'          => 'Tous les outils',
        ],
        'public'         => false,
        'show_ui'        => true,
        'show_in_menu'   => true,
        'menu_icon'      => 'dashicons-hammer',
        'menu_position'  => 22,
        'supports'       => [ 'title', 'page-attributes' ],
        'has_archive'    => false,
        'rewrite'        => false,
    ] );
} );

/* ── Labels partagés pour outil_terrain ───────────────── */
function prh68_ot_struct_labels() {
    return [
        'eaje'                  => '🏠 EAJE',
        'assistante_maternelle' => '❤️ Ass. maternelle',
        'rpe'                   => '💬 RPE',
        'acm'                   => '🎒 ACM',
        'autre'                 => '⚙️ Autre',
        /* Anciennes valeurs conservées pour les outils existants */
        'scolaire'              => '🏫 Scolaire',
        'observation'           => '👁 Observation',
        'communication'         => '📢 Communication',
        'urgence'               => '🛡 Urgence',
        'accueil'               => '🏡 Accueil',
        'soutien'               => '🤝 Soutien',
    ];
}

/* ── Colonnes admin ───────────────────────────────────── */
add_filter( 'manage_outil_terrain_posts_columns', function ( $cols ) {
    return [
        'cb'           => $cols['cb'],
        'title'        => 'Outil',
        'ot_category'  => 'Type de lieu',
        'ot_structure' => 'Structure',
        'ot_ages'      => 'Âges',
        'ot_photos'    => '📷',
        'date'         => $cols['date'],
    ];
} );

add_action( 'manage_outil_terrain_posts_custom_column', function ( $col, $post_id ) {
    if ( ! function_exists( 'get_field' ) ) return;
    $labels = prh68_ot_struct_labels();

    if ( $col === 'ot_category' ) {
        $cat = get_field( 'ot_category', $post_id );
        $sub = get_post_meta( $post_id, '_fot_submission', true );
        $out = $labels[ $cat ] ?? esc_html( $cat ?: '—' );
        if ( $cat === 'autre' && ! empty( $sub['outil_category_autre'] ) ) {
            $out .= ' <span style="color:#888;font-size:.85em;">— ' . esc_html( $sub['outil_category_autre'] ) . '</span>';
        }
        echo $out;

    } elseif ( $col === 'ot_structure' ) {
        $struct = get_field( 'ot_structure', $post_id );
        $sub    = get_post_meta( $post_id, '_fot_submission', true );
        $org    = get_field( 'ot_contact_org', $post_id ) ?: ( $sub['struct_name'] ?? '' );
        $cp     = $sub['struct_postal'] ?? '';
        $type   = $labels[ $struct ] ?? esc_html( $struct ?: '—' );
        if ( $struct === 'autre' && ! empty( $sub['struct_type_autre'] ) ) {
            $type .= ' <span style="color:#888;font-size:.85em;">— ' . esc_html( $sub['struct_type_autre'] ) . '</span>';
        }
        echo $type;
        if ( $org ) echo '<br><span style="color:#555;font-size:.85em;">' . esc_html( $org ) . ( $cp ? ' · ' . esc_html( $cp ) : '' ) . '</span>';

    } elseif ( $col === 'ot_ages' ) {
        $ages = get_field( 'ot_age_ranges', $post_id ) ?: [];
        echo $ages ? esc_html( implode( ', ', (array) $ages ) ) : '—';

    } elseif ( $col === 'ot_photos' ) {
        $photos = get_post_meta( $post_id, '_fot_photos', true ) ?: [];
        if ( empty( $photos ) ) { echo '—'; return; }
        echo '<div style="display:flex;gap:4px;flex-wrap:wrap;">';
        foreach ( array_slice( $photos, 0, 3 ) as $att_id ) {
            $thumb = wp_get_attachment_image_url( $att_id, 'thumbnail' );
            if ( $thumb ) {
                echo '<img src="' . esc_url( $thumb ) . '" style="width:36px;height:36px;object-fit:cover;border-radius:4px;border:1px solid #ddd;">';
            }
        }
        if ( count( $photos ) > 3 ) {
            echo '<span style="font-size:.8em;color:#888;align-self:center;">+' . ( count( $photos ) - 3 ) . '</span>';
        }
        echo '</div>';
    }
}, 10, 2 );

/* ── Colonnes triables ────────────────────────────────── */
add_filter( 'manage_edit-outil_terrain_sortable_columns', function ( $cols ) {
    $cols['ot_category']  = 'ot_category';
    $cols['ot_structure'] = 'ot_structure';
    return $cols;
} );

/* ── Meta box : détails de soumission ─────────────────── */
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'fot_submission_details',
        '📋 Détails de la soumission',
        'prh68_ot_meta_box_submission',
        'outil_terrain',
        'normal',
        'high'
    );
    add_meta_box(
        'fot_photos_box',
        '📷 Photos soumises',
        'prh68_ot_meta_box_photos',
        'outil_terrain',
        'side',
        'default'
    );
} );

function prh68_ot_meta_box_submission( $post ) {
    $sub    = get_post_meta( $post->ID, '_fot_submission', true ) ?: [];
    $labels = prh68_ot_struct_labels();
    if ( empty( $sub ) ) { echo '<p style="color:#888;">Aucune donnée de soumission.</p>'; return; }

    $struct_type = $labels[ $sub['struct_type'] ?? '' ] ?? ( $sub['struct_type'] ?? '—' );
    if ( ( $sub['struct_type'] ?? '' ) === 'autre' && ! empty( $sub['struct_type_autre'] ) ) {
        $struct_type .= ' — ' . $sub['struct_type_autre'];
    }
    $lieu_type = $labels[ $sub['outil_category'] ?? '' ] ?? ( $sub['outil_category'] ?? '—' );
    if ( ( $sub['outil_category'] ?? '' ) === 'autre' && ! empty( $sub['outil_category_autre'] ) ) {
        $lieu_type .= ' — ' . $sub['outil_category_autre'];
    }

    $rows = [
        '🏢 Structure'       => esc_html( $sub['struct_name'] ?? '—' ),
        '🏷 Type structure'  => esc_html( $struct_type ),
        '📮 Code postal'     => esc_html( $sub['struct_postal'] ?? '—' ),
        '👤 Référent'        => esc_html( $sub['ref_name'] ?? '—' ) . ( ! empty( $sub['ref_role'] ) ? ' <span style="color:#888;">(' . esc_html( $sub['ref_role'] ) . ')</span>' : '' ),
        '📧 Email'           => ! empty( $sub['ref_email'] ) ? '<a href="mailto:' . esc_attr( $sub['ref_email'] ) . '">' . esc_html( $sub['ref_email'] ) . '</a>' : '—',
        '📞 Téléphone'       => esc_html( $sub['ref_phone'] ?? '—' ) ?: '—',
        '🏠 Type de lieu'    => esc_html( $lieu_type ),
        '🎂 Tranches d\'âge' => ! empty( $sub['outil_ages'] ) ? esc_html( implode( ', ', (array) $sub['outil_ages'] ) ) : '—',
        '📅 Soumis le'       => esc_html( $sub['submitted_at'] ?? '—' ),
    ];

    echo '<table style="width:100%;border-collapse:collapse;font-size:13px;">';
    foreach ( $rows as $label => $value ) {
        echo '<tr>';
        echo '<td style="padding:6px 10px 6px 0;color:#666;white-space:nowrap;vertical-align:top;width:140px;">' . esc_html( $label ) . '</td>';
        echo '<td style="padding:6px 0;color:#222;font-weight:500;">' . $value . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

function prh68_ot_meta_box_photos( $post ) {
    $photos = get_post_meta( $post->ID, '_fot_photos', true ) ?: [];
    if ( empty( $photos ) ) {
        echo '<p style="color:#888;font-size:13px;">Aucune photo soumise.</p>';
        return;
    }
    echo '<div style="display:flex;flex-direction:column;gap:10px;">';
    foreach ( $photos as $att_id ) {
        $thumb = wp_get_attachment_image_url( $att_id, 'medium' );
        $full  = wp_get_attachment_url( $att_id );
        $edit  = get_edit_post_link( $att_id );
        if ( ! $thumb ) continue;
        echo '<div style="border:1px solid #e2e4e7;border-radius:6px;overflow:hidden;">';
        echo '<a href="' . esc_url( $full ) . '" target="_blank">';
        echo '<img src="' . esc_url( $thumb ) . '" style="width:100%;height:auto;display:block;">';
        echo '</a>';
        echo '<div style="padding:6px 8px;display:flex;gap:8px;align-items:center;background:#fafafa;">';
        echo '<a href="' . esc_url( $edit ) . '" target="_blank" style="font-size:11px;color:#2271b1;">✏️ Modifier</a>';
        echo '<a href="' . esc_url( $full ) . '" download target="_blank" style="font-size:11px;color:#2271b1;">⬇️ Télécharger</a>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}
