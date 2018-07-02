<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress

 */

get_header(); ?>

	<section id="primary" class="content-area">
        <header class="page-header">
            <div class="page-header-content">
				<?php
					the_archive_title( '<p class="page-title">', '</p>' );
				?>
            </div>
        </header><!-- .page-header -->
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<?php
			// Start the Loop.
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
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

	</section><!-- .content-area -->

<?php get_footer(); ?>
