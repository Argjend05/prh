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
        'struct_name'        => sanitize_text_field(     $_POST['fot_struct_name']        ?? '' ),
        'struct_type'        => sanitize_text_field(     $_POST['fot_struct_type']        ?? '' ),
        'struct_type_autre'  => sanitize_text_field(     $_POST['fot_struct_type_autre']  ?? '' ),
        'struct_postal'      => sanitize_text_field(     $_POST['fot_struct_postal']      ?? '' ),
        'ref_name'           => sanitize_text_field(     $_POST['fot_ref_name']           ?? '' ),
        'ref_role'           => sanitize_text_field(     $_POST['fot_ref_role']           ?? '' ),
        'ref_email'          => sanitize_email(          $_POST['fot_ref_email']          ?? '' ),
        'ref_phone'          => sanitize_text_field(     $_POST['fot_ref_phone']          ?? '' ),
        'outil_name'         => sanitize_text_field(     $_POST['fot_outil_name']         ?? '' ),
        'outil_category'     => sanitize_text_field(     $_POST['fot_outil_category']     ?? '' ),
        'outil_category_autre' => sanitize_text_field(  $_POST['fot_outil_category_autre'] ?? '' ),
        'outil_desc'         => sanitize_textarea_field( $_POST['fot_outil_desc']         ?? '' ),
        'outil_context'      => sanitize_textarea_field( $_POST['fot_outil_context']      ?? '' ),
        'outil_envs'         => [],
        'outil_ages'         => array_map( 'sanitize_text_field', (array) ( $_POST['fot_outil_ages']  ?? [] ) ),
        'consent'            => ! empty( $_POST['fot_consent'] ),
    ];

    /* ── Validation ── */
    if ( empty( $d['struct_name'] ) )                                $fot_errors[] = "Le nom de la structure est requis.";
    if ( empty( $d['struct_type'] ) )                                $fot_errors[] = "Le type de structure est requis.";
    if ( $d['struct_type'] === 'autre' && empty( $d['struct_type_autre'] ) ) $fot_errors[] = "Précisez le type de structure.";
    if ( empty( $d['struct_postal'] ) )                              $fot_errors[] = "Le code postal est requis.";
    elseif ( ! preg_match( '/^\d{5}$/', $d['struct_postal'] ) )    $fot_errors[] = "Le code postal doit contenir exactement 5 chiffres.";
    if ( empty( $d['ref_name'] ) )                                   $fot_errors[] = "Le nom du référent est requis.";
    if ( ! is_email( $d['ref_email'] ) )                             $fot_errors[] = "Adresse e-mail invalide.";
    if ( strlen( $d['outil_name'] ) < 3 )                            $fot_errors[] = "Le nom de l'outil est requis.";
    if ( strlen( $d['outil_desc'] ) < 20 )                           $fot_errors[] = "La description doit faire au moins 20 caractères.";
    if ( strlen( $d['outil_desc'] ) > 1200 )                          $fot_errors[] = "La description est trop longue (1200 car. max).";
    if ( strlen( $d['outil_context'] ) < 30 )                        $fot_errors[] = "Le contexte d'utilisation est requis.";
    if ( empty( $d['outil_category'] ) )                             $fot_errors[] = "Sélectionnez un type de lieu d'accueil.";
    if ( $d['outil_category'] === 'autre' && empty( $d['outil_category_autre'] ) ) $fot_errors[] = "Précisez le type de lieu d'accueil.";
    if ( ! $d['consent'] )                                           $fot_errors[] = "Vous devez accepter les conditions avant de soumettre.";

    /* ── Création du post en attente ── */
    if ( empty( $fot_errors ) ) {

        $post_id = wp_insert_post( [
            'post_title'   => sanitize_text_field( $d['outil_name'] ),
            'post_type'    => 'outil_terrain',
            'post_status'  => 'pending',
            'post_content' => '',
        ] );

        if ( $post_id && ! is_wp_error( $post_id ) ) {

            /* ACF */
            if ( function_exists( 'update_field' ) ) {
                update_field( 'ot_description',   $d['outil_desc'],       $post_id );
                update_field( 'ot_story',         $d['outil_context'],    $post_id );
                // ot_envs supprimé du formulaire front-end
                update_field( 'ot_age_ranges',    $d['outil_ages'],       $post_id );
                update_field( 'ot_contact_name',  $d['ref_name'],         $post_id );
                update_field( 'ot_contact_role',  $d['ref_role'],         $post_id );
                update_field( 'ot_contact_org',   $d['struct_name'],      $post_id );
                if ( ! empty( $d['outil_category'] ) ) {
                    update_field( 'ot_category',  $d['outil_category'],   $post_id );
                }
                if ( ! empty( $d['struct_type'] ) ) {
                    update_field( 'ot_structure', $d['struct_type'],      $post_id );
                }
            }

            /* Méta de soumission (pour l'admin) */
            $meta = array_merge( $d, [
                'submitted_at' => current_time( 'mysql' ),
                'submitted_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
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
            $struct_display = $d['struct_type'] === 'autre' ? ( 'Autre : ' . $d['struct_type_autre'] ) : $d['struct_type'];
            $body       .= "Structure   : " . $d['struct_name'] . " (" . $struct_display . ") — CP " . $d['struct_postal'] . "\n";
            $body       .= "Référent    : " . $d['ref_name'] . " — " . $d['ref_email'];
            if ( ! empty( $d['ref_phone'] ) ) {
                $body   .= " — " . $d['ref_phone'];
            }
            $body       .= "\n";
            $lieu_label = $struct_types[ $d['outil_category'] ] ?? $d['outil_category'] ?: '—';
            if ( $d['outil_category'] === 'autre' && ! empty( $d['outil_category_autre'] ) ) {
                $lieu_label .= ' — ' . $d['outil_category_autre'];
            }
            $body       .= "Type de lieu   : " . $lieu_label . "\n";
            if ( ! empty( $d['outil_ages'] ) ) {
                $body   .= "Tranches d'âge : " . implode( ', ', $d['outil_ages'] ) . "\n";
            }
            $body       .= "\n";
            $body       .= "Voir la soumission : " . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . "\n";
            wp_mail( $admin_email, $subject, $body );

            /* Email de confirmation à l'expéditeur */
            $headers_html   = [ 'Content-Type: text/html; charset=UTF-8' ];
            $subj_confirm   = 'Votre contribution a bien été reçue – PRH68';
            $msg_confirm    = "
<!DOCTYPE html>
<html lang='fr'>
<body style='margin:0;padding:0;background:#f4f4f7;font-family:Arial,sans-serif;'>
<table width='100%' cellpadding='0' cellspacing='0' style='background:#f4f4f7;padding:32px 0;'>
  <tr><td align='center'>
    <table width='560' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);'>
      <!-- En-tête -->
      <tr><td style='background:#4b4b8b;padding:28px 32px;'>
        <h1 style='margin:0;color:#ffffff;font-size:20px;font-weight:700;'>Contribution bien reçue !</h1>
        <p style='margin:6px 0 0;color:rgba(255,255,255,.75);font-size:13px;'>PRH68 – Pôle Ressources Handicap du Haut-Rhin</p>
      </td></tr>
      <!-- Corps -->
      <tr><td style='padding:28px 32px 0;'>
        <p style='margin:0;font-size:15px;color:#333;line-height:1.6;'>Bonjour <strong>" . esc_html( $d['ref_name'] ) . "</strong>,</p>
        <p style='font-size:15px;color:#333;line-height:1.6;'>Nous avons bien reçu votre contribution concernant l'outil <strong style='color:#4b4b8b;'>" . esc_html( $d['outil_name'] ) . "</strong>.</p>
        <p style='font-size:15px;color:#333;line-height:1.6;'>Notre équipe va examiner votre fiche sous <strong>5 à 7 jours ouvrés</strong> et vous recontactera si nécessaire.</p>
      </td></tr>
      <!-- Séparateur -->
      <tr><td style='padding:20px 32px;'><hr style='border:none;border-top:1px solid #ebebf0;margin:0;'></td></tr>
      <!-- Récap -->
      <tr><td style='padding:0 32px;'>
        <p style='margin:0 0 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9b9bb0;'>Récapitulatif</p>
        <table width='100%' cellpadding='0' cellspacing='0'>
          <tr>
            <td style='padding:5px 0;font-size:14px;color:#666;width:140px;'>Outil</td>
            <td style='padding:5px 0;font-size:14px;color:#222;font-weight:600;'>" . esc_html( $d['outil_name'] ) . "</td>
          </tr>
          <tr>
            <td style='padding:5px 0;font-size:14px;color:#666;'>Structure</td>
            <td style='padding:5px 0;font-size:14px;color:#222;font-weight:600;'>" . esc_html( $d['struct_name'] ) . "</td>
          </tr>
          <tr>
            <td style='padding:5px 0;font-size:14px;color:#666;'>Référent</td>
            <td style='padding:5px 0;font-size:14px;color:#222;font-weight:600;'>" . esc_html( $d['ref_name'] ) . "</td>
          </tr>
        </table>
      </td></tr>
      <!-- Pied -->
      <tr><td style='padding:24px 32px 32px;background:#f8f8fb;border-radius:0 0 10px 10px;margin-top:20px;'>
        <p style='margin:0 0 4px;font-size:13px;color:#888;'>Des questions ? Contactez-nous :</p>
        <p style='margin:0;font-size:14px;color:#4b4b8b;font-weight:600;'>03 89 32 81 50</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body></html>";
            wp_mail( $d['ref_email'], $subj_confirm, $msg_confirm, $headers_html );

            $fot_success = true;
        } else {
            $fot_errors[] = "Une erreur est survenue lors de la soumission. Merci de réessayer.";
        }
    }
}

get_header();

$uri = get_stylesheet_directory_uri();

$struct_types = [
    ''                      => 'Sélectionnez un type…',
    'eaje'                  => 'EAJE',
    'assistante_maternelle' => 'Assistante maternelle',
    'rpe'                   => 'RPE (Relais Petite Enfance)',
    'acm'                   => 'ACM (Accueil Collectif de Mineurs)',
    'autre'                 => 'Autre',
];

$age_choices = [
    '0-3'   => '0-3 ans',
    '3-6'   => '3-6 ans',
    '6-12'  => '6-12 ans',
    '12-18' => '12-18 ans',
];

$envs_choices = [
    'urbain'   => 'Urbain',
    'rural'    => 'Rural',
    'domicile' => 'Domicile',
    'eaje'     => 'EAJE',
    'rue'      => 'Rue',
];

$category_choices = [
    ''               => 'Sélectionnez une catégorie…',
    'scolaire'       => 'Scolaire',
    'observation'    => 'Observation',
    'communication'  => 'Communication',
    'urgence'        => 'Urgence',
    'accueil'        => 'Accueil',
    'soutien'        => 'Soutien',
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
            <p class="fot-success-sub">Un e-mail de confirmation a été envoyé à <strong><?php echo esc_html( $d['ref_email'] ); ?></strong>.</p>
            <div class="fot-success-btns">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="fot-btn fot-btn--primary">Retour à l'accueil</a>
                <?php
                $_ot_pages = get_posts( [
                    'post_type' => 'page', 'posts_per_page' => 1, 'fields' => 'ids',
                    'meta_key' => '_wp_page_template', 'meta_value' => 'page-outils-terrain.php',
                    'post_status' => 'publish',
                ] );
                $_ot_url = ! empty( $_ot_pages ) ? get_permalink( $_ot_pages[0] ) : home_url( '/' );
                ?>
                <a href="<?php echo esc_url( $_ot_url ); ?>" class="fot-btn fot-btn--ghost">Voir les outils publiés</a>
            </div>
        </div>
    </div>
</section>

<?php else : ?>

<!-- ══ FORMULAIRE ════════════════════════════════════════ -->
<section class="fot-form-section">
    <div class="fot-container">

        <h1 class="fot-page-title">Partagez un Outil de Terrain</h1>

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
                        <label for="fot_struct_type">Type de lieu d'accueil <abbr title="requis">*</abbr></label>
                        <div class="fot-select-wrap">
                            <select id="fot_struct_type" name="fot_struct_type" class="fot-select" required>
                                <?php foreach ( $struct_types as $val => $label ) : ?>
                                <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <svg viewBox="0 0 12 8" fill="none" aria-hidden="true" width="12" class="fot-select-arrow"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </div>
                    </div>

                    <div class="fot-field fot-field--full" id="fot-struct-autre-wrap" hidden>
                        <label for="fot_struct_type_autre">Précisez le type de lieu d'accueil<abbr title="requis">*</abbr></label>
                        <div class="fot-input-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><path d="M4 6h12M4 10h8M4 14h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            <input type="text" id="fot_struct_type_autre" name="fot_struct_type_autre" class="fot-input" placeholder="Ex. Maison d'assistantes maternelles, CAMSP…">
                        </div>
                    </div>

                    <div class="fot-field">
                        <label for="fot_struct_postal">Code postal <abbr title="requis">*</abbr></label>
                        <div class="fot-input-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><path d="M10 2C7.24 2 5 4.24 5 7c0 3.75 5 11 5 11s5-7.25 5-11c0-2.76-2.24-5-5-5zm0 6.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" fill="currentColor" opacity=".4"/></svg>
                            <input type="text" id="fot_struct_postal" name="fot_struct_postal" class="fot-input" placeholder="Ex. 68100" required inputmode="numeric" maxlength="5" pattern="[0-9]{5}" title="5 chiffres requis"
                                value="<?php echo esc_attr( $_POST['fot_struct_postal'] ?? '' ); ?>">
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

                    <div class="fot-field">
                        <label for="fot_outil_category">Type de lieu d'accueil <abbr title="requis">*</abbr></label>
                        <div class="fot-select-wrap">
                            <select id="fot_outil_category" name="fot_outil_category" class="fot-select" required>
                                <?php foreach ( $struct_types as $val => $label ) : ?>
                                <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $_POST['fot_outil_category'] ?? $_POST['fot_struct_type'] ?? '', $val ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <svg viewBox="0 0 12 8" fill="none" aria-hidden="true" width="12" class="fot-select-arrow"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </div>
                    </div>

                    <div class="fot-field fot-field--full" id="fot-outil-autre-wrap" <?php echo ( ( $_POST['fot_outil_category'] ?? $_POST['fot_struct_type'] ?? '' ) !== 'autre' ) ? 'hidden' : ''; ?>>
                        <label for="fot_outil_category_autre">Précisez le type de lieu d'accueil <abbr title="requis">*</abbr></label>
                        <div class="fot-input-wrap">
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="16" height="16" class="fot-input-icon"><path d="M4 6h12M4 10h8M4 14h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            <input type="text" id="fot_outil_category_autre" name="fot_outil_category_autre" class="fot-input"
                                placeholder="Ex. Maison d'assistantes maternelles, CAMSP…"
                                value="<?php echo esc_attr( $_POST['fot_outil_category_autre'] ?? $_POST['fot_struct_type_autre'] ?? '' ); ?>"
                                <?php echo ( ( $_POST['fot_outil_category'] ?? $_POST['fot_struct_type'] ?? '' ) === 'autre' ) ? 'required' : ''; ?>>
                        </div>
                    </div>

                    <div class="fot-field fot-field--full">
                        <label for="fot_outil_desc">
                            Description courte <abbr title="requis">*</abbr>
                            <span class="fot-charcount"><span id="fot-desc-count">0</span> / 1200 caractères</span>
                        </label>
                        <textarea id="fot_outil_desc" name="fot_outil_desc" class="fot-textarea" rows="3" maxlength="1200" placeholder="Décrivez votre outil en quelques phrases…" required></textarea>
                    </div>

                    <div class="fot-field fot-field--full">
                        <label for="fot_outil_context">Contexte d'utilisation <abbr title="requis">*</abbr></label>
                        <textarea id="fot_outil_context" name="fot_outil_context" class="fot-textarea" rows="4" placeholder="Dans quel contexte utilisez-vous cet outil ? Quelle problématique résout-il ?" required></textarea>
                    </div>


                    <div class="fot-field fot-field--full">
                        <fieldset>
                            <legend>Tranche(s) d'âge concernée(s)</legend>
                            <div class="fot-env-pills">
                                <?php foreach ( $age_choices as $val => $label ) : ?>
                                <label class="fot-env-pill">
                                    <input type="checkbox" name="fot_outil_ages[]" value="<?php echo esc_attr( $val ); ?>">
                                    <?php echo esc_html( $label ); ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
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
                    <div class="fot-dropzone-inner">

                        <!-- Vue par défaut : aucune photo -->
                        <div class="fot-dz-empty" id="fot-dz-empty">
                            <svg viewBox="0 0 48 48" fill="none" aria-hidden="true" width="44" height="44">
                                <rect x="4" y="8" width="40" height="32" rx="4" stroke="currentColor" stroke-width="1.5" opacity=".3"/>
                                <circle cx="17" cy="20" r="2.5" fill="currentColor" opacity=".4"/>
                                <path d="M4 33l11-12 7 8 5-6 8 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity=".5"/>
                                <path d="M30 13v10M25 18h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <p><strong>Glissez vos photos ici</strong><br>ou sélectionnez-les depuis votre appareil</p>
                        </div>

                        <!-- Vue résumé : visible dès qu'une photo est ajoutée -->
                        <div class="fot-dz-filled" id="fot-dz-filled" hidden>
                            <svg viewBox="0 0 20 20" fill="none" aria-hidden="true" width="18" height="18">
                                <rect x="2" y="5" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.4"/>
                                <circle cx="10" cy="11" r="3" stroke="currentColor" stroke-width="1.4"/>
                                <path d="M6 5V4a2 2 0 012-2h4a2 2 0 012 2v1" stroke="currentColor" stroke-width="1.4"/>
                            </svg>
                            <span id="fot-photo-count-label">0 photo ajoutée</span>
                        </div>

                        <!-- Bouton (texte mis à jour par JS) -->
                        <button type="button" class="fot-btn fot-btn--outline" id="fot-browse-btn">
                            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" width="14">
                                <path d="M8 1v9M4 5l4-4 4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 13h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                            <span id="fot-browse-label">Choisir des photos</span>
                        </button>

                        <p class="fot-dropzone-hint" id="fot-dropzone-hint">JPG, PNG, WebP · 5 Mo max · 5 photos maximum</p>
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
                            <div id="fot-recap-ages" class="fot-recap-tags" style="margin-top:6px;"></div>
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
