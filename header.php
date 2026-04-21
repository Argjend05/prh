<!DOCTYPE html>
<?php do_action( 'neve_html_start_before' ); ?>
<html <?php language_attributes(); ?>>

<head>
    <?php do_action( 'neve_head_start_after' ); ?>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php endif; ?>
    <?php wp_head(); ?>
    <?php do_action( 'neve_head_end_before' ); ?>
</head>

<body <?php body_class(); ?> <?php neve_body_attrs(); ?>>
<?php do_action( 'neve_body_start_after' ); ?>
<?php wp_body_open(); ?>

<?php do_action( 'neve_before_header_wrapper_hook' ); ?>

<!-- Header en dehors du .wrapper pour que position:sticky fonctionne -->
<header class="prh-header" id="prh-header">
    <a class="neve-skip-link show-on-focus" href="#content"><?php _e( 'Skip to content', 'neve' ); ?></a>

    <div class="prh-header-inner">

        <!-- Logo -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="prh-header-logo" aria-label="Accueil">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/logo-header.png"
                 alt="PRH68 – Pôle Ressources Handicap">
        </a>

        <!-- Nav desktop -->
        <?php
        wp_nav_menu( [
            'theme_location' => 'primary',
            'container'       => 'nav',
            'container_class' => 'prh-nav',
            'menu_class'      => 'prh-nav-list',
            'fallback_cb'     => false,
            'depth'           => 1,
            'walker'          => null,
        ] );
        ?>

        <!-- Burger mobile -->
        <button class="prh-burger" aria-label="Menu" aria-expanded="false" aria-controls="prh-mobile-nav">
            <span></span><span></span><span></span>
        </button>

    </div>

    <!-- Barre décorative -->
    <div class="prh-header-bar"></div>
</header>

<!-- Nav mobile (overlay) -->
<nav id="prh-mobile-nav" class="prh-mobile-nav" aria-hidden="true">
    <?php
    wp_nav_menu( [
        'theme_location' => 'primary',
        'container'      => false,
        'menu_class'     => 'prh-mobile-nav-list',
        'fallback_cb'    => false,
        'depth'          => 1,
    ] );
    ?>
</nav>

<div class="wrapper">

    <?php do_action( 'neve_after_header_wrapper_hook' ); ?>
    <?php do_action( 'neve_before_primary' ); ?>

    <main id="content" class="neve-main">
    <?php do_action( 'neve_after_primary_start' ); ?>
