<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress

 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
            <div class="container-single-page">
                <?php
                // Start the loop.
                while ( have_posts() ) : the_post(); 

                    /*
                     * Include the post format-specific template for the content. If you want to
                     * use this in a child theme, then include a file called called content-___.php
                     * (where ___ is the post format) and that will be used instead.

                    get_template_part( 'content', get_post_format() );*/ ?>
                    <div class="content-single-page">
                        <div class="title-container">
                            <div class="title-content">
                                <h1><?php the_title(); ?></h1>
                            </div>
                        </div>
                        <div class="meta-container">
                            <div class="meta-content">
                                <span class="first-meta"><?php $my_date = the_date('jM', '', '', FALSE); echo $my_date; ?></span> - <span><?php _e('by', 'diamond'); ?> <?php echo get_the_author_link(); ?></span> - <span><i class="fa fa-comments"></i> <?php comments_number( __('0', 'diamond'), __('1', 'diamond'), __('%', 'diamond') );?></span> - <span><?php _e('In', 'diamond'); ?> <?php the_category(' '); ?></span>
                            </div>
                        </div>
                        <div class="content-container">
                            <div class="content-single">
                                <?php the_content(); ?>
                            </div>
                        </div>
                        <?php 
                        $post_tags = wp_get_post_tags($post->ID);
                        if(!empty($post_tags)) { ?>
                        <div class="tags-container">
                            <div class="tags-content">
                                <?php the_tags('<i class="fa fa-tag"></i>',' , '); ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                
                    <div class="author-presentation-container">
                        <?php get_template_part( 'author-presentation' ); ?>
                    </div>
                
                    <?php // If comments are open or we have at least one comment, load up the comment template.
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                    ?>
                
                    <?php
                endwhile;
                ?>
            </div>
            <div class="sidebar-right">
                <?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
                    <div id="widget-right" class="widget-right" role="complementary">
                        <?php dynamic_sidebar( 'sidebar-2' ); ?>
                    </div><!-- .widget-area -->
                <?php endif; ?>
            </div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->
<?php get_footer(); ?>