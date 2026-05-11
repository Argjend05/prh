<?php
/* Template Name: Témoignages Custom */

/* ── Redirection si aucun témoignage publié ───────────── */
$_check = get_posts( [ 'post_type' => 'temoignage', 'posts_per_page' => 1, 'fields' => 'ids' ] );
if ( empty( $_check ) ) {
    wp_redirect( home_url( '/' ), 302 );
    exit;
}

get_header();

/* ── ACF helpers ──────────────────────────────────────── */
$f = fn( $key, $default = '' ) => get_field( $key ) ?: $default;

$hero_title = $f( 'prh68_temp_hero_title', 'Paroles & Témoignages' );
$hero_sub   = $f( 'prh68_temp_hero_subtitle', "Des histoires vraies partagées par des parents, des professionnels et des personnes accompagnées. Chaque parcours est unique, chaque voix compte." );
$cta_title  = $f( 'prh68_temp_cta_title', "Votre témoignage peut aider d'autres familles" );
$cta_sub    = $f( 'prh68_temp_cta_sub', "Vous souhaitez partager votre expérience ou contacter notre équipe ? Nous serions honorés de vous entendre." );
$cta_btn1   = $f( 'prh68_temp_cta_btn1', 'Partager mon témoignage' );
$cta_btn2   = $f( 'prh68_temp_cta_btn2', 'Nous contacter' );
$legal      = $f( 'prh68_temp_legal', "Les témoignages sont publiés avec l'accord explicite des personnes concernées. Certains prénoms ont été modifiés pour préserver l'anonymat." );

/* ── Récupération des témoignages ─────────────────────── */
$temoignages = get_posts( [
    'post_type'      => 'temoignage',
    'posts_per_page' => -1,
    'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
] );

/* Sépare le featured du reste */
$featured = null;
$normaux  = [];
foreach ( $temoignages as $t ) {
    if ( get_field( 'temoig_featured', $t->ID ) && ! $featured ) {
        $featured = $t;
    } else {
        $normaux[] = $t;
    }
}

/* Compte par catégorie */
$cat_counts = [ 'parent' => 0, 'professionnel' => 0, 'personne_accompagnee' => 0 ];
foreach ( $temoignages as $t ) {
    $c = get_field( 'temoig_category', $t->ID );
    if ( isset( $cat_counts[ $c ] ) ) $cat_counts[ $c ]++;
}

/* Labels, icônes et helpers */
$cat_labels = [
    'parent'               => 'Parent',
    'professionnel'        => 'Professionnel',
    'personne_accompagnee' => 'Personne accompagnée',
];

$cat_icons = [
    'parent'               => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="12" height="12" aria-hidden="true"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>',
    'professionnel'        => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="12" height="12" aria-hidden="true"><path d="M20 7h-4V5c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v2H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm-10-2h4v2h-4V5z"/></svg>',
    'personne_accompagnee' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="12" height="12" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
];

$contact_url = get_permalink( get_page_by_path( 'formulaire-contact' ) ) ?: home_url( '/' );

/* Détecte automatiquement la page qui utilise le template du formulaire */
$submit_url = $contact_url;
$_form_pages = get_posts( [
    'post_type'      => 'page',
    'posts_per_page' => 1,
    'fields'         => 'ids',
    'meta_key'       => '_wp_page_template',
    'meta_value'     => 'page-formulaire-temoignage.php',
    'post_status'    => 'publish',
] );
if ( ! empty( $_form_pages ) ) {
    $submit_url = get_permalink( $_form_pages[0] );
}

/* ── Helper rendu carte ───────────────────────────────── */
$render_card = function ( $post, $cat_labels, $cat_icons, $is_featured = false, $anim_col = null ) {
    $cat       = get_field( 'temoig_category', $post->ID ) ?: 'parent';
    $type      = get_field( 'temoig_type', $post->ID ) ?: 'texte';
    $quote     = get_field( 'temoig_quote', $post->ID );
    $name      = get_field( 'temoig_person_name', $post->ID );
    $role      = get_field( 'temoig_person_role', $post->ID );

    $video_url = get_field( 'temoig_video_url', $post->ID );
    $video_th  = get_field( 'temoig_video_thumb', $post->ID );
    $video_dur = get_field( 'temoig_video_duration', $post->ID );

    $audio_file = get_field( 'temoig_audio_file', $post->ID );
    $audio_ext  = get_field( 'temoig_audio_url',  $post->ID );
    $audio_dur  = get_field( 'temoig_audio_duration', $post->ID );
    $audio_src  = '';
    if ( is_array( $audio_file ) && ! empty( $audio_file['url'] ) ) {
        $audio_src = $audio_file['url'];
    } elseif ( ! empty( $audio_ext ) ) {
        $audio_src = $audio_ext;
    }

    $has_video = in_array( $type, [ 'video', 'video_audio' ], true ) && ! empty( $video_url );
    $has_audio = in_array( $type, [ 'audio', 'video_audio' ], true ) && ! empty( $audio_src );

    $classes = [ 'tem-card', 'tem-card--' . esc_attr( $cat ) ];
    if ( $has_video )   $classes[] = 'tem-card--video';
    if ( $has_audio )   $classes[] = 'tem-card--audio';
    if ( $is_featured ) $classes[] = 'tem-card--featured';
    ?>
    <article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
             data-cat="<?php echo esc_attr( $cat ); ?>"
             data-type="<?php echo esc_attr( $type ); ?>"
             <?php if ( $anim_col !== null ) : ?>data-anim-col="<?php echo (int) $anim_col; ?>"<?php endif; ?>>

        <?php if ( $has_video ) :
            $thumb_url = is_array( $video_th ) ? ( $video_th['sizes']['medium_large'] ?? $video_th['url'] ) : ''; ?>
            <button type="button" class="tem-video-thumb"
                    data-video-url="<?php echo esc_url( $video_url ); ?>"
                    aria-label="Lire la vidéo de <?php echo esc_attr( $name ); ?>">
                <?php if ( $thumb_url ) : ?>
                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy">
                <?php else : ?>
                    <div class="tem-video-thumb-placeholder" aria-hidden="true"></div>
                <?php endif; ?>

                <span class="tem-video-type-badge" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="13" height="13"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                    Vidéo
                </span>

                <span class="tem-video-play" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path d="M9 4v16l9-8z"/></svg>
                </span>

                <?php if ( $video_dur ) : ?>
                    <span class="tem-video-duration"><?php echo esc_html( $video_dur ); ?></span>
                <?php endif; ?>
            </button>
        <?php endif; ?>

        <div class="tem-card-body">

            <div class="tem-card-header">
                <span class="tem-cat-badge tem-cat-badge--<?php echo esc_attr( $cat ); ?>">
                    <?php echo $cat_icons[ $cat ] ?? ''; ?>
                    <?php echo esc_html( $cat_labels[ $cat ] ?? '' ); ?>
                </span>

                <?php if ( $is_featured ) : ?>
                    <span class="tem-featured-badge" aria-label="Témoignage mis en avant">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="12" height="12" aria-hidden="true"><path d="M12 17.27l5.18 3.13-1.37-5.89 4.59-3.97-6.05-.51L12 4l-2.35 5.99-6.05.51 4.59 3.97-1.37 5.89z"/></svg>
                        Mis en avant
                    </span>
                <?php endif; ?>
            </div>

            <?php if ( $has_audio ) : ?>
                <div class="tem-audio" data-audio-player>
                    <audio preload="metadata" src="<?php echo esc_url( $audio_src ); ?>"></audio>

                    <button type="button" class="tem-audio-play" aria-label="Lire l'audio">
                        <svg class="tem-audio-icon-play" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                        <svg class="tem-audio-icon-pause" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>
                    </button>

                    <div class="tem-audio-waveform" aria-hidden="true">
                        <?php for ( $i = 0; $i < 32; $i++ ) :
                            $h = 25 + ( ( ( $post->ID * 7 + $i * 13 ) % 60 ) );
                        ?>
                            <span style="height:<?php echo (int) $h; ?>%"></span>
                        <?php endfor; ?>
                    </div>

                    <button type="button" class="tem-audio-volume" aria-label="Couper le son">
                        <svg class="tem-audio-icon-vol-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3A4.5 4.5 0 0 0 14 8.03v7.94c1.48-.74 2.5-2.27 2.5-3.97zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                        <svg class="tem-audio-icon-vol-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M16.5 12A4.5 4.5 0 0 0 14 8.03v2.21l2.45 2.45c.03-.22.05-.45.05-.69zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51A8.96 8.96 0 0 0 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3 3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.17v2.06a8.99 8.99 0 0 0 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4 9.91 6.09 12 8.18V4z"/></svg>
                    </button>

                    <div class="tem-audio-progress">
                        <span class="tem-audio-time tem-audio-time-current">0:00</span>
                        <button type="button" class="tem-audio-bar" aria-label="Barre de progression">
                            <span class="tem-audio-bar-fill"></span>
                        </button>
                        <span class="tem-audio-time tem-audio-time-total"><?php echo esc_html( $audio_dur ?: '–:–' ); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( $quote ) : ?>
                <blockquote class="tem-quote">
                    <p><?php echo nl2br( esc_html( $quote ) ); ?></p>
                </blockquote>
            <?php endif; ?>

            <footer class="tem-meta">
                <?php if ( $name ) : ?><strong class="tem-name"><?php echo esc_html( $name ); ?></strong><?php endif; ?>
                <?php if ( $role ) : ?><span class="tem-role"><?php echo esc_html( $role ); ?></span><?php endif; ?>
            </footer>

        </div>
    </article>
    <?php
};
?>

<div class="tem-page">

    <!-- ──────────────────────── HERO ──────────────────────── -->
    <section class="tem-hero">
        <div class="tem-hero-deco" aria-hidden="true"></div>

        <div class="tem-hero-floats" aria-hidden="true">
            <div class="tem-hf tem-hf--bubble-1"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4C2.9 2 2 2.9 2 4v18l4-4h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg></div>
            <div class="tem-hf tem-hf--bubble-2"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4C2.9 2 2 2.9 2 4v18l4-4h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg></div>
            <div class="tem-hf tem-hf--bubble-3"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4C2.9 2 2 2.9 2 4v18l4-4h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg></div>
            <div class="tem-hf tem-hf--ring-1"></div>
            <div class="tem-hf tem-hf--ring-2"></div>
            <div class="tem-hf tem-hf--dot-1"></div>
            <div class="tem-hf tem-hf--dot-2"></div>
            <div class="tem-hf tem-hf--dot-3"></div>
            <div class="tem-hf tem-hf--dot-4"></div>
        </div>

        <div class="tem-container tem-hero-inner">
            <h1><?php echo esc_html( $hero_title ); ?></h1>
            <p><?php echo esc_html( $hero_sub ); ?></p>

            <div class="tem-hero-stats">
                <div class="tem-hero-stat">
                    <span class="tem-hero-stat-num" data-counter="<?php echo (int) count( $temoignages ); ?>">0</span>
                    <span class="tem-hero-stat-lbl">témoignages</span>
                </div>
                <div class="tem-hero-stat">
                    <span class="tem-hero-stat-num" data-counter="3">0</span>
                    <span class="tem-hero-stat-lbl">publics concernés</span>
                </div>
            </div>
        </div>

        <div class="tem-hero-wave" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2880 70" preserveAspectRatio="none">
                <path d="M0 35 Q360 0 720 35 T1440 35 T2160 35 T2880 35 V70 H0 Z" fill="#ffffff"/>
            </svg>
        </div>
    </section>

    <!-- ───────────────────── FILTRES + GRILLE ───────────────────── -->
    <section class="tem-list-section">
        <div class="tem-container">

            <div class="tem-filters-wrap">
                <div class="tem-filters" role="tablist" aria-label="Filtrer les témoignages par catégorie">
                    <button type="button" class="tem-filter is-active" data-filter="all" role="tab" aria-selected="true">
                        Tous <span class="tem-filter-count"><?php echo (int) count( $temoignages ); ?></span>
                    </button>
                    <?php foreach ( $cat_labels as $key => $label ) : ?>
                        <button type="button"
                                class="tem-filter tem-filter--<?php echo esc_attr( $key ); ?>"
                                data-filter="<?php echo esc_attr( $key ); ?>"
                                role="tab"
                                aria-selected="false">
                            <?php echo esc_html( $label ); ?><span class="tem-filter-count"><?php echo (int) ( $cat_counts[ $key ] ?? 0 ); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
                <p class="tem-result-count" aria-live="polite" aria-atomic="true">
                    <span class="tem-result-num"><?php echo (int) count( $temoignages ); ?></span>&nbsp;témoignage<?php echo count( $temoignages ) !== 1 ? 's' : ''; ?>
                </p>
            </div>

            <?php if ( $featured ) : ?>
                <div class="tem-featured-wrap">
                    <?php $render_card( $featured, $cat_labels, $cat_icons, true ); ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $normaux ) ) : ?>
                <div class="tem-grid">
                    <?php foreach ( $normaux as $i => $post_obj ) :
                        $render_card( $post_obj, $cat_labels, $cat_icons, false, $i % 3 );
                    endforeach; ?>
                </div>
            <?php elseif ( ! $featured ) : ?>
                <p class="tem-empty">Aucun témoignage pour le moment. Revenez bientôt&nbsp;!</p>
            <?php endif; ?>

            <p class="tem-empty-filtered" hidden>Aucun témoignage pour cette catégorie.</p>

        </div>
    </section>

    <!-- ──────────────────────── CTA ──────────────────────── -->
    <section class="tem-cta">
        <div class="tem-cta-deco" aria-hidden="true"></div>
        <div class="tem-container tem-cta-inner">
            <span class="tem-cta-eyebrow">Rejoindre la conversation</span>
            <h2><?php echo esc_html( $cta_title ); ?></h2>
            <p><?php echo esc_html( $cta_sub ); ?></p>
            <div class="tem-cta-btns">
                <a href="<?php echo esc_url( $submit_url ); ?>" class="tem-btn tem-btn--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="17" height="17" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm17.71-10.21a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                    <?php echo esc_html( $cta_btn1 ); ?>
                </a>
                <a href="<?php echo esc_url( $contact_url ); ?>" class="tem-btn tem-btn--ghost">
                    <?php echo esc_html( $cta_btn2 ); ?>
                </a>
            </div>
            <?php if ( $legal ) : ?>
                <p class="tem-legal"><?php echo esc_html( $legal ); ?></p>
            <?php endif; ?>
        </div>
    </section>

</div>

<!-- ─────────────────── MODALE VIDÉO ─────────────────── -->
<div class="tem-video-modal" id="tem-video-modal" hidden>
    <div class="tem-video-modal-backdrop" data-close></div>
    <div class="tem-video-modal-content" role="dialog" aria-modal="true" aria-label="Lecture vidéo">
        <button type="button" class="tem-video-modal-close" data-close aria-label="Fermer">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="tem-video-modal-iframe-wrap" id="tem-video-modal-target"></div>
    </div>
</div>

<?php get_footer(); ?>
