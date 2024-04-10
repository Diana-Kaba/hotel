<?php
/**
 * 404 page (not found) template file..
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

get_header();

?>

	<section class="error-404 not-found">
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'That page can&rsquo;t be found.', 'ba-hotel-light' ); ?></h1>
		</header><!-- .page-header -->

		<div class="page-content">
			<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'ba-hotel-light' ); ?></p>

			<?php
			get_search_form();
			?>

		</div><!-- .page-content -->
	</section><!-- .error-404 -->

<?php

get_footer();