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

		<header class="page-header archive-header">
			<?php
				the_archive_description( '<div class="archive-description">', '</div>' );
			?>
		</header><!-- .page-header -->

		<?php
		/* Start the Loop */
		while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/contents/content', get_post_format());

		endwhile;
        
        do_action( 'bahotel_l_pagination' );

	else :

		get_template_part( 'template-parts/contents/content', 'none');

	endif; ?>

<?php

get_footer();
