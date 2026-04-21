<?php
/* Template Name: Politique de Confidentialité */

$f = fn( $key, $default = '' ) => get_field( $key ) ?: $default;

// Article 1 — Responsable du traitement
$a1_name  = $f( 'prh68_pc_a1_name',  'Association Marguerite Sinclair / PRH 68' );
$a1_addr  = $f( 'prh68_pc_a1_addr',  "2 avenue du Maréchal Joffre\nCS 11035, 68050 MULHOUSE Cedex 1" );
$a1_dept  = $f( 'prh68_pc_a1_dept',  'Haut-Rhin (68)' );
$a1_email = $f( 'prh68_pc_a1_email', 'contact@prh68.fr' );
$a1_phone = $f( 'prh68_pc_a1_phone', '03 89 32 81 50' );

// Article 3 — Durée / Destinataires
$a3_duree = $f( 'prh68_pc_a3_duree', '3 ans à compter du dernier contact' );
$a3_dest  = $f( 'prh68_pc_a3_dest',  "Vos données ne sont transmises à aucun tiers à des fins commerciales. Seuls les sous-traitants techniques strictement nécessaires (hébergeur) peuvent y accéder, sous contrat de confidentialité." );

// Article 4 — DPO / CNIL
$a4_dpo_name  = $f( 'prh68_pc_a4_dpo_name',  'François GILLET' );
$a4_dpo_role  = $f( 'prh68_pc_a4_dpo_role',  'Directeur Général — PRH 68' );
$a4_dpo_email = $f( 'prh68_pc_a4_dpo_email', 'contact@prh68.fr' );
$a4_dpo_phone = $f( 'prh68_pc_a4_dpo_phone', '03 89 32 81 50' );

// Article 5 — Sécurité / Mise à jour
$a5_secu = $f( 'prh68_pc_a5_secu', "Nous mettons en œuvre des mesures techniques et organisationnelles adaptées pour protéger vos données contre tout accès non autorisé, toute perte, altération ou destruction. L'accès aux données est limité aux seules personnes habilitées." );
$a5_date = $f( 'prh68_pc_a5_date', '16 avril 2026' );

get_header();
?>

<div class="pc-page">

    <!-- ===================== HERO ===================== -->
    <section class="pc-hero">
        <div class="pc-container">
            <div class="pc-hero-pill">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4l6 2.67V11c0 3.72-2.56 7.2-6 8.27-3.44-1.07-6-4.55-6-8.27V7.67L12 5z"/>
                </svg>
                Protection des données personnelles
            </div>

            <h1>Politique de <span class="pc-accent">Confidentialité</span></h1>
            <p class="pc-hero-subtitle">Conformément au Règlement Général sur la Protection des Données (RGPD)</p>
        </div>
    </section>

    <!-- ===================== CONTENU ===================== -->
    <main class="pc-main">
        <div class="pc-container pc-content-wrapper">

            <!-- Intro -->
            <div class="pc-intro-card">
                <span class="pc-intro-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                </span>
                <p>L'association PRH 68 / Marguerite Sinclair s'engage à protéger vos données personnelles. Cette politique décrit comment nous collectons, utilisons et protégeons vos informations, conformément au RGPD et à la loi Informatique et Libertés.</p>
            </div>

            <!-- Article 1 — Responsable du traitement -->
            <article class="pc-article">
                <div class="pc-article-header pc-header-dark">
                    <div>
                        <span class="pc-article-num">ARTICLE 1</span>
                        <h2 class="pc-article-title">Responsable du traitement</h2>
                    </div>
                </div>
                <div class="pc-article-body">
                    <div class="pc-card-row">
                        <div class="pc-card-text">
                            <strong>Association Marguerite Sinclair / PRH 68</strong>
                            <span>Mulhouse, Haut-Rhin (68)</span>
                            <p>Responsable du traitement de vos données personnelles collectées via ce site web.</p>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Article 2 — Données collectées & Finalités -->
            <article class="pc-article">
                <div class="pc-article-header pc-header-turquoise">
                    <div>
                        <span class="pc-article-num">ARTICLE 2</span>
                        <h2 class="pc-article-title">Données collectées &amp; Finalités</h2>
                    </div>
                </div>
                <div class="pc-article-body">
                    <h3 class="pc-section-subtitle pc-bar-turquoise">Données collectées via les formulaires</h3>
                    <p class="pc-muted-text">Formulaires de contact et de prêt de matériel</p>
                    
                    <div class="pc-tags-flex">
                        <span class="pc-tag-box">Nom &amp; prénom</span>
                        <span class="pc-tag-box">Adresse email</span>
                        <span class="pc-tag-box">Téléphone</span>
                        <span class="pc-tag-box">Message libre</span>
                    </div>

                    <div class="pc-two-cards">
                        <div class="pc-sub-card">
                            <h4 class="pc-bar-violet">Finalité</h4>
                            <p>Traitement exclusif pour répondre aux demandes soumises via les formulaires du site.</p>
                        </div>
                        <div class="pc-sub-card">
                            <h4 class="pc-bar-violet">Base légale</h4>
                            <p>Consentement de l'utilisateur (Art. 6.1.a RGPD) et Intérêt légitime de l'association (Art. 6.1.f RGPD).</p>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Article 3 — Durée de conservation & Destinataires -->
            <article class="pc-article">
                <div class="pc-article-header pc-header-dark">
                    <div>
                        <span class="pc-article-num">ARTICLE 3</span>
                        <h2 class="pc-article-title">Durée de conservation &amp; Destinataires</h2>
                    </div>
                </div>
                <div class="pc-article-body">
                    <div class="pc-two-cards pc-two-cards-no-bg">
                        <div class="pc-sub-card-outline pc-bar-turquoise">
                            <h4>
                                <svg style="color: var(--turquoise);" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-3.99h-.01L18 18l-4-4 4-3.99-.01-.01H18V2H6zm10 14.5V20H8v-3.5l4-4 4 4zm-4-5l-4-4V4h8v3.5l-4 4z"/></svg>
                                Durée de conservation
                            </h4>
                            <p>Les données de contact sont conservées <strong>jusqu'à 3 ans</strong> après le dernier échange avec l'association.</p>
                        </div>
                        <div class="pc-sub-card-outline pc-bar-violet">
                            <h4>
                                <svg style="color: var(--violet);" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                                Destinataires des données
                            </h4>
                            <p>Exclusivement l'association PRH 68 et ses prestataires techniques soumis à des obligations de confidentialité. Aucune cession à des tiers.</p>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Article 4 — Vos droits -->
            <article class="pc-article">
                <div class="pc-article-header pc-header-turquoise">
                    <div>
                        <span class="pc-article-num">ARTICLE 4</span>
                        <h2 class="pc-article-title">Vos Droits</h2>
                    </div>
                </div>
                <div class="pc-article-body">
                    <h3 class="pc-section-subtitle pc-bar-turquoise">Droits applicables à vos données personnelles</h3>
                    
                    <div class="pc-tags-grid">
                        <span class="pc-tag-box">Droit d'accès</span>
                        <span class="pc-tag-box">Rectification</span>
                        <span class="pc-tag-box">Effacement</span>
                        <span class="pc-tag-box">Limitation</span>
                        <span class="pc-tag-box">Opposition</span>
                        <span class="pc-tag-box">Portabilité</span>
                    </div>

                    <div class="pc-list-cards">
                        <div class="pc-list-card pc-bg-lightviolet">
                            <div class="pc-list-card-content">
                                <strong>Contacter le Délégué à la Protection des Données (DPO)</strong>
                                <p>Délégué à la Protection des Données de l'association <strong>Marguerite SINCLAIR</strong><br>
                                2 avenue du Maréchal Joffre — 68050 MULHOUSE Cedex 1</p>
                            </div>
                        </div>

                        <div class="pc-list-card pc-bg-lightgray">
                            <div class="pc-list-card-content">
                                <strong>Droit de réclamation auprès de la CNIL</strong>
                                <p>Vous pouvez introduire une réclamation auprès de la Commission Nationale de l'Informatique et des Libertés — <a href="https://www.cnil.fr" target="_blank" rel="noopener noreferrer" class="pc-link">www.cnil.fr</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Article 5 — Sécurité & Mise à jour -->
            <article class="pc-article">
                <div class="pc-article-header pc-header-dark">
                    <div>
                        <span class="pc-article-num">ARTICLE 5</span>
                        <h2 class="pc-article-title">Sécurité &amp; Mise à jour</h2>
                    </div>
                </div>
                <div class="pc-article-body">
                    <div class="pc-card-row">
                        <div class="pc-card-text">
                            <strong>Mesures de sécurité</strong>
                            <p>L'association PRH 68 met en œuvre des mesures techniques et organisationnelles appropriées pour garantir la sécurité et la confidentialité de vos données personnelles contre tout accès non autorisé, perte ou divulgation accidentelle.</p>
                        </div>
                    </div>

                    <div class="pc-update-banner">
                        <div class="pc-update-left">
                            <span>Dernière mise à jour de cette politique</span>
                        </div>
                        <div class="pc-update-pill">16 avril 2026</div>
                    </div>
                </div>
            </article>

            <p class="pc-footer-note">Pour toute question relative à cette politique, contactez-nous à l'adresse indiquée ci-dessus.</p>

        </div>
    </main>

</div>

<?php get_footer(); ?>
