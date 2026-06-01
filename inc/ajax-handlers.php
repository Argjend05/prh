<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* =======================================================
   AJAX — Formulaire témoignage
   action: ftem_submit  (nopriv + authentifié)
   ======================================================= */
add_action( 'wp_ajax_nopriv_ftem_submit', 'prh68_handle_ftem_submit' );
add_action( 'wp_ajax_ftem_submit',        'prh68_handle_ftem_submit' );

function prh68_handle_ftem_submit() {

    /* ── Nonce ────────────────────────────────────────── */
    if ( ! check_ajax_referer( 'ftem_submit', 'ftem_nonce', false ) ) {
        wp_send_json_error( [ 'message' => "Session expirée. Merci de recharger la page." ] );
    }

    /* ── Honeypot ─────────────────────────────────────── */
    if ( ! empty( $_POST['ftem_website'] ) ) {
        wp_send_json_error( [ 'message' => "Erreur de validation." ] );
    }

    /* ── Récupération ─────────────────────────────────── */
    $d = [
        'category'  => sanitize_text_field(  $_POST['ftem_category']  ?? '' ),
        'type'      => sanitize_text_field(  $_POST['ftem_type']      ?? 'texte' ),
        'quote'     => sanitize_textarea_field( $_POST['ftem_quote']  ?? '' ),
        'name'      => sanitize_text_field(  $_POST['ftem_name']      ?? '' ),
        'role'      => sanitize_text_field(  $_POST['ftem_role']      ?? '' ),
        'email'     => sanitize_email(       $_POST['ftem_email']     ?? '' ),
        'phone'     => sanitize_text_field(  $_POST['ftem_phone']     ?? '' ),
        'video_url' => esc_url_raw(          $_POST['ftem_video_url'] ?? '' ),
        'consent'   => ! empty( $_POST['ftem_consent'] ),
        'anon'      => ! empty( $_POST['ftem_anonymous'] ),
    ];

    /* ── Validation ───────────────────────────────────── */
    $errors = [];

    if ( ! in_array( $d['category'], [ 'parent', 'professionnel', 'personne_accompagnee' ], true ) ) {
        $errors[] = "Merci de sélectionner une catégorie.";
    }
    if ( ! in_array( $d['type'], [ 'texte', 'audio', 'video', 'video_audio' ], true ) ) {
        $errors[] = "Type de témoignage invalide.";
    }
    if ( strlen( $d['quote'] ) < 30 ) {
        $errors[] = "Votre témoignage doit faire au moins 30 caractères.";
    }
    if ( strlen( $d['quote'] ) > 4000 ) {
        $errors[] = "Votre témoignage est trop long (max 4000 caractères).";
    }
    if ( ! is_email( $d['email'] ) ) {
        $errors[] = "Adresse email invalide.";
    }
    if ( ! $d['consent'] ) {
        $errors[] = "Vous devez accepter les conditions de publication.";
    }
    if ( in_array( $d['type'], [ 'video', 'video_audio' ], true ) && empty( $d['video_url'] ) ) {
        $errors[] = "Merci de fournir un lien YouTube ou Vimeo pour la vidéo.";
    }

    if ( ! empty( $errors ) ) {
        wp_send_json_error( [ 'message' => implode( ' ', $errors ), 'errors' => $errors ] );
    }

    /* ── Upload audio ─────────────────────────────────── */
    $audio_attach_id = 0;
    if ( in_array( $d['type'], [ 'audio', 'video_audio' ], true ) ) {
        if ( empty( $_FILES['ftem_audio_file']['name'] ) ) {
            wp_send_json_error( [ 'message' => "Merci de joindre un fichier audio.", 'errors' => [ "Merci de joindre un fichier audio." ] ] );
        }

        $allowed_mimes = [
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/wav',
            'm4a'  => 'audio/mp4',
            'ogg'  => 'audio/ogg',
            'webm' => 'audio/webm',
        ];

        $check = wp_check_filetype_and_ext(
            $_FILES['ftem_audio_file']['tmp_name'],
            $_FILES['ftem_audio_file']['name'],
            $allowed_mimes
        );

        if ( ! $check['type'] ) {
            wp_send_json_error( [ 'message' => "Format audio non autorisé (mp3, wav, m4a, ogg uniquement).", 'errors' => [ "Format audio non autorisé." ] ] );
        }
        if ( $_FILES['ftem_audio_file']['size'] > 25 * 1024 * 1024 ) {
            wp_send_json_error( [ 'message' => "Fichier audio trop lourd (max 25 Mo).", 'errors' => [ "Fichier audio trop lourd (max 25 Mo)." ] ] );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $movefile = wp_handle_upload( $_FILES['ftem_audio_file'], [ 'test_form' => false, 'mimes' => $allowed_mimes ] );

        if ( ! $movefile || ! empty( $movefile['error'] ) ) {
            wp_send_json_error( [ 'message' => "Échec de l'upload du fichier audio.", 'errors' => [ "Échec de l'upload." ] ] );
        }

        $attachment = [
            'post_mime_type' => $movefile['type'],
            'post_title'     => sanitize_file_name( pathinfo( $movefile['file'], PATHINFO_FILENAME ) ),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ];
        $audio_attach_id = wp_insert_attachment( $attachment, $movefile['file'] );
        if ( $audio_attach_id ) {
            $meta = wp_generate_attachment_metadata( $audio_attach_id, $movefile['file'] );
            wp_update_attachment_metadata( $audio_attach_id, $meta );
        }
    }

    /* ── Création du CPT ──────────────────────────────── */
    $display_name = $d['anon'] ? 'Témoignage anonyme' : ( $d['name'] ?: 'Témoignage anonyme' );

    $post_id = wp_insert_post( [
        'post_type'    => 'temoignage',
        'post_status'  => 'pending',
        'post_title'   => $display_name . ' — ' . wp_date( 'd/m/Y H:i' ),
        'post_content' => $d['quote'],
    ] );

    if ( ! $post_id || is_wp_error( $post_id ) ) {
        wp_send_json_error( [ 'message' => "Erreur lors de l'enregistrement. Merci de réessayer.", 'errors' => [ "Erreur d'enregistrement." ] ] );
    }

    /* ── Champs ACF ───────────────────────────────────── */
    update_field( 'temoig_category',    $d['category'],                                  $post_id );
    update_field( 'temoig_type',        $d['type'],                                       $post_id );
    update_field( 'temoig_quote',       $d['quote'],                                      $post_id );
    update_field( 'temoig_person_name', $d['anon'] ? 'Témoignage anonyme' : $d['name'], $post_id );
    update_field( 'temoig_person_role', $d['anon'] ? '' : $d['role'],                   $post_id );

    if ( in_array( $d['type'], [ 'video', 'video_audio' ], true ) )                     update_field( 'temoig_video_url',  $d['video_url'],  $post_id );
    if ( in_array( $d['type'], [ 'audio', 'video_audio' ], true ) && $audio_attach_id ) update_field( 'temoig_audio_file', $audio_attach_id, $post_id );

    /* ── Meta internes ────────────────────────────────── */
    update_post_meta( $post_id, '_ftem_submit_email', $d['email'] );
    if ( $d['phone'] ) update_post_meta( $post_id, '_ftem_submit_phone', $d['phone'] );
    if ( $d['anon'] && $d['name'] ) update_post_meta( $post_id, '_ftem_submit_real_name', $d['name'] ); // conservé en interne uniquement
    update_post_meta( $post_id, '_ftem_submit_date', current_time( 'mysql' ) );
    update_post_meta( $post_id, '_ftem_submit_ip',   $_SERVER['REMOTE_ADDR'] ?? '' );

    /* ── Email admin ──────────────────────────────────── */
    $admin_email = get_option( 'admin_email' );
    $edit_link   = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
    $subject     = '[PRH68] Nouveau témoignage à valider';
    $body  = "Un nouveau témoignage a été soumis et attend modération.\n\n";
    $body .= "Catégorie : " . $d['category'] . "\n";
    $body .= "Type : "      . $d['type']     . "\n";
    $body .= "Auteur : "    . $display_name  . "\n";
    $body .= "Email : "     . $d['email']    . "\n";
    if ( $d['phone'] ) $body .= "Téléphone : " . $d['phone'] . "\n";
    $body .= "\nExtrait :\n" . wp_trim_words( $d['quote'], 40 ) . "\n\n";
    $body .= "Modérer : " . $edit_link;
    wp_mail( $admin_email, $subject, $body );

    /* ── Email de confirmation à l'expéditeur ─────────── */
    $cat_labels = [
        'parent'               => 'Parent',
        'professionnel'        => 'Professionnel',
        'personne_accompagnee' => 'Personne accompagnée',
    ];
    $type_labels = [
        'texte'       => 'Texte écrit',
        'audio'       => 'Audio',
        'video'       => 'Vidéo',
        'video_audio' => 'Vidéo + Audio',
    ];

    $confirm_subject = 'Votre témoignage a bien été reçu – PRH68';
    $confirm_body = '
<!DOCTYPE html>
<html lang="fr">
<body style="margin:0;padding:0;background:#f4f4f7;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7;padding:32px 0;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);">
      <tr><td style="background:#4b4b8b;padding:28px 32px;">
        <h1 style="margin:0;color:#fff;font-size:20px;font-weight:700;">Témoignage bien reçu !</h1>
        <p style="margin:6px 0 0;color:rgba(255,255,255,.75);font-size:13px;">PRH68 – Pôle Ressources Handicap du Haut-Rhin</p>
      </td></tr>
      <tr><td style="padding:28px 32px 0;">
        <p style="margin:0;font-size:15px;color:#333;line-height:1.6;">Bonjour' . ( $d['name'] ? ' <strong>' . esc_html( $d['name'] ) . '</strong>' : '' ) . ',</p>
        <p style="font-size:15px;color:#333;line-height:1.6;">Nous avons bien reçu votre témoignage et vous en remercions chaleureusement.</p>
        <p style="font-size:15px;color:#333;line-height:1.6;">Notre équipe va l\'examiner et vous recontactera par email si nécessaire avant publication.</p>
      </td></tr>
      <tr><td style="padding:20px 32px;"><hr style="border:none;border-top:1px solid #ebebf0;margin:0;"></td></tr>
      <tr><td style="padding:0 32px;">
        <p style="margin:0 0 14px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9b9bb0;">Récapitulatif</p>
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td style="padding:5px 0;font-size:14px;color:#666;width:140px;">Catégorie</td>
            <td style="padding:5px 0;font-size:14px;color:#222;font-weight:600;">' . esc_html( $cat_labels[ $d['category'] ] ?? $d['category'] ) . '</td>
          </tr>
          <tr>
            <td style="padding:5px 0;font-size:14px;color:#666;">Format</td>
            <td style="padding:5px 0;font-size:14px;color:#222;font-weight:600;">' . esc_html( $type_labels[ $d['type'] ] ?? $d['type'] ) . '</td>
          </tr>' .
          ( $d['anon'] ? '<tr><td style="padding:5px 0;font-size:14px;color:#666;">Anonymat</td><td style="padding:5px 0;font-size:14px;color:#222;font-weight:600;">Oui — votre prénom ne sera pas affiché</td></tr>' : '' ) . '
        </table>
      </td></tr>
      <tr><td style="padding:20px 32px;"><hr style="border:none;border-top:1px solid #ebebf0;margin:0;"></td></tr>
      <tr><td style="padding:0 32px 28px;">
        <p style="margin:0;font-size:13px;color:#999;line-height:1.6;">Vous pouvez nous contacter à tout moment à <a href="mailto:contact@prh68.fr" style="color:#4b4b8b;">contact@prh68.fr</a> pour modifier ou supprimer votre témoignage.</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>';

    wp_mail(
        $d['email'],
        $confirm_subject,
        $confirm_body,
        [ 'Content-Type: text/html; charset=UTF-8' ]
    );

    wp_send_json_success( [ 'message' => "Merci pour votre témoignage ! Il sera relu avant publication." ] );
}
