<?php

/////////////////////////////////////

if ( ! function_exists( 'bahotel_l_get_header_image_thumbnail_id' ) ):
/**
 * Get header image thumbnail id
 * 
 * @return int
 */
function bahotel_l_get_header_image_thumbnail_id() {
        
    global $post;
        
    $thumbnail_id = 0;
        
    if ( is_singular() && 'post' != $post->post_type && has_post_thumbnail()):
        
         $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
         
    elseif ( ( (is_single() && 'post' == $post->post_type) || is_home() )  && !apply_filters( 'bahotel_l_option', '', 'blog_header_default' ) && isset(Bahotel_L_Settings::$settings['blog_header_image']['id'])):
    
         $thumbnail_id = Bahotel_L_Settings::$settings['blog_header_image']['id'];
    
    elseif (is_post_type_archive('event') && !apply_filters( 'bahotel_l_option', '', 'events_header_default' ) && isset(Bahotel_L_Settings::$settings['events_header_image']['id'])):
         
         $thumbnail_id = Bahotel_L_Settings::$settings['events_header_image']['id'];
    
    elseif (is_post_type_archive('service') && !apply_filters( 'bahotel_l_option', '', 'services_header_default' ) && isset(Bahotel_L_Settings::$settings['services_header_image']['id'])):
         
         $thumbnail_id = Bahotel_L_Settings::$settings['services_header_image']['id'];
    
    elseif (is_archive() && !apply_filters( 'bahotel_l_option', '', 'archive_header_default' ) && isset(Bahotel_L_Settings::$settings['archive_header_image']['id'])):
         
         $thumbnail_id = Bahotel_L_Settings::$settings['archive_header_image']['id'];
    
    endif;
        
    return absint($thumbnail_id);
}

endif;

///////////////////////////
add_filter( 'bahotel_l_background_spinner', 'bahotel_l_background_spinner', 10, 1 );

if ( ! function_exists( 'bahotel_l_background_spinner' ) ) :
/**
     * Filter background spinner url
     *
     * @param string $spinner_url
     * 
     * @return string
     */
     function bahotel_l_background_spinner($spinner_url) {
        
        return Bahotel_L_Settings::$default_spinner;
        
     }

endif;

///////////////////////////
add_filter( 'bahotel_l_background_image_thumbnail', 'bahotel_l_background_image_thumbnail', 10, 1 );

if ( ! function_exists( 'bahotel_l_background_image_thumbnail' ) ) :
/**
     * Filter background image thumbnail
     *
     * @param string $thumbnail
     * 
     * @return string
     */
     function bahotel_l_background_image_thumbnail($thumbnail) {
        
        $thumbnail = 'bahotel_gallery_md';//'bahotel_list_vl'; //'bahotel_list_md';
        
        return $thumbnail;
        
     }

endif;

///////////////////////////
add_filter( 'bahotel_l_background_image_thumbnail_md', 'bahotel_l_background_image_thumbnail_md', 10, 1 );

if ( ! function_exists( 'bahotel_l_background_image_thumbnail_md' ) ) :
/**
     * Filter background image thumbnail
     *
     * @param string $thumbnail
     * 
     * @return string
     */
     function bahotel_l_background_image_thumbnail_md($thumbnail) {
        
        $thumbnail = 'bahotel_thumbnail_lg';
        
        return $thumbnail;
        
     }

endif;

///////////////////////////
add_filter( 'bahotel_l_background_image_full', 'bahotel_l_background_image_full', 10, 1 );

if ( ! function_exists( 'bahotel_l_background_image_full' ) ) :
/**
     * Filter background image thumbnail
     *
     * @param string $thumbnail
     * 
     * @return string
     */
     function bahotel_l_background_image_full($thumbnail) {
        
        $thumbnail = 'bahotel_header';
        
        return $thumbnail;
        
     }

endif;

///////////////////////////
add_filter( 'get_the_archive_title', 'bahotel_l_the_archive_title', 10, 1 );
if ( ! function_exists( 'bahotel_l_the_archive_title' ) ) :
/**
     * Filters the archive title.
     *
     * @param string $title Archive title to be displayed.
     * @return string
     */
     function bahotel_l_the_archive_title($title) {
        
        if ( is_post_type_archive() ) {
            $title = post_type_archive_title( '', false );
        }
        
        return $title;
        
     }

endif;

////////////////////////////

add_action( 'pre_get_posts', 'bahotel_l_archive_query', 10, 1 );
if ( ! function_exists( 'bahotel_l_archive_query' ) ) :
/**
     * Filters the archive query params
     *
     * @param object $query
     * @return
     */
     function bahotel_l_archive_query($query) {
        
        if ( is_post_type_archive( 'service' ) && ! is_admin() && $query->is_main_query() && in_array('service', (array)$query->query_vars['post_type'])) {
            
            $query->set( 'posts_per_archive_page', -1 );
            
            $query->set( 'orderby', 'menu_order title' );
            
            $query->set( 'order', 'ASC' );
            
            $services_include_pages = (array) apply_filters( 'bahotel_l_option', array(), 'services_include_pages' );
            
            $services_include_pages = array_map('absint', $services_include_pages);
            
            if (array_sum($services_include_pages)){
                
                $post__in = array();
                
                foreach($services_include_pages as $service_page_id => $service_page_include){
                    
                    if ($service_page_include){
                        $post__in[] = $service_page_id;
                    }   
                    
                }
                
                $query->set( 'post__in', $post__in );  
            } 
        }
        
        if ( is_post_type_archive( 'event' ) && ! is_admin() && $query->is_main_query() && in_array('event', (array)$query->query_vars['post_type'])) {
            
            $orderby_first = apply_filters( 'bahotel_l_option', '', 'events_orderby' );
            
            $order = apply_filters( 'bahotel_l_option', 'DESC', 'events_order' ) == 'DESC' ? 'DESC' : 'ASC';
            
            if ('date_event' == $orderby_first){
                
                $query->set( 'meta_key', 'event_date_timestamp' );
                
                $orderby = array(
                  'meta_value_num' => $order,
                  'modified' => $order,
                );
                
                $query->set( 'orderby', $orderby );
                
            } elseif ('modified' == $orderby_first || 'title' == $orderby_first){
                
                $orderby = array(
                  $orderby_first => $order,
                  'date' => $order,
                );
                
                $query->set( 'orderby', $orderby );
                
            }
            
        }
        
        return;
        
     }

endif;

//////////////////////////////////////////////////
add_filter('bahotel_l_block_title_before', 'bahotel_l_block_title_before', 10, 1);

if ( ! function_exists( 'bahotel_l_block_title_before' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Filter before block title
		* 
        * @param string $output
		* 
		* @return string
		*/
		function bahotel_l_block_title_before( $output = '' ) {
		    
            $output = '
            <span class="block_title_decore">
            <div class="block_title_decore_inner block_title_before">
                <div class="block_title_decore_line_half"></div>
                <div class="block_title_decore_line_full"></div>
            </div>
            </span>';
            
            return $output;
            
		}

endif;

//////////////////////////////////////////////////
add_filter('bahotel_l_block_title_after', 'bahotel_l_block_title_after', 10, 1);

if ( ! function_exists( 'bahotel_l_block_title_after' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Filter after block title
		* 
        * @param string $output
		* 
		* @return string
		*/
		function bahotel_l_block_title_after( $output = '' ) {
		    
            $output = '
            <span class="block_title_decore">
            <div class="block_title_decore_inner block_title_after">
                <div class="block_title_decore_line_half"></div>
                <div class="block_title_decore_line_full"></div>
            </div>
            </span>';
            
            return $output;
            
		}

endif;

//////////////////////////////////////////////////
add_filter('bahotel_l_terms_block', 'bahotel_l_filter_terms_block', 10, 2);

if ( ! function_exists( 'bahotel_l_filter_terms_block' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Gets terms html block
		* 
        * @param string $output
		* @param array $taxonomies - array of taxonomy slugs
		* 
		* @return string
		*/
		function bahotel_l_filter_terms_block( $output = '', $taxonomies = array() ) {
		    
            $output = bahotel_l_terms_block( $taxonomies );
            
            return $output;
            
		}

endif;

//////////////////////////////////////////////////
add_filter('bahotel_l_discount_bar_button_title', 'bahotel_l_filter_button_title_add_arrow', 10, 2);
add_filter('bahotel_l_offer_item_button_title', 'bahotel_l_filter_button_title_add_arrow', 10, 2);
add_filter('bahotel_l_home_carousel_button_title', 'bahotel_l_filter_button_title_add_arrow', 10, 2);

if ( ! function_exists( 'bahotel_l_filter_button_title_add_arrow' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Filter button title, add arrow
		* 
        * @param string $output
		* @param array $args
		* 
		* @return string
		*/
		function bahotel_l_filter_button_title_add_arrow( $output = '', $args = array() ) {
		    
            $output .= ' <span class="lnr lnr-arrow-right"></span>';
            
            return $output;
            
		}

endif;

//////////////////////////////////////////////////
add_filter('bahotel_l_home_carousel_nav_prev', 'bahotel_l_filter_home_carousel_nav_prev', 10, 1);
if ( ! function_exists( 'bahotel_l_filter_home_carousel_nav_prev' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Filter carousel prev button
		* 
        * @param string $output
		* 
		* @return string
		*/
		function bahotel_l_filter_home_carousel_nav_prev( $output = '' ) {
		    
            $output = ' <span class="lnr lnr-chevron-left"></span>';
            
            return $output;
            
		}

endif;

//////////////////////////////////////////////////
add_filter('bahotel_l_home_carousel_nav_next', 'bahotel_l_filter_home_carousel_nav_next', 10, 1);
if ( ! function_exists( 'bahotel_l_filter_home_carousel_nav_next' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Filter carousel next button
		* 
        * @param string $output
		* 
		* @return string
		*/
		function bahotel_l_filter_home_carousel_nav_next( $output = '' ) {
		    
            $output = ' <span class="lnr lnr-chevron-right"></span>';
            
            return $output;
            
		}

endif;

//////////////////////////////////////////////////
add_filter('bahotel_l_terms_block_classes', 'bahotel_l_filter_block_classes', 10, 2);
add_filter('bahotel_l_booking_items_block_classes', 'bahotel_l_filter_block_classes', 10, 2);
add_filter('bahotel_l_events_block_classes', 'bahotel_l_filter_block_classes', 10, 2);
add_filter('bahotel_l_news_block_classes', 'bahotel_l_filter_block_classes', 10, 2);
add_filter('bahotel_l_gallery_block_classes', 'bahotel_l_filter_block_classes', 10, 2);
add_filter('bahotel_l_why_choose_us_classes', 'bahotel_l_filter_block_classes', 10, 2);
add_filter('bahotel_l_address_contact_form_classes', 'bahotel_l_filter_block_classes', 10, 2);
add_filter('bahotel_l_address_block_classes', 'bahotel_l_filter_block_classes', 10, 2);
add_filter('bahotel_l_counters_bar_block_classes', 'bahotel_l_filter_block_classes', 10, 2);
add_filter('bahotel_l_contact_form_bar_classes', 'bahotel_l_filter_block_classes', 10, 2);

if ( ! function_exists( 'bahotel_l_filter_block_classes' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Filter terms block classes
		* 
        * @param string $output
		* @param array $args
		* 
		* @return string
		*/
		function bahotel_l_filter_block_classes( $output = '', $args = array() ) {
		    
            if (Bahotel_L_Settings::$layout_current == 'frontpage' || Bahotel_L_Settings::$layout_current == 'no-sidebars-wide') {
                $output .= ' container';
            }
            
            return $output;
            
		}

endif;

////////////////////////////

if ( ! function_exists( 'bahotel_l_terms_block' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Gets terms html block
		* 
		* @param array $taxonomies - array of taxonomy slugs
		* 
		* @return string
		*/
		function bahotel_l_terms_block( $taxonomies = array() ) {
			
			$output = '';
            
            $terms = get_terms( array(
               'taxonomy' => $taxonomies,
               'hide_empty' => false,
            ) );
			
			if ( ! empty( $terms ) && !is_wp_error($terms) ) {
			    
                $results = array();
				
				foreach ( $terms as $term ) {
					
					$term_output = '';
                    
                    $term_meta = get_term_meta($term->term_id);
                    
                    if ( ( isset( $term_meta['image_id'] ) && $term_meta['image_id'][0]) || ( isset( $term_meta['lnr_class'] ) && $term_meta['lnr_class'][0] ) || ( isset( $term_meta['el_class'] ) && $term_meta['el_class'][0] ) || ( isset( $term_meta['fa_class'] ) && $term_meta['fa_class'][0] ) ) {
							 
                         $term_title = '<div class="bahotel_l_term_name feature_title">'.esc_html($term->name).'</div>';
                         if ( isset( $term_meta['image_id'] ) && $term_meta['image_id'][0] ) {
									// Image.
							$src_arr = wp_get_attachment_image_src( $term_meta['image_id'][0], 'full' );
									
							$term_output = '
								<div class="bahotel_l_term_img">
									<img src="'.esc_url($src_arr[0]).'">
										';
						 } elseif ( isset( $term_meta['lnr_class'] ) && $term_meta['lnr_class'][0] ) {	
									// Linear icons.
							$term_output = '
								<div class="bahotel_l_term_icon">
									<span class="' . esc_attr($term_meta['lnr_class'][0]) . '"></span>
										';	
						 } elseif ( isset( $term_meta['el_class'] ) && $term_meta['el_class'][0] ) {	
									// Elegant Icons.
							 $term_output = '
								 <div class="bahotel_l_term_icon">
									 <span class="' . esc_attr($term_meta['el_class'][0]) . '"></span>
										';	
						 } elseif ( isset( $term_meta['fa_class'] ) && $term_meta['fa_class'][0] ) {	
									// Fontawesome.
							 $term_output = '
								 <div class="bahotel_l_term_icon">
									 <i class="' . esc_attr($term_meta['fa_class'][0]) . '"></i>
									   ';	
						 }
                         
                         $term_output .= $term_title.'
									  </div>
									';
								
					}
							
					$term_output = apply_filters( 'bahotel_l_terms_block_term_html', $term_output, $term, $taxonomies );
							
					if ( $term_output ) {
						$results[] = $term_output;
					}
				}
                
                $output = '
					<div class="bahotel_l_terms_block bahotel_l_terms_icon_text">
						<div class="bahotel_l_terms_block_inner">' . wp_kses_post(implode( '', $results )) . '</div>
					</div>
				';
			}
			//// return escaped value
			return $output;
		}
        
endif;

//////////////////////////////////////////////////
add_filter('bahotel_l_booking_items_block_item', 'bahotel_l_filter_booking_items_block_item', 10, 5);

if ( ! function_exists( 'bahotel_l_filter_booking_items_block_item' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Get booking items html block
		* 
        * @param string $output
        * @param object $post - WP Post object
        * @param string $view - col1_s, col1, col2, col3
		* @param array $taxonomies - array of taxonomy slugs
        * @param boolean $with_icons
		* 
		* @return string
		*/
		function bahotel_l_filter_booking_items_block_item( $output = '', $post, $view, $taxonomies, $with_icons ) {
		    
            $output = bahotel_l_room_view( $post, $view, $taxonomies, $with_icons );
            
            return $output;
            
		}

endif;

//////////////////////////////////////////////////

add_filter('bahotel_l_events_block_item', 'bahotel_l_filter_events_block_item', 10, 2);

if ( ! function_exists( 'bahotel_l_filter_events_block_item' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Get event html block
		* 
        * @param string $output
		* @param object $post WP Post object
		* 
		* @return string
		*/
		function bahotel_l_filter_events_block_item( $output = '', $post ) {
		    
            $output = bahotel_l_event_view( $post );
            
            return $output;
            
		}

endif;

//////////////////////

add_filter('bahotel_l_news_block_item', 'bahotel_l_filter_news_block_item', 10, 2);

if ( ! function_exists( 'bahotel_l_filter_news_block_item' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Get news html block
		* 
        * @param string $output
		* @param object $post WP Post object
		* 
		* @return string
		*/
		function bahotel_l_filter_news_block_item( $output = '', $post ) {
		    
            $output = bahotel_l_news_view( $post );
            
            return $output;
            
		}

endif;

//////////////////////////////

if ( ! function_exists( 'bahotel_l_event_subtitle' ) ) :
/**
 * Get subtitle html
 * 
 * @param int $post_id
 * @param string $echo
 * 
 * @return
 */
   function bahotel_l_event_subtitle( $post_id, $echo = true ) {
    
       $output = '';
    
       $tags = array();
       
       $terms = get_the_terms($post_id, 'event_category');
       
       if (!empty($terms) && !is_wp_error($terms)){
          $tags[] = apply_filters('translate_text', $terms[0]->name);
       }
       
       $event_date_timestamp = get_post_meta( $post_id, 'event_date_timestamp', 1);
       
       if ($event_date_timestamp){
          $tags[] = date_i18n( apply_filters( 'bahotel_l_option', '', 'events_post_dateformat' ), $event_date_timestamp );
       }
       
       $output = '<div class="event_subtitle">'.esc_html(implode(' / ', $tags)).'</div>';
       
       if (!empty($tags) && $echo){
          /// already escaped on line 632
          echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          return;
       }
       
       return $output; 
   }
   
endif;

//////////////////////////////

if ( ! function_exists( 'bahotel_l_event_view' ) ) :
/**
 * Get event preview html
 * 
 * @param object $post WP Post object
 * 
 * @return string
 */
   function bahotel_l_event_view( $post ) {
    
       $output = '';
       
       $thumbnail = apply_filters('bahotel_l_event_view_thumbnail', 'bahotel_list_vl', $post );
       $col_class = apply_filters('bahotel_l_event_view_col_class', 'col-sm-12 col-md-6 col-lg-3', $post );
       
       $image_html = wp_get_attachment_image( get_post_thumbnail_id( $post->ID ), $thumbnail );
       $image_html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $image_html );
       
       $image_html = '<a href="' . esc_url(get_permalink($post)) . '">' . $image_html . '</a>';
       
       $output .= '
			<div class="block_event '.esc_attr($col_class).'">
				<div class="block_event_inner">
				   '.$image_html.'
                   <div class="event-title-group">
                     '.bahotel_l_event_subtitle( $post->ID, false ).'
                     <h3 class="entry-title"><a href="' . esc_url( get_permalink($post->ID) ) . '">'.esc_html( get_the_title($post->ID) ).'</a></h3>
                   </div>
                </div>
            </div>';
       
       return $output; 
   }
   
endif;

///////////////////////////////////////////////////
add_filter( 'the_content', 'bahotel_l_event_post_content', 10, 1 );
    
if ( ! function_exists( 'bahotel_l_event_post_content' ) ) :
    /**
	 * Add content to event post
     * 
     * @param string $content
     * @return string
	 */
    function bahotel_l_event_post_content($content){
        global $post;
        $output = $content;

        if (is_single() && in_the_loop() && is_main_query()){
          if ( class_exists( 'Bathemeaddon_Shortcodes' ) && $post->post_type == 'event'){
            
            $title = apply_filters( 'bahotel_l_option', '', 'events_related_title' );
            
            $subtitle = apply_filters( 'bahotel_l_option', '', 'events_related_subtitle' );
            
            $orderby = apply_filters( 'bahotel_l_option', '', 'events_related_orderby' );
            
            $order = apply_filters( 'bahotel_l_option', '', 'events_related_order' );
            
            $ids = '';
            
            $ids_arr = (array) get_post_meta($post->ID, 'related_items', 1);
            
            if (!empty($ids_arr)){
                
               $ids = implode(',', $ids_arr);
                
            }
            
            $output .= do_shortcode('[events ids="'.$ids.'" title="'.$title.'" subtitle="'.$subtitle.'" order="'.$order.'" orderby="'.$orderby.'"]');
            
          }           
        }
        
        return $output; 
    }
    
endif;

//////////////////////////////

if ( ! function_exists( 'bahotel_l_news_view' ) ) :
/**
 * Get news preview html
 * 
 * @param object $post WP Post object
 * 
 * @return string
 */
   function bahotel_l_news_view( $post ) {
    
       $output = '';
       
       ob_start();
       
       get_template_part( 'template-parts/contents/content', get_post_format() );
       
       $output = ob_get_clean();
       
       $output = '
       <div class="col-sm-12 col-md-12 col-lg-6">
         '.$output.'
       </div>
       ';
       
       return $output; 
   }
   
endif;

//////////////////////////////
add_action( 'bahotel_l_footer_before', 'bahotel_l_footer_before', 10);

if ( ! function_exists( 'bahotel_l_footer_before' ) ) :
/**
 * Add html to the page footer top
 * 
 * @return
 */
   function bahotel_l_footer_before() {
    
        $thumbnail_id = isset(Bahotel_L_Settings::$settings['footer_logo']['id']) ? absint(Bahotel_L_Settings::$settings['footer_logo']['id']) : 0;
        
        if ($thumbnail_id){
            
            $img_full_src = wp_get_attachment_image_src( $thumbnail_id, 'full' );
            
            echo '<div class="footer-logo-image"><img src="' . esc_url( $img_full_src[0] ) . '"></div>';
            
        }
        
        return;
    
   }

endif;

////////load panels/////////

add_action( 'bahotel_l_get_panel', 'bahotel_l_get_panel', 10, 1 );

if ( ! function_exists( 'bahotel_l_get_panel' ) ):
    /**
     * Get sidebar for selected widget area
     * 
     * @param string $sidebar_name
     * 
     * @return
     */
    function bahotel_l_get_panel( $sidebar_name ) {
        
        if (isset(Bahotel_L_Settings::$sidebars[$sidebar_name]) && is_active_sidebar($sidebar_name)){
            
            $sidebar_width = isset(Bahotel_L_Settings::$layout_vars['width'][$sidebar_name]) ? Bahotel_L_Settings::$layout_vars['width'][$sidebar_name] : 12;
            
            if ($sidebar_width){
                
                $sidebar_width_class = 'col-lg-'.$sidebar_width;
                
                if ($sidebar_name == 'before-header' || $sidebar_name == 'header' || $sidebar_name == 'before-footer' || $sidebar_name == 'footer'){
                    
                    $sidebar_width_class = '';
                    
                } elseif ($sidebar_name == 'footer-left' || $sidebar_name == 'footer-middle-left' || $sidebar_name == 'footer-middle-right' || $sidebar_name == 'footer-right') {
                    
                    $sidebar_width_class =  'col-12 col-sm-6 col-md-4 col-lg-'.$sidebar_width;
                    
                    if (isset(Bahotel_L_Settings::$layout_vars['offset'][$sidebar_name]) && Bahotel_L_Settings::$layout_vars['offset'][$sidebar_name]){
                        
                        $sidebar_width_class .=  ' offset-lg-'.Bahotel_L_Settings::$layout_vars['offset'][$sidebar_name];
                        
                    }
                    
                }
                
                //// we use 'include' to make variables $sidebar_width_class and $sidebar_name accessable inside template
                // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
                include( locate_template( 'template-parts/sidebar.php', false, false ) );
                
            }
        }
    }

endif;

//////////////////////////////

add_filter( 'bahotel_l_style', 'bahotel_l_style_footer', 10, 2 );

if ( ! function_exists( 'bahotel_l_style_footer' ) ):
    /**
     * Patch footer width
     * 
     * @param string $class
     * @param string $region
     * 
     * @return string
     */
    function bahotel_l_style_footer($class, $region) {
        
        if ( $region == 'sidebar-footer-left' || $region == 'sidebar-footer-middle-left' || $region == 'sidebar-footer-middle-right' || $region == 'sidebar-footer-right') {
           $class .= ' col text-center text-lg-left';
        }
        
        return $class;
    }

endif;

///////Content styling////////

add_filter( 'bahotel_l_style', 'bahotel_l_style_content', 10, 2 );

if ( ! function_exists( 'bahotel_l_style_content' ) ):
    /**
     * Get sidebar for selected widget area
     * 
     * @param string $sidebar_name
     * @param string $region
     * 
     * @return string
     */
    function bahotel_l_style_content( $class, $region ) {
        
        if ($region == 'content'){
            
           if (Bahotel_L_Settings::$layout_current == 'frontpage' || Bahotel_L_Settings::$layout_current == 'no-sidebars-wide') {
            $class = 'container-fluid';
            }
            
            $class .= ' '.Bahotel_L_Settings::$layout_current;
            
        }
        
        return $class;
    }

endif;

////////////////////////////////////

add_filter( 'bahotel_l_column_width', 'bahotel_l_column_width_content', 'content', 10, 2 );

if ( ! function_exists( 'bahotel_l_column_width_content' ) ):
    /**
     * Filtering class for wide template
     * 
     * @param string $sidebar_name
     * @param string $region
     * 
     * @return string
     */
    function bahotel_l_column_width_content( $class, $region ) {
        
        if ($region == 'content' && (Bahotel_L_Settings::$layout_current == 'frontpage' || Bahotel_L_Settings::$layout_current == 'no-sidebars-wide')){
            
            $class = '';
            
        }
        
        return $class;
        
    }
    
endif;    

//////Primary menu items//////
add_action('bahotel_l_header_navbar_after', 'bahotel_l_header_navbar_after', 10);

if ( ! function_exists( 'bahotel_l_header_navbar_after' ) ):
    /**
     * Add items to header after navbar.
     * 
     * @return
     */
    function bahotel_l_header_navbar_after() {

        if ( !class_exists('BABE_Settings') ){
            return;
        }
        
        $title = is_user_logged_in() ? '<span class="eleganticon icon_lock-open_alt"></span>'.esc_html__( 'My account', 'ba-hotel-light' ) : '<span class="eleganticon icon_lock_alt"></span>'.esc_html__( 'Login', 'ba-hotel-light' );
        $url = BABE_Settings::get_my_account_page_url();
        
        /// $title is already escaped
        echo '<div class="header-login">';
        echo '  <a href="' . esc_url( $url ) . '" tabindex="0">' . $title . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
        
        return;
    }

endif;

///////////////////////////////

add_filter( 'wp_nav_menu_items', 'bahotel_l_wp_nav_menu_primary_items', 10, 2 );

if ( ! function_exists( 'bahotel_l_wp_nav_menu_primary_items' ) ):
    /**
     * Filters the HTML list content for a specific navigation menu.
     *
     * @param string   $items The HTML list content for the menu items.
     * @param stdClass $args  An object containing wp_nav_menu() arguments.
     */
    function bahotel_l_wp_nav_menu_primary_items($items, $args) {

        if ( $args->theme_location == 'primary' ){
            $items .= '<div class="mobile-menu-close text-center"><a class="mobile-menu-close-link" href="#" tabindex="0"><span class="eleganticon icon_close_alt2"></span></a></div>';
        }

        return $items;
    }

endif;

////////////Breadcrumbs/////////

if ( ! function_exists( 'bahotel_l_breadcrumbs' ) ) :
/**
 * Display Yoast SEO breadcrumbs or Breadcrumb NavXT below the header.
 */
function bahotel_l_breadcrumbs() {
    
    if (!is_front_page()){ 
	
     if ( function_exists( 'bcn_display' ) ) {
		?><div class="breadcrumbs bcn">
			<?php bcn_display(); ?>
		</div><?php
	 } elseif ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div class="breadcrumbs">','</div>' );
	 }
    
    }
    
    return;
    
}
endif;

//////////////////////////////
add_filter( 'bahotel_l_copyright_text', 'bahotel_l_copyright_text', 10, 1);

if ( ! function_exists( 'bahotel_l_copyright_text' ) ) :
/**
 * Get the footer copyright text.
 * 
 * @param string $content
 * 
 * @return string
 */
function bahotel_l_copyright_text($content) {

	$text = apply_filters( 'bahotel_l_option', '', 'copyrights' );
    
    $text = $text ? $text : __( 'Copyright &copy; {year}, {sitename}', 'ba-hotel-light' );
    
    $text = str_replace(
		array( '{sitename}', '{year}' ),
		array( get_bloginfo( 'sitename' ), date_i18n( 'Y' ) ),
		$text
	);
    
	return wp_kses_post( $text );
}
endif; 

//////Comments////////////////

if ( ! function_exists( 'bahotel_l_comment_callback' ) ) :
    /**
     * Template for comments and pingbacks.
     *
     * Used as a callback by wp_list_comments() for displaying the comments.
     */
    function bahotel_l_comment_callback( $comment, $args, $depth ) {
       // $GLOBALS['comment'] = $comment;
       
        if ( class_exists( 'BABE_Post_types' ) ) {
       
        $comment_rating_arr = BABE_Rating::get_comment_rating($comment->comment_ID);
        
        $comment_rating = !empty($comment_rating_arr) ? BABE_Rating::comment_stars_rendering($comment->comment_ID) : '';
        
        } else {
            $comment_rating = '';
        }
        

        if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

            <li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'media' ); ?>>
            <div class="comment-body">
                <?php esc_html_e( 'Pingback:', 'ba-hotel-light' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'ba-hotel-light' ), '<span class="edit-link">', '</span>' ); ?>
            </div>

        <?php else : ?>

        <li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body container">

                    <div class="comment-wrapper row">

                        <div class="comment-header col-sm-auto">
                             <?php 
                               if ( $args['avatar_size'] != 0 ) {
                                echo get_avatar( $comment, $args['avatar_size'] ); 
                                }
                             ?>  
                        </div><!-- .comment-header -->

                        <div class="comment-content col">
                            <div class="comment-meta">
                               <span class="says">
                                    <cite class="comment-author-name"><?php comment_author();?></cite>
                                    <time datetime="<?php comment_time( 'c' ); ?>">
                                        <?php 
                                        comment_date();
                                        ?>
                                    </time>
                                <?php edit_comment_link( __( '<span style="margin-left: 5px;" class="glyphicon glyphicon-edit"></span> Edit', 'ba-hotel-light'), '<span class="edit-link">', '</span>' ); ?>
                                    <?php echo wp_kses_post($comment_rating); ?>
                               </span>
                            </div>
                            <?php if ( '0' == $comment->comment_approved ) : ?>
                            <p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'ba-hotel-light' ); ?></p>
                            <?php endif; ?>
                            <?php 
                            remove_filter( 'get_comment_text', array( 'BABE_Rating', 'get_comment_text'), 10, 3);                            
                            comment_text(); 
                            
                            add_filter( 'get_comment_text', array( 'BABE_Rating', 'get_comment_text'), 10, 3);
                            ?>
                            
                            <?php
			comment_reply_link(
				array_merge(
					$args,
					array(
						'add_below' => 'div-comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<div class="comment-reply"><i class="fas fa-reply-all"></i>',
						'after'     => '</div>',
					)
				)
			);
			?>
                        </div><!-- .comment-content -->

                    </div><!-- .comment-wrapper -->

            </article><!-- .comment-body -->

            <?php
        endif;
        
        return;
    }

endif;

/////////////////////////////////////////////////
    /**
     * Filters the adjacent post link.
     *
     * @param string  $output   The adjacent post link.
     * @param string  $format   Link anchor format.
     * @param string  $link     Link permalink format.
     * @param WP_Post $post     The adjacent post.
     * @param string  $adjacent Whether the post is previous or next.
     */
    add_filter( "previous_post_link", 'bahotel_l_post_navigation_link', 10, 5 );
    add_filter( "next_post_link", 'bahotel_l_post_navigation_link', 10, 5 );
    if ( ! function_exists( 'bahotel_l_post_navigation_link' ) ) :
        
        function bahotel_l_post_navigation_link( $output, $format, $link, $post, $adjacent ) {
            
            if ($output && $post->post_title){
                
                $title = $post->post_title;
                $title = apply_filters( 'the_title', $title, $post->ID );
                
                $rel = $adjacent == 'previous' ? 'prev' : 'next';
                
                $output = '<div class="nav-'.$adjacent.'">
                <a href="' . esc_url(get_permalink( $post )) . '" rel="'.$rel.'"><div class="nav-prevnext-wrapper nav-'.$rel.'-wrapper">';
                
                if ($adjacent == 'previous'){
                    
                   $output .= '<div class="nav-prevnext-chevron"><span class="lnr lnr-chevron-left"></span></div>
                  <div class="nav-prevnext-text">
                    <div class="nav-prevnext-title">'.esc_html($title).'</div>
                    <div class="nav-prevnext-label">'.esc_html__( 'Previous post', 'ba-hotel-light' ).'</div>
                  </div>'; 
                    
                } else {
                    
                    $output .= '
                  <div class="nav-prevnext-text">
                    <div class="nav-prevnext-title">'.esc_html($title).'</div>
                    <div class="nav-prevnext-label">'.esc_html__( 'Next post', 'ba-hotel-light' ).'</div>
                  </div>
                  <div class="nav-prevnext-chevron"><span class="lnr lnr-chevron-right"></span></div>';
                    
                }
                
                $output .= '  
                </div></a>
                </div>';
             
            }
            
            return $output;
        }
        
    endif;


//////////////////////////////////////////////////
/**
 * BA Book Everything plugin is required.
 */
if ( class_exists( 'BABE_Post_types' ) ) {
    
	//////////////////////////////////////////////////
	add_action( 'bahotel_l_entry_header', 'bahotel_l_entry_header', 10 );

	if ( ! function_exists( 'bahotel_l_entry_header' ) ) :

		//////////////////////////////////////////////////
		/**
		* Adds room entry header.
		* 
		* @param string $post_type
		*
		* @return null
		*/
		function bahotel_l_entry_header( $post_type ) {
			
            global $post;
			$output = '';
			
			if ( is_single() && class_exists('BABE_Post_types') && BABE_Post_types::$booking_obj_post_type == $post_type ) {
				
				$output .= bahotel_l_room_info();
                
                $output = apply_filters( 'bahotel_l_room_entry_header', $output, $post_type);
			}
            
			echo wp_kses($output, Bahotel_L_Settings::$wp_allowedposttags);
			
			return;
		}
	endif;
    
	///////////////////////////////////////////

	if ( ! function_exists( 'bahotel_l_room_info' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Creates room info block.
		* 
		* @return string
		*/
		function bahotel_l_room_info() {
			
			global $post;
			
			$babe_post = BABE_Post_types::get_post($post->ID);
			
			$room_info = '';
            
            $slides = BABE_html::block_slider( $babe_post );
			
			$room_info .= '
				<div class="room_page_slideshow">
					' . wp_kses($slides, Bahotel_L_Settings::$wp_allowedposttags) . '
				</div>
			';
            
            
            ///////////title section
            
            $room_info .= '
            <div class="room_info_group">
               <div class="room_info_group_title">
                <h1 class="entry-title room_title"><span class="entry-title-border background-yellow"></span> '.esc_html(get_the_title()).'</h1>';
              
            /////////////////////////////////    
			
			$room_info .= bahotel_l_room_info_tags($post->ID);
            
            $room_info .= '
              </div>
              ';
            
            if (apply_filters( 'bahotel_l_option', '', 'room_rating' )){  
                $room_info .= wp_kses(BABE_Rating::post_stars_rendering($post->ID), Bahotel_L_Settings::$wp_allowedposttags);
            }
              
            $room_info .= '
              </div>
              ';
            
            if (apply_filters( 'bahotel_l_option', '', 'room_booknow_button' )){
              $room_info .= '
              <div class="room_info_book_now">
                <a class="btn button" href="#booking_form_block">' . __( 'Book now', 'ba-hotel-light' ) . '</a>
              </div>';
            }
			
			$room_info = apply_filters( 'bahotel_l_content_room_info', $room_info, $post->ID, $babe_post);
			
			$output = '
				<div class="room_page_room_info">
                    <div class="room_page_block_inner">
					' . $room_info . '
                    </div>
				</div>
			';
			
			return $output;
		}
	endif;
    
    ///////////////////////////////////////////

	if ( ! function_exists( 'bahotel_l_room_info_tags' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Get room tags.
		* 
        * @param int $post_id - room id
        * @param string $style - 'full', 'price'
        * 
		* @return string
		*/
		function bahotel_l_room_info_tags($post_id, $style = 'full') {
			
			$babe_post = BABE_Post_types::get_post($post_id);
			
			$room_info = '';
            
            if ($style == 'full'){
            
            $room_info .= '<div class="room_info_icons">';
            
            //////////////////
            
            $max_guests = absint(get_post_meta( $post_id, 'guests', 1 ));
				
			$room_info .= '
				<div class="room_info_guests">
                   '.apply_filters('bahotel_l_icon', '', 'guests').'
					<label>' . sprintf(
                       /* translators: %s: Number of guests */
                       esc_html__( '%s Guests', 'ba-hotel-light' ), $max_guests
                    ) . '</label>
				</div>
			';
			
			if (isset($babe_post['room_size']) && $babe_post['room_size']){
			 
                $last_char = mb_substr($babe_post['room_size'], -1);
                
                if ($last_char == 2){
                    
                    $room_size = mb_substr($babe_post['room_size'], 0, -1).'<span class="super_half">'.$last_char.'</span>';
                    
                } else {
                    $room_size = $babe_post['room_size'];
                } 
             
				$room_info .= '
					<div class="room_info_size">
						'.apply_filters('bahotel_l_icon', '', 'size').'
                        <label>'.wp_kses($room_size, Bahotel_L_Settings::$wp_allowedposttags).'</label>
					</div>
				';
			  }
            
            }
            
            ///////////////// price
            
            $room_info .= '
               <div class="room_info_group_price">
               ';
               
            $prices = BABE_Post_types::get_post_price_from($post_id);   
            
            $price_old = $prices['discount_price_from'] < $prices['price_from'] ? '<span class="room_info_price_old">' . wp_kses_post(BABE_Currency::get_currency_price( $prices['price_from'] )) . '</span>' : '';
				
				$discount = $prices['discount'] ? '<div class="room_info_price_discount">-' . $prices['discount'] . '%</div>' : '';
				
			$room_info .= $style == 'full' ? '
                                <div class="room_info_price">
                                    '.apply_filters('bahotel_l_icon', '', 'price').'
									<label class="room_info_before_price">' . esc_html__( 'From', 'ba-hotel-light' ) . '</label>
									' . $price_old . '
									<span class="room_info_price_new">' . wp_kses_post(BABE_Currency::get_currency_price( $prices['discount_price_from'] ) ). '</span>
								</div>
                                ' : '
                                <div class="room_info_price">
									<label class="room_info_before_price">' . esc_html__( 'From', 'ba-hotel-light' ) . '</label>
									' . $price_old . '
									<span class="room_info_price_new">' . wp_kses_post(BABE_Currency::get_currency_price( $prices['discount_price_from'] )) . '</span><span class="room_info_after_price">' . esc_html__( '/Night', 'ba-hotel-light' ) . '</span>
								</div>
                                ';                    
			
			$room_info .= '
              </div>
              ';
              
            //////////////////// 
            
            if ($style == 'full'){ 
			
			   $room_info .= '</div>';
            
            }
			
			return $room_info;
		}
        
	endif;
	
	//////////////////////////////////////////////////
	add_action( 'cmb2_booking_obj_after_prices', 'bahotel_l_cmb2_booking_obj_after_prices', 10, 3 );

	if ( ! function_exists( 'bahotel_l_cmb2_booking_obj_after_prices' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Adds ages comment field.
		* 
		* @param object $cmb
		* @param string $prefix
		* @param object $category
		* 
		* @return null
		*/
		function bahotel_l_cmb2_booking_obj_after_prices( $cmb, $prefix, $category ) {
			
			$cmb->add_field( array(
				'name'       => __( 'Room size', 'ba-hotel-light' ),
				'id'         => $prefix . 'room_size_' . $category->slug,
				'type'       => 'text',
				'desc'       => __( 'with units of measurement', 'ba-hotel-light' ),
				'default'    => '',
				'attributes' => array(
					'data-conditional-id'    => $prefix . BABE_Post_types::$categories_tax,
					'data-conditional-value' => $category->slug,
				),
			) );
            
            $cmb->add_field( array(
				'name'       => __( 'Beds', 'ba-hotel-light' ),
				'id'         => $prefix . 'beds_' . $category->slug,
				'type'       => 'text',
				'desc'       => '',
				'default'    => '',
				'attributes' => array(
					'data-conditional-id'    => $prefix . BABE_Post_types::$categories_tax,
					'data-conditional-value' => $category->slug,
				),
			) );
			
			return;
		}
	endif;
	
	//////////////////////////////////////////////////
	add_filter( 'babe_init_unitegallery_settings', 'bahotel_l_unitegallery_settings' );

	if ( ! function_exists( 'bahotel_l_unitegallery_settings' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Filters unitegallery settings.
		* 
		* @param array $unitegallery_settings
		*
		* @return array
		*/
		function bahotel_l_unitegallery_settings( $unitegallery_settings ) {
			
			$unitegallery_settings['gallery_width'] = '100%';
			
			$unitegallery_settings['gallery_height'] = 620;
            
            $unitegallery_settings['gallery_min_width'] = 300;
            
            $unitegallery_settings['gallery_min_height'] = 280;
            
            $unitegallery_settings['gallery_mousewheel_role'] = 'none';
            
            //$unitegallery_settings['slider_control_zoom'] = false;
			
			$unitegallery_settings['thumb_overlay_color'] = '#000000';
			
			$unitegallery_settings['thumb_overlay_opacity'] = 0;
			
			$unitegallery_settings['thumb_width'] = 160;
			
			$unitegallery_settings['thumb_height'] = 100;
            
            $unitegallery_settings['thumb_border_width'] = 1;
			
			$unitegallery_settings['thumb_border_color'] = '#ffffff';
			
			$unitegallery_settings['thumb_selected_border_width'] = 5;
			
			$unitegallery_settings['thumb_selected_border_color'] = apply_filters( 'bahotel_l_color', '#cccccc', 'color_links' );
			
			$unitegallery_settings['strip_thumbs_align'] = 'center';
			
			$unitegallery_settings['strippanel_padding_top'] = 16;
			
			$unitegallery_settings['strip_space_between_thumbs'] = 24;
			
			$unitegallery_settings['strippanel_padding_left'] = 40;
			
			$unitegallery_settings['strippanel_padding_right'] = 40;
			
			$unitegallery_settings['strippanel_padding_bottom'] = 16;
			
			$unitegallery_settings['strippanel_background_color'] = apply_filters( 'bahotel_l_color', '#ffffff', 'color_bg_gray' );
			
			$unitegallery_settings['strippanel_enable_buttons'] = false;
			
			$unitegallery_settings['strippanel_padding_buttons'] = 20;
            
            if (!apply_filters( 'bahotel_l_option', '', 'room_slideshow_thumbnails' )){
                
                $unitegallery_settings['gallery_theme'] = 'slider';
                $unitegallery_settings['slider_enable_bullets'] = true;
                $unitegallery_settings['slider_bullets_space_between'] = 10;
                $unitegallery_settings['slider_bullets_offset_vert'] = 30;
                
            }
			
			return $unitegallery_settings;
		}
	endif;
    
    //////////////////////////////////////////////////
    
    if ( ! function_exists( 'bahotel_l_turnoff_sassy_social_share' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Patch for class-sassy-social-share-public.
		* 
        * @param obj $post
		* @param string $content
		*
		* @return string
		*/
		function bahotel_l_turnoff_sassy_social_share( $post ) {
		     return 1;
		}
	
    endif;
	
	///////////////////////////////////////////////////
    add_filter( 'the_content', 'bahotel_l_post_content', 100, 1 );
    
    if ( ! function_exists( 'bahotel_l_post_content' ) ) :
    /**
	 * Add content to booking_obj page.
     * @param string $content
     * @return string
	 */
    function bahotel_l_post_content($content){
        global $post;
        $output = $content;

        if (is_single() && in_the_loop() && is_main_query()){
          if ($post->post_type == BABE_Post_types::$booking_obj_post_type){  
            
            $babe_post = BABE_Post_types::get_post($post->ID);
            if (!empty($babe_post)){
              remove_filter( 'the_content', 'bahotel_l_post_content', 100, 1 );
              $output = apply_filters( 'bahotel_l_post_content', $content, $post->ID, $babe_post);
            }
          }           
        }
        
        return $output; 
    }
    
    endif;
    
    //// replace BA Book Everything content filter by theme one
	remove_filter( 'babe_post_content', array( 'BABE_html', 'babe_post_content'), 10, 3 );
	
	add_filter( 'bahotel_l_post_content', 'bahotel_l_room_post_content', 10, 3 );

	if ( ! function_exists( 'bahotel_l_room_post_content' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Creates room post content.
		* 
		* @param string $content
		* @param int $post_id
		* @param array $post - BABE post
		*
		* @return string
		*/
		function bahotel_l_room_post_content( $content, $post_id, $post ) {
		  
          //// avoid the doubling of the "sharing block"
          add_filter( 'heateor_sss_disable_sharing', 'bahotel_l_turnoff_sassy_social_share' );
			
			$rules_cat = BABE_Booking_Rules::get_rule_by_obj_id( $post_id );
			
			$output = '
            <h2 class="room_sub_title">'.__( 'Description', 'ba-hotel-light' ).'</h2>
            <div class="room_sub_title_bottom"><div class="room_sub_title_bottom_line"></div></div>
            ';
            
            $output .= '
				<div class="room_page_room_content">
					<div class="room_page_block_inner">
							' . wp_kses( $content, Bahotel_L_Settings::$wp_allowedposttags) . '
					</div>
				</div>
			';
            
            $taxonomies = apply_filters( 'bahotel_l_option', '', 'taxonomies_on_room_page' );
				
			if ( ! empty( $taxonomies ) && apply_filters( 'bahotel_l_option', '', 'room_features' ) ) {
			 
                $output .= '
                  <h2 class="room_sub_title">'.__( 'Room Features', 'ba-hotel-light' ).'</h2>
                  <div class="room_sub_title_bottom"><div class="room_sub_title_bottom_line"></div></div>
                ';
            
				$output .= '
					<div class="room_info_terms">
						' . bahotel_l_room_terms_section( $post_id, $taxonomies, 'icon_text', false ) . '
					</div>
				';
			}
            
            //$output .= $content;			
			
			$block_steps = BABE_html::block_steps( $post );
            
            if ($block_steps){
                
                $step_title = isset( $rules_cat['category_meta']['categories_step_title'] ) && ! empty( $rules_cat['category_meta']['categories_step_title'] ) ? $rules_cat['category_meta']['categories_step_title'] : __( 'Details', 'ba-hotel-light' );
                
                $output .= '
				<h2 class="room_sub_title">'. esc_html($step_title) .'</h2>
                <div class="room_sub_title_bottom"><div class="room_sub_title_bottom_line"></div></div>
					<div class="room_page_block_inner">
						' . wp_kses( $block_steps, Bahotel_L_Settings::$wp_allowedposttags) . '
					</div>
			    ';
                
            }
			
			$block_faq = BABE_html::block_faqs( $post );
            
            if ($block_faq){
                
                $faq_title = isset( $rules_cat['category_meta']['categories_faq_title'] ) && ! empty( $rules_cat['category_meta']['categories_faq_title'] ) ? $rules_cat['category_meta']['categories_faq_title'] : __( 'Questions & Answers', 'ba-hotel-light' );
                
                $output .= '
				<h2 class="room_sub_title">'.esc_html($faq_title).'</h2>
                <div class="room_sub_title_bottom"><div class="room_sub_title_bottom_line"></div></div>
					<div class="room_page_block_inner">
						' . wp_kses( $block_faq, Bahotel_L_Settings::$wp_allowedposttags) . '
					</div>
			';
                
            }
			
            
            $output .= '
				<h2 class="room_sub_title">'.__( 'Book this room', 'ba-hotel-light' ).'</h2>
                <div class="room_sub_title_bottom"><div class="room_sub_title_bottom_line"></div></div>
                    <div class="room_page_block_inner">
						' . wp_kses( BABE_html::booking_form($post_id), Bahotel_L_Settings::$wp_allowedposttags) . '
					</div>
			';
            
            ////////////////////////////////
            
            if (isset($post['related_items']) && !empty($post['related_items'])){
                
                $related_arr = BABE_Post_types::get_post_related($post);
                
                $taxonomies = apply_filters('bahotel_l_option', '', 'search_res_preview_taxonomies');
                
                $output .= '
				<h2 class="room_sub_title">'.__( 'You may like', 'ba-hotel-light' ).'</h2>
                <div class="room_sub_title_bottom"><div class="room_sub_title_bottom_line"></div></div>
                    <div class="room_page_block_inner related_rooms_wrap row">
						';
                
                foreach( $related_arr as $related_post ) {
                    $output .= bahotel_l_room_view( $related_post, 'col3', array_keys($taxonomies), true );
                }
                
                $output .= '
                    </div>
			    ';
                
            }
            
            //// restore previous, remove theme filter
            remove_filter( 'heateor_sss_disable_sharing', 'bahotel_l_turnoff_sassy_social_share' );
			
			return $output; 
		}
	endif;
    
    //////////////////////////////////////////////////
    //////////////////Checkout//////////////////
    
    if ( ! function_exists( 'bahotel_l_get_checkout_process_line' ) ) :
    
    /**
	 * Get checkout process status line html
     * 
     * @param int $step
     * 
     * @return string
	 */
         function bahotel_l_get_checkout_process_line($step = 2){
            
            $output = '';
            
            $step = absint($step);
            if ($step == 0 || $step > 3){
                $step = 2;
            }
            
            $message_arr = array(
               1 => array(
                  'class' => 'content-light',
                  'message' => __('1. Choose Room', 'ba-hotel-light'),
               ),
               2 => array(
                  'class' => 'content-light',
                  'message' => __('2. Make a Reservation', 'ba-hotel-light'),
               ),
               3 => array(
                  'class' => 'content-light',
                  'message' => __('3. Confirmation', 'ba-hotel-light'),
               ),
            );
            
            $message_arr[$step]['class'] = 'checkout_process_line_item_active';
            
            $message_arr = apply_filters( 'bahotel_l_checkout_process_line_items', $message_arr, $step);
            
            $output .= '
            <div class="checkout_process_line">
               ';
            
            foreach($message_arr as $message_item){
                
                $output .= '<span class="checkout_process_line_item '.esc_attr($message_item['class']).'">'.esc_html($message_item['message']).'</span>';
                
            }
            
            $output .= '
            </div>';
            
            return $output;
         }
         
	endif;
    
    //////////////////////////////////////////////////
    
    add_filter('babe_chekout_after_order_items', 'bahotel_l_chekout_order_items', 100, 2);
    if ( ! function_exists( 'bahotel_l_chekout_order_items' ) ) :
    
    /**
	 * Filter checkout order items
     * 
     * @param string $output
     * @param array $args
     * 
     * @return string
	 */
         function bahotel_l_chekout_order_items($output, $args){
            
            $output = bahotel_l_get_checkout_process_line(2).'
         <div class="row">
            <div class="col-md-12 col-lg-4">
              <div class="checkout_order_items_wrap">
              <h3 class="checkout_order_items_title">'.esc_html__('Your reservation', 'ba-hotel-light').'</h3>
              <div class="checkout_sub_title_bottom"><div class="checkout_sub_title_bottom_line"></div></div>
              '.$output.'
              </div>
            </div>
            <div class="col-md-12 col-lg-8">
              <div class="checkout_form_wrap">';
            
            return $output;
         }
    
    endif;
    
    ///////////////////////////////////////////
    
    add_filter('babe_chekout_form_html', 'bahotel_l_chekout_form_html', 100, 2);
    if ( ! function_exists( 'bahotel_l_chekout_form_html' ) ) :
    
    /**
	 * Filter checkout order items
     * 
     * @param string $output
     * @param array $args
     * 
     * @return string
	 */
         function bahotel_l_chekout_form_html($output, $args){
            
            $output = $output.'
                </div>
            </div>
            </div>';
            
            return $output;
         }
    
    endif;
    
    ///////////////////////////////////////////
    
    add_filter('babe_order_items_date_from_html', 'bahotel_l_order_items_date_from_html', 10, 5);
    if ( ! function_exists( 'bahotel_l_order_items_date_from_html' ) ) :
    
    /**
	 * Filter checkout order items
     * 
     * @param string $date_from_html
     * @param array $post
     * @param array $rules_cat
     * @param obj $date_from_obj
     * @param array $item
     * 
     * @return string
	 */
         function bahotel_l_order_items_date_from_html($date_from_html, $post, $rules_cat, $date_from_obj, $item){
            
            $date_format = $rules_cat['rules']['basic_booking_period'] == 'day' ? get_option('date_format').' - '.get_option('time_format') : get_option('date_format');
            
            $date_from_html = '
                  <span class="order_item_td_value">'.( date_i18n( $date_format, strtotime($date_from_obj->format('Y-m-d')) ) ).'</span>';
                  
                if ($rules_cat['rules']['basic_booking_period'] != 'recurrent_custom'){
                    $date_to_obj = new DateTime($item['meta']['date_to']);
                    $date_from_html = '<span class="order_item_td_label">'.__( 'Check In:', 'ba-hotel-light' ).'</span>'. $date_from_html . '
                    </td></tr>
                    <tr><td class="order_item_info order_item_info_dates">
                    <span class="order_item_td_label">'.__( 'Check Out:', 'ba-hotel-light' ).'</span>
                    <span class="order_item_td_value">'.( date_i18n( $date_format, strtotime($date_to_obj->format('Y-m-d')) ) ).'</span>';
                    
                } else {
                    $date_from_html = '<span class="order_item_td_label">'.__( 'Date:', 'ba-hotel-light' ).'</span>'. $date_from_html . '<span class="order_item_td_label">'.__( 'Time:', 'ba-hotel-light' ).'</span>
                  <span class="order_item_td_value">'.$date_from_obj->format(get_option('time_format')).'</span>';
                }
                
                $date_from_html = '<tr><td class="order_item_info order_item_info_dates">
                  '.$date_from_html.'
                  </td></tr>';
            
            return $date_from_html;
         }
    
    endif;
    
    //////////////////Confirmation//////////////////
    
    add_filter('babe_confirm_content_html', 'bahotel_l_confirm_content_html', 10, 2);
    if ( ! function_exists( 'bahotel_l_confirm_content_html' ) ) :
    
    /**
	 * Confirm page content
     * 
     * @param string $output
     * @param array $args
     * 
     * @return string
	 */
         function bahotel_l_confirm_content_html($output, $args){
            
            $order_id = $args['order_id'];
            
            $message = '';
            
            if ($args['order_status'] && isset(BABE_Settings::$settings['message_'.$args['order_status']])){
          
               $message .= '
            <div class="bahotel_l_message_order bahotel_l_message_order_status_'.$args['order_status'].'">
               ' . wp_kses( BABE_Settings::$settings['message_'.$args['order_status']], Bahotel_L_Settings::$wp_allowedposttags) . '
            </div>';
            
            }
            
            if ($args['order_status'] == 'payment_expected'){
               
               $message .= '<div class="babe_order_confirm">
              <a href="' . esc_url( BABE_Order::get_order_payment_page($order_id) ) . '" class="btn button babe_button_order_to_pay">'.esc_html__('Pay now', 'ba-hotel-light').' <span class="lnr lnr-arrow-right"></span></a>
            </div>';
            }
            
            switch ($args['order_status']){
                
                case 'payment_expected':
                    $confirmation_title = esc_html__('Payment is expected', 'ba-hotel-light');
                    break;
                case 'payment_processing':
                    $confirmation_title = esc_html__('Payment in progress', 'ba-hotel-light');
                    break; 
                case 'payment_deferred':
                case 'payment_received':
                default:
                    $confirmation_title = esc_html__('Reservation complete', 'ba-hotel-light');
                    break;
                           
            }
            
            $output = bahotel_l_get_checkout_process_line(3).'
         <div class="row">
            <div class="col-md-12 col-lg-4 order-last order-lg-first">
              <div class="checkout_order_items_wrap">
              <h3 class="checkout_order_items_title">'.esc_html__('Your reservation', 'ba-hotel-light').'</h3>
              <div class="checkout_sub_title_bottom"><div class="checkout_sub_title_bottom_line"></div></div>
              <h2>' . wp_kses_post( sprintf(
                 /* translators: %1$s: Order number */
                 __('Order #%1$s', 'ba-hotel-light'),
                 $args['order_num']
                   ) . '</h2>'.BABE_html::order_items($order_id).bahotel_l_order_customer_details($order_id)
                ).'
              </div>
            </div>
            <div class="col-md-12 col-lg-8 order-first order-lg-last">
              <div id="confirmation_message" class="confirmation_wrap">
              <h2 class="confirmation_title">'.$confirmation_title.'</h2>
              '. $message .'
              </div>
            </div>
          </div>';
            
            return $output;
            
         }
         
    endif;
    
    ////////////////////////////////////////////
    
    if ( ! function_exists( 'bahotel_l_order_customer_details' ) ) :
    /**
	 * Create order customer details html.
     * @param int $order_id
     * @return string
	 */
    function bahotel_l_order_customer_details($order_id){
        
        $output = '';
        
        $order_meta = BABE_Order::get_order_customer_details($order_id);
        
        unset($order_meta['email_check']);
        
        $order_meta = apply_filters('babe_order_customer_details_fields', $order_meta, $order_id);
        
        $output .= '<div class="bahotel_l_order_customer_details">';
            
        foreach($order_meta as $field_name => $field_content){
            $output .= '
            <div class="order_customer_details_row">
              <div class="order_customer_details_label">'.esc_html(BABE_html::checkout_field_label($field_name)).':</div>
              <div class="order_customer_details_content">'.esc_html($field_content).'</div>
            </div>
            ';
        }
        
        $output .= '</div>';
        
        $output = apply_filters('bahotel_l_order_customer_details_html', $output, $order_id);
        
        return $output;
    }
    
    endif;     
    
	//////////////////////////////////////////////////
    //////////////////Booking form//////////////////
    
    ///////////////////////////////////////////////
    
   add_filter('babe_booking_form_date_from_label', 'bahotel_l_booking_form_date_from_label', 10, 1);
   if ( ! function_exists( 'bahotel_l_booking_form_date_from_label' ) ) :
   /**
	 * Filter booking form date from label
     * 
     * @param string $label
     * @return string
	 */
    function bahotel_l_booking_form_date_from_label($label){
        
        $label = esc_html__('Check In:', 'ba-hotel-light');
        
        return $label;
    }   
   endif;

   ////////////////////////////

   add_filter('babe_booking_form_date_to_label', 'bahotel_l_booking_form_date_to_label', 10, 1);
   if ( ! function_exists( 'bahotel_l_booking_form_date_to_label' ) ) :
   /**
	 * Filter booking form date from label
     * 
     * @param string $label
     * @return string
	 */
    function bahotel_l_booking_form_date_to_label($label){
        
        $label = esc_html__('Check Out:', 'ba-hotel-light');
        
        return $label;
    }   
   endif;

   //////////////////////////////////////////////////
	if ( ! function_exists( 'bahotel_l_button_mobile' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Creates button for mobile screens.
		* 
		* @param string $title
		* @param string $url
		* @param string $classes
		* 
		* @return string
		*/
		function bahotel_l_button_mobile( $title, $url, $classes = '' ) {
			
			$output = '';
			
			$output .= '
				<div class="button-mobile-block">
					<a href="' . esc_url($url) . '" class="btn button' . esc_attr($classes) . '">' . esc_html($title) . '</a>
				</div>
			';
			
			return $output;
		}
	endif;

	//////////////////////////////////////////////////
	if ( ! function_exists( 'bahotel_l_room_terms_section' ) ) :
		
		//////////////////////////////////////////////////
		/**
		* Gets room terms.
		* 
		* @param int $post_id
		* @param array $taxonomies - array of taxonomy slugs
		* @param string $style - icon_text, icon, text
		* @param boolean $with_title
		* @param string $classes
		* 
		* @return string
		*/
		function bahotel_l_room_terms_section( $post_id, $taxonomies = array(), $style = 'icon_text', $with_title = false, $classes = '' ) {
			
			$output = '';
			
			$divider = $style == 'text' ? ', ' : '';
			
			$terms = BABE_Post_types::get_post_terms( $post_id );
			
			if ( ! empty( $terms ) ) {
				
				foreach ( $terms as $taxonomy_slug => $taxonomy_terms ) {
					
					if (
						isset( $taxonomy_terms['terms'] ) &&
						(
							empty( $taxonomies ) ||
							( ! empty( $taxonomies ) && isset( $taxonomies[ $taxonomy_slug ] ) && $taxonomies[ $taxonomy_slug ] )
						)
					) {
						
						$results = array();
						
						$taxonomy_title = $with_title ? ( $style != 'text' ? '<h3 class="bahotel_l_terms_block_title">' . $taxonomy_terms['name'] . '</h3>' : '<label class="bahotel_l_terms_block_title">' . esc_html($taxonomy_terms['name']) . ':</label>' ) : '';
						
						foreach( $taxonomy_terms['terms'] as $term ) {
							
							$term_output = '';
							
							if (
								( $style == 'icon_text' || $style == 'icon') &&
								( $term['image_id'] || ( isset( $term['lnr_class'] ) && $term['lnr_class'] ) || ( isset( $term['el_class'] ) && $term['el_class'] ) || ( isset( $term['fa_class'] ) && $term['fa_class'] ) )
							) {
							 
                                $term_title = '<div class="bahotel_l_term_name feature_title">'.esc_html($term['name']).'</div>';
							
								if ( $term['image_id'] ) {
									// Image.
									$src_arr = wp_get_attachment_image_src( $term['image_id'], 'full' );
									
									$term_output = '
										<div class="bahotel_l_term_img">
											<img src="'.esc_url($src_arr[0]).'">
											';
								} elseif ( isset( $term['lnr_class'] ) && $term['lnr_class'] ) {	
									// Linear icons.
									$term_output = '
										<div class="bahotel_l_term_icon">
											<span class="' . esc_attr($term['lnr_class']) . '"></span>
											';	
								} elseif ( isset( $term['el_class'] ) && $term['el_class'] ) {	
									// Elegant icons.
									$term_output = '
										<div class="bahotel_l_term_icon">
											<span class="' . esc_attr($term['el_class']) . '"></span>
											';	
								} elseif ( isset( $term['fa_class'] ) && $term['fa_class'] ) {	
									// Fontawesome.
									$term_output = '
										<div class="bahotel_l_term_icon">
											<i class="' . esc_attr($term['fa_class']) . '"></i>
											';	
								}
                                
                                $term_output .= $term_title.'
										</div>
									';
								
							} elseif ( $style == 'text' ) {
								
								$term_output = $term['name'];
							}
							
							
							$term_output = apply_filters( 'bahotel_l_room_term_html', $term_output, $term, $style, $taxonomy_slug, $post_id, $taxonomies );
							
							if ( $term_output ) {
								
								$results[] = $term_output;
							}
						}
						
						
						$result_row = implode( $divider, $results );
						
						$result_row = $style != 'text' ? '<div class="bahotel_l_terms_block_inner row">' . $result_row . '</div>' : $result_row;
						
						$output .= $taxonomy_title . $result_row;
					}
				}
			
			
				$output = '
					<div class="bahotel_l_terms_block bahotel_l_terms_' . $style . ' ' . $classes . '">
						' . $output . '
					</div>
				';
			}
			
			return $output;
		}
	endif;
	
	//////////////////////////////////////////////////
	if ( ! function_exists( 'bahotel_l_room_term_icons' ) ) :
		//////////////////////////////////////////////////
		/**
		 * Gets room term icons.
		 * 
		 * @param int $post_id
		 * @param array $taxonomies - array of taxonomy slugs
         * @param string $divider
		 * 
		 * @return string
		 */
		function bahotel_l_room_term_icons( $post_id, $taxonomies = array(), $style = 'icon_text' ) {
			
			$output = '';
            
            $divider = $style == 'text' ? ', ' : '';
			
			$terms = BABE_Post_types::get_post_terms( $post_id );
			
			if ( ! empty( $terms ) && ! empty( $taxonomies ) ) {
			 
                $taxonomies = array_flip($taxonomies);
				
				foreach ( $terms as $taxonomy_slug => $taxonomy_terms ) {
					
					if ( isset( $taxonomy_terms['terms'] ) && isset( $taxonomies[ $taxonomy_slug ] ) ) {
						
						$results = array();
						
						foreach( $taxonomy_terms['terms'] as $term ) {
							
						  $term_output = '';
                            
                          if ($style != 'text'){
                            
                            if ( $term['image_id'] ) {
								// Image.
								$src_arr = wp_get_attachment_image_src( $term['image_id'], 'full' );
								
								$term_output = '
									<img src="'.esc_url($src_arr[0]).'">
									';
							} elseif ( isset( $term['lnr_class'] ) && $term['lnr_class'] ) {	
								// Linear icons.
								$term_output = '
									<span class="' . esc_attr($term['lnr_class']) . '"></span>
									';	
								} elseif ( isset( $term['el_class'] ) && $term['el_class'] ) {	
									// Elegant icons.
									$term_output = '
									    <span class="' . esc_attr($term['el_class']) . '"></span>
									';	
								} elseif ( isset( $term['fa_class'] ) && $term['fa_class'] ) {	
									// Fontawesome.
									$term_output = '
										<i class="' . esc_attr($term['fa_class']) . '"></i>
									';	
							}
                          }  
                            
                            $term_output = $term_output ? '<span class="term_line_icon">'.$term_output.'</span>' : $term_output;
                            
                            $term_output .= $style == 'text' ? esc_html($term['name']) : ( ($style == 'icon_text' && $term_output) ? '<span class="term_line_title">'.esc_html($term['name']).'</span>' : '' );
                            
                            if ($term_output) {
                                $results[] = $term_output;
                            }   
                            
						}
                        
                       if (!empty($results)){
                        
                        $results_html = $style == 'text' ? implode( $divider, $results ) : '<div class="term_line">' . implode( $divider.'</div><div class="term_line">', $results ). '</div>';
                        
                        $output .= '
					    <div class="room_info_preview_icons preview_style_'.esc_attr($style).'">
						   '.$results_html.'
                        </div>    ';
                       
                       }
                    
					}   
				}
			}
			
			
			return $output;
		}
	endif;
    
    //////////////////////////////////////////////////
    
	if ( ! function_exists( 'bahotel_l_get_term_icon' ) ) :
		//////////////////////////////////////////////////
		/**
		 * Gets term icon.
		 * 
		 * @param int $term_id
		 * @param string $taxonomy
		 * 
		 * @return string
		 */
		function bahotel_l_get_term_icon( $term_id, $taxonomy ) {
			
			$output = '';
            
            $image_id = get_term_meta($term_id, 'image_id', 1);
            $lnr_class = get_term_meta($term_id, 'lnr_class', 1);
            $el_class = get_term_meta($term_id, 'el_class', 1);
            $fa_class = get_term_meta($term_id, 'fa_class', 1);
            
            if ($image_id) {
									
				// Image.
				$src_arr = wp_get_attachment_image_src( $image_id, 'full' );
									
				$output .= '
					<div class="bahotel_l_preview_term_img">
						<img src="' . esc_url($src_arr[0]) . '">
					</div>
					';
			} elseif ( $lnr_class ) {
				// Fontawesome.
				$output .= '
					<div class="bahotel_l_preview_term_icon">
						<span class="' . esc_attr($lnr_class) . '"></span>
					</div>
				';
									
			} elseif ( $el_class ) {
				// Fontawesome.
				$output .= '
					<div class="bahotel_l_preview_term_icon">
						<span class="' . esc_attr($el_class) . '"></span>
					</div>
				';
									
			} elseif ( $fa_class ) {
				// Fontawesome.
				$output .= '
					<div class="bahotel_l_preview_term_icon">
						<i class="' . esc_attr($fa_class) . '"></i>
					</div>
				';
									
			}			
			
			return $output;
		}
	endif;
    
    //////////////////////////////////////////
    
    add_action( 'bahotel_l_after_header', 'bahotel_l_after_header_search_form', 10 );
    
    if ( ! function_exists( 'bahotel_l_after_header_search_form' ) ) :
    
    /**
	 * Add search form html.
     * 
     * @return string
	 */
     function bahotel_l_after_header_search_form(){
        
        global $post;
        
        $exclude_pages = apply_filters('bahotel_l_option', array(), 'search_form_exclude_pages');
        
        $exclude_post_types = apply_filters('bahotel_l_option', array(), 'search_form_exclude_post_types');
        
        $show = true;
        
        $show = is_page() && isset($exclude_pages[$post->ID]) && $exclude_pages[$post->ID] ? false : $show;
        
        $show = isset($post->post_type) && isset($exclude_post_types[$post->post_type]) && $exclude_post_types[$post->post_type] ? false : $show;
        
        if ( apply_filters('bahotel_l_show_search_form', $show) ){
            
            if (apply_filters('bahotel_l_option', '', 'search_form_collapsible')){
                
                echo '
            <div class="search-box-mobile"><span class="search-box-mobile-expand">'.esc_html__('Check availability', 'ba-hotel-light').' <span class="lnr lnr-chevron-down"></span></span><span class="search-box-mobile-close lnr lnr-chevron-up"></span></div>
            ';
                
            }
            
            echo wp_kses( bahotel_l_search_form_html(), Bahotel_L_Settings::$wp_allowedposttags);
            
        }
        
        return;
     
     }
    
    endif;
     
    //////////////////////////////////////////////////

	if ( ! function_exists( 'bahotel_l_search_form_html' ) ) :
    
    /**
	 * Get search form html.
     * 
     * @return string
	 */
     function bahotel_l_search_form_html(){
   
        $output = '';
        
        if ( class_exists( 'BABE_Search_From' )){
      
        $search_box_class = 'search_form_white_bg';
      
        $search_box_class .= ' search_form_over_header';
      
        $output .= BABE_Search_From::render_form('', array(
          'wrapper_class' => $search_box_class,
          'form_class' => 'active',
        ));
        
        }
                    
        return $output; 
   
      }    
	
    endif;
    
    //////////////////////////////////////////
    
    add_action( 'bahotel_l_content_before', 'bahotel_l_checkout_progress_start', 100 );
    
    if ( ! function_exists( 'bahotel_l_checkout_progress_start' ) ) :
    
    /**
	 * Add checkout progress line to search results page
     * 
     * @return string
	 */
     function bahotel_l_checkout_progress_start(){
        
        global $post;
        
        if ( is_page() && $post->ID == BABE_Settings::$settings['search_result_page']){
            
            echo '<div class="col-12">'. wp_kses_post( bahotel_l_get_checkout_process_line(1) ) .'</div>';
        
        }
        
        return;
     
     }
    
    endif;
    
    //////////////////////////////////////////////////
    add_filter('babe_search_filter_sort_by_args', 'bahotel_l_search_filter_sort_by_args', 10, 1);
    
    if ( ! function_exists( 'bahotel_l_search_filter_sort_by_args' ) ) :
		
		//////////////////////////////////////////////////
		/**
		 * Filter sort by args array
		 * 
		 * @param array $args
		 * 
		 * @return array
		 */
		function bahotel_l_search_filter_sort_by_args( $args ) {
		  
            $show_filter = apply_filters('bahotel_l_option', '', 'search_result_sortby');
            
            $show_rating = apply_filters('bahotel_l_option', '', 'search_result_rating');
            
            if (!$show_filter){
                $args = array();
            } elseif (!$show_rating) {
                unset($args['rating']);
            }
            
            return $args;
		  
        }
        
	endif;  
    
    /////////////////////////////////////////////
    
    add_filter('babe_search_result_inner_class', 'bahotel_l_add_row_class', 10, 1);
    
    if ( ! function_exists( 'bahotel_l_add_row_class' ) ) :
		
		//////////////////////////////////////////////////
		/**
		 * Add row class to string
		 * 
		 * @param string $class
		 * 
		 * @return string
		 */
		function bahotel_l_add_row_class( $class = '' ) {
		  
            return trim($class.' row');
		  
		}
        
    endif;
    
    /////////////////////////////////////////////
    
    if ( ! function_exists( 'bahotel_l_room_meta' ) ) :
		
		//////////////////////////////////////////////////
		/**
		 * Get room meta html
		 * 
		 * @param int $post_id - room post id
		 * 
		 * @return string
		 */
		function bahotel_l_room_meta( $post_id ) {
		  
            $output = '';
            
            $babe_post = BABE_Post_types::get_post($post_id);
            
            $max_guests = absint(get_post_meta( $post_id, 'guests', 1 ));
			
			if (isset($babe_post['room_size']) && $babe_post['room_size']){
			 
                $last_char = mb_substr($babe_post['room_size'], -1);
                
                if ($last_char == 2){
                    
                    $room_size = esc_html(mb_substr($babe_post['room_size'], 0, -1)).'<span class="super_half">'.$last_char.'</span>';
                    
                } else {
                    $room_size = esc_html($babe_post['room_size']);
                } 
             
				$output .= '
					<div class="room_meta room_meta_size">
                        <label>'.esc_html__( 'Room size:', 'ba-hotel-light' ).'</label> <span>'.$room_size.'</span>
					</div>
				';
			}
            
            $output .= '
				<div class="room_meta room_meta_guests">
					<label>'.esc_html__( 'Guests:', 'ba-hotel-light' ).'</label> <span>'.$max_guests.'</span>
				</div>
			';
            
            if (isset($babe_post['beds']) && $babe_post['beds']){
                
                $output .= '
				<div class="room_meta room_meta_beds">
					<label>'.esc_html__( 'Beds:', 'ba-hotel-light' ).'</label> <span>'.esc_html($babe_post['beds']).'</span>
				</div>
			   ';
                
            }
            
            return $output;
		  
		}
        
    endif;
    
    /////////////////////////////////////////////
    
    if ( ! function_exists( 'bahotel_l_room_view' ) ) :
		
		//////////////////////////////////////////////////
		/**
		 * Gets room tile view.
		 * 
		 * @param array $post - BABE post
         * @param string $view - col1_s, col1, col2, col3
         * @param array $taxonomies - array of slugs
         * @param boolean $with_icons
		 * 
		 * @return string
		 */
		function bahotel_l_room_view( $post, $view, $taxonomies, $with_icons = false ) {
		     
             $output = '';
             
             switch($view){   
                case 'col2':
                default:    
                   $thumbnail = 'bahotel_thumbnail_sm';
                   $col_class = 'list_col2 col-sm-12 col-lg-6';
                   $icon_style = '';
                   $info_tags = 'full';
                   break;  
             }
             
             $thumbnail = apply_filters('bahotel_l_room_view_thumbnail', $thumbnail, $post, $view, $taxonomies, $with_icons );
             $col_class = apply_filters('bahotel_l_room_view_col_class', $col_class, $post, $view, $taxonomies, $with_icons );
             $icon_style = apply_filters('bahotel_l_room_view_icon_style', $icon_style, $post, $view, $taxonomies, $with_icons );
             $info_tags = apply_filters('bahotel_l_room_view_info_tags', $info_tags, $post, $view, $taxonomies, $with_icons );
             
             $image_html = wp_get_attachment_image( get_post_thumbnail_id( $post['ID'] ), $thumbnail );
             
             $image_html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $image_html );
				
			 $item_url = BABE_Functions::get_page_url_with_args($post['ID'], $_GET);
				
			 $image = '<a href="' . esc_url($item_url) . '">' . $image_html . '</a>';
				
				
			 $price_old = $post['discount_price_from'] < $post['price_from'] ? '<span class="room_info_price_old">' . BABE_Currency::get_currency_price( $post['price_from'] ) . '</span>' : '';
				
			 $discount = $post['discount'] ? '<div class="room_info_price_discount">-' . $post['discount'] . '%</div>' : '';				
				
			 $icons = $with_icons && $icon_style && ! empty( $taxonomies ) ? bahotel_l_room_term_icons( $post['ID'], $taxonomies, $icon_style ) : '';
                
			 $output .= '
					<div class="block_room '.esc_attr($col_class).'">
						<div class="block_room_inner">
							<div class="search_res_img">
								'.$image.'
							</div>
							';
              
              $output .= '
                            <div class="search_res_text">
                                <div class="search_res_title">
                                   <h3><a href="' . esc_url($item_url) . '"><span class="entry-title-border background-yellow"></span> ' . esc_html($post['post_title']) . '</a></h3>';
              
              $output .= bahotel_l_room_info_tags($post['ID'], $info_tags).'
                              </div>
                              <div class="search_res_description">
                                    <div class="search_res_tags_line">
										' . wp_kses( $icons, Bahotel_L_Settings::$wp_allowedposttags) . '
									</div>
							  </div>
						   </div>
                              ';                    
                                   
              $output .= '
						</div>
					</div>
				';
                
                return $output;
             
		}
        
     endif;   

	//////////////////////////////////////////////////
    add_filter( 'babe_search_result_view_full', 'bahotel_l_search_result_view', 10, 3 );
    
    if ( ! function_exists( 'bahotel_l_search_result_view' ) ) :
		
		//////////////////////////////////////////////////
		/**
		 * Styling search results.
		 * 
		 * @param string $content
         * @param array $post - BABE post
         * @param string $image
		 * 
		 * @return string
		 */
		function bahotel_l_search_result_view( $content, $post, $image ) {
		    
            $output = '';
            
            $view = apply_filters('bahotel_l_option', '', 'search_result_view');
			
			$taxonomies = apply_filters('bahotel_l_option', '', 'search_res_preview_taxonomies');
            
            $output .= bahotel_l_room_view( $post, $view, array_keys($taxonomies), true );
          
            return $output;
		}
	
    endif;
    
	//////////////////////////////////////////////////
	add_filter( 'body_class', 'bahotel_l_body_custom_class', 10, 1 );

	if ( ! function_exists( 'bahotel_l_body_custom_class' ) ) :
		
		//////////////////////////////////////////////////
		/**
		 * Adds classes to body.
		 * 
		 * @param array $classes
		 * 
		 * @return array
		 */
		function bahotel_l_body_custom_class( $classes ) {
		  
            $classes[] = Bahotel_L_Settings::$layout_current;
            
            if (apply_filters( 'bahotel_l_option', '', 'header_transparent' )){
                $classes[] = 'header_transparent';
            }
            
            if (apply_filters( 'bahotel_l_option', '', 'header_sticky' )){
                $classes[] = 'header_sticky';
            }
            
			return $classes;
		}
	endif;
    
    ////////////////////////////////////////////////////////////
	
	////////////////////////////////////////////////////////////
	//// End if class exist.
	////////////////////////////////////////////////////////////
}

//////////////////////////////////////////////////

    /**
     * Filters the default gallery shortcode output.
     *
     * @see gallery_shortcode()
     *
     * @param string $output   The gallery output. Default empty.
     * @param array  $attr     Attributes of the gallery shortcode.
     * @param int    $instance Unique numeric ID of this gallery shortcode instance.
     */
    add_filter( 'post_gallery', 'bahotel_l_post_gallery', 10, 3 );
    if ( ! function_exists( 'bahotel_l_post_gallery' ) ) :
        
        function bahotel_l_post_gallery($output, $attr, $instance){
            
            $post = get_post();
            
            $atts = shortcode_atts( array(
              'order'      => 'ASC',
              'orderby'    => 'menu_order ID',
              'id'         => $post ? $post->ID : 0,
              'itemtag'    => 'figure',
              'icontag'    => 'div',
              'captiontag' => 'figcaption',
              'columns'    => 3,
              'style' => '',
              'size'       => 'bahotel_l_gallery_sm',
              'include'    => '',
              'exclude'    => '',
              'link'       => 'file'
            ), $attr, 'gallery' );
            
            $gallery_style = $atts['style'] ? $atts['style'] : apply_filters( 'bahotel_l_option', '', 'gallery_style' );
            
            $masonry = $gallery_style == 'masonry' ? true : false;
            
            $id = intval( $atts['id'] );
            
            if ( ! empty( $atts['include'] ) ) {
                $_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
                $attachments = array();
                foreach ( $_attachments as $key => $val ) {
                    $attachments[$val->ID] = $_attachments[$key];
                }
            } elseif ( ! empty( $atts['exclude'] ) ) {
                $attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'], 'posts_per_page' => 9 ) );
            } else {
                $attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'], 'posts_per_page' => 9 ) );
            }
            
            if ( empty( $attachments ) ) {
                return '';
            }
            
            if ( is_feed() ) {
                $output = "\n";
                foreach ( $attachments as $att_id => $attachment ) {
                    $output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
                }
                return $output;
            }
            
            $itemtag = tag_escape( $atts['itemtag'] );
            $captiontag = tag_escape( $atts['captiontag'] );
            $icontag = tag_escape( $atts['icontag'] );
            $valid_tags = wp_kses_allowed_html( 'post' );
            
            if ( ! isset( $valid_tags[ $itemtag ] ) ) {
                $itemtag = 'figure';
            }
            if ( ! isset( $valid_tags[ $captiontag ] ) ) {
                $captiontag = 'figcaption';
            }
            if ( ! isset( $valid_tags[ $icontag ] ) ) {
                $icontag = 'div';
            }
            
            $columns = intval( $atts['columns'] );
            $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
            $float = is_rtl() ? 'right' : 'left';
            
            $selector = "gallery-{$instance}";
            $size_class = sanitize_html_class( $atts['size'] );
            
            $format_class = $atts['size'] != 'thumbnail' ? 'row' : "gallery-columns-{$columns}";
            
            $output = "<div id='$selector' class='gallery galleryid-{$id} gallery-size-{$size_class} {$format_class}'>";
            
            $i = 0;
            $total_images = count($attachments);
            
            $col_class = $masonry ? 'col-4' : 'col-sm-4';
            
            foreach ( $attachments as $id => $attachment ) {
                
                if ($atts['size'] != 'thumbnail' && $masonry){
                    $col_class = ($total_images > 2 && $i == 0) || ($total_images > 5 && $i == 5) ? 'col-8' : 'col-4';
                    $atts['size'] = $col_class == 'col-8' ? 'bahotel_gallery_md' : 'bahotel_gallery_sm';
                }
                
                $attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
                $img_full_src = wp_get_attachment_image_src( $id, 'full' );
                
                $img_html = wp_get_attachment_image( $id, $atts['size'], false, $attr );
                
                $img_html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $img_html );
                
                $image_output = '<a href="'.esc_url($img_full_src[0]).'" itemprop="contentUrl" data-size="'.esc_attr($img_full_src[1]).'x'.esc_attr($img_full_src[2]).'">'.$img_html.'</a>';
                
                $image_meta = wp_get_attachment_metadata( $id );
                
                $orientation = '';
                
                if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
                    $orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
                }
                
                if ($atts['size'] != 'thumbnail'){
                    $output .= $masonry && ($i == 2 || ($total_images > 5 && $i == 4)) ? '' : "<div class='{$col_class}'>";
                }
                
                $output .= "<{$itemtag} class='gallery-item'>";
                
                $output .= "
                <{$icontag} class='gallery-icon {$orientation}' data-index='{$i}'>
                   $image_output
                </{$icontag}>";
                
                if ( $captiontag && trim($attachment->post_excerpt) ) {
                    $output .= "
                <{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
                " . wptexturize($attachment->post_excerpt) . "
                </{$captiontag}>";
                }
                
                $output .= "</{$itemtag}>";
                
                if ($atts['size'] != 'thumbnail'){
                    $output .= $masonry && ($i == 1 || ($total_images > 5 && $i == 3)) ? '' : "</div>";
                }
                
                $i++;
                
             }
             
             $output .= "
            </div>\n";
            
            return $output;
        }
        
    endif;

//////////////////////////////////////////////////
add_filter( 'post_thumbnail_html', 'bahotel_l_remove_thumbnail_width_height', 10, 5 );

if ( ! function_exists( 'bahotel_l_remove_thumbnail_width_height' ) ) :
        //////////////////////////////////////////////////
		/**
		 * Remove width and height attributes from the returning image html
		 *  
		 * @return
		 */
        function bahotel_l_remove_thumbnail_width_height( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
            
            $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
            return $html;
       }

endif;

//////////////////////////////////////////////////
    add_action( 'bahotel_l_pagination', 'bahotel_l_pagination', 10 );

	if ( ! function_exists( 'bahotel_l_pagination' ) ) :
		
		//////////////////////////////////////////////////
		/**
		 * Adds pagination.
		 *  
		 * @return
		 */
		function bahotel_l_pagination() {
		  
            the_posts_pagination( array(
					'prev_text' => '&lt;<span class="screen-reader-text">' . esc_html__( 'Previous page', 'ba-hotel-light' ) . '</span>',
					'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'ba-hotel-light' ) . '</span>&gt;' ,
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'ba-hotel-light' ) . ' </span>',
		    ) );
            
            return;
        
        }
    endif; 

///////////////////////////////////////////////
    add_filter( 'excerpt_more', 'bahotel_l_excerpt_read_more', 10, 1 );

	if ( ! function_exists( 'bahotel_l_excerpt_read_more' ) ) :
		
		//////////////////////////////////////////////////
		/**
         * Filter the "read more" excerpt string link to the post.
         * 
         * @param string $more "Read more" excerpt string.
         * 
         * @return string (Maybe) modified "read more" excerpt string.
         */
		function bahotel_l_excerpt_read_more( $more ) {
		  
           return '';
          
		}
        
    endif;
    
//////////////////////////////
add_filter( 'bahotel_l_post_excerpt', 'bahotel_l_filter_post_excerpt', 10, 2 );

if ( ! function_exists( 'bahotel_l_filter_post_excerpt' ) ) :
		
		//////////////////////////////////////////////////
		/**
         * Filter the excerpt
         * 
         * @param string $excerpt excerpt string
         * @param object $post
         * 
         * @return string
         */
		function bahotel_l_filter_post_excerpt( $excerpt, $post ) {
		  
           return bahotel_l_get_excerpt($post->ID, 90);
          
		}
        
endif;    

/////////////////////////////    

if ( ! function_exists( 'bahotel_l_get_excerpt' ) ) :
    /**
     * Get the post excerpt.
     *
     * @param int $post_id
     * @param int $excerpt_length - words limit
     * 
     * @return string
     */   
   function bahotel_l_get_excerpt($post_id = 0, $excerpt_length = 38){
    
    $output = '';
    
    $post_id = $post_id ? absint($post_id) : get_the_ID();
    $post = get_post($post_id);
    $excerpt_length = absint($excerpt_length);
    
    if (!empty($post) && $excerpt_length){
        
        if ( post_password_required( $post ) ) {
            return __( 'There is no excerpt because this is a protected post.', 'ba-hotel-light' );
        }
        
        if ( metadata_exists( 'post', $post_id, '_post_excerpt' ) ) :
        
            $excerpt = get_post_meta( $post_id, '_post_excerpt', 1 );
            
        else:
            
         if ($post->post_excerpt){
            
            $excerpt = $post->post_excerpt;
        
         } else {
            
            $excerpt = $post->post_content;
            
            // get first page content for excerpt
            $pages = explode('<!--nextpage-->', $excerpt);
            $excerpt = $pages[0];
            
            // get content until more tag only
            if ( preg_match( '/<!--more(.*?)?-->/', $excerpt, $matches ) ) {
                $excerpt_arr = explode( $matches[0], $excerpt, 2 );
                $excerpt = $excerpt_arr[0];
            }
            
            // remove shortcodes
            $excerpt = strip_shortcodes($excerpt);
            
            // remove divs with content
            $excerpt = bahotel_l_strip_tags_content($excerpt, '<div>', true);
            
            // remove scripts and styles
            $excerpt = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $excerpt );
            
            // remove tags
            $excerpt = strip_tags($excerpt);
            
            // remove urls
            $excerpt = preg_replace( "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", '', $excerpt );
            
            $excerpt = str_replace('&nbsp;', ' ', $excerpt);
            
            // remove line breaks
            $excerpt = preg_replace( '/[\r\n\t ]+/', ' ', $excerpt );
        
          }
          
          // prepare excerpt source to save in DB
          $words = explode(' ', $excerpt, 150);
          array_pop($words);
          $excerpt = implode(' ', $words);
          //$excerpt = force_balance_tags( $excerpt );
          update_post_meta( $post_id, '_post_excerpt', $excerpt );
        
        endif;
        
        $words = explode(' ', $excerpt, $excerpt_length + 1);
        
        if (count($words) > $excerpt_length){
            array_pop($words);
        }
        
        $excerpt = implode(' ', $words);
        
        $output = $excerpt ? '<p>'.wp_kses_post($excerpt) .'</p>' : '';
    
    }
    
    return $output;
    
  }

endif;

//////////////////////////////////////////

if ( ! function_exists( 'bahotel_l_strip_tags_content' ) ) :    
    
    /**
	 * Strip tags with content
     * 
     * @param string $text
     * @param string $tags
     * @param boolean $invert
     * 
     * @return string
	 */
    function bahotel_l_strip_tags_content($text, $tags = '', $invert = false) { 
        
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags_arr); 
        $tags_arr = array_unique($tags_arr[1]); 
        
        if(is_array($tags_arr) AND count($tags_arr) > 0) { 
           if($invert == false) { 
             return preg_replace('@<(?!(?:'. implode('|', $tags_arr) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); 
           } 
           else { 
             return preg_replace('@<('. implode('|', $tags_arr) .')\b.*?>.*?</\1>@si', '', $text); 
           } 
        } elseif($invert == false) { 
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); 
        } 
        return $text; 
    }
    
endif;
