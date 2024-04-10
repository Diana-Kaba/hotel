<?php
/**
 * Displays an optional post thumbnail.
 *
 */


if ( post_password_required() || is_attachment() ) {
	return;
}

global $post;

$is_singular = is_singular();

if (!$is_singular && !has_post_thumbnail() && 'video' == get_post_format()){
    
    $content = apply_filters( 'the_content', get_the_content() );
	$video   = false;
	
    // Only get video from the content if a playlist isn't present.
	if ( false === strpos( $content, 'wp-playlist-script' ) ) {
		$video = get_media_embedded_in_content( $content, array( 'video', 'object', 'embed', 'iframe' ) );
	}
    
    if ( ! empty( $video ) ) {
        echo '<div class="entry-video post-thumbnail">';
			echo $video[0]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}
}

if (!has_post_thumbnail()){
    return;
}

if (!$is_singular || ($is_singular && 'gallery' != get_post_format())):

if (!$is_singular) : ?>
<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
<?php else: ?>
<div class="post-thumbnail">
<?php endif; ?>
	<?php
	the_post_thumbnail( 'bahotel_thumbnail_sm', array(
		'alt' => the_title_attribute( array(
			'echo' => false,
		) ),
	) );
    
	?>
<?php if (!$is_singular) : ?>    
</a>
<?php else: ?>
</div>
<?php endif;

endif;



