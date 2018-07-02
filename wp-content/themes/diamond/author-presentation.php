<div class="container-author1">
    <div class="content-author">
        <div class="content-author2">
            <div class='image-author-single'>
            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_avatar( get_the_author_meta('ID'), 200); ?></a>
            </div>
            <div class="container-name-description-author">
                <div class='nameauthorsingle'>
                    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author_meta( 'display_name' ); ?></a>
                </div>
                <div class='descriptionauthorsingle'>
                    <p><?php the_author_meta('description'); ?></p>
                </div>
                <a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
                    <?php printf( __( 'View all posts by %s', 'diamond' ), get_the_author() ); ?>
                </a>
            </div>
        </div>
    </div>
</div>