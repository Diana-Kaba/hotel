<?php
/**
 * Theme's defaults.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
}

//////////////////////////////////////////////////
/**
 * Theme settings
 */
class Bahotel_L_Settings {
    
	static $settings = [];
    
    static $layouts = [];
    
    static $layout_previews = [];
    
    public static $layout_current = 'no-sidebars';
    
    static $layout_vars = [];
    
    static $image_sizes = [];
    
    static $color_selectors = [];
    
    static $custom_fonts = [];
    
    static $inline_scripts = [];
    
    static $sidebars = [];
    
    public static $icons = [];
    
    static $wp_allowedposttags = [];
    
    static $option_name = 'bahotel_settings';
    
    public static $default_header_image_full = '';
    
    public static $default_header_image_thumbnail = '';
    
    public static $default_header_image_thumbnail_md = '';
    
    public static $default_spinner = '';
    
    public static $map_icon_url = '';
	
	//////////////////////////////////////////////////
	
    public static function init() {
        
        self::init_settings();
        
        add_action( 'template_redirect', array( __CLASS__, 'init_layout_current'), 10 );
        
        add_action( 'template_redirect', array( __CLASS__, 'init_layout_vars'), 20 );
        
        add_filter( 'bahotel_l_option', array( __CLASS__, 'get_option'), 10, 2);
        
        add_filter( 'bahotel_l_icon', array( __CLASS__, 'get_icon'), 10, 2);
        
        add_filter( 'shortcode_atts_address-map', array( __CLASS__, 'filter_map_style_name'), 10, 4);
        
        add_filter( 'bahotel_l_map_icon_url', array( __CLASS__, 'get_map_icon_url'), 10, 2);
             
	}
    
    //////////////////////////////
	
    /**
	 * Get option value
     * 
     * @param mixed $default_value
     * @param string $option_name
     * 
     * @return mixed
	 */
    public static function get_option($default_value, $option_name) {
        
        $key = sanitize_key($option_name);
        
        return isset(self::$settings[$key]) ? self::$settings[$key] : $default_value;
        
    }

    //////////////////////////////

    /**
     * Set option value
     *
     * @param string $option_name
     * @param mixed $value
     *
     * @return mixed
     */
    public static function set_option( $option_name, $value ) {

        $key = sanitize_key($option_name);
        $settings = get_option(self::$option_name);
        $settings[$key] = $value;
        update_option(self::$option_name, $settings);
        return;

    }
    
    //////////////////////////////
	
    /**
	 * Filter map style name
     * 
     * @param array $out The output array of shortcode attributes
     * @param array $pairs The supported attributes and their defaults
     * @param array $atts The user defined shortcode attributes
     * @param string $shortcode The shortcode name
     * 
     * @return array
	 */
    public static function filter_map_style_name($out, $pairs, $atts, $shortcode ) {
        
        $out['style'] = $out['style'] ? $out['style'] : 'silver';
        
        return $out;
        
    }
    
    //////////////////////////////
	
    /**
	 * Get map icon url
     * 
     * @param string $icon_url
     * @param array $args
     * 
     * @return string
	 */
    public static function get_map_icon_url($icon_url, $args) {
        
        return self::$map_icon_url;
        
    }
    
    //////////////////////////////
	
    /**
	 * Get icon image
     * 
     * @param mixed $default_value
     * @param string $option_name
     * 
     * @return mixed
	 */
    public static function get_icon($default_value, $option_name) {
        
        $key = sanitize_key($option_name);
        
        return isset(self::$icons[$key]) ? self::$icons[$key] : $default_value;
        
    }
    
    //////////////////////////////
	
    /**
	 * Init settings.
     * 
     * @return
	 */
    public static function init_settings() {
        
        global $allowedposttags;
        
        self::$wp_allowedposttags = $allowedposttags;
        self::$wp_allowedposttags['script'] = [];
        self::$wp_allowedposttags['time'] = array(
           'class' => [],
           'datetime' => [],
        );
        self::$wp_allowedposttags['svg'] = array(
           'class' => [],
           'xmlns' => [],
           'width' => [],
           'height' => [],
           'viewBox' => [],
        );
        self::$wp_allowedposttags['path'] = array(
           'fill' => [],
           'd' => [],
        );
        self::$wp_allowedposttags['form'] = array(
           'name' => [],
           'class' => [],
           'action' => [],
           'method' => [],
           'id' => [],
           'data-post-id' => [],
        );
        self::$wp_allowedposttags['input'] = array(
            'type' => [],
            'class' => [],
            'id' => [],
            'name' => [],
            'value' => [],
            'placeholder' => [],
            'tabindex' => [],
            'data-post-id' => [],
            'data-type' => [],
            'data-skin' => [],
            'data-prefix' => [],
            'data-min' => [],
            'data-max' => [],
            'data-from' => [],
            'data-to' => [],
            'checked' => [],
            'autocomplete' => [],
            'required' => [],
        );

        self::$wp_allowedposttags['div']['tabindex'] = [];
        
        self::$map_icon_url = BAHOTEL_L_STYLESHEET_URI . '/css/images/map_marker.png';
        
        self::$layouts = array(
            'no-sidebars' => esc_html__( 'No sidebars', 'ba-hotel-light' ),
            'no-sidebars-wide' => esc_html__( 'No sidebars (wide)', 'ba-hotel-light' ),
            'sidebar-left' => esc_html__( 'Left sidebar', 'ba-hotel-light' ),
            'sidebar-right' => esc_html__( 'Right sidebar', 'ba-hotel-light' ),
            'frontpage' => esc_html__( 'Front page', 'ba-hotel-light' ),
        );
        
        self::$layout_previews = array(
            'no-sidebars' => BAHOTEL_L_URI . '/admin/images/layout-no-sidebars.png',
            'no-sidebars-wide' => BAHOTEL_L_URI . '/admin/images/layout-no-sidebars-wide.png',
            'sidebar-left' => BAHOTEL_L_URI . '/admin/images/layout-sidebar-left.png',
            'sidebar-right' => BAHOTEL_L_URI . '/admin/images/layout-sidebar-right.png',
            'frontpage' => BAHOTEL_L_URI . '/admin/images/layout-frontpage.png',
        );
         
        self::$layout_vars['width'] = array(
            'main' => 6,
            'left' => 3,
            'right' => 3,
            'footer-left' => 3,
            'footer-middle-left' => 3,
            'footer-middle-right' => 3,
            'footer-right' => 3,
        );
        
        self::$layout_vars['offset'] = array(
            'footer-left' => 0,
            'footer-middle-left' => 0,
            'footer-middle-right' => 0,
            'footer-right' => 0,
        );
        
        self::$default_spinner = BAHOTEL_L_STYLESHEET_URI.'/css/images/spinner.gif';
        
        self::$default_header_image_full = BAHOTEL_L_STYLESHEET_URI.'/css/images/background_default_1920.jpg';
        
        self::$default_header_image_thumbnail_md = BAHOTEL_L_STYLESHEET_URI.'/css/images/background_default_1140.jpg';
        
        self::$default_header_image_thumbnail = BAHOTEL_L_STYLESHEET_URI.'/css/images/background_default_510.jpg';
        
        self::$settings = wp_parse_args( get_option(self::$option_name), array(
            'layout' => 'sidebar-right',
            'header_bg_image' => array( 'url' => ''),
            'header_bg_default' => 1,
            'header_slideshow' => 0,
            'header_sticky' => 1,
            'header_transparent' => 1,
            'gallery_style' => 'masonry',
            'blog_header_image' => array( 'url' => ''),
            'blog_header_default' => 1,
            'blog_layout' => 'sidebar-right',
            'blog_post_layout' => 'sidebar-right',
            'blog_columns' => 1,
            'blog_author' => 1,
            'blog_categories' => 1,
            'blog_tags' => 1,
            'blog_date' => 1,
            'services_header_image' => array( 'url' => ''),
            'services_header_default' => 1,
            'services_layout' => 'no-sidebars-wide',
            'services_post_layout' => 'sidebar-right',
            'services_include_pages' => [],
            'services_excerpt' => 1,
            'services_excerpt_title' => '',
            'services_excerpt_text' => '',
            'services_reviews' => 1,
            'services_review_ids' => '',
            'events_header_image' => array( 'url' => ''),
            'events_header_default' => 1,
            'events_layout' => 'no-sidebars',
            'events_post_layout' => 'no-sidebars',
            'events_post_dateformat' => 'j F',
            'events_orderby' => 'date_event',
            'events_order' => 'DESC',
            'events_related_title' => esc_html('Similar events', 'ba-hotel-light'),
            'events_related_subtitle' => esc_html('Other events you might be interested in', 'ba-hotel-light'),
            'events_related_orderby' => 'date_event',
            'events_related_order' => 'DESC',
            'footer_logo' => array( 'url' => ''),
            'archive_header_image' => array( 'url' => ''),
            'archive_header_default' => 1,
            'archive_layout' => 'sidebar-right',
            'room_layout' => 'no-sidebars',
            'room_slideshow_thumbnails' => 0,
            'room_booknow_button' => 1,
            'room_features' => 1,
            'room_booking_form' => 1,
            'room_rating' => 0,
            'room_reviews' => 1,
            'room_reviews_open' => 1,
            'search_form_add_guests' => 1,
            'search_form_add_taxonomy' => 0,
            'search_form_bg' => 0,
            'search_form_exclude_pages' => [],
            'search_form_over_header' => 1,
            'search_form_collapsible' => 0,
            'search_result_rating' => 0,
            'search_result_sortby' => 0,
            'search_result_view' => 'col1_s',
            'custom_css' => '',
            'color_main' => '#666666',
            'color_main_light' => '#999999',
            'color_main_extra_light' => '#f2f2f2',
            'color_main_yellow' => '#dfb83f',
            'color_search_form_bg' => '#dfb83f',
            'color_search_form_button' => '#012353',
            'color_search_form_button_alt' => '#dfb83f',
            'color_search_form_button_text' => '#ffffff',
            'color_title_widget' => '#333333',
            'color_block_title' => '#012353',
            'color_title_h1' => '#333333',
            'color_title_h2' => '#333333',
            'color_title_h3' => '#333333',
            'color_title_h4' => '#333333',
            'color_title_h5' => '#333333',
            'color_title_h6' => '#333333',
            'color_menu' => '#ffffff',
            'color_menu_current' => '#dfb83f',
            'color_current' => '#dfb83f',
            'color_links' => '#666666',
            'color_icons' => '#dfb83f',
            'color_buttons' => '#dfb83f',
            'color_buttons_grey' => '#333333',
            'color_buttons_alt' => '#012353',
            'color_buttons_text' => '#ffffff',
            'color_important' => '#EC0707',
            'color_header' => '#012353',
            'color_header_text' => '#ffffff',
            'color_bg' => '#012353',
            'color_bg_text' => '#ffffff',
            'color_footer' => '#012353',
            'color_footer_title' => '#ffffff',
            'color_footer_text' => '#dbdbdb',
            'font_header_menu' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '400',
                    'font-size'  => '15px',
            ),
            'font_header_submenu' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '400',
                    'font-size'  => '13px',
            ),
            'font_header_brand' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Raleway',
                    'font-weight'  => '300',
                    'font-size'  => '22px',
            ),
            'font_header_title' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Raleway',
                    'font-weight'  => '500',
                    'font-size'  => '60px',
            ),
            'font_header_title_front' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Raleway',
                    'font-weight'  => '500',
                    'font-size'  => '66px',
            ),
            'font_header_subtitle' => array(
                    'google'      => true,
                    'subsets' => 'latin-ext',
                    'font-family' => 'Great Vibes',
                    'font-weight'  => '400',
                    'font-size'  => '55px',
            ),
            'font_header_breadcrumbs' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '400',
                    'font-size'  => '15px',
            ),
            'font_title_f' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '32px',
            ),
            'font_title_ff' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '22px',
            ),
            'font_title_fff' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '18px',
            ),
            'font_title_ffff' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '17px',
            ),
            'font_title_fffff' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '15px',
            ),
            'font_title_ffffff' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '11px',
            ),
            'font_title_feature' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '16px',
            ),
            'font_button' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '400',
                    'font-size'  => '15px',
            ),
            'font_text_main' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '400',
                    'font-size'  => '15px',
            ),
            'font_text_main_blockquote' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '400',
                    'font-style'  => 'italic',
                    'font-size'  => '15px',
            ),
            'font_text_meta' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '400',
                    'font-size'  => '14px',
            ),
            'font_footer_title' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '22px',
            ),
            'font_footer_text' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '300',
                    'font-size'  => '15px',
            ),
            'font_block_title' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '42px',
            ),
            'font_block_subtitle' => array(
                    'google'      => true,
                    'subsets' => 'latin-ext',
                    'font-family' => 'Great Vibes',
                    'font-weight'  => '400',
                    'font-size'  => '25px',
            ),
            'font_block_label' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '400',
                    'font-size'  => '18px',
            ),
            'font_block_article_title' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '35px',
            ),
            'font_room_title_f' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '50px',
            ),
            'font_room_title_ff' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '500',
                    'font-size'  => '35px',
            ),
            'font_room_icon' => array(
                    'google'      => true,
                    'subsets' => 'latin',
                    'font-family' => 'Poppins',
                    'font-weight'  => '400',
                    'font-size'  => '22px',
            ),
        ));
        
        self::$sidebars = array(
           'left' => array(
                'name' => esc_html__( 'Left Sidebar', 'ba-hotel-light' ),
                //'desc' => __( 'Left Sidebar', 'ba-hotel-light' ),
                ),
           'right' => array(
                'name' => esc_html__( 'Right Sidebar', 'ba-hotel-light' ),
                ),
           'before-header' => array(
                'name' => esc_html__( 'Before-Header panel', 'ba-hotel-light' ),
                ),
           'header'=> array(
                'name' => esc_html__( 'Header panel', 'ba-hotel-light' ),
                ),
           'before-footer' => array(
                'name' => esc_html__( 'Before-Footer panel', 'ba-hotel-light' ),
                ),
           'footer' => array(
                'name' => esc_html__( 'Footer panel', 'ba-hotel-light' ),
                ),
           'footer-left' => array(
                'name' => esc_html__( 'Footer left panel', 'ba-hotel-light' ),
                ),
           'footer-middle-left' => array(
                'name' => esc_html__( 'Footer middle left panel', 'ba-hotel-light' ),
                ),
           'footer-middle-right' => array(
                'name' => esc_html__( 'Footer middle right panel', 'ba-hotel-light' ),
                ),     
           'footer-right' => array(
                'name' => esc_html__( 'Footer right panel', 'ba-hotel-light' ),
                ),
        );
        
        self::$custom_fonts = array(
              'font_header_brand' => array(
                 'name' => esc_html__( 'Site brand name', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.brand_name',
                 ),
              ),
              'font_header_menu' => array(
                 'name' => esc_html__( 'Header menu', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.header_menu',
                   '.nav-menu li',
                   '.nav-menu li a',
                 ),
              ),
              'font_header_submenu' => array(
                 'name' => esc_html__( 'Header second menu', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.header_menu_second',
                 ),
              ),
              'font_header_title' => array(
                 'name' => esc_html__( 'Header page title', 'ba-hotel-light' ),
                 'selectors' => array(
                   'h1.page_title',
                 ),
              ),
              'font_header_title_front' => array(
                 'name' => esc_html__( 'Header frontpage title', 'ba-hotel-light' ),
                 'selectors' => array(
                   'h1.page_title_front',
                   '#home_carousel .home-slide-title',
                   '#home_carousel .home-slide-title-grand',
                 ),
              ),
              'font_header_subtitle' => array(
                 'name' => esc_html__( 'Header page subtitle', 'ba-hotel-light' ),
                 'selectors' => array(
                   'h1.page_subtitle',
                   '#home_carousel .home-slide-title-before',
                 ),
              ),
              'font_header_breadcrumbs' => array(
                 'name' => esc_html__( 'Header breadcrumbs', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.header_breadcrumbs',
                 ),
              ),
              'font_title_f' => array(
                 'name' => esc_html__( 'H1', 'ba-hotel-light' ),
                 'selectors' => array(
                   'h1',
                   'h2.entry-title',
                 ),
              ),
              'font_title_ff' => array(
                  'name' => esc_html__( 'H2', 'ba-hotel-light' ),
                  'selectors' => array(
                     'h2',
                     '.search_res_title h3',
                     '.widget-area section[class^="widget_babe_"] h3',
                     '.widget-area section[class*=" widget_babe_"] h3',
                  ),
              ),
              'font_title_fff' => array(
                  'name' => esc_html__( 'H3', 'ba-hotel-light' ),
                  'selectors' => array(
                     'h3',
                     'h3.widget-title'                     
                  ),
              ),
              'font_title_ffff' => array(
                  'name' => esc_html__( 'H4', 'ba-hotel-light' ),
                  'selectors' => array(
                     'h4',
                     '.comment-author-name',
                     '.gallery-item-hover-desc',
                  ),
              ),
              'font_title_fffff' => array(
                  'name' => esc_html__( 'H5', 'ba-hotel-light' ),
                  'selectors' => array(
                      'h5',
                  ),
              ),
              'font_title_ffffff' => array(
                  'name' => esc_html__( 'H6', 'ba-hotel-light' ),
                  'selectors' => array(
                      'h6',
                  ),
              ),
              'font_title_feature' => array(
                 'name' => esc_html__( 'Feature title', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.feature_title',
                 ),
              ),
              'font_button' => array(
                 'name' => esc_html__( 'Buttons', 'ba-hotel-light' ),
                 'selectors' => array(
                   'button',
                   '.btn',
                   '.button',
                   'input[type=button]',
                   'input[type=reset]',
                   'input[type=submit]',
                 ),
              ),
              'font_text_main' => array(
                 'name' => esc_html__( 'Main text', 'ba-hotel-light' ),
                 'selectors' => array(
                   'body',
                   'button',
                   'input',
                   'optgroup',
                   'select',
                   'textarea',
                   '.front_top_block.services_block .service_list_subtitle',
                 ),
              ),
              'font_text_main_blockquote' => array(
                 'name' => esc_html__( 'Main text blockquote', 'ba-hotel-light' ),
                 'selectors' => array(
                   'blockquote',
                 ),
              ),
              'font_text_meta' => array(
                 'name' => esc_html__( 'Post meta', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.text_meta',
                 ),
              ),
              'font_footer_title' => array(
                 'name' => esc_html__( 'Footer title', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.site-footer h2',
                 ),
              ),
              'font_footer_text' => array(
                 'name' => esc_html__( 'Footer text', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.site-footer',
                 ),
              ),
              'font_block_title' => array(
                 'name' => esc_html__( 'Frontpage block title', 'ba-hotel-light' ),
                 'selectors' => array(
                   'h1.block_title',
                   '.confirmation_title',
                   'h2.front_top_title'
                 ),
              ),
              'font_block_subtitle' => array(
                 'name' => esc_html__( 'Frontpage block subtitle', 'ba-hotel-light' ),
                 'selectors' => array(
                   'h2.block_subtitle',
                   '.bahotel_l_message_order',
                   'h3.service_list_subtitle',
                   'h3.front_top_subtitle',
                   '.story_block .story_title',
                 ),
              ),
              'font_block_article_title' => array(
                 'name' => esc_html__( 'Frontpage block article title', 'ba-hotel-light' ),
                 'selectors' => array(
                   'h2.block_article_title',
                   '.address_bar_block h2',
                   '.why_choose_us_block h2.why_choose_us_title',
                 ),
              ),
              'font_block_label' => array(
                 'name' => esc_html__( 'Frontpage block label', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.block_label',
                 ),
              ),
              'font_room_title_f' => array(
                 'name' => esc_html__( 'Room page H1', 'ba-hotel-light' ),
                 'selectors' => array(
                   'h1.room_title',
                 ),
              ),
              'font_room_title_ff' => array(
                 'name' => esc_html__( 'Room page H2', 'ba-hotel-light' ),
                 'selectors' => array(
                   'h2.room_sub_title',
                   'h2.services_sub_title',
                 ),
              ),
              'font_room_icon' => array(
                 'name' => esc_html__( 'Room page icon label', 'ba-hotel-light' ),
                 'selectors' => array(
                   '.room_icon_label',
                 ),
              ),
         );
         
         self::$image_sizes = array(
           'bahotel_header' => array(
              'width' => 1920,
              'height' => 1038,
              'crop' => true,
           ),
           'bahotel_thumbnail_lg' => array(
              'width' => 1140,
              'height' => 684,
              'crop' => true,
           ),
           'bahotel_thumbnail_md' => array(
              'width' => 870,
              'height' => 522,
              'crop' => true,
           ),
           'bahotel_thumbnail_sm' => array(
              'width' => 555,
              'height' => 333,
              'crop' => true,
           ),
           'bahotel_list_lg' => array(
              'width' => 800,
              'height' => 440,
              'crop' => true,
           ),
           'bahotel_list_md' => array(
              'width' => 440,
              'height' => 242,
              'crop' => true,
           ),
           'bahotel_list_v' => array(
              'width' => 360,
              'height' => 490,
              'crop' => true,
           ),
           'bahotel_list_vl' => array(
              'width' => 510,
              'height' => 694,
              'crop' => true,
           ),
           'bahotel_gallery_md' => array(
              'width' => 750,
              'height' => 630,
              'crop' => true,
           ),
           'bahotel_gallery_sm' => array(
              'width' => 360,
              'height' => 302,
              'crop' => true,
           ),
        );
        
        self::$image_sizes = apply_filters('bahotel_l_init_image_sizes', self::$image_sizes);

        self::$custom_fonts = apply_filters('bahotel_l_init_custom_fonts', self::$custom_fonts);
        
        self::$sidebars = apply_filters('bahotel_l_init_sidebars', self::$sidebars);
        
        self::$layouts = apply_filters('bahotel_l_init_layouts', self::$layouts);
        
        self::$layout_previews = apply_filters('bahotel_l_init_layout_previews', self::$layout_previews);
        
        self::$layout_vars = apply_filters('bahotel_l_init_layout_vars', self::$layout_vars);
        
        self::$settings = apply_filters('bahotel_l_init_settings', self::$settings);
        
        self::$layout_current = self::$settings['layout'];
        
        self::init_custom_colors();
        
        self::$icons = array(
            'guests' => '<svg class="bah_icon bah_icon_guests" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><path fill="currentColor" d="M40 19.9C40 8.9 31-0.1 20-0.1c-11 0-20 9-20 20.1 0 6.7 3.3 12.7 8.4 16.3 0 0 0 0 0 0 0 0 0.1 0 0.1 0.1 0.3 0.2 0.6 0.4 0.8 0.6 0.2 0.1 0.4 0.3 0.7 0.4 0.3 0.2 0.5 0.3 0.8 0.5 0.3 0.1 0.6 0.3 0.8 0.4 0.2 0.1 0.5 0.2 0.7 0.3 0.5 0.2 1 0.4 1.6 0.6 0.2 0 0.3 0.1 0.5 0.1 0.4 0.1 0.9 0.2 1.3 0.3 0.2 0 0.4 0.1 0.6 0.1 0.4 0.1 0.8 0.1 1.3 0.2 0.2 0 0.4 0 0.6 0.1C18.8 40 19.4 40 20 40s1.2 0 1.8-0.1c0.2 0 0.4 0 0.6-0.1 0.4-0.1 0.8-0.1 1.3-0.2 0.2 0 0.4-0.1 0.6-0.1 0.4-0.1 0.9-0.2 1.3-0.3 0.2 0 0.3-0.1 0.5-0.1 0.5-0.2 1.1-0.4 1.6-0.6 0.3-0.1 0.5-0.2 0.7-0.3 0.3-0.1 0.5-0.3 0.8-0.4 0.3-0.1 0.6-0.3 0.8-0.5 0.2-0.1 0.4-0.3 0.6-0.4 0.3-0.2 0.6-0.4 0.9-0.6 0 0 0.1 0 0.1-0.1 0 0 0 0 0 0C36.7 32.6 40 26.7 40 19.9zM14.9 26.8c-0.4-0.5-0.8-1.2-1.2-3.1 -0.2-0.9-0.7-1.2-1-1.3 -0.4-0.2-0.8-0.4-0.9-2.1 0-1 0.4-1.2 0.4-1.2 0.3-0.1 0.4-0.4 0.3-0.7 0 0-0.2-1-0.4-2.2 -0.5-3.1-0.5-6.5 3.4-8.1 4-1.6 6.8-0.5 7.2 0.1 0.1 0.1 0.4 0.3 0.5 0.3 0.1 0 0.1 0 0.2 0 0.3 0 2.1 0 3.2 1.3 1.6 1.9 1.1 6.9 0.8 8.5 0 0.3 0.1 0.5 0.3 0.7 0 0 0.4 0.3 0.4 1.2 -0.1 1.8-0.5 2-0.9 2.2 -0.3 0.2-0.9 0.4-1 1.3 -0.3 1.9-0.8 2.6-1.2 3.1 -0.4 0.5-0.7 1-0.7 2.2 0 3.5 1.3 5.3 5.5 7 -0.1 0.1-0.3 0.2-0.4 0.3 -0.2 0.1-0.4 0.3-0.7 0.4 -0.4 0.2-0.9 0.4-1.3 0.6 -0.2 0.1-0.4 0.2-0.6 0.2 -0.3 0.1-0.7 0.3-1 0.4 -0.2 0.1-0.4 0.1-0.5 0.2 -0.5 0.2-1 0.3-1.5 0.4 -0.1 0-0.2 0-0.3 0.1 -0.5 0.1-0.9 0.2-1.4 0.2 -0.2 0-0.3 0-0.5 0 -0.6 0.1-1.1 0.1-1.7 0.1s-1.1 0-1.7-0.1c-0.2 0-0.3 0-0.5 0 -0.5-0.1-0.9-0.1-1.4-0.2 -0.1 0-0.2 0-0.3 0 -0.5-0.1-1-0.2-1.5-0.4 -0.2-0.1-0.3-0.1-0.5-0.2 -0.3-0.1-0.7-0.2-1-0.4 -0.2-0.1-0.4-0.1-0.6-0.2 -0.4-0.2-0.9-0.4-1.3-0.6 -0.2-0.1-0.4-0.2-0.6-0.4 -0.2-0.1-0.3-0.2-0.5-0.3 4.2-1.7 5.5-3.5 5.5-7C15.6 27.8 15.3 27.3 14.9 26.8zM31.1 35.1c-4.7-1.8-5.5-3.3-5.5-6.2 0-0.8 0.2-1 0.5-1.5 0.4-0.6 1-1.4 1.4-3.6 0.1-0.3 0.1-0.3 0.4-0.5 0.7-0.4 1.4-0.9 1.6-3.2 0-1.1-0.4-1.7-0.7-2 0.2-1.5 0.9-6.8-1.2-9.2 -1.3-1.5-3.2-1.7-3.9-1.7 -1-1.1-4.3-2-8.5-0.3 -4.7 1.9-4.7 6.1-4.2 9.4 0.1 0.8 0.2 1.4 0.3 1.8 -0.3 0.3-0.7 0.9-0.7 2.1 0.2 2.3 0.9 2.8 1.6 3.2 0.3 0.1 0.3 0.2 0.4 0.5 0.4 2.1 1 3 1.4 3.6 0.3 0.5 0.5 0.7 0.5 1.5 0 2.8-0.8 4.4-5.5 6.2 -4.6-3.4-7.7-8.9-7.7-15.2C1.2 9.5 9.6 1.1 20 1.1c10.4 0 18.8 8.5 18.8 18.8C38.8 26.2 35.8 31.7 31.1 35.1z"/></svg>',
            'size' => '<svg class="bah_icon bah_icon_size" xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38"><path fill="currentColor" d="M37.3 0H0.7C0.3 0 0 0.3 0 0.7v36.6C0 37.7 0.3 38 0.7 38h36.6C37.7 38 38 37.7 38 37.3V0.7C38 0.3 37.7 0 37.3 0zM31.2 1.4L18.8 13.8l-4.4-4.4 8.1-8H31.2zM27.4 7.1l4.2 4.2L10.8 32l-4.2-4.2 12.6-12.6c0 0 0 0 0 0L27.4 7.1zM20.5 1.4L7.7 14.1 3.1 9.5l8.6-8.1H20.5zM1.4 1.4H9.8L1.7 8.9c0 0-0.1 0-0.1 0.1 0 0 0 0 0 0.1L1.4 9.3V1.4zM1.4 11.1l0.8-0.7 4.6 4.6 -5.4 5.3V11.1zM1.4 22.2l12-11.9 4.4 4.4L5.2 27.4c0 0 0 0 0 0 0 0 0 0 0 0l-3.9 3.9V22.2zM1.4 36.6v-3.5l4.4-4.4 4.2 4.2L6.2 36.6H1.4zM8.1 36.6l3.2-3.2 12.5-12.5 4 4L16 36.6H8.1zM26.5 36.6h-8.6l5.4-5.4 4.3 4.3L26.5 36.6zM36.6 36.6h-8.2L29 36.1c0 0 0 0 0 0 0 0 0 0 0 0l7.6-7.6V36.6zM36.6 26.5l-8.1 8.1 -4.3-4.3 4.9-4.9 7.5-7.5V26.5zM36.6 16c0 0 0 0 0 0l-7.9 7.9 -4-4L36.6 8.1V16zM36.6 6.2l-4.1 4.1L28.4 6.2l4.8-4.8h3.5V6.2z"/></svg>',
            'price' => '<svg class="bah_icon bah_icon_price" xmlns="http://www.w3.org/2000/svg" width="48" height="30" viewBox="0 0 48 30"><path fill="currentColor" d="M47.3 0H0.7C0.3 0 0 0.3 0 0.7v28.6C0 29.7 0.3 30 0.7 30h46.7C47.7 30 48 29.7 48 29.3V0.7C48 0.3 47.7 0 47.3 0zM1.3 1.4h4.1c-0.3 2.3-2 4.1-4.1 4.4V1.4zM1.3 28.6v-4.4c2.1 0.3 3.8 2.1 4.1 4.4H1.3zM46.7 28.6h-4.1c0.3-2.3 2-4.1 4.1-4.4V28.6zM46.7 22.8c-2.9 0.3-5.2 2.8-5.5 5.8H6.8c-0.3-3.1-2.6-5.5-5.5-5.8V7.2C4.2 6.9 6.5 4.5 6.8 1.4h34.4c0.3 3.1 2.6 5.5 5.5 5.8V22.8zM46.7 5.8c-2.1-0.3-3.8-2.1-4.1-4.4h4.1V5.8zM24.9 14.5l-0.2-0.1V8.8c1.8 0.2 2.9 1.1 3 1.2 0.3 0.2 0.7 0.2 0.9-0.1 0.2-0.3 0.2-0.7-0.1-1 -0.1-0.1-1.5-1.3-3.8-1.5V6.4c0-0.4-0.3-0.7-0.7-0.7 -0.4 0-0.7 0.3-0.7 0.7v1.1c-2.3 0.3-3.8 1.8-3.8 4.1 0 2.2 2.3 3.3 3.8 3.9v5.4c-1.2-0.3-2.6-1-3.3-1.6 -0.3-0.2-0.7-0.2-0.9 0.1 -0.2 0.3-0.2 0.7 0.1 1 0.8 0.7 2.5 1.6 4.1 1.9v1.5c0 0.4 0.3 0.7 0.7 0.7 0.4 0 0.7-0.3 0.7-0.7v-1.4c1.9-0.2 4.2-1.4 4.2-3.8C29 15.9 26.5 15 24.9 14.5zM23.4 13.9c-1.3-0.6-2.5-1.3-2.5-2.4 0-1.5 0.9-2.5 2.5-2.7V13.9zM24.7 20.9v-5c1.7 0.6 2.9 1.2 2.9 2.6C27.6 20 26 20.7 24.7 20.9z"/></svg>',
        );
        
        self::$icons = apply_filters('bahotel_l_init_icons', self::$icons);
        
        /////Inline loadCSS////
        self::$inline_scripts = '/*! loadCSS. [c]2017 Filament Group, Inc. MIT License */
/* This file is meant as a standalone workflow for
- testing support for link[rel=preload]
- enabling async CSS loading in browsers that do not support rel=preload
- applying rel preload css once loaded, whether supported or not.
*/
(function( w ){
	"use strict";
	// rel=preload support test
	if( !w.loadCSS ){
		w.loadCSS = function(){};
	}
	// define on the loadCSS obj
	var rp = loadCSS.relpreload = {};
	// rel=preload feature support test
	// runs once and returns a function for compat purposes
	rp.support = (function(){
		var ret;
		try {
			ret = w.document.createElement( "link" ).relList.supports( "preload" );
		} catch (e) {
			ret = false;
		}
		return function(){
			return ret;
		};
	})();

	// if preload isn\'t supported, get an asynchronous load by using a non-matching media attribute
	// then change that media back to its intended value on load
	rp.bindMediaToggle = function( link ){
		// remember existing media attr for ultimate state, or default to \'all\'
		var finalMedia = link.media || "all";

		function enableStylesheet(){
			// unbind listeners
			if( link.addEventListener ){
				link.removeEventListener( "load", enableStylesheet );
			} else if( link.attachEvent ){
				link.detachEvent( "onload", enableStylesheet );
			}
			link.setAttribute( "onload", null ); 
			link.media = finalMedia;
		}

		// bind load handlers to enable media
		if( link.addEventListener ){
			link.addEventListener( "load", enableStylesheet );
		} else if( link.attachEvent ){
			link.attachEvent( "onload", enableStylesheet );
		}

		// Set rel and non-applicable media type to start an async request
		// note: timeout allows this to happen async to let rendering continue in IE
		setTimeout(function(){
			link.rel = "stylesheet";
			link.media = "only x";
		});
		// also enable media after 3 seconds,
		// which will catch very old browsers (android 2.x, old firefox) that don\'t support onload on link
		setTimeout( enableStylesheet, 3000 );
	};

	// loop through link elements in DOM
	rp.poly = function(){
		// double check this to prevent external calls from running
		if( rp.support() ){
			return;
		}
		var links = w.document.getElementsByTagName( "link" );
		for( var i = 0; i < links.length; i++ ){
			var link = links[ i ];
			// qualify links to those with rel=preload and as=style attrs
			if( link.rel === "preload" && link.getAttribute( "as" ) === "style" && !link.getAttribute( "data-loadcss" ) ){
				// prevent rerunning on link
				link.setAttribute( "data-loadcss", true );
				// bind listeners to toggle media back
				rp.bindMediaToggle( link );
			}
		}
	};

	// if unsupported, run the polyfill
	if( !rp.support() ){
		// run once at least
		rp.poly();

		// rerun poly on an interval until onload
		var run = w.setInterval( rp.poly, 500 );
		if( w.addEventListener ){
			w.addEventListener( "load", function(){
				rp.poly();
				w.clearInterval( run );
			} );
		} else if( w.attachEvent ){
			w.attachEvent( "onload", function(){
				rp.poly();
				w.clearInterval( run );
			} );
		}
	}
	// commonjs
	if( typeof exports !== "undefined" ){
		exports.loadCSS = loadCSS;
	}
	else {
		w.loadCSS = loadCSS;
	}
}( typeof global !== "undefined" ? global : this ) );';
        
        return;
        
    }
    
    ///////////////////////////
    
    /**
	 * Init layout vars.
     * 
     * @return
	 */
    public static function init_layout_current() {
        
        global $post;
        
            $custom_layout = apply_filters( 'bahotel_l_page_option', '', 'layout' );
            $blog_layout = apply_filters( 'bahotel_l_option', '', 'blog_layout' );
            $blog_post_layout = apply_filters( 'bahotel_l_option', '', 'blog_post_layout' );
            $archive_layout = apply_filters( 'bahotel_l_option', '', 'archive_layout' );
            $room_layout = apply_filters( 'bahotel_l_option', '', 'room_layout' );
            $services_layout = apply_filters( 'bahotel_l_option', '', 'services_layout' );
            $services_post_layout = apply_filters( 'bahotel_l_option', '', 'services_post_layout' );
            $events_layout = apply_filters( 'bahotel_l_option', '', 'events_layout' );
            $events_post_layout = apply_filters( 'bahotel_l_option', '', 'events_post_layout' );
            
            if (is_singular() && $custom_layout && $custom_layout != self::$layout_current && isset(self::$layouts[$custom_layout])){
                
                self::$layout_current = $custom_layout;
                
            } elseif (!$custom_layout && is_front_page() && is_home() && isset(self::$layouts[$blog_layout])){
                
                self::$layout_current = $blog_layout;
                
            } elseif (!$custom_layout && is_front_page()){
                
                self::$layout_current = 'frontpage';
                
            } elseif (!$custom_layout && is_home() && isset(self::$layouts[$blog_layout])){
                
                self::$layout_current = $blog_layout;
                
            } elseif (!$custom_layout && is_post_type_archive('service') && isset(self::$layouts[$services_layout])){
                
                self::$layout_current = $services_layout;
                
            } elseif (!$custom_layout && is_post_type_archive('event') && isset(self::$layouts[$events_layout])){
                
                self::$layout_current = $events_layout;
                
            } elseif (!$custom_layout && is_archive() && isset(self::$layouts[$archive_layout])){
                
                self::$layout_current = $archive_layout;
                
            } elseif (!$custom_layout && is_single() && 'to_book' == $post->post_type && isset(self::$layouts[$room_layout])){
                
                self::$layout_current = $room_layout;
                
            } elseif (!$custom_layout && is_single() && 'post' == $post->post_type && isset(self::$layouts[$blog_post_layout])){
                
                self::$layout_current = $blog_post_layout;
                
            } elseif (!$custom_layout && is_single() && 'service' == $post->post_type && isset(self::$layouts[$services_post_layout])){
                
                self::$layout_current = $services_post_layout;
                
            } elseif (!$custom_layout && is_single() && 'event' == $post->post_type && isset(self::$layouts[$events_post_layout])){
                
                self::$layout_current = $events_post_layout;
                
            }
        
        return;
        
     }   
    
    //////////////////////////////
	
    /**
	 * Init layout vars.
     * 
     * @return
	 */
    public static function init_layout_vars() {
        
        if (!is_active_sidebar('left') || (self::$layout_current != 'sidebar-left' && self::$layout_current != 'sidebars-left-right')){
            self::$layout_vars['width']['main'] = self::$layout_vars['width']['main'] + self::$layout_vars['width']['left'];
            self::$layout_vars['width']['left'] = 0;
        }
        
        if (!is_active_sidebar('right') || (self::$layout_current != 'sidebar-right' && self::$layout_current != 'sidebars-left-right')){
            self::$layout_vars['width']['main'] = self::$layout_vars['width']['main'] + self::$layout_vars['width']['right'];
            self::$layout_vars['width']['right'] = 0; 
        }
        
        $footer_left = is_active_sidebar('footer-left') ? 1 : 0;
        $footer_middle_left = is_active_sidebar('footer-middle-left') ? 1 : 0;
        $footer_middle_right = is_active_sidebar('footer-middle-right') ? 1 : 0;
        $footer_right = is_active_sidebar('footer-right') ? 1 : 0;
        
        $footers = $footer_left + $footer_middle_left + $footer_middle_right + $footer_right;
        
        if (!$footers){
            
            self::$layout_vars['width']['footer-left'] = 0;
            self::$layout_vars['width']['footer-middle-left'] = 0;
            self::$layout_vars['width']['footer-middle-right'] = 0;
            self::$layout_vars['width']['footer-right'] = 0;
            
        } else {
            
            self::$layout_vars['offset']['footer-left'] = 0;
            self::$layout_vars['offset']['footer-middle-left'] = (1-$footer_left)*3;
            self::$layout_vars['offset']['footer-middle-right'] = (1-$footer_middle_left)*(1+(1-$footer_left))*3;
            self::$layout_vars['offset']['footer-right'] = (1-$footer_middle_right)*(1+(1-$footer_middle_left)+ (1-$footer_middle_left)*(1-$footer_left))*3;  
            
        }
        
        return;
    }
    
    //////////////////////////////
	
    /**
	 * Init custom colors.
     * 
     * @return
	 */
    public static function init_custom_colors() {
        
        $color_selectors = array(
           'meta'	 => array(
             // Color set meta data.
             'name'  => esc_html__( 'Color scheme', 'ba-hotel-light' ),
             'desc' => esc_html__( 'Color scheme.', 'ba-hotel-light' ),
           ),
    'color_links' => array(
		'name'  => esc_html__( 'Links', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'a',
				'a:visited',
                'a:active',
				'a:focus',
				'a:hover',
				'.main-link-color',
                '.block_faq .block_faq_title',
                '.block_faq .block_faq_title h4',
                '.babe_search_results_filters .input_select_input:hover',
                '.babe_search_results_filters .input_select_input:focus',
                '.checkout_form_input_field.checkout_form_input_field_focus',
                '.checkout_form_input_field.checkout_form_input_field_focus label',
                '.checkout_form_input_field .checkout_form_input_ripple',
                '#checkout_form .tab_title',
			),
		),
	),
    'color_main' => array(
              'name'  => esc_html__( 'Main text', 'ba-hotel-light' ),
              'desc'  => '',
              'selectors' => array(
                'color' => array(
                    'body',
                    'input',
                    'code',
                    'optgroup',
                    'select',
                    'textarea',
                    'label',
                    'p',
                    'blockquote',
                    '.widget .tag-cloud-link',
//                    '.add_input_field .add_ids_list .term_item',
//                    '.search_guests_block.input_select_field .input_select_list .term_item',
                    '#my_account_page_wrapper .my_account_page_nav_wrapper a',
                    '#my_account_page_wrapper .my_account_page_nav_wrapper a:hover',
                    '#my_account_page_wrapper .my_account_page_nav_wrapper a:focus',
                    '#my_account_page_wrapper .my_account_page_nav_wrapper a:visited',
                    '#my_account_page_wrapper .my_account_page_nav_wrapper .my_account_nav_item .my_account_nav_item_with_menu > .my_account_nav_item_title',
                    '.widget .tag-cloud-link:hover',
                    '.widget .tag-cloud-link:focus',
                    '#content .btn.btn_white',
                    '#content a.btn.btn_white',
                    '.checkout_form_wrap h2',
                    '.checkout_form_wrap h3',
                    '.checkout_order_items_wrap h2',
                    '.event_subtitle',
                    '.front_top_block.services_block a.btn.button',
                    '.offer_item_button_wrapper a.btn.button',
                 ),
               ),
            ),
    'color_main_light' => array(
		'name'  => esc_html__( 'Light text', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.content-light',
                '#comments .comment-date',
                '#comments .comment-content time',
                '#comments .comment-reply',
                '#comments .comment-reply a',
                '.widget .post-date',
                '.nav-prevnext-label',
                '.nav-prevnext-chevron',
                '.pagination .page-numbers.current',
                '.babe_pager .page-numbers.current',
                '.search_res_text_side .search_res_tags_line',
                '.front_top_subtitle',
			),
		),
	),
    'color_main_extra_light' => array(
		'name'  => esc_html__( 'Extra Light text', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
                '.entry-header-container a .entry-date',
                '.entry-header-container a .posted-on-year-wrap',
			),
		),
	),
    'color_main_yellow' => array(
		'name'  => esc_html__( 'Yellow text', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.text-yellow',
                '.text-yellow p',
                'p.text-yellow',
                'h1.text-yellow',
                'h2.text-yellow',
                'h3.text-yellow',
                'h4.text-yellow',
                'h5.text-yellow',
                'h6.text-yellow',
                '.room_sub_title_bottom_line',
                '.services_sub_title_bottom_line',
                '.bahotel_l_term_icon i',
                '.bahotel_l_term_icon span',
                '.search_res_text:hover .search_res_title a',
                '.search_res_text:focus .search_res_title a',
                '.front_top_block.services_block .service_list_subtitle',
                '.block_team .team-title-group a:hover',
                '.block_team .team-title-group a:focus',
                '.block_team:hover .team-title-group .entry-title',
                '.block_team:focus .team-title-group .entry-title',
                '.story_video_control .story-video-button .eleganticon',
                '.why_choose_us_block .why_choose_us_item_subtitle',
                '#home_carousel .home-slide-title-grand',
                '#home_carousel .home-slide-title-discount',
			),
            'border-color' => array(
                '.room_sub_title_bottom_line',
                '.services_sub_title_bottom_line',
                '.widget-area section[class^="widget_babe_"] .sidebar_sub_title_bottom_line',
                ' .widget-area section[class*=" widget_babe_"] .sidebar_sub_title_bottom_line',
                '.checkout_sub_title_bottom_line',
                'body.single-event .event-title-group',
                '.block_team .team-title-group',
                '.offer_item_selected .offer_item_button_wrapper .button',
                '#home_carousel .home-slide-title-button-wrapper .button:hover',
                '#home_carousel .home-slide-title-button-wrapper .button:focus',
			),
            'background-color' => array(
                '.background-yellow',
                '.block_room.list_col3 .btn.search_res_reserv_button:hover',
                '.block_room.list_col3 .btn.search_res_reserv_button:focus',
                '.history_event_item_date',
                '.offer_item_selected .offer_item_title',
                '#home_carousel .home-slide-title-button-wrapper .button:hover',
                '#home_carousel .home-slide-title-button-wrapper .button:focus',
                '.search-box-mobile',
			),
		),
	),
	'color_title_widget' => array(
		'name'  => esc_html__( 'Widget title', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.widget-title',
                '.sidebar-right a',
                '.sidebar-left a',
			),
		),
	),
    'color_block_title' => array(
		'name'  => esc_html__( 'Block title', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.block_title',
                '.room_title',
                '.room_sub_title',
                '.front_top_title',
                '.services_sub_title',
                '.confirmation_title',
                '.widget-area section[class^="widget_babe_"] h3',
                '.widget-area section[class*=" widget_babe_"] h3',
                '.checkout_order_items_title',
                '.checkout_process_line_item_active',
                '.story_block h2.story_title',
                '.address_bar_block h2',
                '.why_choose_us_block h2.why_choose_us_title',
                '#my_account_page_wrapper .my_account_inner_page_block h2',
                '#login_form h2',
                '.search_form_white_bg #search_form',
			),
            'background-color' => array(
                '.block_title_decore_line_half',
                '.block_title_decore_line_full',
            ),
		),
	),
    'color_title_h1' => array(
		'name'  => esc_html__( 'H1', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'h1',
                'h1 a',
                'h1 a:hover',
                'h1 a:visited',
                'h1 a:active',
				'h1 a:focus',
                'body.single-event .header-post-thumbnail .entry-title',
			),
		),
	),
    'color_title_h2' => array(
		'name'  => esc_html__( 'H2', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'h2',
                'h2 a',
                'h2 a:hover',
                'h2 a:visited',
                'h2 a:active',
				'h2 a:focus',
			),
		),
	),
    'color_title_h3' => array(
		'name'  => esc_html__( 'H3', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'h3',
                'h3 a',
                'h3 a:hover',
                'h3 a:focus',
                'h3 a:visited',
                'h3 a:active',
                '.search_res_text_side h3 a',
                '.address_item_title',
                '.why_choose_us_item_title',
			),
		),
	),
    'color_title_h4' => array(
		'name'  => esc_html__( 'H4', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'h4',
                'h4 a',
                'h4 a:hover',
                'h4 a:focus',
                'h4 a:visited',
                'h4 a:active',
                '.comment-author-name',
                '.nav-prevnext-title',
			),
		),
	),
    'color_title_h5' => array(
		'name'  => esc_html__( 'H5', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'h5',
                'h5 a',
                'h5 a:hover',
                'h5 a:focus',
                'h5 a:visited',
                'h5 a:active',
			),
		),
	),
    'color_title_h6' => array(
		'name'  => esc_html__( 'H6', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'h6',
                'h6 a',
                'h6 a:hover',
                'h6 a:focus',
                'h6 a:visited',
                'h6 a:active',
			),
		),
	),
    'color_menu' => array(
		'name'  => esc_html__( 'Menu items', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.menu-item',
                '.menu-item a',
                '.menu-item a:visited',
                '.site-header a',
                '.site-header a:visited',
			),
		),
	),
    'color_menu_current' => array(
		'name'  => esc_html__( 'Menu current item', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.current-menu-item',
                '.current-menu-item > a',
                '.current-menu-item > a:visited',
                '.current-menu-parent',
                '.current-menu-parent > a',
                '.current-menu-parent > a:visited',
                '.dropdown-menu > .menu-item.current-menu-item > a',
                '.dropdown-menu > .menu-item:hover > a',
                '.dropdown-menu > .menu-item:focus > a',
                '.menu-item a:active',
                '.menu-item a:focus',
                '.nav-item:hover a',
                '.nav-item:focus a',
                '.site-header a:active',
                '.site-header a:focus',
                '.site-header a:hover',
			),
            'background-color' => array(
				'#nav_menu .menu-item .menu-top-border',
			),
            'border-color' => array(
                '.menu-underline',
			),
		),
	),
    'color_current' => array(
		'name'  => esc_html__( 'Current item', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.current_item',
                '.current_item a',
			),
		),
	),
    'color_icons' => array(
		'name'  => esc_html__( 'Icons', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.search_res_tags_line .term_line_icon',
				'.feature_item_icon i',
                '.babe-search-filter-terms .bahotel_l_preview_term_icon i',
			),
		),
	),
	'color_buttons' => array(
		'name'  => esc_html__( 'Buttons', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
                '.btn.btn-search-guests-change',
            ),
			'background-color' => array(
				'#infinite-handle span button',
				'.added_to_cart',
                '.gallery-item-hover-cross',
				'button',
				'.button',
				'.btn',
				'.btn.btn-red:hover',
                '.btn.btn-red:focus',
				'input[type=button]',
				'input[type=reset]',
				'input[type=submit]',
				'.block_top_tours .tour_info_price_discount',
                '.block_search_res .tour_info_price_discount',
                '.single-to_book .tour_info_price_discount',
                '.babe_pager .current',
                '.babe_price_slider .ui-slider-range.ui-corner-all',
                '.btn.btn-search:hover',
                '.btn.btn-search:focus',
                '.block_special_tours .tour_info_price',
                '.search_form_white_bg #search_form .input-group > div.submit button',
                '#checkout_form .tab_title.tab_active',
                '.offer_item_selected .offer_item_button_wrapper .button',
			),
            'border-color' => array(
				'.gallery-item-hover-desc',
                '.contact_form_bar_content .wpcf7 input[type="submit"]',
                '.btn.btn-search-guests-change',
			),
		),
	),
	'color_buttons_alt' => array(
		'name'  => esc_html__( 'Alternative buttons', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'background-color' => array(
				'.btn-red',
				'.button:hover',
                '.button.button-grey:hover',
                'button:hover',
                'button.button-grey:hover',
				'.btn:hover',
                '.btn.button-grey:hover',
				'.btn-primary:hover',
                'input[type=submit]:hover',
                '.button:focus',
                '.button.button-grey:focus',
                'button:focus',
                'button.button-grey:focus',
                '.btn:focus',
                '.btn.button-grey:focus',
                '.btn-primary:focus',
                'input[type=submit]:focus',
                '.pagination .page-numbers:hover',
                '.babe_pager .page-numbers:hover',
                '.pagination .page-numbers:focus',
                '.babe_pager .page-numbers:focus',
                '.page-links a:hover span',
                '.page-links a:focus span',
                '.search_form_white_bg #search_form .input-group > div.submit button:hover',
                '.search_form_white_bg #search_form .input-group > div.submit button:focus',
                '#checkout_form .tab_title:hover',
                '#checkout_form .tab_title:focus',
                '.front_top_block.services_block .button:hover',
                '.offer_item_selected .offer_item_button_wrapper .button:hover',
                '.front_top_block.services_block .button:focus',
                '.offer_item_selected .offer_item_button_wrapper .button:focus',
			),
            'border-color' => array(
                '.front_top_block.services_block .button:hover',
                '.offer_item_button_wrapper .button:hover',
                '.offer_item_selected .offer_item_button_wrapper .button:hover',
                '.front_top_block.services_block .button:focus',
                '.offer_item_button_wrapper .button:focus',
                '.offer_item_selected .offer_item_button_wrapper .button:focus',
            ),
		),
	),
    'color_buttons_grey' => array(
		'name'  => esc_html__( 'Grey buttons', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'background-color' => array(
				'.button-grey',
                'button.button-grey',
                '.button.button-grey',
                '.btn.button-grey',
			),
		),
	),
	'color_buttons_text' => array(
		'name'  => esc_html__( 'Buttons text', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'#infinite-handle span button',
				'.added_to_cart',
				'.button',
				'button',
				'.btn',
				'a.btn',
				'a.btn:visited',
				'a.btn:hover',
                'a.btn:focus',
				'input[type=button]',
				'input[type=reset]',
				'input[type=submit]',
				'.block_step_title:not(.block_active)',
				'.block_step_title:not(.block_active) h4',
                '.pagination .page-numbers:hover',
                '.babe_pager .page-numbers:hover',
                '.page-links a:hover span',
                '.pagination .page-numbers:focus',
                '.babe_pager .page-numbers:focus',
                '.page-links a:focus span',
//                '.add_input_field .add_ids_list .term_item:hover',
//                '.add_input_field .add_ids_list .term_item.term_item_selected',
//                '.input_select_field .input_select_list .term_item:hover',
//                '.input_select_field .input_select_list .term_item.term_item_selected',
                '.block_special_tours .tour_info_price',
                '.front_top_block.services_block a.btn.button:hover',
                '.front_top_block.services_block a.btn.button:focus',
                '.offer_item_selected .offer_item_button_wrapper a.btn.button',
                '.offer_item_button_wrapper a.btn.button:hover',
                '#content .btn.btn_white:hover',
                '#content a.btn.btn_white:hover',
                '.offer_item_button_wrapper a.btn.button:focus',
                '#content .btn.btn_white:focus',
                '#content a.btn.btn_white:focus',
			),
		),
	),
	'color_important' => array(
		'name'  => esc_html__( 'Important text', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.highlight-text',
				'.highlight-text a',
				'.block_top_tours .tour_info_price',
				'.block_search_res .tour_info_price',
                '.single-to_book .tour_info_price',
			),
		),
	),
    'color_header' => array(
		'name'  => esc_html__( 'Header background', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'background-color' => array(
				'.site-header',
                '.block_step_title',
                '#nav_menu .dropdown-menu > .menu-item:hover > a .menu-top-border',
                '#nav_menu .dropdown-menu > .menu-item:focus > a .menu-top-border',
                '.search_res_text',
                '.block_team .team-title-group',
                '.history_event_item_text_wrapper',
                '.offer_item_header_wrapper',
			),
            'color' => array(
                '.dropdown-menu > .menu-item',
                '.dropdown-menu > .menu-item a',
            ),
		),
	),
    'color_header_text' => array(
		'name'  => esc_html__( 'Header text', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.header-background',
                '.header-background h1',
                '.header-background h2',
                '.header-background h3',
                '.header-background span',
                '.header-background div',
                '.header-background ul',
                '.header-background p',
                '.header-background a',
                '.header-background a:focus',
                '.header-background a:active',
                '.header-background a:visited',
                '.search_res_text',
                '.search_res_text a',
                '.search_res_text a:visited',
                '.search_res_text .search_res_title',
                '.search_res_text label',
                '.block_team .team-title-group .entry-title',
                '.history_event_item_text_wrapper',
                '.history_event_block .history_event_item_title',
                '.offer_item_header_wrapper',
			),
		),
	),
    'color_bg' => array(
		'name'  => esc_html__( 'Main background', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'background-color' => array(
				'.main-background',
                'body .ui-datepicker .ui-datepicker-header',
//                '.babe_search_results_filters .input_select_field .input_select_list .term_item.term_item_selected',
//                '.babe_search_results_filters .input_select_field .input_select_list .term_item:hover',
                '#my_account_page_wrapper .my_account_page_nav_wrapper .my_account_nav_item_current',
                '#my_account_page_wrapper .my_account_page_nav_wrapper .my_account_nav_item a:hover span',
                '#my_account_page_wrapper .my_account_page_nav_wrapper .my_account_nav_item a:focus span',
                '.contact_form_bar_content_wrapper',
			),
		),
	),
    'color_bg_text' => array(
		'name'  => esc_html__( 'Text color with Main background', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.main-background',
                '.main-background p:not(.text-yellow)',
                '.main-background h1:not(.text-yellow)',
                '.main-background h2:not(.text-yellow)',
                '.main-background h3:not(.text-yellow)',
                '.main-background h4:not(.text-yellow)',
                '.main-background h5:not(.text-yellow)',
                '.main-background h6:not(.text-yellow)',
                '.block_step_title',
                '.block_step_title h4',
                '#checkout_form .tab_title.tab_active',
                '#checkout_form .tab_title:hover',
                '#checkout_form .tab_title:focus',
                '#my_account_page_wrapper .my_account_page_nav_wrapper .my_account_nav_item_current span',
                '#my_account_page_wrapper .my_account_page_nav_wrapper .my_account_nav_item a:hover span',
                '#my_account_page_wrapper .my_account_page_nav_wrapper .my_account_nav_item a:focus span',
			),
            'border-color' => array(
				'.search_form_color_bg #search_form button.main-background:hover',
                '.search_form_color_bg #search_form button.main-background:focus',
			),
		),
	),
    'color_footer' => array(
		'name'  => esc_html__( 'Footer background', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'background-color' => array(
				'.site-footer',
			),
		),
	),
    'color_footer_title' => array(
		'name'  => esc_html__( 'Footer titles', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
                '.site-footer .widget-title',
			),
		),
	),
	'color_footer_text' => array(
		'name'  => esc_html__( 'Footer text', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
				'.site-footer',
				'.site-footer p',
				'.site-footer ul',
                '.site-footer a',
                '.site-footer a:hover',
                '.site-footer a:focus',
                '.site-footer a:visited',
                '.block_team .team-title-group',
                '.block_team .team-title-group a',
                '.block_team .team-title-group a:visited',
			),
		),
	),
    'color_search_form_bg' => array(
		'name'  => esc_html__( 'Search form: Background', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'background-color' => array(
                '#search-box.search_form_color_bg.search_form_over_header #search_form',
                '#search-box.search_form_under_header.search_form_color_bg'
			),
		),
	),
    'color_search_form_button' => array(
		'name'  => esc_html__( 'Search form: Button', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'background-color' => array(
                '#search_form .input-group > div.submit button',
			),
		),
	),
    'color_search_form_button_alt' => array(
		'name'  => esc_html__( 'Search form: Button hover', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'background-color' => array(
                '#search_form .input-group > div.submit button:hover',
                '#search_form .input-group > div.submit button:focus',
//                '.add_input_field .add_ids_list .term_item.term_item_selected',
//               '.add_input_field .add_ids_list .term_item:hover',
//                '.input_select_field .input_select_list .term_item:hover',
//                '.input_select_field .input_select_list .term_item.term_item_selected',
			),
		),
	),
    'color_search_form_button_text' => array(
		'name'  => esc_html__( 'Search form: Button text', 'ba-hotel-light' ),
		'desc'  => '',
		'selectors' => array(
			'color' => array(
                '#search_form .input-group > div.submit button',
			),
		),
	),
    
    );
        
        self::$color_selectors = apply_filters('bahotel_l_init_custom_colors', $color_selectors);
        
        return;
    }
    
    //////////////////////////////
	
    /**
	 * Inline styles.
     * 
     * @return string
	 */
    public static function inline_styles() {
        
        $output = '';
        
        $saved_custom_css = self::get_option('', 'custom_css');
        
        if ($saved_custom_css && !BAHOTEL_L_DEV){
            return $saved_custom_css;
        }
        
        /// add color styles
        foreach (self::$color_selectors as $option_id => $style_arr){
            
            if (isset($style_arr['selectors']) && is_array($style_arr['selectors']) && isset(self::$settings[$option_id])){
                foreach ( $style_arr['selectors'] as $attr => $selectors_list ) {
						
					if ( empty( $selectors_list ) ) {
						continue;
					}
                    
                    $selectors = implode( ",\n", $selectors_list );
						
					$output .= $selectors . " { \n\t" . $attr . ": " . self::$settings[$option_id] . ";\n}\n";
				}
            }
            
        }
        
        /// add font styles
        foreach (self::$custom_fonts as $option_id => $style_arr){
            
            if (isset($style_arr['selectors']) && is_array($style_arr['selectors']) && isset(self::$settings[$option_id]) && is_array(self::$settings[$option_id])){
                
                $selectors = implode( ",\n", $style_arr['selectors'] );
                
                $attrs = [];
					
				foreach ( self::$settings[$option_id] as $attr => $value ) {
						
				    if ( $attr != 'google' && $attr != 'subsets' && $value) {
				        
                        if ($attr != 'font-size'){
						   $attrs[] = $attr . ": " . ($attr == 'font-family' ? "'".$value."',Helvetica,Arial,sans-serif" : $value);
                        } else { 
                           // $attr_suff = false === strpos($value, 'px') ? 'px' : '';
                           $attrs[] = $attr . ": " . $value;
                        }
					}
				}
					
				$output .= $selectors . " { \n\t" . implode( ";\n\t", $attrs ) . ";\n}\n";
                
            }
            
        }
        
        $bg_image_url = '';
        
        $bg_image_url = isset(self::$settings['header_bg_image']['url']) && self::$settings['header_bg_image']['url'] ? self::$settings['header_bg_image']['url'] : $bg_image_url;
        
        $bg_image_url = self::$settings['header_bg_default'] ? BAHOTEL_L_URI.'/css/images/bg_pattern.png' : $bg_image_url;
        
        if ($bg_image_url){
            
           $output .= ".site-header, .site-footer{\n\tbackground-image: url('".$bg_image_url."');\n}\n";
        }
        
        if (self::$settings['custom_css']){
            
            $output .= "\n".self::$settings['custom_css']."\n";
            
        }
        
        //// cache custom styles
        if (!BAHOTEL_L_DEV){
            self::set_option( 'custom_css', $output );
        }
        
        return $output;
        
    }
    
    //////////////////////////////
	
    /**
	 * Get google font styles
     * 
     * @return array
	 */
    public static function google_font_styles() {
        
        $output = '';
        
        $url_base = 'https://fonts.googleapis.com/css';
        
        $url_arr = [];
        
        $fonts = [];
        
        $subsets = [];
		
		foreach (self::$custom_fonts as $option_id => $style_arr){
            // Process only google fonts.
			if ( isset(self::$settings[$option_id]['google']) && self::$settings[$option_id]['google'] && isset(self::$settings[$option_id]['font-family']) ) {
				
                $family = self::$settings[$option_id]['font-family'];
                
                $style = isset(self::$settings[$option_id]['font-weight']) && self::$settings[$option_id]['font-weight'] ? self::$settings[$option_id]['font-weight'] : '';
                
                $style .= isset(self::$settings[$option_id]['font-style']) && self::$settings[$option_id]['font-style'] ? self::$settings[$option_id]['font-style'] : '';
                
                $subset = isset(self::$settings[$option_id]['subsets']) && self::$settings[$option_id]['subsets'] ? self::$settings[$option_id]['subsets'] : '';
                
                if ($style){
                    $fonts[$family][$style] = $style;
                }
                
                if ($subset){
                    $subsets[$subset] = $subset;
                }
				
			}
            
		}
		
		if (!empty($fonts)){
		  
            $fontnames_arr = [];
		  
            foreach($fonts as $family => $font_args){
                
                $fontnames_arr[$family] = trim(str_replace( ' ', '+', $family ));
                
                if (!empty($font_args)){
                    $fontnames_arr[$family] .= ':' . urlencode(implode(',',$font_args));
                }
                
            }
            
            $url_arr['family'] = implode('|', $fontnames_arr);
            
            if (!empty($subsets)){
                
                $url_arr['subset'] = urlencode(implode(',', $subsets));
                
            }
            
            $output = add_query_arg( $url_arr, $url_base ); 
          
		}
        
        return $output;
        
    }

	////////////////////////////////////////////////////////////
	//// End of our class.
	////////////////////////////////////////////////////////////
}

/**
 * Calling to setup class.
 */
Bahotel_L_Settings::init();
