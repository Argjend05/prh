<?php get_header(); ?>

<div class="prh-404">

    <!-- Illustration -->
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 200" fill="none"
         aria-hidden="true" class="prh-404-illustration">
        <defs>
            <linearGradient id="g404" x1="0" y1="0" x2="220" y2="200" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#4b4b8b"/>
                <stop offset="100%" stop-color="#3fb7ab"/>
            </linearGradient>
        </defs>
        <!-- Halo de fond -->
        <ellipse cx="110" cy="105" rx="95" ry="85" fill="url(#g404)" opacity="0.07"/>
        <!-- Chemin pointillé qui se perd -->
        <path d="M 38 155 C 55 135, 68 155, 85 128 C 102 101, 118 122, 137 95 C 156 68, 170 88, 185 70"
              stroke="url(#g404)" stroke-width="3" stroke-dasharray="7 5" stroke-linecap="round"/>
        <!-- Point de départ -->
        <circle cx="38" cy="155" r="7" fill="#4b4b8b"/>
        <!-- Pin de destination avec ? -->
        <path d="M185 52 C185 38, 170 32, 163 40 C156 48, 160 56, 163 60 L168 70 L173 60 C178 56, 185 66, 185 52Z"
              fill="url(#g404)"/>
        <circle cx="168" cy="52" r="9" fill="white" opacity="0.25"/>
        <text x="168" y="57" font-family="sans-serif" font-size="13" font-weight="800"
              fill="white" text-anchor="middle">?</text>
        <!-- Boussole -->
        <g transform="translate(88,90)">
            <circle r="26" fill="white" stroke="url(#g404)" stroke-width="2.5" opacity="0.9"/>
            <circle r="26" fill="url(#g404)" opacity="0.08"/>
            <!-- Aiguille N (légèrement inclinée pour "perdu") -->
            <g transform="rotate(22)">
                <path d="M0,-18 L5,-4 L0,0 L-5,-4Z" fill="url(#g404)"/>
                <path d="M0,18 L5,4 L0,0 L-5,4Z" fill="#c8c8d8"/>
            </g>
            <circle r="4" fill="url(#g404)"/>
        </g>
        <!-- Petit personnage perdu -->
        <circle cx="140" cy="142" r="8" fill="url(#g404)" opacity="0.7"/>
        <path d="M135 155 Q140 165 145 155" stroke="url(#g404)" stroke-width="2.5"
              stroke-linecap="round" fill="none" opacity="0.7"/>
        <path d="M133 150 L140 155 L147 150" stroke="url(#g404)" stroke-width="2.5"
              stroke-linecap="round" fill="none" opacity="0.7"/>
    </svg>

    <p class="prh-404-code">404</p>
    <h1 class="prh-404-title">Page introuvable</h1>
    <p class="prh-404-text">La page que vous cherchez n'existe pas ou a été déplacée. Utilisez les liens ci-dessous pour continuer votre visite.</p>

    <div class="prh-404-actions">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="prh-404-btn prh-404-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16" aria-hidden="true">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
            </svg>
            Retour à l'accueil
        </a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'formulaire-contact' ) ) ); ?>" class="prh-404-btn prh-404-btn--outline">
            Nous contacter
        </a>
    </div>

    <nav class="prh-404-links" aria-label="Liens utiles">
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'professionnels' ) ) ); ?>">Professionnels</a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'observatoire' ) ) ); ?>">Observatoire</a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'malettes-pedagogiques' ) ) ); ?>">Sacs pédagogiques</a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'evenements-prh68' ) ) ); ?>">Évènements</a>
    </nav>

</div>

<?php get_footer(); ?>
