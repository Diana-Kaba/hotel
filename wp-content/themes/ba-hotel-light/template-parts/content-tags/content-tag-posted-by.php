<?php
/**
 * Prints HTML with meta information for the current author.
 *
 */
 
 if (apply_filters( 'bahotel_l_option', '', 'blog_author' )):
 
    echo '<span class="posted_by"><span class="author vcard"><a class="" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_avatar( get_the_author_meta( 'ID' ), 30 ) . esc_html( get_the_author() ) . '</a></span></span>';

 endif;
 
 if (apply_filters( 'bahotel_l_option', '', 'blog_categories' )):

	$categories_list = get_the_category_list( esc_html__( ' - ', 'ba-hotel-light' ) );

	if ( $categories_list ) {
       echo '<span class="cat-links"><span class="eleganticon icon_folder-alt"></span>' . wp_kses_post($categories_list) . '</span>';
	}
    
 endif;
 
 if (apply_filters( 'bahotel_l_option', '', 'blog_tags' )):  

	$tags_list = get_the_tag_list( '', esc_html_x( ' - ', 'list item separator', 'ba-hotel-light' ) );
	
	if ( $tags_list ) {
       echo '<span class="tags-links"><span class="lnr lnr-tag"></span>' . wp_kses_post($tags_list) . '</span>';
	}
 
 endif;

