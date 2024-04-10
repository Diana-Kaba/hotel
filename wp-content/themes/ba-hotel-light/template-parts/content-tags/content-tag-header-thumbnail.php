<?php
/**
 * Displays an optional post thumbnail.
 *
 */


if ( post_password_required() || is_attachment() ) {
	
	return;
}

global $post;

    $img_src = Bahotel_L_Settings::$default_header_image_full;
    $img_thumbnail_src = Bahotel_L_Settings::$default_header_image_thumbnail;
    $img_thumbnail_md_src = Bahotel_L_Settings::$default_header_image_thumbnail_md;
    $thumbnail_id = bahotel_l_get_header_image_thumbnail_id();
    
    if ($thumbnail_id):
    
        $src_arr = wp_get_attachment_image_src( $thumbnail_id, 'bahotel_l_header' );
        $img_src = $src_arr[0];
        
        $src_thumbnail_arr = wp_get_attachment_image_src( $thumbnail_id, 'bahotel_list_vl' );
        $img_thumbnail_src = $src_thumbnail_arr[0];
        
        $src_thumbnail_md_arr = wp_get_attachment_image_src( $thumbnail_id, 'bahotel_thumbnail_lg' );
        $img_thumbnail_md_src = $src_thumbnail_md_arr[0];
    
    endif;
    
    $title_class = is_front_page() ? 'page_title_front' : 'page_title';
    $title_before = '<h1 class="entry-title '.$title_class.'">';
    $title_after = '</h1>';     

	?>

	<div class="post-thumbnail header-post-thumbnail" style="background-image: url('<?php echo esc_url(Bahotel_L_Settings::$default_spinner); ?>'); background-size: 80px 80px;" data-imgsrc="url('<?php echo esc_url($img_src); ?>')" data-imgsrc-md="url('<?php echo esc_url($img_thumbnail_md_src); ?>')" data-imgsrc-sm="url('<?php echo esc_url($img_thumbnail_src); ?>')">
        <div class="header-post-thumbnail-inner">
        
        <?php
        if ( is_single() && 'event' == $post->post_type ) :
        ?>
          <div class="header-event-thumbnail-title container">
           <div class="event-title-group"> 
             <?php 
             
             bahotel_l_event_subtitle(get_the_ID());
             
             the_title( $title_before, $title_after );
             
             ?>
           </div>  
          </div> 
        <?php
        else: 
        ?>
         
           <div class="header-post-thumbnail-title">
    
		<?php
        
        if ( apply_filters( 'bahotel_l_page_option', true, 'page_title' ) ) :
        
                if (is_archive()){
                    
                    the_archive_title( $title_before, $title_after );
                    
                } elseif ('post' === get_post_type()) {
                    
                    /// $title_before & $title_after contains only clear html from lines 37-39
                    echo $title_before.esc_html__('Blog', 'ba-hotel-light').$title_after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    
                } elseif (is_search()) {
                    /// $title_before & $title_after contains only clear html from lines 37-39
                    echo $title_before.esc_html__('Search results', 'ba-hotel-light').$title_after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    
                } elseif (is_404()) {
                    /// $title_before & $title_after contains only clear html from lines 37-39
                    echo $title_before.esc_html__('Error 404', 'ba-hotel-light').$title_after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    
                } else {
                    the_title( $title_before, $title_after );
                }
                
                bahotel_l_breadcrumbs();
                
		endif;
        
        ?>
           
              </div>
        <?php
         endif;
        ?>   
           </div>
	</div><!-- .post-thumbnail -->

<?php

