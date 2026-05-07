<?php
/* Template Name: Formulaire Témoignage */

/* ─────────────────────────────────────────────────────────
   TRAITEMENT DU FORMULAIRE
   ───────────────────────────────────────────────────────── */
$ftem_errors  = [];
$ftem_success = false;
$ftem_old     = [];

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['ftem_nonce'] ) ) {

    /* ── Sécurité : nonce + honeypot ── */
    if ( ! wp_verify_nonce( $_POST['ftem_nonce'], 'ftem_submit' ) ) {
        $ftem_errors[] = "Session expirée. Merci de recharger la page.";
    }
    if ( ! empty( $_POST['ftem_website'] ) ) {
        // Honeypot rempli = bot
        $ftem_errors[] = "Erreur de validation.";
    }

    /* ── Récupération des champs ── */
    $ftem_old = [
        'category' => sanitize_text_field( $_POST['ftem_category'] ?? '' ),
        'type'     => sanitize_text_field( $_POST['ftem_type'] ?? 'texte' ),
        'quote'    => sanitize_textarea_field( $_POST['ftem_quote'] ?? '' ),
        'name'     => sanitize_text_field( $_POST['ftem_name'] ?? '' ),
        'role'     => sanitize_text_field( $_POST['ftem_role'] ?? '' ),
        'email'    => sanitize_email( $_POST['ftem_email'] ?? '' ),
        'phone'    => sanitize_text_field( $_POST['ftem_phone'] ?? '' ),
        'video_url'=> esc_url_raw( $_POST['ftem_video_url'] ?? '' ),
        'consent'  => ! empty( $_POST['ftem_consent'] ),
        'anon'     => ! empty( $_POST['ftem_anonymous'] ),
    ];

    /* ── Validation ── */
    $allowed_cats  = [ 'parent', 'professionnel', 'personne_accompagnee' ];
    $allowed_types = [ 'texte', 'audio', 'video' ];

    if ( ! in_array( $ftem_old['category'], $allowed_cats, true ) ) {
        $ftem_errors[] = "Merci de sélectionner une catégorie.";
    }
    if ( ! in_array( $ftem_old['type'], $allowed_types, true ) ) {
        $ftem_errors[] = "Type de témoignage invalide.";
    }
    if ( strlen( $ftem_old['quote'] ) < 30 ) {
        $ftem_errors[] = "Votre témoignage doit faire au moins 30 caractères.";
    }
    if ( strlen( $ftem_old['quote'] ) > 4000 ) {
        $ftem_errors[] = "Votre témoignage est trop long (max 4000 caractères).";
    }
    if ( ! is_email( $ftem_old['email'] ) ) {
        $ftem_errors[] = "Adresse email invalide.";
    }
    if ( ! $ftem_old['consent'] ) {
        $ftem_errors[] = "Vous devez accepter les conditions de publication.";
    }
    if ( $ftem_old['type'] === 'video' && empty( $ftem_old['video_url'] ) ) {
        $ftem_errors[] = "Merci de fournir un lien YouTube ou Vimeo pour la vidéo.";
    }

    /* ── Gestion du fichier audio ── */
    $ftem_audio_attach_id = 0;
    if ( $ftem_old['type'] === 'audio' && empty( $ftem_errors ) ) {
        if ( empty( $_FILES['ftem_audio_file']['name'] ) ) {
            $ftem_errors[] = "Merci de joindre un fichier audio.";
        } else {
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
                $ftem_errors[] = "Format audio non autorisé (mp3, wav, m4a, ogg uniquement).";
            } elseif ( $_FILES['ftem_audio_file']['size'] > 25 * 1024 * 1024 ) {
                $ftem_errors[] = "Fichier audio trop lourd (max 25 Mo).";
            } else {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $upload_overrides = [ 'test_form' => false, 'mimes' => $allowed_mimes ];
                $movefile = wp_handle_upload( $_FILES['ftem_audio_file'], $upload_overrides );

                if ( $movefile && empty( $movefile['error'] ) ) {
                    $attachment = [
                        'post_mime_type' => $movefile['type'],
                        'post_title'     => sanitize_file_name( pathinfo( $movefile['file'], PATHINFO_FILENAME ) ),
                        'post_content'   => '',
                        'post_status'    => 'inherit',
                    ];
                    $ftem_audio_attach_id = wp_insert_attachment( $attachment, $movefile['file'] );
                    if ( $ftem_audio_attach_id ) {
                        $meta = wp_generate_attachment_metadata( $ftem_audio_attach_id, $movefile['file'] );
                        wp_update_attachment_metadata( $ftem_audio_attach_id, $meta );
                    }
                } else {
                    $ftem_errors[] = "Échec de l'upload du fichier audio.";
                }
            }
        }
    }

    /* ── Création du post si tout est OK ── */
    if ( empty( $ftem_errors ) ) {
        $display_name = $ftem_old['anon']
            ? 'Témoignage anonyme'
            : ( $ftem_old['name'] ?: 'Témoignage anonyme' );

        $post_id = wp_insert_post( [
            'post_type'    => 'temoignage',
            'post_status'  => 'pending',
            'post_title'   => $display_name . ' — ' . wp_date( 'd/m/Y H:i' ),
            'post_content' => $ftem_old['quote'],
        ] );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_field( 'temoig_category', $ftem_old['category'], $post_id );
            update_field( 'temoig_type', $ftem_old['type'], $post_id );
            update_field( 'temoig_quote', $ftem_old['quote'], $post_id );
            update_field( 'temoig_person_name', $ftem_old['anon'] ? '' : $ftem_old['name'], $post_id );
            update_field( 'temoig_person_role', $ftem_old['role'], $post_id );

            if ( $ftem_old['type'] === 'video' ) {
                update_field( 'temoig_video_url', $ftem_old['video_url'], $post_id );
            }
            if ( $ftem_old['type'] === 'audio' && $ftem_audio_attach_id ) {
                update_field( 'temoig_audio_file', $ftem_audio_attach_id, $post_id );
            }

            // Stocker email/téléphone pour contact admin (champs internes)
            update_post_meta( $post_id, '_ftem_submit_email', $ftem_old['email'] );
            if ( $ftem_old['phone'] ) {
                update_post_meta( $post_id, '_ftem_submit_phone', $ftem_old['phone'] );
            }
            update_post_meta( $post_id, '_ftem_submit_date', current_time( 'mysql' ) );
            update_post_meta( $post_id, '_ftem_submit_ip', $_SERVER['REMOTE_ADDR'] ?? '' );

            // Notification email à l'admin
            $admin_email = get_option( 'admin_email' );
            $edit_link   = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
            $subject     = '[PRH68] Nouveau témoignage à valider';
            $body  = "Un nouveau témoignage a été soumis et attend modération.\n\n";
            $body .= "Catégorie : " . ( $ftem_old['category'] ) . "\n";
            $body .= "Type : " . $ftem_old['type'] . "\n";
            $body .= "Auteur : " . $display_name . "\n";
            $body .= "Email : " . $ftem_old['email'] . "\n";
            if ( $ftem_old['phone'] ) $body .= "Téléphone : " . $ftem_old['phone'] . "\n";
            $body .= "\nExtrait :\n" . wp_trim_words( $ftem_old['quote'], 40 ) . "\n\n";
            $body .= "Modérer : " . $edit_link;

            wp_mail( $admin_email, $subject, $body );

            $ftem_success = true;
            $ftem_old = []; // reset
        } else {
            $ftem_errors[] = "Erreur lors de l'enregistrement. Merci de réessayer.";
        }
    }
}

get_header();
?>

<div class="ftem-page">

    <!-- ────────── HERO ────────── -->
    <section class="ftem-hero">
        <div class="ftem-hero-deco" aria-hidden="true"></div>
        <div class="ftem-container ftem-hero-inner">
            <span class="ftem-hero-eyebrow">Partager une expérience</span>
            <h1>Votre témoignage compte</h1>
            <p>Parents, professionnels, personnes accompagnées : votre parcours peut éclairer, inspirer et aider d'autres familles. Tous les témoignages sont relus avant publication.</p>
        </div>
        <div class="ftem-hero-wave" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2880 70" preserveAspectRatio="none">
                <path d="M0 35 Q360 0 720 35 T1440 35 T2160 35 T2880 35 V70 H0 Z" fill="#fff"/>
            </svg>
        </div>
    </section>

    <!-- ────────── FORMULAIRE ────────── -->
    <section class="ftem-form-section">
        <div class="ftem-container">

            <?php if ( $ftem_success ) : ?>
                <div class="ftem-success" role="status">
                    <div class="ftem-success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="48" height="48">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <h2>Merci pour votre témoignage&nbsp;!</h2>
                    <p>Il a bien été reçu et sera relu par notre équipe avant publication. Nous reviendrons vers vous par email si besoin.</p>
                    <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'temoignages' ) ) ?: home_url( '/' ) ); ?>" class="ftem-btn ftem-btn--primary">
                        Retour aux témoignages
                    </a>
                </div>
            <?php else : ?>

                <?php if ( ! empty( $ftem_errors ) ) : ?>
                    <div class="ftem-errors" role="alert">
                        <strong>Quelques points à corriger :</strong>
                        <ul>
                            <?php foreach ( $ftem_errors as $err ) : ?>
                                <li><?php echo esc_html( $err ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="ftem-form" method="post" enctype="multipart/form-data" novalidate>
                    <?php wp_nonce_field( 'ftem_submit', 'ftem_nonce' ); ?>
                    <input type="text" name="ftem_website" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">

                    <!-- Étape 1 : Catégorie -->
                    <fieldset class="ftem-fieldset">
                        <legend class="ftem-legend">
                            <span class="ftem-step">1</span>
                            Vous êtes…
                        </legend>
                        <div class="ftem-radio-cards">
                            <?php
                            $cats = [
                                'parent' => [ 'label' => 'Parent', 'desc' => 'Parent ou aidant familial' ],
                                'professionnel' => [ 'label' => 'Professionnel', 'desc' => 'Éducateur, soignant, animateur…' ],
                                'personne_accompagnee' => [ 'label' => 'Personne accompagnée', 'desc' => 'Vous avez vous-même bénéficié d\'un accompagnement' ],
                            ];
                            foreach ( $cats as $key => $info ) :
                                $checked = ( ( $ftem_old['category'] ?? '' ) === $key ) ? 'checked' : '';
                            ?>
                                <label class="ftem-radio-card ftem-radio-card--<?php echo esc_attr( $key ); ?>">
                                    <input type="radio" name="ftem_category" value="<?php echo esc_attr( $key ); ?>" <?php echo $checked; ?> required>
                                    <span class="ftem-radio-card-title"><?php echo esc_html( $info['label'] ); ?></span>
                                    <span class="ftem-radio-card-desc"><?php echo esc_html( $info['desc'] ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <!-- Étape 2 : Format -->
                    <fieldset class="ftem-fieldset">
                        <legend class="ftem-legend">
                            <span class="ftem-step">2</span>
                            Format du témoignage
                        </legend>
                        <div class="ftem-radio-pills">
                            <?php
                            $types = [
                                'texte' => 'Texte écrit',
                                'audio' => 'Audio',
                                'video' => 'Vidéo (lien)',
                            ];
                            foreach ( $types as $key => $label ) :
                                $checked = ( ( $ftem_old['type'] ?? 'texte' ) === $key ) ? 'checked' : '';
                            ?>
                                <label class="ftem-radio-pill">
                                    <input type="radio" name="ftem_type" value="<?php echo esc_attr( $key ); ?>" <?php echo $checked; ?>>
                                    <span><?php echo esc_html( $label ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- Champs conditionnels Audio -->
                        <div class="ftem-cond" data-show-if-type="audio">
                            <label class="ftem-label" for="ftem_audio_file">
                                Fichier audio <span class="ftem-required">*</span>
                                <span class="ftem-hint">mp3, wav, m4a, ogg — max 25 Mo</span>
                            </label>
                            <div class="ftem-file-wrap">
                                <input type="file" id="ftem_audio_file" name="ftem_audio_file" accept="audio/mpeg,audio/wav,audio/mp4,audio/ogg,audio/webm,.mp3,.wav,.m4a,.ogg">
                                <span class="ftem-file-name" data-empty="Aucun fichier sélectionné"></span>
                            </div>
                        </div>

                        <!-- Champs conditionnels Vidéo -->
                        <div class="ftem-cond" data-show-if-type="video">
                            <label class="ftem-label" for="ftem_video_url">
                                Lien YouTube ou Vimeo <span class="ftem-required">*</span>
                                <span class="ftem-hint">La vidéo doit être hébergée et publique</span>
                            </label>
                            <input type="url" id="ftem_video_url" name="ftem_video_url"
                                   value="<?php echo esc_attr( $ftem_old['video_url'] ?? '' ); ?>"
                                   placeholder="https://www.youtube.com/watch?v=…"
                                   class="ftem-input">
                        </div>
                    </fieldset>

                    <!-- Étape 3 : Témoignage -->
                    <fieldset class="ftem-fieldset">
                        <legend class="ftem-legend">
                            <span class="ftem-step">3</span>
                            Votre témoignage
                        </legend>

                        <label class="ftem-label" for="ftem_quote">
                            Texte du témoignage <span class="ftem-required">*</span>
                            <span class="ftem-hint">Pour les vidéos/audio, écrivez un résumé ou la transcription</span>
                        </label>
                        <textarea id="ftem_quote" name="ftem_quote" rows="7" required
                                  minlength="30" maxlength="4000"
                                  class="ftem-textarea"
                                  placeholder="Racontez votre expérience…"><?php echo esc_textarea( $ftem_old['quote'] ?? '' ); ?></textarea>
                        <div class="ftem-counter"><span class="ftem-counter-num">0</span> / 4000 caractères</div>
                    </fieldset>

                    <!-- Étape 4 : Identité -->
                    <fieldset class="ftem-fieldset">
                        <legend class="ftem-legend">
                            <span class="ftem-step">4</span>
                            Vos coordonnées
                        </legend>

                        <div class="ftem-grid-2">
                            <div>
                                <label class="ftem-label" for="ftem_name">Prénom <span class="ftem-hint">(optionnel)</span></label>
                                <input type="text" id="ftem_name" name="ftem_name"
                                       value="<?php echo esc_attr( $ftem_old['name'] ?? '' ); ?>"
                                       class="ftem-input"
                                       placeholder="Marie">
                            </div>
                            <div>
                                <label class="ftem-label" for="ftem_role">Rôle / contexte <span class="ftem-hint">(optionnel)</span></label>
                                <input type="text" id="ftem_role" name="ftem_role"
                                       value="<?php echo esc_attr( $ftem_old['role'] ?? '' ); ?>"
                                       class="ftem-input"
                                       placeholder="Maman de Léo, 6 ans">
                            </div>
                        </div>

                        <label class="ftem-checkbox">
                            <input type="checkbox" name="ftem_anonymous" value="1" <?php checked( $ftem_old['anon'] ?? false ); ?>>
                            <span>Publier mon témoignage de façon <strong>anonyme</strong> (votre prénom ne sera pas affiché)</span>
                        </label>

                        <div class="ftem-grid-2">
                            <div>
                                <label class="ftem-label" for="ftem_email">Email <span class="ftem-required">*</span> <span class="ftem-hint">(non publié)</span></label>
                                <input type="email" id="ftem_email" name="ftem_email" required
                                       value="<?php echo esc_attr( $ftem_old['email'] ?? '' ); ?>"
                                       class="ftem-input"
                                       placeholder="vous@email.fr">
                            </div>
                            <div>
                                <label class="ftem-label" for="ftem_phone">Téléphone <span class="ftem-hint">(optionnel)</span></label>
                                <input type="tel" id="ftem_phone" name="ftem_phone"
                                       value="<?php echo esc_attr( $ftem_old['phone'] ?? '' ); ?>"
                                       class="ftem-input"
                                       placeholder="06 12 34 56 78">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Étape 5 : Consentement -->
                    <fieldset class="ftem-fieldset">
                        <legend class="ftem-legend">
                            <span class="ftem-step">5</span>
                            Consentement
                        </legend>
                        <label class="ftem-checkbox ftem-checkbox--consent">
                            <input type="checkbox" name="ftem_consent" value="1" required <?php checked( $ftem_old['consent'] ?? false ); ?>>
                            <span>
                                J'autorise le PRH68 à publier mon témoignage sur son site internet, à le relire et l'éditer si besoin pour des raisons éditoriales (sans en altérer le sens). Je peux à tout moment demander sa suppression en contactant l'équipe.
                                <span class="ftem-required">*</span>
                            </span>
                        </label>
                    </fieldset>

                    <div class="ftem-submit-wrap">
                        <button type="submit" class="ftem-btn ftem-btn--primary ftem-submit">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" aria-hidden="true"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                            Envoyer mon témoignage
                        </button>
                        <p class="ftem-submit-note">Votre témoignage sera relu avant publication.</p>
                    </div>

                </form>

            <?php endif; ?>

        </div>
    </section>

</div>

<?php get_footer(); ?>
