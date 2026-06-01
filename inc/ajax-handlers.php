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
    if ( ! in_array( $d['type'], [ 'texte', 'audio', 'video' ], true ) ) {
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
    if ( $d['type'] === 'video' && empty( $d['video_url'] ) ) {
        $errors[] = "Merci de fournir un lien YouTube ou Vimeo pour la vidéo.";
    }

    if ( ! empty( $errors ) ) {
        wp_send_json_error( [ 'message' => implode( ' ', $errors ), 'errors' => $errors ] );
    }

    /* ── Upload audio ─────────────────────────────────── */
    $audio_attach_id = 0;
    if ( $d['type'] === 'audio' ) {
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
    update_field( 'temoig_person_name', $d['anon'] ? '' : $d['name'],                    $post_id );
    update_field( 'temoig_person_role', $d['role'],                                       $post_id );

    if ( $d['type'] === 'video' )                     update_field( 'temoig_video_url',  $d['video_url'],      $post_id );
    if ( $d['type'] === 'audio' && $audio_attach_id ) update_field( 'temoig_audio_file', $audio_attach_id,     $post_id );

    /* ── Meta internes ────────────────────────────────── */
    update_post_meta( $post_id, '_ftem_submit_email', $d['email'] );
    if ( $d['phone'] ) update_post_meta( $post_id, '_ftem_submit_phone', $d['phone'] );
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

    wp_send_json_success( [ 'message' => "Merci pour votre témoignage ! Il sera relu avant publication." ] );
}
