<?php
/**
 * Archive pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

get_header();
?>

	<?php
	if ( have_posts() ) : ?>

		<div id="event_list">

		<?php
		/* Start the Loop */
		while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        
        <div class="entry-header entry-header-event">
        
          <a class="post-thumbnail" href="<?php the_permalink(); ?>">
	    <?php
	    the_post_thumbnail( 'bahotel_l_thumbnail_sm', array(
		  'alt' => the_title_attribute( array(
			'echo' => false,
		  ) ),
	    ) );
	    ?> 
          </a>

	        <div class="event-title-group">
		
            <?php
              bahotel_l_event_subtitle(get_the_ID());
            
              the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			?>
            </div><!-- .entry-title-group -->
            
        </div><!-- .entry-header -->
	
        </article><!-- #post-<?php the_ID(); ?> -->	

		<?php 
        
        endwhile;
        
        do_action( 'bahotel_l_pagination' );
        
        ?>
        
        </div>
        
        <?php

	endif; ?>

<?php

get_footer();
