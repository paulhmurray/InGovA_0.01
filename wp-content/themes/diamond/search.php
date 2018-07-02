<?php
/**
 * The template for displaying search results pages.
 *
 * @package WordPress

 */

get_header(); ?>

	<section id="primary" class="content-area">

            <header class="page-header">
                <div class="page-header-content">
                    <p><?php _e( 'Search Results for: ', 'diamond' ) ?></p>
                    <h1 class="page-title"><?php printf( __( '%s', 'diamond' ), get_search_query() ); ?></h1>
                </div>
            </header><!-- .page-header -->
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>


			<?php
			// Start the loop.
			while ( have_posts() ) : the_post(); ?>

				<?php
				/*
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'content', 'search' );

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
