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

if (apply_filters( 'bahotel_l_option', '', 'room_reviews_open' )) :

?>
<div id="comments" class="comments-area">

    <div class="comments_bg_inner">

	<?php
    
    $comment_fields = array(
         'author' => '<p class="comment-form-author"><input id="author" name="author" type="text" value="" size="30" maxlength="245" placeholder="'.esc_attr__( 'Your Name *', 'ba-hotel-light' ).'" required="required"></p>',
         'email' => '<p class="comment-form-email"><input id="email" name="email" type="email" value="" size="30" maxlength="100" aria-describedby="email-notes" placeholder="'.esc_attr__( 'Your Email *', 'ba-hotel-light' ).'" required="required"></p>',
         'url' => '<p class="comment-form-url"><input id="url" name="url" type="url" value="" size="30" maxlength="200" placeholder="'.esc_attr__( 'Website', 'ba-hotel-light' ).'"></p>',
    );

	comment_form(array(
         'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
         'title_reply_after' => '</h2>',
         'fields' => $comment_fields,
         'comment_field' => '<p class="comment-form-from-city"><input id="from_city" name="from_city" type="text" value="" size="30" maxlength="100" placeholder="'.esc_attr__( 'City/Country (where are you from) *', 'ba-hotel-light' ).'" required="required"></p>
         <p class="comment-form-comment"><textarea id="comment" name="comment" cols="45" rows="5" maxlength="65525" placeholder="'.esc_attr__( 'Review text *', 'ba-hotel-light' ).'" required="required"></textarea></p>',
         'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s <span class="lnr lnr-arrow-right"></span></button>',//HTML format for the Submit button
         'class_submit' => 'submit comment-submit button-grey',
         'title_reply'       => esc_html__( 'Leave a Review', 'ba-hotel-light' ),
         'title_reply_to'    => esc_html__( 'Leave a Review', 'ba-hotel-light' ),
         'cancel_reply_link' => esc_html__( 'Cancel Review', 'ba-hotel-light' ),
         'label_submit'      => esc_html__( 'Post Review', 'ba-hotel-light' ),
  ));
	?>
    
    </div>

</div><!-- #comments -->

<?php endif;
