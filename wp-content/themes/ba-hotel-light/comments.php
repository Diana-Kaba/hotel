<?php
/**
 * Comments area template file.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */


if ( post_password_required() ) {
	return;
}

?>
<div id="comments" class="comments-area">

    <div class="comments_bg_inner">

	<?php
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			if ( '1' === $comment_count ) {
				
				printf(
					esc_html__( 'One comment', 'ba-hotel-light' )
				);
				
			} else {
				
				printf(
                /* translators: %1$s: number of comments */
					esc_html( _nx( '%1$s comment', '%1$s comments', $comment_count, 'multiple_comments_title', 'ba-hotel-light' ) ),
					esc_html( number_format_i18n( $comment_count ) )
				);
			}
			?>
		</h2><!-- .comments-title -->

		<?php
		the_comments_navigation( array(
			'prev_text'          => __( 'Older comments', 'ba-hotel-light' ),
			'next_text'          => __( 'Newer comments', 'ba-hotel-light' ),
			'screen_reader_text' => __( 'Continue reading', 'ba-hotel-light' ),
		) );
		?>

		<ul class="comment-list">
			<?php
			wp_list_comments( array(
				'short_ping' => true,
                'callback' => 'bahotel_l_comment_callback',
                'avatar_size' => 96,
			) );
			?>
		</ul><!-- .comment-list -->

		<?php
		the_comments_navigation( array(
			'prev_text'          => __( 'Older comments', 'ba-hotel-light' ),
			'next_text'          => __( 'Newer comments', 'ba-hotel-light' ),
			'screen_reader_text' => __( 'Continue reading', 'ba-hotel-light' ),
		) );

		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'ba-hotel-light' ); ?></p>
			<?php
		endif;

	endif;
    
    $comment_fields = array(
         'author' => '<p class="comment-form-author"><input id="author" name="author" type="text" value="" size="30" maxlength="245" placeholder="'.esc_attr__( 'Your Name *', 'ba-hotel-light' ).'" required="required"></p>',
         'email' => '<p class="comment-form-email"><input id="email" name="email" type="email" value="" size="30" maxlength="100" aria-describedby="email-notes" placeholder="'.esc_attr__( 'Your Email *', 'ba-hotel-light' ).'" required="required"></p>',
         'url' => '<p class="comment-form-url"><input id="url" name="url" type="url" value="" size="30" maxlength="200" placeholder="'.esc_attr__( 'Website', 'ba-hotel-light' ).'"></p>',
    );

	comment_form( array(
         'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
         'title_reply_after' => '</h2>',
         'fields' => $comment_fields,
         'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="5" maxlength="65525" placeholder="'.esc_attr__( 'Your message *', 'ba-hotel-light' ).'" required="required"></textarea></p>',
         'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s <span class="lnr lnr-arrow-right"></span></button>',//HTML format for the Submit button
         'class_submit' => 'submit comment-submit button-grey',
    ) );
	?>
    
    </div>

</div><!-- #comments -->

