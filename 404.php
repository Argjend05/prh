<?php get_header(); ?>

<style>
.prh-404 {
    min-height: 70vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 80px 24px;
    gap: 24px;
}
.prh-404-code {
    font-size: clamp(5rem, 15vw, 9rem);
    font-weight: 800;
    line-height: 1;
    background: linear-gradient(135deg, #4b4b8b, #3fb7ab);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
}
.prh-404-title {
    font-size: clamp(1.3rem, 3vw, 2rem);
    font-weight: 700;
    color: #222;
    margin: 0;
}
.prh-404-text {
    font-size: 1.05rem;
    color: #666;
    max-width: 480px;
    line-height: 1.7;
    margin: 0;
}
.prh-404-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: center;
    margin-top: 8px;
}
.prh-404-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 26px;
    border-radius: 8px;
    font-size: .95rem;
    font-weight: 600;
    text-decoration: none;
    transition: transform .2s, box-shadow .2s;
}
.prh-404-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.15); }
.prh-404-btn--primary { background: #4b4b8b; color: #fff; }
.prh-404-btn--outline { background: #fff; color: #4b4b8b; border: 2px solid #4b4b8b; }
.prh-404-links {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    justify-content: center;
    margin-top: 16px;
}
.prh-404-links a {
    color: #4b4b8b;
    font-size: .9rem;
    font-weight: 600;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: border-color .2s;
}
.prh-404-links a:hover { border-color: #ec6f04; color: #ec6f04; }
</style>

<div class="prh-404">
    <p class="prh-404-code">404</p>
    <h1 class="prh-404-title">Page introuvable</h1>
    <p class="prh-404-text">La page que vous cherchez n'existe pas ou a été déplacée. Utilisez les liens ci-dessous pour continuer votre visite.</p>

    <div class="prh-404-actions">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="prh-404-btn prh-404-btn--primary">
            ← Retour à l'accueil
        </a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('formulaire-contact'))); ?>" class="prh-404-btn prh-404-btn--outline">
            Nous contacter
        </a>
    </div>

    <nav class="prh-404-links" aria-label="Liens utiles">
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('professionnels'))); ?>">Professionnels</a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('observatoire'))); ?>">Observatoire</a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('malettes-pedagogiques'))); ?>">Sacs pédagogiques</a>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('evenements-prh68'))); ?>">Évènements</a>
    </nav>
</div>

<?php get_footer(); ?>
