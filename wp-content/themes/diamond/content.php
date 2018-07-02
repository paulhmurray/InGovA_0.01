<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		// Post thumbnail.
		diamond_post_thumbnail();
	?>
    <div class="entry-container">
        <header class="entry-header">
            <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
        </header><!-- .entry-header -->

        <div class="entry-content">
            <p><?php echo diamond_excerpt('10'); ?></p>
        </div><!-- .entry-content -->
    </div>
	<?php
		// Author bio.
		/*if ( is_single() && get_the_author_meta( 'description' ) ) :
			get_template_part( 'author-bio' );
		endif;*/
	?>
	<footer class="entry-footer">
		<?php $my_date = the_date('j M', '', '', FALSE); echo $my_date; ?> - <?php _e('by', 'diamond'); ?> <?php echo get_the_author_link(); ?> - <i class="fa fa-comments" aria-hidden="true"></i> <?php comments_number( __('0', 'diamond'), __('1', 'diamond'), __('%', 'diamond') );?> - <?php _e('In', 'diamond'); ?> <?php the_category(' '); ?>
		<?php edit_post_link( __( 'Edit', 'diamond' ), '<span class="edit-link" aria-hidden="true">', '</span>' ); ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-## -->