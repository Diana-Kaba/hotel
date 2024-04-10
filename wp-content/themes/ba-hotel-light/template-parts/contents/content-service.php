<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">
		<?php
        
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
        
		?>
	</div><!-- .entry-content -->
    
    <?php if ( get_edit_post_link() && !is_home() && !is_front_page() ) : ?>
	<footer class="entry-footer">
		<?php get_template_part( 'template-parts/content-tags/content-tag-entry-footer' ); ?>
	</footer><!-- .entry-footer -->
    <?php endif; ?>
	
</article><!-- #post-<?php the_ID(); ?> -->
