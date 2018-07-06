<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress

 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<meta name = "viewport" content = "user-scalable=no, width=device-width">
		<meta name="apple-mobile-web-app-capable" content="yes" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'diamond' ); ?></a>

	<div id="sidebar" class="sidebar">
		<header id="masthead" class="site-header" role="banner">
			<div class="site-branding">
				<?php
                if (get_header_image() != '') { ?>
                    <div id="logo">
                        <div id="logo-center-responsive">
                        <a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'><img src="<?php header_image(); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" /></a>

                        </div>
                    </div>
                <?php } else {
                $description = get_bloginfo( 'description', 'display' );
                ?>
                <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <p class="site-description"><?php echo $description; ?></p>
                <?php }
				?>
				<button class="secondary-toggle"><?php _e( 'Menu and widgets', 'diamond' ); ?></button>
			</div><!-- .site-branding -->
		</header><!-- .site-header -->
		<?php get_sidebar(); ?>
	</div><!-- .sidebar -->

	<div id="content" class="site-content">
            <?php if ( has_nav_menu( 'second' ) ) : ?>
                    <nav id="main-menu">
                        <?php wp_nav_menu( array( 'theme_location' => 'second', 'container_id' => 'menu-principale-container', 'menu_class' => 'active' ) ); ?>
                    </nav>
            <?php endif; ?>
