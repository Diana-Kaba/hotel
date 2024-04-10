<?php
/**
 * Single post template file.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

get_header();

?>

	<?php
	while ( have_posts() ) :

		the_post();

		get_template_part( 'template-parts/contents/content', get_post_format());
        
        the_post_navigation( array(
			'prev_text'          => '%title',
			'next_text'          => '%title',
			'in_same_term'       => false,
			'screen_reader_text' => __( 'Continue reading', 'ba-hotel-light' ),
        ) );

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
		
			comments_template();
			
		endif;

	endwhile; // End of the loop.
	?>

<?php

get_footer();
