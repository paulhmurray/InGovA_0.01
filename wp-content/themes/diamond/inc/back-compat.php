<?php
/**
 * Diamond back compat functionality
 *
 * Prevents Diamond from running on WordPress versions prior to 4.1,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 4.1.
 *
 * @package WordPress

 */

/**
 * Prevent switching to Diamond on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since Diamond 1.0
 */
function diamond_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'diamond_upgrade_notice' );
}
add_action( 'after_switch_theme', 'diamond_switch_theme' );

/**
 * Add message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * Diamond on WordPress versions prior to 4.1.
 *
 * @since Diamond 1.0
 */
function diamond_upgrade_notice() {
	$message = sprintf( __( 'Diamond requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'diamond' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );
}

/**
 * Prevent the Customizer from being loaded on WordPress versions prior to 4.1.
 *
 * @since Diamond 1.0
 */
function diamond_customize() {
	wp_die( sprintf( __( 'Diamond requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'diamond' ), $GLOBALS['wp_version'] ), '', array(
		'back_link' => true,
	) );
}
add_action( 'load-customize.php', 'diamond_customize' );

/**
 * Prevent the Theme Preview from being loaded on WordPress versions prior to 4.1.
 *
 * @since Diamond 1.0
 */
function diamond_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die( sprintf( __( 'Diamond requires at least WordPress version 4.1. You are running version %s. Please upgrade and try again.', 'diamond' ), $GLOBALS['wp_version'] ) );
	}
}
add_action( 'template_redirect', 'diamond_preview' );
