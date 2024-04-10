<?php
/**
 * Template part for displaying posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

     $column_wrapper_end = false;
        
     if ( is_home() && 2 == apply_filters( 'bahotel_l_option', '', 'blog_columns' ) && Bahotel_L_Settings::$layout_vars['width']['main'] == 12){
        
        $column_wrapper_end = true;
            
        echo '<div class="col-sm-12 col-md-12 col-lg-6">
        ';
    }

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		
        <?php get_template_part( 'template-parts/content-tags/content-tag-post-thumbnail' ); ?>
        
        <?php if ( 'post' === get_post_type() ) : ?>
        <div class="entry-header-container">
        
            <?php if (apply_filters( 'bahotel_l_option', '', 'blog_date' )): ?>
            
            <div class="entry-meta entry-date">
				<?php
                get_template_part( 'template-parts/content-tags/content-tag-posted-on' );
				?>
			</div><!-- .entry-date -->
            
             <?php endif; ?>
            
            <div class="entry-title-wrap">
            
        <?php endif; ?>
        
            <?php
        
            if ( is_single() ) :
				the_title( '<h2 class="entry-title">', '</h2>' );
			else :
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		    endif;
            
			?>
        
        <?php if ( 'post' === get_post_type() ) :  ?>  
            
            <?php if ( is_single() ) : ?>
			<div class="entry-meta entry-author entry-tags">
				<?php get_template_part( 'template-parts/content-tags/content-tag-posted-by' ); ?>
			</div><!-- .entry-meta -->
            
            <?php else: ?>
            
            <div class="entry-excerpt">
            
            <?php
            
            $excerpt = bahotel_l_get_excerpt();
            echo wp_kses_post($excerpt);
            
            ?>
            
              <div class="entry-excerpt-footer">
            
            <?php
            
            $read_more = sprintf( '<div class="more-link-wrapper"><a class="more-link" href="%1$s">%2$s</a></div>',
              esc_url(get_permalink( get_the_ID() )),
              __( 'Read more', 'ba-hotel-light' )
            );
            echo wp_kses_post($read_more);
            
            ?>
			  <div class="entry-meta entry-author entry-tags">
				<?php get_template_part( 'template-parts/content-tags/content-tag-posted-by' ); ?>
			  </div><!-- .entry-meta -->
              
              </div><!-- .entry-excerpt-footer -->
            
            </div><!-- .entry-excerpt -->
        <?php endif; ?>
            
            </div><!-- .entry-title-wrap -->
            
        </div><!-- .entry-header-container -->    
		<?php endif; ?>
		
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
        
        if ( is_single() ) :
        
          the_content( sprintf(
			wp_kses(
            /* translators: %s: Name of current post. Only visible to screen readers */
				 __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'ba-hotel-light' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		) );

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'ba-hotel-light' ),
			'after'  => '</div>',
            'link_before' => '<span class="page-links-page">',
            'link_after' => '</span>',
		) );
        
        
        endif;
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php get_template_part( 'template-parts/content-tags/content-tag-entry-footer' ); ?>
	</footer><!-- .entry-footer -->
	
</article><!-- #post-<?php the_ID(); ?> -->

<?php if ($column_wrapper_end){
    echo '</div>';
}


