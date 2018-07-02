<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>
			<?php
			// Start the loop.
			while ( have_posts() ) : the_post();

				get_template_part( 'content', get_post_format() );

			// End the loop.
			endwhile;

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'content', 'none' );

		endif;
		?>

		</main><!-- .site-main -->
        <?php
			// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( '', 'diamond' ),
				'next_text'          => __( '', 'diamond' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'diamond' ) . ' </span>',
			) );
        ?>
	</div><!-- .content-area -->

<?php get_footer(); ?>
