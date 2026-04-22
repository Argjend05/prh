<?php
/* =======================================================
   CHAMPS ACF — Enregistrement des groupes de champs
   ======================================================= */

// Dire à ACF où sauvegarder les exports JSON
add_filter( 'acf/settings/save_json', function ( $path ) {
    return get_stylesheet_directory() . '/acf-json';
} );

// Dire à ACF où charger les JSON (thème enfant EN PLUS du défaut)
add_filter( 'acf/settings/load_json', function ( $paths ) {
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
} );

add_action( 'acf/init', 'prh68_acf_register_groups' );
function prh68_acf_register_groups() {

    /* ── PAGE OBSERVATOIRE ─────────────────────────────── */
    $loc = [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-observatoire.php' ] ] ];

    acf_add_local_field_group( [
        'key'        => 'group_prh68_hero',
        'title'      => 'Section Hero',
        'menu_order' => 1,
        'position'   => 'normal',
        'location'   => $loc,
        'fields'     => [
            [ 'key' => 'field_prh68_hero_title',    'label' => 'Titre principal', 'name' => 'prh68_hero_title',    'type' => 'text', 'default_value' => "Observatoire départemental de l'inclusion" ],
            [ 'key' => 'field_prh68_hero_subtitle', 'label' => 'Sous-titre',      'name' => 'prh68_hero_subtitle', 'type' => 'text', 'default_value' => "Améliorer l'accueil de tous les enfants au sein des lieux collectifs." ],
            [ 'key' => 'field_prh68_hero_btn',      'label' => 'Texte du bouton', 'name' => 'prh68_hero_btn',      'type' => 'text', 'default_value' => 'Voir les questionnaires' ],
        ],
    ] );

    acf_add_local_field_group( [
        'key'        => 'group_prh68_mission',
        'title'      => 'Notre Ambition — Mission',
        'menu_order' => 2,
        'position'   => 'normal',
        'location'   => $loc,
        'fields'     => [
            [ 'key' => 'field_prh68_mission_title',    'label' => 'Titre de section', 'name' => 'prh68_mission_title',    'type' => 'text',     'default_value' => 'Notre Ambition' ],
            [ 'key' => 'field_prh68_mission_subtitle', 'label' => 'Sous-titre',       'name' => 'prh68_mission_subtitle', 'type' => 'textarea', 'rows' => 2, 'default_value' => "Partir des besoins concrets du terrain – Enfants, Parents, Professionnels – pour éclairer et faire évoluer les pratiques au regard des réalités du quotidien" ],
            [ 'key' => 'field_prh68_m1_title', 'label' => 'Carte Enfants — Titre',        'name' => 'prh68_m1_title', 'type' => 'text',     'default_value' => 'Enfants' ],
            [ 'key' => 'field_prh68_m1_desc',  'label' => 'Carte Enfants — Points',       'name' => 'prh68_m1_desc',  'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_prh68_m2_title', 'label' => 'Carte Parents — Titre',        'name' => 'prh68_m2_title', 'type' => 'text',     'default_value' => 'Parents' ],
            [ 'key' => 'field_prh68_m2_desc',  'label' => 'Carte Parents — Points',       'name' => 'prh68_m2_desc',  'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_prh68_m3_title', 'label' => 'Carte Professionnels — Titre', 'name' => 'prh68_m3_title', 'type' => 'text',     'default_value' => 'Professionnels' ],
            [ 'key' => 'field_prh68_m3_desc',  'label' => 'Carte Professionnels — Points','name' => 'prh68_m3_desc',  'type' => 'textarea', 'rows' => 3 ],
        ],
    ] );

    acf_add_local_field_group( [
        'key'        => 'group_prh68_participez',
        'title'      => 'Participez — Questionnaires',
        'menu_order' => 3,
        'position'   => 'normal',
        'location'   => $loc,
        'fields'     => [
            [ 'key' => 'field_prh68_part_title',     'label' => 'Titre de section',          'name' => 'prh68_part_title',          'type' => 'text',     'default_value' => 'Participez à notre démarche' ],
            [ 'key' => 'field_prh68_part_subtitle',  'label' => 'Sous-titre',                'name' => 'prh68_part_subtitle',       'type' => 'textarea', 'rows' => 2 ],
            [ 'key' => 'field_prh68_part_pro_title', 'label' => 'Professionnels — Titre',    'name' => 'prh68_part_pro_title',      'type' => 'text',     'default_value' => 'Professionnels' ],
            [ 'key' => 'field_prh68_part_pro_desc',  'label' => 'Professionnels — Description','name' => 'prh68_part_pro_desc',    'type' => 'textarea', 'rows' => 2 ],
            [ 'key' => 'field_prh68_url_pro',        'label' => 'Professionnels — Lien',     'name' => 'prh68_url_professionnels',  'type' => 'url' ],
            [ 'key' => 'field_prh68_part_par_title', 'label' => 'Parents — Titre',           'name' => 'prh68_part_par_title',      'type' => 'text',     'default_value' => 'Parents' ],
            [ 'key' => 'field_prh68_part_par_desc',  'label' => 'Parents — Description',     'name' => 'prh68_part_par_desc',       'type' => 'textarea', 'rows' => 2 ],
            [ 'key' => 'field_prh68_url_parents',    'label' => 'Parents — Lien',            'name' => 'prh68_url_parents',         'type' => 'url' ],
        ],
    ] );

    $step_names = [ 1 => 'AVRIL - MAI', 2 => 'JUILLET - NOVEMBRE', 3 => 'FEVRIER' ];
    $fields     = [
        [ 'key' => 'field_prh68_cycle_title',    'label' => 'Titre de section', 'name' => 'prh68_cycle_title',    'type' => 'text',     'default_value' => "Les étapes clés" ],
        [ 'key' => 'field_prh68_cycle_subtitle', 'label' => 'Sous-titre',       'name' => 'prh68_cycle_subtitle', 'type' => 'textarea', 'rows' => 2 ],
    ];
    for ( $n = 1; $n <= 3; $n++ ) {
        $fields[] = [ 'key' => "field_prh68_c{$n}_title", 'label' => "Étape {$n} — Titre", 'name' => "prh68_c{$n}_title", 'type' => 'text', 'default_value' => $step_names[ $n ] ];
        for ( $i = 1; $i <= 3; $i++ ) {
            $fields[] = [ 'key' => "field_prh68_c{$n}_i{$i}_title", 'label' => "Étape {$n} · Point {$i} — Titre",       'name' => "prh68_c{$n}_i{$i}_title", 'type' => 'text' ];
            $fields[] = [ 'key' => "field_prh68_c{$n}_i{$i}_desc",  'label' => "Étape {$n} · Point {$i} — Description", 'name' => "prh68_c{$n}_i{$i}_desc",  'type' => 'textarea', 'rows' => 1 ];
        }
    }
    acf_add_local_field_group( [
        'key'        => 'group_prh68_cycle',
        'title'      => "Le Cycle d'Observation",
        'menu_order' => 4,
        'position'   => 'normal',
        'location'   => $loc,
        'fields'     => $fields,
    ] );

    acf_add_local_field_group( [
        'key'        => 'group_prh68_obs_def',
        'title'      => 'Bulle "inclusion"',
        'menu_order' => 5,
        'position'   => 'normal',
        'location'   => $loc,
        'fields'     => [
            [ 'key' => 'field_prh68_obs_def_text', 'label' => 'Définition', 'name' => 'prh68_obs_def_text', 'type' => 'textarea', 'rows' => 3, 'default_value' => "L'inclusion désigne l'accueil de tous les enfants, quels que soient leurs besoins, au sein des lieux d'accueil de droit commun, avec les adaptations nécessaires pour garantir leur participation, leur bien-être et leur développement." ],
            [ 'key' => 'field_prh68_obs_def_note', 'label' => 'Note (optionnelle)', 'name' => 'prh68_obs_def_note', 'type' => 'text', 'default_value' => "Cette définition est issue d'une réflexion partagée et en lien avec les réalités du terrain." ],
        ],
    ] );

    /* ── PAGE MENTIONS LÉGALES ─────────────────────────── */
    $loc_ml = [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-mentions-legales.php' ] ] ];

    acf_add_local_field_group( [
        'key' => 'group_prh68_ml_editeur', 'title' => 'Éditeur du site', 'menu_order' => 20, 'position' => 'normal', 'location' => $loc_ml,
        'fields' => [
            [ 'key' => 'field_prh68_ml_s1_title',  'label' => 'Titre section',        'name' => 'prh68_ml_s1_title',  'type' => 'text',     'default_value' => 'Éditeur du site' ],
            [ 'key' => 'field_prh68_ml_denom',      'label' => 'Dénomination sociale', 'name' => 'prh68_ml_denom',     'type' => 'text',     'default_value' => 'Association Marguerite Sinclair / PRH 68' ],
            [ 'key' => 'field_prh68_ml_forme',      'label' => 'Forme juridique',      'name' => 'prh68_ml_forme',     'type' => 'text',     'default_value' => 'Association de droit local (Bas-Rhin, Haut-Rhin et Moselle)' ],
            [ 'key' => 'field_prh68_ml_siege',      'label' => 'Siège social',         'name' => 'prh68_ml_siege',     'type' => 'textarea', 'rows' => 2, 'default_value' => "2 avenue du Maréchal Joffre\nCS 11035, 68050 MULHOUSE Cedex 1" ],
            [ 'key' => 'field_prh68_ml_siret',      'label' => 'SIRET',                'name' => 'prh68_ml_siret',     'type' => 'text',     'default_value' => '77892930700032' ],
            [ 'key' => 'field_prh68_ml_dir_name',   'label' => 'Directeur — Nom',      'name' => 'prh68_ml_dir_name',  'type' => 'text',     'default_value' => 'François GILLET' ],
            [ 'key' => 'field_prh68_ml_dir_role',   'label' => 'Directeur — Rôle',     'name' => 'prh68_ml_dir_role',  'type' => 'text',     'default_value' => 'Directeur Général' ],
            [ 'key' => 'field_prh68_ml_email',      'label' => 'Email contact',        'name' => 'prh68_ml_email',     'type' => 'text',     'default_value' => 'contact@prh68.fr' ],
            [ 'key' => 'field_prh68_ml_phone',      'label' => 'Téléphone contact',    'name' => 'prh68_ml_phone',     'type' => 'text',     'default_value' => '03 89 32 81 50' ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_ml_hebergement', 'title' => 'Hébergement', 'menu_order' => 21, 'position' => 'normal', 'location' => $loc_ml,
        'fields' => [
            [ 'key' => 'field_prh68_ml_s2_title',    'label' => 'Titre section',   'name' => 'prh68_ml_s2_title',    'type' => 'text',     'default_value' => 'Hébergement' ],
            [ 'key' => 'field_prh68_ml_host_name',   'label' => 'Hébergeur',        'name' => 'prh68_ml_host_name',   'type' => 'text',     'default_value' => 'SAS DIGITALYZ' ],
            [ 'key' => 'field_prh68_ml_host_addr',   'label' => 'Adresse',          'name' => 'prh68_ml_host_addr',   'type' => 'textarea', 'rows' => 2, 'default_value' => "255 route du Ségala\n48500 Massegros Causses Gorges" ],
            [ 'key' => 'field_prh68_ml_host_siret',  'label' => 'SIRET / RCS',      'name' => 'prh68_ml_host_siret',  'type' => 'text',     'default_value' => '849 858 261 RCS Mende' ],
            [ 'key' => 'field_prh68_ml_host_email',  'label' => 'Email hébergeur',  'name' => 'prh68_ml_host_email',  'type' => 'text',     'default_value' => 'contact@digitalyz.fr' ],
            [ 'key' => 'field_prh68_ml_host_phone',  'label' => 'Tél. hébergeur',   'name' => 'prh68_ml_host_phone',  'type' => 'text',     'default_value' => '09 72 16 91 58' ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_ml_pi', 'title' => 'Propriété intellectuelle', 'menu_order' => 22, 'position' => 'normal', 'location' => $loc_ml,
        'fields' => [
            [ 'key' => 'field_prh68_ml_s3_title',  'label' => 'Titre section',    'name' => 'prh68_ml_s3_title',  'type' => 'text',     'default_value' => 'Propriété intellectuelle' ],
            [ 'key' => 'field_prh68_ml_pi_text1',  'label' => 'Texte propriété',  'name' => 'prh68_ml_pi_text1',  'type' => 'textarea', 'rows' => 3, 'default_value' => "L'ensemble des contenus présents sur ce site (textes, images, graphismes, logos, vidéos, etc.) est la propriété exclusive de l'association Marguerite Sinclair." ],
            [ 'key' => 'field_prh68_ml_pi_text2',  'label' => 'Texte Canva',      'name' => 'prh68_ml_pi_text2',  'type' => 'textarea', 'rows' => 2, 'default_value' => "Les éléments Canva utilisés sur ce site sont exploités sous licences conformément aux conditions d'utilisation de la plateforme Canva." ],
            [ 'key' => 'field_prh68_ml_pi_warning','label' => 'Avertissement',    'name' => 'prh68_ml_pi_warning','type' => 'text',     'default_value' => "Toute reproduction est interdite sans autorisation préalable écrite de l'association." ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_ml_resp', 'title' => 'Responsabilité & Cookies', 'menu_order' => 23, 'position' => 'normal', 'location' => $loc_ml,
        'fields' => [
            [ 'key' => 'field_prh68_ml_s4_title',      'label' => 'Titre section',          'name' => 'prh68_ml_s4_title',      'type' => 'text',     'default_value' => 'Responsabilité & Cookies' ],
            [ 'key' => 'field_prh68_ml_resp_title',    'label' => 'Responsabilité — Titre', 'name' => 'prh68_ml_resp_title',    'type' => 'text',     'default_value' => 'Responsabilité' ],
            [ 'key' => 'field_prh68_ml_resp_text',     'label' => 'Responsabilité — Texte', 'name' => 'prh68_ml_resp_text',     'type' => 'textarea', 'rows' => 3, 'default_value' => "L'association s'efforce de fournir des informations précises et à jour, mais décline toute responsabilité pour d'éventuelles inexactitudes ou omissions présentes sur le site." ],
            [ 'key' => 'field_prh68_ml_cookies_title', 'label' => 'Cookies — Titre',        'name' => 'prh68_ml_cookies_title', 'type' => 'text',     'default_value' => 'Cookies' ],
            [ 'key' => 'field_prh68_ml_cookies_text',  'label' => 'Cookies — Texte',        'name' => 'prh68_ml_cookies_text',  'type' => 'textarea', 'rows' => 3, 'default_value' => "Le site ne collecte ni n'utilise de cookies. Aucune donnée de navigation n'est enregistrée ou transmise à des tiers." ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_ml_droit', 'title' => 'Droit applicable', 'menu_order' => 24, 'position' => 'normal', 'location' => $loc_ml,
        'fields' => [
            [ 'key' => 'field_prh68_ml_s5_title',    'label' => 'Titre section',    'name' => 'prh68_ml_s5_title',    'type' => 'text',     'default_value' => 'Droit applicable' ],
            [ 'key' => 'field_prh68_ml_droit_text',  'label' => 'Texte droit',      'name' => 'prh68_ml_droit_text',  'type' => 'textarea', 'rows' => 3, 'default_value' => "Le présent site et les présentes mentions légales sont soumis au droit français. En cas de litige, et à défaut de résolution amiable, la compétence exclusive est attribuée aux tribunaux français." ],
            [ 'key' => 'field_prh68_ml_update_date', 'label' => 'Date de mise à jour','name' => 'prh68_ml_update_date','type' => 'text',    'default_value' => 'Juin 2025' ],
        ],
    ] );

    /* ── PAGE ACCUEIL ──────────────────────────────────── */
    $loc_acc = [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-accueil.php' ] ] ];

    acf_add_local_field_group( [
        'key' => 'group_prh68_acc_hero', 'title' => 'Annonce & Hero', 'menu_order' => 10, 'position' => 'normal', 'location' => $loc_acc,
        'fields' => [
            [ 'key' => 'field_prh68_acc_announce',    'label' => "Barre d'annonce",           'name' => 'prh68_acc_announce',    'type' => 'text',     'default_value' => 'Bienvenue sur le site du PRH68 – Pôle Ressources Handicap 68' ],
            [ 'key' => 'field_prh68_acc_hero_title',  'label' => 'Titre H1',                  'name' => 'prh68_acc_hero_title',  'type' => 'textarea', 'rows' => 2, 'default_value' => "Vous accompagnez des enfants de 0 à 17 ans et souhaitez favoriser l'inclusion ?" ],
            [ 'key' => 'field_prh68_acc_hero_subtitle','label' => 'Sous-titre',               'name' => 'prh68_acc_hero_subtitle','type' => 'text',    'default_value' => 'Nous sommes à vos côtés pour construire un accueil inclusif.' ],
            [ 'key' => 'field_prh68_acc_hero_btn1',   'label' => 'Bouton 1 — texte',          'name' => 'prh68_acc_hero_btn1',   'type' => 'text',     'default_value' => 'Découvrir nos missions' ],
            [ 'key' => 'field_prh68_acc_hero_btn2',   'label' => 'Bouton 2 — texte',          'name' => 'prh68_acc_hero_btn2',   'type' => 'text',     'default_value' => 'Nous contacter' ],
            [ 'key' => 'field_prh68_acc_def_text',    'label' => 'Bulle "inclusion" — Définition','name' => 'prh68_acc_def_text','type' => 'textarea', 'rows' => 3, 'default_value' => "L'inclusion désigne l'accueil de tous les enfants, quels que soient leurs besoins, au sein des lieux d'accueil de droit commun, avec les adaptations nécessaires pour garantir leur participation, leur bien-être et leur développement." ],
            [ 'key' => 'field_prh68_acc_def_note',    'label' => 'Bulle "inclusion" — Note',  'name' => 'prh68_acc_def_note',    'type' => 'text',     'default_value' => "Cette définition est issue d'une réflexion partagée et en lien avec les réalités du terrain." ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_acc_stats', 'title' => 'Chiffres clés', 'menu_order' => 11, 'position' => 'normal', 'location' => $loc_acc,
        'fields' => [
            [ 'key' => 'field_prh68_acc_stat1_num', 'label' => 'Stat 1 — Chiffre', 'name' => 'prh68_acc_stat1_num', 'type' => 'text', 'default_value' => '3' ],
            [ 'key' => 'field_prh68_acc_stat1_lbl', 'label' => 'Stat 1 — Label',   'name' => 'prh68_acc_stat1_lbl', 'type' => 'text', 'default_value' => 'Intervenants spécialisés' ],
            [ 'key' => 'field_prh68_acc_stat2_num', 'label' => 'Stat 2 — Chiffre', 'name' => 'prh68_acc_stat2_num', 'type' => 'text', 'default_value' => '7' ],
            [ 'key' => 'field_prh68_acc_stat2_lbl', 'label' => 'Stat 2 — Label',   'name' => 'prh68_acc_stat2_lbl', 'type' => 'text', 'default_value' => 'Types de structures accompagnées' ],
            [ 'key' => 'field_prh68_acc_stat3_num', 'label' => 'Stat 3 — Chiffre', 'name' => 'prh68_acc_stat3_num', 'type' => 'text', 'default_value' => '68' ],
            [ 'key' => 'field_prh68_acc_stat3_lbl', 'label' => 'Stat 3 — Label',   'name' => 'prh68_acc_stat3_lbl', 'type' => 'text', 'default_value' => 'Haut-Rhin : notre territoire' ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_acc_presentation', 'title' => 'Qui sommes-nous', 'menu_order' => 12, 'position' => 'normal', 'location' => $loc_acc,
        'fields' => [
            [ 'key' => 'field_prh68_acc_pres_title', 'label' => 'Titre section',  'name' => 'prh68_acc_pres_title', 'type' => 'text',     'default_value' => 'Qui sommes-nous ?' ],
            [ 'key' => 'field_prh68_acc_pres_p1',    'label' => 'Paragraphe 1',   'name' => 'prh68_acc_pres_p1',    'type' => 'textarea', 'rows' => 3, 'default_value' => "Le Pôle Ressources Handicap du Haut-Rhin (PRH68) a officiellement ouvert ses portes le 1er janvier 2025. Il est le fruit d'une collaboration entre trois associations engagées au service de l'enfance et du handicap." ],
            [ 'key' => 'field_prh68_acc_pres_p2',    'label' => 'Paragraphe 2',   'name' => 'prh68_acc_pres_p2',    'type' => 'textarea', 'rows' => 3, 'default_value' => "Notre équipe, composée de 3 intervenants spécialistes de l'enfance et du handicap, accompagne les professionnels de terrain dans leurs démarches d'accueil inclusif." ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_acc_info', 'title' => 'Ce que nous faisons', 'menu_order' => 13, 'position' => 'normal', 'location' => $loc_acc,
        'fields' => [
            [ 'key' => 'field_prh68_acc_info_title',  'label' => 'Titre section',         'name' => 'prh68_acc_info_title',  'type' => 'text',     'default_value' => 'Ce que nous faisons' ],
            [ 'key' => 'field_prh68_acc_card1_title', 'label' => 'Carte Mission — Titre', 'name' => 'prh68_acc_card1_title', 'type' => 'text',     'default_value' => 'Mission' ],
            [ 'key' => 'field_prh68_acc_card1_text',  'label' => 'Carte Mission — Texte', 'name' => 'prh68_acc_card1_text',  'type' => 'textarea', 'rows' => 3, 'default_value' => "Soutenir et accompagner les professionnels dans l'accueil inclusif des enfants de 0 à 17 ans en situation de handicap ou ayant des besoins spécifiques." ],
            [ 'key' => 'field_prh68_acc_card2_title', 'label' => 'Carte Objectif — Titre','name' => 'prh68_acc_card2_title', 'type' => 'text',     'default_value' => 'Objectif' ],
            [ 'key' => 'field_prh68_acc_card2_text',  'label' => 'Carte Objectif — Texte','name' => 'prh68_acc_card2_text',  'type' => 'textarea', 'rows' => 3, 'default_value' => "Que chaque enfant puisse bénéficier d'un accueil adapté au sein d'un établissement de droit commun." ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_acc_pro', 'title' => 'Section Professionnels', 'menu_order' => 14, 'position' => 'normal', 'location' => $loc_acc,
        'fields' => [
            [ 'key' => 'field_prh68_acc_pro_intro', 'label' => "Phrase d'intro",    'name' => 'prh68_acc_pro_intro', 'type' => 'text', 'default_value' => 'Vous êtes professionnel dans une structure de droit commun :' ],
            [ 'key' => 'field_prh68_acc_pro_sub',   'label' => 'Phrase de clôture', 'name' => 'prh68_acc_pro_sub',   'type' => 'text', 'default_value' => 'Une équipe est disponible pour vous accompagner dans vos pratiques professionnelles.' ],
            [ 'key' => 'field_prh68_acc_pro_i1', 'label' => 'Structure 1', 'name' => 'prh68_acc_pro_i1', 'type' => 'text', 'default_value' => 'EAJE' ],
            [ 'key' => 'field_prh68_acc_pro_i2', 'label' => 'Structure 2', 'name' => 'prh68_acc_pro_i2', 'type' => 'text', 'default_value' => 'RPE' ],
            [ 'key' => 'field_prh68_acc_pro_i3', 'label' => 'Structure 3', 'name' => 'prh68_acc_pro_i3', 'type' => 'text', 'default_value' => 'Assistantes maternelles' ],
            [ 'key' => 'field_prh68_acc_pro_i4', 'label' => 'Structure 4', 'name' => 'prh68_acc_pro_i4', 'type' => 'text', 'default_value' => 'ACM' ],
            [ 'key' => 'field_prh68_acc_pro_i5', 'label' => 'Structure 5', 'name' => 'prh68_acc_pro_i5', 'type' => 'text', 'default_value' => 'Service Jeunesse' ],
            [ 'key' => 'field_prh68_acc_pro_i6', 'label' => 'Structure 6', 'name' => 'prh68_acc_pro_i6', 'type' => 'text', 'default_value' => 'Collectivité territoriale' ],
            [ 'key' => 'field_prh68_acc_pro_i7', 'label' => 'Structure 7', 'name' => 'prh68_acc_pro_i7', 'type' => 'text', 'default_value' => 'Collectivité jeunesse' ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_acc_services', 'title' => 'Services — Accordéons', 'menu_order' => 15, 'position' => 'normal', 'location' => $loc_acc,
        'fields' => [
            [ 'key' => 'field_prh68_acc_srv_title',  'label' => 'Titre section',        'name' => 'prh68_acc_srv_title',  'type' => 'text', 'default_value' => 'Nous vous proposons' ],
            [ 'key' => 'field_prh68_acc_srv1_title', 'label' => 'Accordéon 1 — Titre',  'name' => 'prh68_acc_srv1_title', 'type' => 'text', 'default_value' => "Solliciter l'équipe pour un accompagnement" ],
            [ 'key' => 'field_prh68_acc_srv1_items', 'label' => 'Accordéon 1 — Points', 'name' => 'prh68_acc_srv1_items', 'type' => 'textarea', 'rows' => 5, 'default_value' => "Bénéficier d'un espace de soutien et de réflexion pour prendre du recul sur les situations rencontrées.\nRenforcer vos compétences et valoriser vos pratiques professionnelles.\nExpérimenter des pistes concrètes d'adaptation et d'évolution de vos pratiques.\nGagner en fluidité dans la communication et la co-construction avec les parents et les partenaires." ],
            [ 'key' => 'field_prh68_acc_srv2_title', 'label' => 'Accordéon 2 — Titre',  'name' => 'prh68_acc_srv2_title', 'type' => 'text', 'default_value' => "S'appuyer sur un réseau de partenaires dans le Haut-Rhin" ],
            [ 'key' => 'field_prh68_acc_srv2_items', 'label' => 'Accordéon 2 — Points', 'name' => 'prh68_acc_srv2_items', 'type' => 'textarea', 'rows' => 5, 'default_value' => "Participer à des temps interprofessionnels et initier de nouvelles rencontres.\nFavoriser l'interconnaissance, le partage d'expériences et la mutualisation des pratiques.\nEncourager la pair-aidance et l'essaimage d'initiatives inspirantes.\nRenforcer les dynamiques de réseau et les coopérations entre acteurs." ],
            [ 'key' => 'field_prh68_acc_srv3_title', 'label' => 'Accordéon 3 — Titre',  'name' => 'prh68_acc_srv3_title', 'type' => 'text', 'default_value' => 'Partager vos besoins et questionnements professionnels' ],
            [ 'key' => 'field_prh68_acc_srv3_items', 'label' => 'Accordéon 3 — Points', 'name' => 'prh68_acc_srv3_items', 'type' => 'textarea', 'rows' => 5, 'default_value' => "Mieux comprendre les besoins et les comportements des enfants.\nAdapter vos pratiques pour soutenir leur développement et leurs apprentissages.\nRenforcer la dynamique inclusive de votre collectif afin de répondre aux besoins de chaque enfant.\nDévelopper des adaptations bénéfiques et transférables au plus grand nombre d'enfants.\nÊtre orienté vers les ressources et dispositifs du Haut-Rhin lorsque les situations le nécessitent." ],
            [ 'key' => 'field_prh68_acc_srv4_title', 'label' => 'Accordéon 4 — Titre',  'name' => 'prh68_acc_srv4_title', 'type' => 'text', 'default_value' => 'Puisez dans nos ressources conçues spécialement pour vous' ],
            [ 'key' => 'field_prh68_acc_srv4_items', 'label' => 'Accordéon 4 — Points', 'name' => 'prh68_acc_srv4_items', 'type' => 'textarea', 'rows' => 3, 'default_value' => "S'approprier l'usage de matériel et d'outils d'adaptation à travers des actions de sensibilisation en lieu d'accueil.\nConsultez et emprunter les ressources disponibles." ],
            [ 'key' => 'field_prh68_acc_srv4_btn',   'label' => 'Accordéon 4 — Bouton', 'name' => 'prh68_acc_srv4_btn',   'type' => 'text', 'default_value' => 'Emprunter des ressources' ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_acc_cta', 'title' => 'CTA Contact', 'menu_order' => 16, 'position' => 'normal', 'location' => $loc_acc,
        'fields' => [
            [ 'key' => 'field_prh68_acc_cta_title',      'label' => 'Titre section',    'name' => 'prh68_acc_cta_title',      'type' => 'text',     'default_value' => "Besoin d'un conseil ou d'un accompagnement ?" ],
            [ 'key' => 'field_prh68_acc_cta_card_title', 'label' => 'Titre carte',      'name' => 'prh68_acc_cta_card_title', 'type' => 'text',     'default_value' => 'Contactez-nous' ],
            [ 'key' => 'field_prh68_acc_cta_card_text',  'label' => 'Texte carte',      'name' => 'prh68_acc_cta_card_text',  'type' => 'textarea', 'rows' => 2, 'default_value' => "Nous sommes à votre écoute pour vous accompagner dans vos projets d'inclusion." ],
            [ 'key' => 'field_prh68_acc_cta_phone',      'label' => 'Numéro de téléphone','name' => 'prh68_acc_cta_phone',   'type' => 'text',     'default_value' => '03 89 32 81 50' ],
            [ 'key' => 'field_prh68_acc_cta_btn',        'label' => 'Bouton — texte',   'name' => 'prh68_acc_cta_btn',        'type' => 'text',     'default_value' => 'Nous contacter' ],
        ],
    ] );

    /* ── PAGE PROFESSIONNELS ───────────────────────────── */
    $loc_pro = [ [ [ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-professionnels.php' ] ] ];

    acf_add_local_field_group( [
        'key' => 'group_prh68_pro_hero', 'title' => 'Hero – Page Professionnels', 'menu_order' => 30, 'position' => 'normal', 'location' => $loc_pro,
        'fields' => [
            [ 'key' => 'field_prh68_pro_hero_title', 'label' => 'Titre principal', 'name' => 'prh68_pro_hero_title', 'type' => 'text',     'default_value' => "Espace Professionnels : Co-construire l'inclusion" ],
            [ 'key' => 'field_prh68_pro_hero_p1',    'label' => 'Paragraphe 1',    'name' => 'prh68_pro_hero_p1',    'type' => 'textarea', 'rows' => 2, 'default_value' => "Vos compétences, votre expérience et votre engagement sont au cœur de cette mission." ],
            [ 'key' => 'field_prh68_pro_hero_p2',    'label' => 'Paragraphe 2',    'name' => 'prh68_pro_hero_p2',    'type' => 'textarea', 'rows' => 2, 'default_value' => "Votre créativité et votre capacité à vous adapter au quotidien permettent de répondre au plus près aux besoins de chaque enfant." ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_pro_why', 'title' => 'Pourquoi nous solliciter', 'menu_order' => 31, 'position' => 'normal', 'location' => $loc_pro,
        'fields' => [
            [ 'key' => 'field_prh68_pro_why_title', 'label' => 'Titre section', 'name' => 'prh68_pro_why_title', 'type' => 'text',     'default_value' => 'Pourquoi nous solliciter ?' ],
            [ 'key' => 'field_prh68_pro_why_c1',    'label' => 'Carte 1',       'name' => 'prh68_pro_why_c1',    'type' => 'textarea', 'rows' => 2, 'default_value' => "Vous souhaitez approfondir la question de l'accueil inclusif au sein de votre lieu d'accueil" ],
            [ 'key' => 'field_prh68_pro_why_c2',    'label' => 'Carte 2',       'name' => 'prh68_pro_why_c2',    'type' => 'textarea', 'rows' => 2, 'default_value' => "Vous vous questionnez ou rencontrez des difficultés dans l'accueil d'enfants ayant des besoins spécifiques" ],
            [ 'key' => 'field_prh68_pro_why_c3',    'label' => 'Carte 3',       'name' => 'prh68_pro_why_c3',    'type' => 'textarea', 'rows' => 2, 'default_value' => "Vous cherchez des pistes éducatives et des outils concrets pour votre pratique au quotidien" ],
            [ 'key' => 'field_prh68_pro_why_c4',    'label' => 'Carte 4',       'name' => 'prh68_pro_why_c4',    'type' => 'textarea', 'rows' => 2, 'default_value' => "Vous avez besoin d'outils, pistes et idées concrètes pour l'inclusion" ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_pro_nvp', 'title' => 'Nous vous proposons', 'menu_order' => 32, 'position' => 'normal', 'location' => $loc_pro,
        'fields' => [
            [ 'key' => 'field_prh68_pro_nvp_title',  'label' => 'Titre section',        'name' => 'prh68_pro_nvp_title',  'type' => 'text',     'default_value' => 'Nous vous proposons' ],
            [ 'key' => 'field_prh68_pro_nvp_sub',    'label' => 'Sous-titre',            'name' => 'prh68_pro_nvp_sub',    'type' => 'textarea', 'rows' => 2, 'default_value' => "Six types d'interventions co-construites avec vous à partir de vos besoins et de votre réalité de terrain" ],
            [ 'key' => 'field_prh68_pro_acc1_title', 'label' => 'Accordéon 1 — Titre',  'name' => 'prh68_pro_acc1_title', 'type' => 'text',     'default_value' => 'Appui téléphonique' ],
            [ 'key' => 'field_prh68_pro_acc2_title', 'label' => 'Accordéon 2 — Titre',  'name' => 'prh68_pro_acc2_title', 'type' => 'text',     'default_value' => "Appui sur votre lieu d'accueil" ],
            [ 'key' => 'field_prh68_pro_acc3_title', 'label' => 'Accordéon 3 — Titre',  'name' => 'prh68_pro_acc3_title', 'type' => 'text',     'default_value' => 'Sensibilisation collective' ],
            [ 'key' => 'field_prh68_pro_acc4_title', 'label' => 'Accordéon 4 — Titre',  'name' => 'prh68_pro_acc4_title', 'type' => 'text',     'default_value' => 'Immersion dans des lieux inclusifs' ],
            [ 'key' => 'field_prh68_pro_acc5_title', 'label' => 'Accordéon 5 — Titre',  'name' => 'prh68_pro_acc5_title', 'type' => 'text',     'default_value' => 'Sacs pédagogiques' ],
            [ 'key' => 'field_prh68_pro_acc6_title', 'label' => 'Accordéon 6 — Titre',  'name' => 'prh68_pro_acc6_title', 'type' => 'text',     'default_value' => 'Projets / Partenariats' ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_pro_def', 'title' => 'Bulle "inclusion"', 'menu_order' => 34, 'position' => 'normal', 'location' => $loc_pro,
        'fields' => [
            [ 'key' => 'field_prh68_pro_def_text', 'label' => 'Définition', 'name' => 'prh68_pro_def_text', 'type' => 'textarea', 'rows' => 3, 'default_value' => "L'inclusion désigne l'accueil de tous les enfants, quels que soient leurs besoins, au sein des lieux d'accueil de droit commun, avec les adaptations nécessaires pour garantir leur participation, leur bien-être et leur développement." ],
            [ 'key' => 'field_prh68_pro_def_note', 'label' => 'Note (optionnelle)', 'name' => 'prh68_pro_def_note', 'type' => 'text', 'default_value' => "Cette définition est issue d'une réflexion partagée et en lien avec les réalités du terrain." ],
        ],
    ] );

    acf_add_local_field_group( [
        'key' => 'group_prh68_pro_cta', 'title' => 'CTA – Page Professionnels', 'menu_order' => 35, 'position' => 'normal', 'location' => $loc_pro,
        'fields' => [
            [ 'key' => 'field_prh68_pro_cta_title', 'label' => 'Titre section',    'name' => 'prh68_pro_cta_title', 'type' => 'text',     'default_value' => "Prêt à développer l'inclusion dans votre structure ?" ],
            [ 'key' => 'field_prh68_pro_cta_sub',   'label' => 'Sous-texte',       'name' => 'prh68_pro_cta_sub',   'type' => 'textarea', 'rows' => 2, 'default_value' => "Contactez-nous dès aujourd'hui pour échanger sur vos besoins et construire ensemble un accompagnement sur mesure" ],
            [ 'key' => 'field_prh68_pro_cta_btn',   'label' => 'Texte du bouton',  'name' => 'prh68_pro_cta_btn',   'type' => 'text',     'default_value' => 'Contactez le Pôle Ressources' ],
            [ 'key' => 'field_prh68_pro_cta_phone', 'label' => 'Téléphone',        'name' => 'prh68_pro_cta_phone', 'type' => 'text',     'default_value' => '03 89 32 81 50' ],
            [ 'key' => 'field_prh68_pro_cta_email', 'label' => 'Email',            'name' => 'prh68_pro_cta_email', 'type' => 'text',     'default_value' => 'contact@prh68.fr' ],
        ],
    ] );
}
