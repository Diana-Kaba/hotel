<?php
/**
 * Default template file.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

get_header();

?>

	<?php
	if ( have_posts() ) :

		if ( is_home() && ! is_front_page() ) :
		
			?>
			<header>
				<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
			</header>
			<?php
			
		endif;
        
        $column_wrapper_end = false;
        
        if ( is_home() && 2 == apply_filters( 'bahotel_l_option', '', 'blog_columns' )){
            
            echo '<div class="row">
            ';
            
            $column_wrapper_end = true;
        }
        
		/* Start the Loop */
		while ( have_posts() ) :
		
			the_post();

			get_template_part( 'template-parts/contents/content', get_post_format());

		endwhile;
        
        if ($column_wrapper_end){
            echo '</div>';
        }
        
        if ( !is_author() ):
        
        do_action( 'bahotel_l_pagination' );
        
        endif;

	else :

		get_template_part( 'template-parts/contents/content', 'none');

	endif;
	?>

<?php

get_footer();
