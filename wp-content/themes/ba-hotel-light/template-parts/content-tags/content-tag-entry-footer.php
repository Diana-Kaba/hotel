<?php
/**
 * Prints HTML with meta information for the categories, tags and comments.
 *
 */

if ( is_singular() ) {

edit_post_link(
	sprintf(
		wp_kses(
        /* translators: %s: Name of current post. Only visible to screen readers */
			__( 'Edit <span class="screen-reader-text">%s</span>', 'ba-hotel-light' ),
			array(
				'span' => array(
					'class' => array(),
				),
			)
		),
		get_the_title()
	),
	'<span class="edit-link">',
	'</span>'
);

}
