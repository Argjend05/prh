<?php
/* Template Name: Formulaire Outil Terrain */

/* ═══════════════════════════════════════════════════════════
   TRAITEMENT PHP
   ═══════════════════════════════════════════════════════════ */
$fot_errors  = [];
$fot_success = false;

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['fot_nonce'] ) ) {

    /* ── Sécurité ── */
    if ( ! wp_verify_nonce( $_POST['fot_nonce'], 'fot_submit' ) ) {
        $fot_errors[] = "Session expirée, merci de recharger la page.";
    }
    if ( ! empty( $_POST['fot_website'] ) ) {
        $fot_errors[] = "Erreur de validation."; // honeypot
    }

    /* ── Collecte & sanitize ── */
    $d = [
        'struct_name'   => sanitize_text_field(     $_POST['fot_struct_name']   ?? '' ),
        'struct_type'   => sanitize_text_field(     $_POST['fot_struct_type']   ?? '' ),
        'struct_dept'   => sanitize_text_field(     $_POST['fot_struct_dept']   ?? '' ),
        'ref_name'      => sanitize_text_field(     $_POST['fot_ref_name']      ?? '' ),
        'ref_role'      => sanitize_text_field(     $_POST['fot_ref_role']      ?? '' ),
        'ref_email'     => sanitize_email(          $_POST['fot_ref_email']     ?? '' ),
        'ref_phone'     => sanitize_text_field(     $_POST['fot_ref_phone']     ?? '' ),
        'outil_name'    => sanitize_text_field(     $_POST['fot_outil_name']    ?? '' ),
        'outil_desc'    => sanitize_textarea_field( $_POST['fot_outil_desc']    ?? '' ),
        'outil_context' => sanitize_textarea_field( $_POST['fot_outil_context'] ?? '' ),
        'outil_envs'    => array_map( 'sanitize_text_field', (array) ( $_POST['fot_outil_envs'] ?? [] ) ),
        'outil_freq'    => (int) ( $_POST['fot_outil_freq']  ?? 2 ),
        'outil_tags'    => sanitize_text_field(     $_POST['fot_outil_tags']    ?? '' ),
        'consent'       => ! empty( $_POST['fot_consent'] ),
    ];

    /* ── Validation ── */
    if ( empty( $d['struct_name'] ) )             $fot_errors[] = "Le nom de la structure est requis.";
    if ( empty( $d['struct_type'] ) )             $fot_errors[] = "Le type de structure est requis.";
    if ( empty( $d['ref_name'] ) )                $fot_errors[] = "Le nom du référent est requis.";
    if ( ! is_email( $d['ref_email'] ) )          $fot_errors[] = "Adresse e-mail invalide.";
    if ( strlen( $d['outil_name'] ) < 3 )         $fot_errors[] = "Le nom de l'outil est requis.";
    if ( strlen( $d['outil_desc'] ) < 20 )        $fot_errors[] = "La description doit faire au moins 20 caractères.";
    if ( strlen( $d['outil_desc'] ) > 400 )       $fot_errors[] = "La description est trop longue (400 car. max).";
    if ( strlen( $d['outil_context'] ) < 30 )     $fot_errors[] = "Le contexte d'utilisation est requis.";
    if ( empty( $d['outil_envs'] ) )              $fot_errors[] = "Sélectionnez au moins un environnement.";
    if ( ! $d['consent'] )                        $fot_errors[] = "Vous devez accepter les conditions avant de soumettre.";

    /* ── Création du post en attente ── */
    if ( empty( $fot_errors ) ) {

        $freq_labels = [ 1 => 'Rare', 2 => 'Régulier', 3 => 'Quotidien' ];

        $post_id = wp_insert_post( [
            'post_title'   => sanitize_text_field( $d['outil_name'] ),
            'post_type'    => 'outil_terrain',
            'post_status'  => 'pending',
            'post_content' => '',
        ] );

        if ( $post_id && ! is_wp_error( $post_id ) ) {

            /* ACF */
            if ( function_exists( 'update_field' ) ) {
                update_field( 'ot_description',   $d['outil_context'], $post_id );
                update_field( 'ot_story',         $d['outil_context'], $post_id );
                update_field( 'ot_envs',          $d['outil_envs'],    $post_id );
                update_field( 'ot_contact_name',  $d['ref_name'],      $post_id );
                update_field( 'ot_contact_role',  $d['ref_role'],      $post_id );
                update_field( 'ot_contact_org',   $d['struct_name'],   $post_id );
                update_field( 'ot_extra_tags',    $d['outil_tags'],    $post_id );
            }

            /* Méta de soumission (pour l'admin) */
            $meta = array_merge( $d, [
                'outil_freq_label' => $freq_labels[ $d['outil_freq'] ] ?? 'Régulier',
                'submitted_at'     => current_time( 'mysql' ),
                'submitted_ip'     => $_SERVER['REMOTE_ADDR'] ?? '',
            ] );
            update_post_meta( $post_id, '_fot_submission', $meta );

            /* Photos */
            if ( ! empty( $_FILES['fot_photos']['name'][0] ) ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $files = $_FILES['fot_photos'];
                foreach ( $files['name'] as $i => $name ) {
                    if ( empty( $name ) || $files['error'][$i] !== UPLOAD_ERR_OK ) continue;
                    $upload = [
                        'name'     => $files['name'][$i],
                        'type'     => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error'    => $files['error'][$i],
                        'size'     => $files['size'][$i],
                    ];
                    $_FILES['fot_photo_single'] = $upload;
                    $att_id = media_handle_upload( 'fot_photo_single', $post_id );
                    if ( ! is_wp_error( $att_id ) ) {
                        $existing = get_post_meta( $post_id, '_fot_photos', true ) ?: [];
                        $existing[] = $att_id;
                        update_post_meta( $post_id, '_fot_photos', $existing );
                    }
                }
            }

            /* Email notification admin */
            $admin_email = get_option( 'admin_email' );
            $subject     = "[PRH68] Nouvel outil soumis : " . $d['outil_name'];
            $body        = "Un nouvel outil de terrain a été soumis et attend modération.\n\n";
            $body       .= "Outil       : " . $d['outil_name'] . "\n";
            $body       .= "Structure   : " . $d['struct_name'] . " (" . $d['struct_type'] . ")\n";
            $body       .= "Référent    : " . $d['ref_name'] . " — " . $d['ref_email'] . "\n";
            $body       .= "Environnements : " . implode( ', ', $d['outil_envs'] ) . "\n\n";
            $body       .= "Voir la soumission : " . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . "\n";
            wp_mail( $admin_email, $subject, $body );

            $fot_success = true;
        } else {
            $fot_errors[] = "Une erreur est survenue lors de la soumission. Merci de réessayer.";
        }
    }
}

get_header();

$uri = get_stylesheet_directory_uri();

$struct_types = [
    ''           => 'Sélectionnez un type…',
    'eaje'       => 'EAJE / Crèche / Multi-accueil',
    'ecole'      => 'École maternelle / élémentaire',
    'college'    => 'Collège / Lycée',
    'sessad'     => 'SESSAD',
    'itep'       => 'ITEP / IME',
    'service_j'  => 'Service Jeunesse / Centre de loisirs',
    'assoce'     => 'Association',
    'hosp'       => 'Établissement de santé',
    'autre'      => 'Autre',
];

$envs_choices = [
    'urbain'       => 'Urbain',
    'rural'        => 'Rural',
    'domicile'     => 'Domicile',
    'eaje'         => 'EAJE',
    'rue'          => 'Rue',
    'collectivite' => 'Collectivité',
];

/* ── Suggestions de structures existantes ── */
$_org_posts = get_posts( [
    'post_type'      => 'outil_terrain',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'fields'         => 'ids',
] );
$_existing_orgs = [];
foreach ( $_org_posts as $_pid ) {
    $org = trim( get_field( 'ot_contact_org', $_pid ) ?: '' );
    if ( $org ) $_existing_orgs[] = $org;
}
$_existing_orgs = array_values( array_unique( $_existing_orgs ) );
sort( $_existing_orgs );
?>

<?php if ( $fot_success ) : ?>

<!-- ══ SUCCÈS ═══════════════════════════════════════════ -->
<section class="fot-success-page">
    <div class="fot-container">
        <div class="fot-success-inner">
            <div class="fot-success-icon">
                <svg viewBox="0 0 56 56" fill="none" aria-hidden="true" width="56" height="56">
                    <circle cx="28" cy="28" r="28" fill="rgba(13,122,128,.12)"/>
                    <path d="M17 28l8 8 14-16" stroke="#0d7a80" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Contribution reçue !</h1>
            <p>Merci pour votre partage. Votre outil <strong><?php echo esc_html( $d['outil_name'] ); ?></strong> a bien été reçu et sera examiné par l'équipe PRH68 sous <strong>5 à 7 jours ouvrés</strong>.</p>
            <p class="fot-success-sub">Vous recevrez un e-mail de confirmation à <strong><?php echo esc_html( $d['ref_email'] ); ?></strong> lors de la publication.</p>
            <div class="fot-success-btns">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="fot-btn fot-btn--primary">Retour à l'accueil</a>
                <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'outils-terrain' ) ) ?: home_url( '/' ) ); ?>" class="fot-btn fot-btn--ghost">Voir les outils publiés</a>
            </div>
        </div>
    </div>
</section>

<?php else : ?>

<!-- ══ HERO ══════════════════════════════════════════════ -->
<section class="fot-hero">
    <div class="fot-hero-bg" aria-hidden="true"></div>
    <div class="fot-container">
        <div class="fot-breadcrumb">
            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="13" height="13"><path d="M10 2v16M2 10h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Contribuer à la base
        </div>
        <h1>Partagez un Outil de Terrain</h1>
        <p>Enrichissez la bibliothèque collaborative PRH68 en partageant vos pratiques innovantes avec d'autres professionnels.</p>
    </div>
</section>

<!-- ══ FORMULAIRE ════════════════════════════════════════ -->
<section class="fot-form-section">
    <div class="fot-container">

        <?php if ( ! empty( $fot_errors ) ) : ?>
        <div class="fot-error-banner" role="alert">
            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="18" height="18"><circle cx="10" cy="10" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v5M10 13.5v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <div>
                <strong>Merci de corriger les erreurs suivantes :</strong>
                <ul><?php foreach ( $fot_errors as $err ) echo '<li>' . esc_html( $err ) . '</li>'; ?></ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Barre de progression -->
        <div class="fot-progress" role="list" aria-label="Progression du formulaire">
            <div class="fot-progress-track"><div class="fot-progress-fill" id="fot-progress-fill"></div></div>
            <?php
            $steps = ['Structure', 'Outil', 'Photos', 'Validation'];
            foreach ( $steps as $i => $label ) : ?>
            <div class="fot-progress-step <?php echo $i === 0 ? 'is-active' : ''; ?>" data-step="<?php echo $i + 1; ?>" role="listitem">
                <div class="fot-progress-dot"><span><?php echo $i + 1; ?></span><svg class="fot-check-icon" viewBox="0 0 12 12" fill="none" aria-hidden="true" width="12"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                <span class="fot-progress-label"><?php echo esc_html( $label ); ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulaire principal -->
        <form id="fot-form" method="post" enctype="multipart/form-data" novalidate>
            <?php wp_nonce_field( 'fot_submit', 'fot_nonce' ); ?>
            <input type="text" name="fot_website" class="fot-honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">

            <!-- ═══ ÉTAPE 1 : STRUCTURE ═══ -->
            <div class="fot-step is-active" id="fot-step-1" data-step="1">
                <div class="fot-step-header">
                    <div class="fot-step-badge">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16"><rect x="2" y="3" width="16" height="13" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M2 7h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        Étape 1 / 4
                    </div>
                    <div class="fot-step-tabs" aria-hidden="true">
                        <span class="is-active">Structure</span><span>Outil</span><span>Photos</span><span>Validation</span>
                    </div>
                </div>
                <h2>Informations sur votre Structure</h2>
                <p class="fot-step-sub">Parlez-nous de votre structure et de votre contact référent.</p>

                <div class="fot-fields">
                    <div class="fot-field fot-field--full">
                        <label for="fot_struct_name">Nom de la structure <abbr title="requis">*</abbr></label>
                        <div class="fot-input-wrap fot-autocomplete-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><path d="M3 17v-1a4 4 0 014-4h6a4 4 0 014 4v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                            <input type="text" id="fot_struct_name" name="fot_struct_name" class="fot-input" placeholder="Ex. Service Jeunesse de Mulhouse" required autocomplete="off" data-autocomplete="fot-org-suggestions" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="fot-org-suggestions">
                            <ul class="fot-suggestions" id="fot-org-suggestions" role="listbox" aria-label="Structures existantes" hidden></ul>
                        </div>
                    </div>

                    <div class="fot-field">
                        <label for="fot_struct_type">Type de structure <abbr title="requis">*</abbr></label>
                        <div class="fot-select-wrap">
                            <select id="fot_struct_type" name="fot_struct_type" class="fot-select" required>
                                <?php foreach ( $struct_types as $val => $label ) : ?>
                                <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <svg viewBox="0 0 12 8" fill="none" aria-hidden="true" width="12" class="fot-select-arrow"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </div>
                    </div>

                    <div class="fot-field">
                        <label for="fot_struct_dept">Département <abbr title="requis">*</abbr></label>
                        <div class="fot-input-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><path d="M10 2C7.24 2 5 4.24 5 7c0 3.75 5 11 5 11s5-7.25 5-11c0-2.76-2.24-5-5-5zm0 6.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" fill="currentColor" opacity=".4"/></svg>
                            <input type="text" id="fot_struct_dept" name="fot_struct_dept" class="fot-input" placeholder="Ex. 68 — Haut-Rhin" required>
                        </div>
                    </div>

                    <div class="fot-field">
                        <label for="fot_ref_name">Nom du référent <abbr title="requis">*</abbr></label>
                        <div class="fot-input-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><circle cx="10" cy="6" r="3.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 18c0-3.87 3.13-7 7-7s7 3.13 7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            <input type="text" id="fot_ref_name" name="fot_ref_name" class="fot-input" placeholder="Prénom Nom" required autocomplete="name">
                        </div>
                    </div>

                    <div class="fot-field">
                        <label for="fot_ref_role">Poste / Fonction</label>
                        <div class="fot-input-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><rect x="3" y="5" width="14" height="11" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M7 5V4a1 1 0 011-1h4a1 1 0 011 1v1" stroke="currentColor" stroke-width="1.5"/></svg>
                            <input type="text" id="fot_ref_role" name="fot_ref_role" class="fot-input" placeholder="Ex. Coordinatrice Inclusion">
                        </div>
                    </div>

                    <div class="fot-field">
                        <label for="fot_ref_email">Email professionnel <abbr title="requis">*</abbr></label>
                        <div class="fot-input-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><rect x="2" y="4" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M2 7l8 5 8-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            <input type="email" id="fot_ref_email" name="fot_ref_email" class="fot-input" placeholder="prenom.nom@structure.fr" required autocomplete="email">
                        </div>
                    </div>

                    <div class="fot-field">
                        <label for="fot_ref_phone">Téléphone</label>
                        <div class="fot-input-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><path d="M4.44 3.44l1.8-1.8a1 1 0 011.37-.07l2.22 1.87a1 1 0 01.1 1.41l-1.2 1.48a8.56 8.56 0 004.04 4.04l1.48-1.2a1 1 0 011.41.1l1.87 2.22a1 1 0 01-.07 1.37l-1.8 1.8a1.5 1.5 0 01-1.55.36A15.45 15.45 0 014.08 4.99a1.5 1.5 0 01.36-1.55z" stroke="currentColor" stroke-width="1.5"/></svg>
                            <input type="tel" id="fot_ref_phone" name="fot_ref_phone" class="fot-input" placeholder="03 89 32 81 50" autocomplete="tel">
                        </div>
                    </div>
                </div>

                <div class="fot-step-actions fot-step-actions--right">
                    <button type="button" class="fot-btn fot-btn--primary fot-next-btn" data-next="2">
                        Étape suivante
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="16"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>

            <!-- ═══ ÉTAPE 2 : OUTIL ═══ -->
            <div class="fot-step" id="fot-step-2" data-step="2" hidden>
                <div class="fot-step-header">
                    <div class="fot-step-badge fot-step-badge--2">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16"><path d="M10 2l2 4h4l-3 3 1 4-4-2-4 2 1-4-3-3h4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                        Étape 2 / 4
                    </div>
                    <div class="fot-step-tabs" aria-hidden="true">
                        <span class="is-done">Structure</span><span class="is-active">Outil</span><span>Photos</span><span>Validation</span>
                    </div>
                </div>
                <h2>Description de l'Outil</h2>
                <p class="fot-step-sub">Décrivez votre outil, son contexte d'usage et ses caractéristiques.</p>

                <div class="fot-fields">
                    <div class="fot-field fot-field--full">
                        <label for="fot_outil_name">Nom de l'outil <abbr title="requis">*</abbr></label>
                        <div class="fot-input-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><path d="M15 7H5M15 10H5M10 13H5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            <input type="text" id="fot_outil_name" name="fot_outil_name" class="fot-input" placeholder="Ex. Kit d'inclusion scolaire" required>
                        </div>
                    </div>

                    <div class="fot-field fot-field--full">
                        <label for="fot_outil_desc">
                            Description courte <abbr title="requis">*</abbr>
                            <span class="fot-charcount"><span id="fot-desc-count">0</span> / 400 caractères</span>
                        </label>
                        <textarea id="fot_outil_desc" name="fot_outil_desc" class="fot-textarea" rows="3" maxlength="400" placeholder="Décrivez votre outil en quelques phrases…" required></textarea>
                    </div>

                    <div class="fot-field fot-field--full">
                        <label for="fot_outil_context">Contexte d'utilisation <abbr title="requis">*</abbr></label>
                        <textarea id="fot_outil_context" name="fot_outil_context" class="fot-textarea" rows="4" placeholder="Dans quel contexte utilisez-vous cet outil ? Quelle problématique résout-il ?" required></textarea>
                    </div>

                    <div class="fot-field fot-field--full">
                        <fieldset>
                            <legend>Environnement d'usage <abbr title="requis">*</abbr></legend>
                            <div class="fot-env-pills">
                                <?php foreach ( $envs_choices as $val => $label ) : ?>
                                <label class="fot-env-pill">
                                    <input type="checkbox" name="fot_outil_envs[]" value="<?php echo esc_attr( $val ); ?>">
                                    <?php echo esc_html( $label ); ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                    </div>

                    <div class="fot-field fot-field--full">
                        <label for="fot_outil_freq">Fréquence d'usage</label>
                        <div class="fot-slider-wrap">
                            <input type="range" id="fot_outil_freq" name="fot_outil_freq" class="fot-slider" min="1" max="3" value="2" step="1">
                            <div class="fot-slider-labels" aria-hidden="true">
                                <span>Rare</span><span>Régulier</span><span>Quotidien</span>
                            </div>
                        </div>
                    </div>

                    <div class="fot-field fot-field--full">
                        <label>Mots-clés</label>
                        <div class="fot-tags-wrap">
                            <div class="fot-tags-list" id="fot-tags-list"></div>
                            <div class="fot-tags-input-row">
                                <input type="text" id="fot-tag-input" class="fot-input fot-tag-input" placeholder="Ajouter un mot-clé…">
                                <button type="button" class="fot-tag-add-btn" id="fot-tag-add" aria-label="Ajouter">
                                    <svg viewBox="0 0 16 16" fill="none" width="16" height="16"><path d="M8 2v12M2 8h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="fot_outil_tags" id="fot-tags-hidden">
                    </div>
                </div>

                <div class="fot-step-actions">
                    <button type="button" class="fot-btn fot-btn--ghost fot-back-btn" data-back="1">
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="16"><path d="M13 8H3M7 4L3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Retour
                    </button>
                    <button type="button" class="fot-btn fot-btn--primary fot-next-btn" data-next="3">
                        Étape suivante
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="16"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>

            <!-- ═══ ÉTAPE 3 : PHOTOS ═══ -->
            <div class="fot-step" id="fot-step-3" data-step="3" hidden>
                <div class="fot-step-header">
                    <div class="fot-step-badge fot-step-badge--3">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16"><rect x="2" y="5" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.5"/><circle cx="10" cy="11" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M6 5V4a2 2 0 012-2h4a2 2 0 012 2v1" stroke="currentColor" stroke-width="1.5"/></svg>
                        Étape 3 / 4
                    </div>
                    <div class="fot-step-tabs" aria-hidden="true">
                        <span class="is-done">Structure</span><span class="is-done">Outil</span><span class="is-active">Photos</span><span>Validation</span>
                    </div>
                </div>
                <h2>Photos du Terrain</h2>
                <p class="fot-step-sub">Ajoutez des photos illustrant votre outil en situation réelle.</p>

                <div class="fot-dropzone" id="fot-dropzone" role="region" aria-label="Zone de dépôt de photos">
                    <input type="file" id="fot_photos" name="fot_photos[]" accept="image/jpeg,image/png,image/webp" multiple class="fot-file-input" aria-label="Sélectionner des photos">
                    <div class="fot-dropzone-inner" id="fot-dropzone-inner">
                        <svg viewBox="0 0 48 48" fill="none" aria-hidden="true" width="48" height="48"><rect x="4" y="8" width="40" height="32" rx="4" stroke="currentColor" stroke-width="1.5" opacity=".3"/><path d="M16 28l6-8 5 6 3-4 6 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity=".5"/><circle cx="17" cy="18" r="2" fill="currentColor" opacity=".4"/><path d="M24 36v-12M18 30l6-6 6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <p><strong>Glissez vos photos ici</strong><br>ou cliquez pour parcourir vos fichiers</p>
                        <button type="button" class="fot-btn fot-btn--outline" id="fot-browse-btn">
                            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="14"><path d="M2 12l4-4 3 3 2-2 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            Parcourir les fichiers
                        </button>
                        <p class="fot-dropzone-hint">JPG, PNG — 5 Mo max — 5 photos maximum</p>
                    </div>
                </div>

                <div class="fot-photo-preview" id="fot-photo-preview"></div>

                <p class="fot-photos-note">
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="14" height="14"><circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.2"/><path d="M8 7v4M8 5.5v.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                    Les photos seront examinées par l'équipe PRH avant toute publication. Assurez-vous qu'elles respectent la vie privée des personnes photographiées (visages floutés ou consentement recueilli).
                </p>

                <div class="fot-step-actions">
                    <button type="button" class="fot-btn fot-btn--ghost fot-back-btn" data-back="2">
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="16"><path d="M13 8H3M7 4L3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Retour
                    </button>
                    <button type="button" class="fot-btn fot-btn--primary fot-next-btn" data-next="4">
                        Étape suivante
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="16"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>

            <!-- ═══ ÉTAPE 4 : VALIDATION ═══ -->
            <div class="fot-step" id="fot-step-4" data-step="4" hidden>
                <div class="fot-step-header">
                    <div class="fot-step-badge fot-step-badge--4">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16"><path d="M5 10l4 4 6-8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Étape 4 / 4
                    </div>
                    <div class="fot-step-tabs" aria-hidden="true">
                        <span class="is-done">Structure</span><span class="is-done">Outil</span><span class="is-done">Photos</span><span class="is-active">Validation</span>
                    </div>
                </div>
                <h2>Soumission &amp; Modération</h2>
                <p class="fot-step-sub">Vérifiez votre contribution avant de la transmettre à l'équipe PRH68.</p>

                <!-- Récap -->
                <div class="fot-recap">
                    <div class="fot-recap-header">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16"><path d="M5 10l4 4 6-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Récapitulatif de votre contribution
                    </div>
                    <div class="fot-recap-grid">
                        <div class="fot-recap-col">
                            <p class="fot-recap-label">STRUCTURE</p>
                            <p class="fot-recap-val" id="fot-recap-struct">—</p>
                            <p class="fot-recap-sub" id="fot-recap-type">—</p>
                        </div>
                        <div class="fot-recap-col">
                            <p class="fot-recap-label">RÉFÉRENT</p>
                            <p class="fot-recap-val" id="fot-recap-ref">—</p>
                            <p class="fot-recap-sub" id="fot-recap-role">—</p>
                        </div>
                        <div class="fot-recap-col fot-recap-col--full">
                            <p class="fot-recap-label">OUTIL PARTAGÉ</p>
                            <p class="fot-recap-val" id="fot-recap-outil">—</p>
                            <div id="fot-recap-envs" class="fot-recap-tags"></div>
                        </div>
                        <div class="fot-recap-col fot-recap-col--full">
                            <p class="fot-recap-label">PHOTOS</p>
                            <div class="fot-recap-photos-row">
                                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16"><rect x="2" y="5" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.4"/><circle cx="10" cy="11" r="3" stroke="currentColor" stroke-width="1.4"/><path d="M6 5V4a2 2 0 012-2h4a2 2 0 012 2v1" stroke="currentColor" stroke-width="1.4"/></svg>
                                <span class="fot-recap-val" id="fot-recap-photos">0 photo ajoutée</span>
                            </div>
                            <p class="fot-recap-sub" id="fot-recap-photos-sub"></p>
                        </div>
                    </div>
                </div>

                <!-- Processus de modération -->
                <div class="fot-moderation">
                    <h3>Processus de modération</h3>
                    <ol class="fot-mod-steps">
                        <li class="fot-mod-step">
                            <div class="fot-mod-icon fot-mod-icon--1" aria-hidden="true">
                                <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M4 10l5 5 7-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <div class="fot-mod-card">
                                <strong>Réception de votre contribution</strong>
                                <p>Votre fiche est reçue et enregistrée dans notre système. Vous recevrez un e-mail de confirmation immédiatement.</p>
                                <span class="fot-mod-badge fot-mod-badge--auto">
                                    <svg viewBox="0 0 12 12" fill="none" width="10" height="10" aria-hidden="true"><circle cx="6" cy="6" r="5" fill="currentColor" opacity=".3"/><circle cx="6" cy="6" r="2.5" fill="currentColor"/></svg>
                                    Immédiat
                                </span>
                            </div>
                        </li>
                        <li class="fot-mod-step">
                            <div class="fot-mod-icon fot-mod-icon--2" aria-hidden="true">
                                <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M10 6v4.5l3 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            </div>
                            <div class="fot-mod-card">
                                <strong>Examen par l'équipe PRH (5-7 jours)</strong>
                                <p>Notre équipe examine votre contribution : vérification de la pertinence, de la qualité des photos et de la conformité aux critères de la base.</p>
                                <span class="fot-mod-badge fot-mod-badge--review">
                                    <svg viewBox="0 0 12 12" fill="none" width="10" height="10" aria-hidden="true"><circle cx="6" cy="6" r="5" fill="currentColor" opacity=".3"/><circle cx="6" cy="6" r="2.5" fill="currentColor"/></svg>
                                    5-7 jours ouvrés
                                </span>
                            </div>
                        </li>
                        <li class="fot-mod-step">
                            <div class="fot-mod-icon fot-mod-icon--3" aria-hidden="true">
                                <svg viewBox="0 0 20 20" fill="none" width="18" height="18"><path d="M10 2l2.2 4.5 4.8.7-3.5 3.4.8 4.9L10 13.1l-4.3 2.4.8-4.9L3 7.2l4.8-.7z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                            </div>
                            <div class="fot-mod-card">
                                <strong>Publication dans la base PRH68</strong>
                                <p>Votre outil est accessible à l'ensemble des professionnels du réseau PRH68. Vous serez notifié par email lors de la mise en ligne.</p>
                                <span class="fot-mod-badge fot-mod-badge--pub">
                                    <svg viewBox="0 0 12 12" fill="none" width="10" height="10" aria-hidden="true"><circle cx="6" cy="6" r="5" fill="currentColor" opacity=".3"/><circle cx="6" cy="6" r="2.5" fill="currentColor"/></svg>
                                    Publication &amp; visibilité
                                </span>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Consentement -->
                <label class="fot-consent" id="fot-consent-label">
                    <input type="checkbox" name="fot_consent" id="fot_consent" value="1">
                    <span>En soumettant, vous acceptez que vos informations soient examinées par l'équipe PRH68 et publiées sous licence Creative Commons BY-SA 4.0. <a href="<?php echo esc_url( site_url( '/mentions-legales/' ) ); ?>" target="_blank">En savoir plus</a></span>
                </label>

                <div class="fot-step-actions">
                    <button type="button" class="fot-btn fot-btn--ghost fot-back-btn" data-back="3">
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="16"><path d="M13 8H3M7 4L3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Retour
                    </button>
                    <button type="submit" class="fot-btn fot-btn--submit">
                        Soumettre ma contribution
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="16"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>

        </form>
    </div>
</section>

<?php endif; ?>

<script>window.fotOrgSuggestions = <?php echo wp_json_encode( $_existing_orgs, JSON_UNESCAPED_UNICODE ); ?>;</script>
<?php get_footer(); ?>
