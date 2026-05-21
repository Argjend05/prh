<?php
/* Template Name: Outils de Terrain */

/* ── Redirection si aucun outil publié ───────────────── */
$_check = get_posts( [ 'post_type' => 'outil_terrain', 'posts_per_page' => 1, 'fields' => 'ids' ] );
if ( empty( $_check ) ) {
    wp_redirect( home_url( '/' ), 302 );
    exit;
}

get_header();

$uri = get_stylesheet_directory_uri();
$f   = fn( $key, $default ) => get_field( $key ) ?: $default;

/* ── Textes ACF avec fallbacks ─────────────────────────── */
$hero_title  = $f( 'ot_hero_title',  'Les Outils de Terrain' );
$hero_sub    = $f( 'ot_hero_sub',    'Une cartographie collaborative des pratiques innovantes, partagée par les professionnels du terrain.' );
$hero_stat1  = '0'; /* remplacé dynamiquement après chargement CPT */
$hero_stat2  = '0'; /* remplacé dynamiquement après chargement CPT */
$grid_title  = $f( 'ot_grid_title',  'Les Outils du Terrain' );
$grid_sub    = $f( 'ot_grid_sub',    'Explorez les pratiques innovantes partagées par vos pairs' );
$cta_title   = $f( 'ot_cta_title',  'Vous utilisez un outil innovant ?' );
$cta_sub     = $f( 'ot_cta_sub',    "Partagez-le avec la communauté PRH et contribuez à améliorer les pratiques inclusives pour tous les enfants de 0 à 17 ans." );

/* ── Mapping catégorie → couleurs + icône (auto, sans champ admin) ── */
$cat_meta = [
    'scolaire'      => [ 'g1' => '#1a8fa8', 'g2' => '#0d5c7a', 'icon' => 'school',  'label' => 'Scolaire'      ],
    'observation'   => [ 'g1' => '#2a9d6e', 'g2' => '#1a5c3a', 'icon' => 'eye',     'label' => 'Observation'   ],
    'communication' => [ 'g1' => '#8e44ad', 'g2' => '#5b2d8e', 'icon' => 'chat',    'label' => 'Communication' ],
    'urgence'       => [ 'g1' => '#c0392b', 'g2' => '#8a1a1a', 'icon' => 'shield',  'label' => 'Urgence'       ],
    'accueil'       => [ 'g1' => '#e07b2a', 'g2' => '#b55015', 'icon' => 'home',    'label' => 'Accueil'       ],
    'soutien'       => [ 'g1' => '#1a7ab5', 'g2' => '#0d4d8a', 'icon' => 'heart',   'label' => 'Soutien'       ],
];

/* ── Récupération depuis le CPT ─────────────────────────── */
$outil_posts = get_posts( [
    'post_type'      => 'outil_terrain',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
] );

$tools = [];
foreach ( $outil_posts as $op ) {
    $pid      = $op->ID;
    $cat_slug = get_field( 'ot_category',     $pid ) ?: 'scolaire';
    $meta     = $cat_meta[ $cat_slug ] ?? $cat_meta['scolaire'];

    $specs_raw = get_field( 'ot_specs', $pid ) ?: [];
    $specs     = array_map( fn( $s ) => [ $s['spec_label'] ?? '', $s['spec_value'] ?? '' ], $specs_raw );

    $extra_raw  = get_field( 'ot_extra_tags', $pid ) ?: '';
    $extra_tags = array_values( array_filter( array_map( 'trim', explode( ',', $extra_raw ) ) ) );

    /* Photos soumises via le formulaire */
    $photo_ids  = get_post_meta( $pid, '_fot_photos', true ) ?: [];
    $photo_urls = [];
    foreach ( (array) $photo_ids as $_att_id ) {
        $_url = wp_get_attachment_image_url( (int) $_att_id, 'medium_large' );
        if ( $_url ) $photo_urls[] = $_url;
    }

    $tools[] = [
        'id'           => 'outil-' . $pid,
        'title'        => get_the_title( $pid ),
        'category'     => $meta['label'],
        'cat_slug'     => $cat_slug,
        'icon'         => $meta['icon'],
        'g1'           => $meta['g1'],
        'g2'           => $meta['g2'],
        'envs'         => get_field( 'ot_envs',        $pid ) ?: ['urbain'],
        'structure'    => get_field( 'ot_structure',   $pid ) ?: 'tous',
        'description'  => get_field( 'ot_description', $pid ) ?: '',
        'age_ranges'   => get_field( 'ot_age_ranges',  $pid ) ?: [],
        'story'        => get_field( 'ot_story',        $pid ) ?: '',
        'specs'        => $specs,
        'contact_name' => get_field( 'ot_contact_name', $pid ) ?: '',
        'contact_role' => get_field( 'ot_contact_role', $pid ) ?: '',
        'contact_org'  => get_field( 'ot_contact_org',  $pid ) ?: '',
        'date'         => get_the_date( 'd/m/Y', $pid ),
        'extra_tags'   => $extra_tags,
        'photos'       => $photo_urls,
    ];
}

/* Stat 1 : nombre d'outils publiés */
$hero_stat1 = (string) count( $tools );

/* Stat 2 : structures contributrices uniques, sans doublons exacts ni quasi-doublons */

/**
 * Normalise un nom de structure pour la comparaison :
 * minuscules, sans accents, sans ponctuation, espaces réduits.
 */
function ot_normalize_org( string $s ): string {
    $s = mb_strtolower( trim( $s ), 'UTF-8' );
    $s = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $s );
    $s = preg_replace( '/[^a-z0-9\s]/', '', $s );   // retire ponctuation
    $s = preg_replace( '/\s+/', ' ', $s );            // espaces multiples → 1
    return trim( $s );
}

$raw_orgs = array_filter( array_map(
    fn( $t ) => trim( $t['contact_org'] ),
    $tools
) );

/* 1. Dédoublonnage par normalisation (casse, accents, ponctuation, espaces) */
$normalized = [];
foreach ( $raw_orgs as $org ) {
    $normalized[ ot_normalize_org( $org ) ] = true;
}
$norm_keys = array_keys( $normalized );

/* 2. Dédoublonnage flou via levenshtein (seuil = 3 caractères d'écart) */
$unique_orgs = [];
foreach ( $norm_keys as $candidate ) {
    $is_dup = false;
    foreach ( $unique_orgs as $accepted ) {
        if ( levenshtein( $candidate, $accepted ) <= 3 ) {
            $is_dup = true;
            break;
        }
    }
    if ( ! $is_dup ) {
        $unique_orgs[] = $candidate;
    }
}

$hero_stat2 = (string) count( $unique_orgs );

/* Détecte automatiquement la page qui utilise le template du
   formulaire de partage d'outil → CTA pointe sur le bon formulaire,
   pas sur /contact générique. Fallback : /contact. */
$submit_url = get_permalink( get_page_by_path( 'formulaire-contact' ) ) ?: home_url( '/' );
$_form_pages = get_posts( [
    'post_type'      => 'page',
    'posts_per_page' => 1,
    'fields'         => 'ids',
    'meta_key'       => '_wp_page_template',
    'meta_value'     => 'page-formulaire-outil.php',
    'post_status'    => 'publish',
] );
if ( ! empty( $_form_pages ) ) {
    $submit_url = get_permalink( $_form_pages[0] );
}

/* Fin du bloc données CPT */

/* ── Labels tranches d'âge ──────────────────────────────── */
$age_labels = [
    '0-3'   => '0-3 ans',
    '3-6'   => '3-6 ans',
    '6-12'  => '6-12 ans',
    '12-18' => '12-18 ans',
];

/* ── SVG icons inline ───────────────────────────────────── */
$icons = [
    'school' => '<svg viewBox="0 0 48 48" fill="none" aria-hidden="true" class="ot-card-icon-svg"><path d="M6 42V22L24 10l18 12v20" stroke="rgba(255,255,255,.85)" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/><rect x="18" y="30" width="12" height="12" rx="1.5" stroke="rgba(255,255,255,.85)" stroke-width="2.5"/><circle cx="24" cy="20" r="4" stroke="rgba(255,255,255,.45)" stroke-width="1.5"/></svg>',
    'eye'    => '<svg viewBox="0 0 48 48" fill="none" aria-hidden="true" class="ot-card-icon-svg"><ellipse cx="24" cy="24" rx="18" ry="11" stroke="rgba(255,255,255,.85)" stroke-width="2.5"/><circle cx="24" cy="24" r="6" fill="rgba(255,255,255,.2)" stroke="rgba(255,255,255,.85)" stroke-width="2"/><circle cx="24" cy="24" r="2.5" fill="rgba(255,255,255,.9)"/></svg>',
    'chat'   => '<svg viewBox="0 0 48 48" fill="none" aria-hidden="true" class="ot-card-icon-svg"><rect x="6" y="7" width="36" height="26" rx="6" stroke="rgba(255,255,255,.85)" stroke-width="2.5"/><path d="M13 40l6-7h10" stroke="rgba(255,255,255,.85)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 19h20M14 25h13" stroke="rgba(255,255,255,.55)" stroke-width="2" stroke-linecap="round"/></svg>',
    'shield' => '<svg viewBox="0 0 48 48" fill="none" aria-hidden="true" class="ot-card-icon-svg"><path d="M24 5l16 6v13c0 9-6.5 15.5-16 18-9.5-2.5-16-9-16-18V11z" stroke="rgba(255,255,255,.85)" stroke-width="2.5" stroke-linejoin="round"/><path d="M16 24l5.5 5.5L33 18" stroke="rgba(255,255,255,.95)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    'home'   => '<svg viewBox="0 0 48 48" fill="none" aria-hidden="true" class="ot-card-icon-svg"><path d="M8 24L24 10l16 14" stroke="rgba(255,255,255,.85)" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/><path d="M12 20v20h9v-9h6v9h9V20" stroke="rgba(255,255,255,.85)" stroke-width="2.5" stroke-linejoin="round"/></svg>',
    'heart'  => '<svg viewBox="0 0 48 48" fill="none" aria-hidden="true" class="ot-card-icon-svg"><path d="M24 40S8 30 8 18.5C8 13.5 11.5 9 16.5 9c2.8 0 5.5 1.5 7.5 4.5C26 10.5 28.7 9 31.5 9 36.5 9 40 13.5 40 18.5 40 30 24 40 24 40z" fill="rgba(255,255,255,.18)" stroke="rgba(255,255,255,.85)" stroke-width="2.5" stroke-linejoin="round"/></svg>',
];
?>

<!-- ══ HERO ══════════════════════════════════════════════ -->
<header class="ot-page-header">
    <div class="ot-container">
        <h1 id="ot-hero-title"><?php echo esc_html( $hero_title ); ?></h1>
        <p class="ot-page-header-sub"><?php echo esc_html( $hero_sub ); ?></p>
    </div>
    <div class="ot-hero-wave" aria-hidden="true">
        <svg viewBox="0 0 2880 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,40 C360,80 1080,0 1440,40 C1800,80 2520,0 2880,40 L2880,80 L0,80 Z" fill="#f8f8fc" />
        </svg>
    </div>
</header>

<!-- ══ FILTRES ═══════════════════════════════════════════ -->
<section class="ot-filters-section" aria-label="Filtres">
    <div class="ot-container">
        <div class="ot-filters-card">
            <div class="ot-filters-row">

                <!-- Environnements -->
                <div class="ot-filters-col ot-filters-col--env">
                    <span class="ot-filters-label" id="ot-env-label">Environnement :</span>
                    <div class="ot-env-pills" role="group" aria-labelledby="ot-env-label">
                        <button class="ot-env-pill is-active" data-env="tous">Tous</button>
                        <button class="ot-env-pill" data-env="urbain">Urbain</button>
                        <button class="ot-env-pill" data-env="rural">Rural</button>
                        <button class="ot-env-pill" data-env="domicile">Domicile</button>
                        <button class="ot-env-pill" data-env="eaje">EAJE</button>
                        <button class="ot-env-pill" data-env="rue">Rue</button>
                    </div>
                </div>

                <div class="ot-filters-divider" aria-hidden="true"></div>

                <!-- Type de structure -->
                <div class="ot-filters-col ot-filters-col--struct">
                    <label class="ot-filters-label" for="ot-structure-select">Type de structure :</label>
                    <div class="ot-select-wrap">
                        <select id="ot-structure-select" class="ot-select">
                            <option value="tous">Toutes</option>
                            <option value="eaje">EAJE / Crèche</option>
                            <option value="assistante_maternelle">Assistante maternelle</option>
                            <option value="rpe">RPE</option>
                            <option value="acm">ACM</option>
                            <option value="autre">Autre</option>
                        </select>
                        <svg class="ot-select-arrow" viewBox="0 0 12 8" fill="none" aria-hidden="true" width="12"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </div>
                </div>

                <div class="ot-filters-divider" aria-hidden="true"></div>

                <!-- Recherche -->
                <div class="ot-filters-col ot-filters-col--search">
                    <div class="ot-search-wrap">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="ot-search-icon"><circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.5"/><path d="M13 13l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        <input type="search" id="ot-search" class="ot-search" placeholder="Rechercher un outil…" aria-label="Rechercher un outil">
                    </div>
                </div>

            </div>

            <div class="ot-active-filters" id="ot-active-filters" aria-live="polite"></div>
        </div>
    </div>
</section>

<!-- ══ GRILLE OUTILS ═════════════════════════════════════ -->
<section class="ot-grid-section" aria-label="Catalogue des outils de terrain">
    <div class="ot-container">

        <?php if ( empty( $tools ) ) : ?>
        <div class="ot-empty-state">
            <svg viewBox="0 0 64 64" fill="none" aria-hidden="true" width="56" height="56"><circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="1.5" opacity=".2"/><path d="M22 32h20M32 22v20" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".4" transform="rotate(45 32 32)"/><circle cx="32" cy="32" r="10" stroke="currentColor" stroke-width="1.5" opacity=".3"/></svg>
            <h3>Aucun outil publié pour le moment</h3>
            <p>Les outils partagés par les professionnels du terrain apparaîtront ici après validation par l'équipe PRH68.</p>
            <a href="<?php echo esc_url( $submit_url ); ?>" class="ot-cta-btn ot-cta-btn--primary" style="margin-top:8px;">
                Proposer un outil
            </a>
        </div>
        <?php else :
            /* Colonnes adaptatives : la grille reste dense quel que soit
               le volume (1 → 1 col centrée, 2-4 → 2 cols, 5+ → 3 cols).
               Recalculée par JS à chaque filtrage. */
            $_n = count( $tools );
            $grid_cols = $_n <= 1 ? 1 : ( $_n <= 4 ? 2 : 3 );
        ?>
        <div class="ot-grid ot-grid--c<?php echo (int) $grid_cols; ?>" id="ot-grid" role="list">
            <?php foreach ( $tools as $t ) :
                $envs_str = implode( ',', $t['envs'] );
            ?>
            <article
                class="ot-card"
                role="listitem"
                data-id="<?php echo esc_attr( $t['id'] ); ?>"
                data-envs="<?php echo esc_attr( $envs_str ); ?>"
                data-structure="<?php echo esc_attr( $t['structure'] ); ?>"
                data-search="<?php echo esc_attr( strtolower( $t['title'] . ' ' . $t['category'] . ' ' . $t['description'] . ' ' . implode( ' ', $t['age_ranges'] ) ) ); ?>"
                tabindex="0"
                aria-label="<?php echo esc_attr( 'Voir les détails : ' . $t['title'] ); ?>">

                <!-- Glow 3D Ombre Colorée -->
                <div class="ot-card-glow" style="background:linear-gradient(135deg,<?php echo esc_attr( $t['g1'] ); ?>,<?php echo esc_attr( $t['g2'] ); ?>)" aria-hidden="true"></div>

                <?php
                $has_photo = ! empty( $t['photos'] );
                $vis_style = $has_photo
                    ? 'background-color:' . esc_attr( $t['g1'] ) . ';background-image:url(\'' . esc_url( $t['photos'][0] ) . '\');background-size:cover;background-position:center'
                    : 'background:linear-gradient(135deg,' . esc_attr( $t['g1'] ) . ',' . esc_attr( $t['g2'] ) . ')';
                ?>
                <div class="ot-card-visual<?php echo $has_photo ? ' ot-card-visual--photo' : ''; ?>" style="<?php echo $vis_style; ?>" aria-hidden="true">
                    <?php echo $icons[ $t['icon'] ] ?? ''; ?>
                    <span class="ot-card-cat-badge"><?php echo esc_html( $t['category'] ); ?></span>
                </div>

                <div class="ot-card-body">
                    <div class="ot-card-tags">
                        <?php foreach ( array_slice( $t['envs'], 0, 2 ) as $env ) : ?>
                        <span class="ot-env-tag"><?php echo esc_html( ucfirst( $env ) ); ?></span>
                        <?php endforeach; ?>
                        <?php foreach ( array_slice( $t['extra_tags'], 0, 1 ) as $tag ) : ?>
                        <span class="ot-env-tag ot-env-tag--extra"><?php echo esc_html( $tag ); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <h3 class="ot-card-title"><?php echo esc_html( $t['title'] ); ?></h3>
                    <p class="ot-card-desc"><?php echo esc_html( $t['description'] ); ?></p>

                    <?php if ( ! empty( $t['age_ranges'] ) ) : ?>
                    <div class="ot-card-ages" aria-label="Tranches d'âge">
                        <?php foreach ( $t['age_ranges'] as $age ) : ?>
                        <span class="ot-age-tag"><?php echo esc_html( $age_labels[ $age ] ?? $age ); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="ot-card-footer">
                    <span class="ot-card-see">Voir la fiche
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="14" height="14"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <p class="ot-no-results" id="ot-no-results" aria-live="polite" hidden>
            <svg viewBox="0 0 48 48" fill="none" aria-hidden="true" width="40" height="40"><circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="1.5" opacity=".3"/><path d="M16 24h16M24 16v16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity=".5" transform="rotate(45 24 24)"/></svg>
            Aucun outil ne correspond à votre recherche.
            <button class="ot-reset-btn" id="ot-reset-btn">Réinitialiser les filtres</button>
        </p>
    </div>
</section>

<!-- ══ MODAL DÉTAIL ══════════════════════════════════════ -->
<div class="ot-modal-overlay" id="ot-modal-overlay" aria-hidden="true">
    <div class="ot-modal" role="dialog" aria-modal="true" aria-labelledby="ot-modal-title" id="ot-modal">
        <button class="ot-modal-close" id="ot-modal-close" aria-label="Fermer la fiche outil">
            <svg viewBox="0 0 24 24" fill="none" width="20" height="20"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>

        <div class="ot-modal-inner">
            <div class="ot-modal-visual" id="ot-modal-visual" aria-hidden="true">
                <div class="ot-modal-icon-wrap" id="ot-modal-icon"></div>
                <div class="ot-modal-visual-tags" id="ot-modal-visual-tags"></div>
            </div>

            <div class="ot-modal-content" id="ot-modal-content">
                <!-- Populated by JS -->
            </div>
        </div>
    </div>
</div>

<!-- ══ CTA CONTRIBUTION ══════════════════════════════════ -->
<section class="ot-cta" aria-labelledby="ot-cta-title">
    <div class="ot-cta-bg" aria-hidden="true"></div>
    <div class="ot-container">
        <div class="ot-cta-inner">
            <div class="ot-cta-badge">
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="14" height="14"><path d="M8 2l1.5 3.5L13 6l-2.5 2.5.5 3.5L8 10.5 5 12l.5-3.5L3 6l3.5-.5z" fill="currentColor" opacity=".8"/></svg>
                Contribuer à la base
            </div>
            <h2 id="ot-cta-title"><?php echo esc_html( $cta_title ); ?></h2>
            <p><?php echo esc_html( $cta_sub ); ?></p>
            <div class="ot-cta-btns">
                <a href="<?php echo esc_url( $submit_url ); ?>" class="ot-cta-btn ot-cta-btn--primary">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="18" height="18"><path d="M10 13V4M6 7l4-4 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 14v1a2 2 0 002 2h10a2 2 0 002-2v-1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    Partager un Outil
                </a>
            </div>
            <p class="ot-cta-note"><?php if ( $hero_stat2 > 0 ) echo esc_html( $hero_stat2 ) . ' structure' . ( $hero_stat2 > 1 ? 's ont' : ' a' ) . ' déjà contribué · '; ?>Processus de validation sous 5 à 7 jours ouvrés</p>
        </div>
    </div>
</section>

<!-- ══ Données JSON pour le modal JS ═════════════════════ -->
<script>
window.prhOutilsContactUrl = <?php echo wp_json_encode( get_permalink( get_page_by_path( 'formulaire-contact' ) ) ?: home_url( '/formulaire-contact/' ) ); ?>;
window.prhOutils = <?php
    $json_tools = array_map( function( $t ) {
        return [
            'id'           => $t['id'],
            'title'        => $t['title'],
            'category'     => $t['category'],
            'cat_slug'     => $t['cat_slug'],
            'icon'         => $t['icon'],
            'g1'           => $t['g1'],
            'g2'           => $t['g2'],
            'envs'         => $t['envs'],
            'structure'    => $t['structure'],
            'description'  => $t['description'],
            'age_ranges'   => $t['age_ranges'],
            'story'        => $t['story'],
            'specs'        => $t['specs'],
            'contact_name' => $t['contact_name'],
            'contact_role' => $t['contact_role'],
            'contact_org'  => $t['contact_org'],
            'date'         => $t['date'],
            'extra_tags'   => $t['extra_tags'],
            'photos'       => $t['photos'],
        ];
    }, $tools );
    echo wp_json_encode( $json_tools, JSON_UNESCAPED_UNICODE );
?>;
</script>

<?php get_footer(); ?>
