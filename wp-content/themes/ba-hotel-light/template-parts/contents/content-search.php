<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="entry-header">
		
        <?php get_template_part( 'template-parts/content-tags/content-tag-post-thumbnail' ); ?>
		
        <?php
		if ( 'post' === get_post_type() ) :
			?>
        <div class="entry-header-container">
            
            <div class="entry-meta entry-date">
				<?php
                get_template_part( 'template-parts/content-tags/content-tag-posted-on' );
				?>
			</div><!-- .entry-meta -->
            
            <div class="entry-title-wrap">
            
        <?php endif; ?>
        
            <?php
            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );            
			?>
        
        <?php if ( 'post' === get_post_type() ) :  ?>  
            
            <div class="entry-excerpt">
            <?php
            $excerpt = bahotel_l_get_excerpt();
            echo wp_kses_post($excerpt);
            ?>
            </div><!-- .entry-excerpt -->
            
            </div><!-- .entry-title-wrap -->
            
        </div><!-- .entry-header-container -->    
		<?php endif; ?>
		
	</header><!-- .entry-header -->
    
     <?php if ( 'post' !== get_post_type() ) : ?>
	<div class="entry-summary">
		<?php 
        $excerpt = bahotel_l_get_excerpt();
        echo wp_kses_post($excerpt);
        ?>
	</div><!-- .entry-summary -->
    <?php endif; ?>
	
</article><!-- #post-<?php the_ID(); ?> -->
