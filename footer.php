<?php
/*
 * Sur les pages NON-observatoire, Neve ouvre <main class="neve-main"> et
 * <div class="wrapper"> dans son header. Il faut les fermer ici avant le footer.
 */
$custom_templates = ['page-observatoire.php', 'page-accueil.php', 'page-mentions-legales.php', 'page-professionnels.php', 'page-politique-confidentialite.php'];
if (!is_page_template($custom_templates)):
    do_action('neve_before_primary_end');
    ?>
    </main><!--/.neve-main-->
    <?php
    do_action('neve_after_primary');
endif;
?>

<footer class="obs-footer">
    <div class="obs-container obs-footer-grid">
        <div class="obs-footer-brand">
            <div class="obs-footer-logo">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/logo.svg" alt="Logo PRH68" width="52"
                    height="56">
                <div>
                    <strong>Pôle Ressources Handicap</strong>
                    <span>PRH 68</span>
                </div>
            </div>
            <p>À vos côtés pour construire un accueil inclusif</p>
            <div class="obs-footer-social">
                <a href="https://www.linkedin.com/company/pôle-ressources-handicap-68/" target="_blank"
                    rel="noopener noreferrer" class="obs-social-link" aria-label="LinkedIn PRH68">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18"
                        height="18">
                        <path
                            d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="obs-footer-col">
            <h4>Navigation</h4>
            <ul>
                <?php if (is_page_template('page-accueil.php')): ?>
                    <li><a href="#presentation">Qui sommes-nous</a></li>
                    <li><a href="#services">Notre Démarche</a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>">Contact</a>
                    </li>
                <?php elseif (is_page_template('page-professionnels.php')): ?>
                    <li><a href="#pourquoi">Pourquoi nous solliciter</a></li>
                    <li><a href="#nos-accompagnements">Nos accompagnements</a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>">Contact</a></li>
                <?php elseif (is_page('evenements-prh68')): ?>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('malettes-pedagogiques'))); ?>">Sacs pédagogiques</a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>">Contact</a></li>
                <?php elseif (is_page('malettes-pedagogiques')): ?>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a></li>
                    <li><a href="#formulaireLocation">Faire une demande</a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>">Contact</a></li>
                <?php elseif (is_page_template('page-mentions-legales.php')): ?>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('observatoire'))); ?>">Observatoire</a>
                    </li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>">Contact</a>
                    </li>
                <?php elseif (is_page_template('page-politique-confidentialite.php')): ?>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a></li>
                    <li><a href="<?php echo esc_url(site_url('/mentions-legales/')); ?>">Mentions légales</a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>">Contact</a>
                    </li>
                <?php else: ?>
                    <li><a href="#mission">Notre Ambition</a></li>
                    <li><a href="#cycle">Les Étapes Clés</a></li>
                    <li><a href="#questionnaires">Questionnaires</a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>">Contact</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="obs-footer-col">
            <h4>Contact</h4>
            <ul class="obs-footer-contact">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15"
                        height="15">
                        <path
                            d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                    </svg>
                    03 89 32 81 50
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15"
                        height="15">
                        <path
                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                    </svg>
                    Haut-Rhin (68)
                </li>
            </ul>
        </div>
    </div>

    <!-- Financeurs -->
    <div class="obs-footer-financeurs">
        <div class="obs-container">
            <p class="obs-footer-financeurs-label">Nos financeurs</p>
            <div class="obs-footer-financeurs-logos">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/logos/caf.png" alt="CAF du Haut-Rhin" width="55" height="80" loading="lazy">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/logos/alsace.png"
                    alt="Alsace Collectivité européenne" width="66" height="80" loading="lazy">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/logos/academie.jpg"
                    alt="Direction des services départementaux – Haut-Rhin" width="251" height="80" loading="lazy">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/logos/msa.jpg"
                    alt="MSA – Santé Famille Retraite Services" width="133" height="80" loading="lazy">
            </div>
        </div>
    </div>

    <div class="obs-footer-bottom">
        <div class="obs-container obs-footer-bottom-inner">
            <span>© <?php echo date('Y'); ?> PRH68. Tous droits réservés.</span>
            <div class="obs-footer-legal">
                <a href="<?php echo esc_url(site_url('/mentions-legales/')); ?>">Mentions légales</a>
                <a href="<?php echo esc_url(site_url('/politique-de-confidentialite/')); ?>">Politique de
                    confidentialité</a>
            </div>
        </div>
    </div>
</footer>

<?php if (!is_page_template('page-observatoire.php') && !is_page_template('page-professionnels.php')): ?>
    </div><!--/.wrapper-->
<?php endif; ?>

<?php
wp_footer();
do_action('neve_body_end_before');
?>
</body>

</html>