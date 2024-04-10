<?php
/**
 * Search results page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

get_header();

?>

	<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<h1 class="page-title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Search results for: %s', 'ba-hotel-light' ), '<span>' . get_search_query() . '</span>' );
				?>
			</h1>
		</header><!-- .page-header -->

		<?php
		/* Start the Loop */
		while ( have_posts() ) :
		
			the_post();
                       
            get_template_part( 'template-parts/contents/content', 'search');

		endwhile;

		the_posts_navigation();

	else :
    
        get_template_part( 'template-parts/contents/content', 'none');

	endif;
	?>

<?php

get_footer();
