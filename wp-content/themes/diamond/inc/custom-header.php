<?php
if ( ! function_exists( 'diamond_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog.
 *
 * @since Diamond 1.0
 *
 * @see diamond_custom_header_setup()
 */
function diamond_header_style() {
	$header_image = get_header_image();

	// If no custom options for text are set, let's bail.
	if ( empty( $header_image ) && display_header_text() ) {
		return;
	}

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css" id="diamond-header-css">
	<?php
		// Short header for when there is no Custom Header and Header Text is hidden.
		if ( empty( $header_image ) && ! display_header_text() ) :
	?>
		.site-header {
			padding-top: 14px;
			padding-bottom: 14px;
		}

		.site-branding {
			min-height: 42px;
		}

		@media screen and (min-width: 46.25em) {
			.site-header {
				padding-top: 21px;
				padding-bottom: 21px;
			}
			.site-branding {
				min-height: 56px;
			}
		}
		@media screen and (min-width: 55em) {
			.site-header {
				padding-top: 25px;
				padding-bottom: 25px;
			}
			.site-branding {
				min-height: 62px;
			}
		}
		@media screen and (min-width: 59.6875em) {
			.site-header {
				padding-top: 0;
				padding-bottom: 0;
			}
			.site-branding {
				min-height: 0;
			}
		}
	<?php
		endif;

		// Has a Custom Header been added?
		if ( ! empty( $header_image ) ) :
	?>
		.site-header {
			background: url(<?php header_image(); ?>) no-repeat 50% 50%;
			-webkit-background-size: cover;
			-moz-background-size:    cover;
			-o-background-size:      cover;
			background-size:         cover;
		}

		@media screen and (min-width: 59.6875em) {
			body:before {
				background: url(<?php header_image(); ?>) no-repeat 100% 50%;
				-webkit-background-size: cover;
				-moz-background-size:    cover;
				-o-background-size:      cover;
				background-size:         cover;
				border-right: 0;
			}

			.site-header {
				background: transparent;
			}
		}
	<?php
		endif;

		// Has the text been hidden?
		if ( ! display_header_text() ) :
	?>
		.site-title,
		.site-description {
			clip: rect(1px, 1px, 1px, 1px);
			position: absolute;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // diamond_header_style