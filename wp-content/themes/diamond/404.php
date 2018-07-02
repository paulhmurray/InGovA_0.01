<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress

 */

get_header(); ?>

<section class="no-results not-found">

	<div class="page-content">
            <h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'diamond' ); ?></h1>
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'diamond' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'diamond' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'diamond' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>

	</div><!-- .page-content -->
</section><!-- .no-results -->

<?php get_footer(); ?>
