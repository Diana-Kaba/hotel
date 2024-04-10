<?php
/**
 * Theme's functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 */

////////////////////////////////////////////////////////////
//// Theme setup section.
////////////////////////////////////////////////////////////

/**
 * System defines.
 */
define( 'BAHOTEL_L', __FILE__ );
define( 'BAHOTEL_L_VERSION', '1.0.18' );
define( 'BAHOTEL_L_NAME', 'BA Hotel light' );
define( 'BAHOTEL_L_URI', get_template_directory_uri() );
define( 'BAHOTEL_L_STYLESHEET_URI', get_stylesheet_directory_uri() );
define( 'BAHOTEL_L_DIR', untrailingslashit( dirname( BAHOTEL_L ) ) );
define( 'BAHOTEL_L_TEXTDOMAIN', 'ba-hotel-light' );
define( 'BAHOTEL_L_AUTHOR', 'Booking Algorithms' );
define( 'BAHOTEL_L_AUTHOR_URL', 'https://ba-booking.com/' );
define( 'BAHOTEL_L_DEV', true );

//////////////////////////////////////

add_action( 'after_setup_theme', 'bahotel_l_setup', 10 );

function bahotel_l_setup(){
    
    /* Add thumbnails support */
	add_theme_support( 'post-thumbnails' );

	/* Add theme support for title tag */
	add_theme_support( 'title-tag' );
    
    /* Add post formats support */
	add_theme_support( 'post-formats', [ 'audio', 'gallery', 'video' ] );
    
    // Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/* Support for HTML5 */
	add_theme_support( 'html5', [ 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ] );

    load_theme_textdomain( 'ba-hotel-light' );

	/* Automatic Feed Links */
	add_theme_support( 'automatic-feed-links' );
    
    add_theme_support( 'yoast-seo-breadcrumbs' );

    add_theme_support( 'ba-theme-core', [ 'current' => 'bahotel_l', 'version' => BAHOTEL_L_VERSION ] );
    
    add_theme_support(
		'custom-logo', apply_filters(
			'bahotel_l_custom_logo_args', [
				'height'      => 80,
                'width'       => 200,
                'flex-height' => true,
                'flex-width'  => true,
                'header-text' => [ 'site-title', 'site-description' ],
			]
		)
	);
    
    /* Add image sizes */
	$image_sizes = Bahotel_L_Settings::$image_sizes;
	if ( !empty( $image_sizes ) ) {
		foreach ( $image_sizes as $id => $size ) {
			add_image_size( $id, $size['width'], $size['height'], $size['crop'] );
		}
	}
    
    // Register navigation menus.
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'ba-hotel-light' )
		)
	);
    
    return;
}

//////////////////////////////////////

add_action( 'wp_enqueue_scripts', 'bahotel_l_enqueue_scripts', 30, 1 );
/**
 * Loads required styles and scripts.
 *
 */
function bahotel_l_enqueue_scripts(){
    
        // Output Google fonts if set.
        $google_fonts = Bahotel_L_Settings::google_font_styles();
        if ( $google_fonts ) {
            wp_enqueue_style( 'bahotel-l-gfonts', esc_url( $google_fonts ), false );
        }
    
        wp_enqueue_style( 'dashicons' );
        
        wp_enqueue_style( 'bahotel-l-linearicons' , BAHOTEL_L_URI . '/fonts/linearicons-free/web_font/style.css', false, BAHOTEL_L_VERSION );
        
        wp_enqueue_style( 'bahotel-l-elegantfont' , BAHOTEL_L_URI . '/fonts/elegant-font/css/style.min.css', false, BAHOTEL_L_VERSION );
        
        if (BAHOTEL_L_DEV){
           //included into theme style.min.css
           $styles = [
			'normalize' => 'normalize.css', 
            'bootstrap' => 'bootstrap.min.css',
		   ];

		   foreach ( $styles as $id => $style ) {
			 wp_enqueue_style( 'bahotel-l-' . $id, BAHOTEL_L_URI . '/css/' . $style, false, BAHOTEL_L_VERSION );
		   }
        
           wp_enqueue_style( 'bahotel-l-main' , BAHOTEL_L_URI . '/style.css', false, BAHOTEL_L_VERSION );
        
        } else {
        
           //// main styles file
           wp_enqueue_style( 'bahotel-l-main' , BAHOTEL_L_URI . '/style.min.css', false, BAHOTEL_L_VERSION );
        }
        
        //// custom styles
        wp_add_inline_style( 'bahotel-l-main', Bahotel_L_Settings::inline_styles() );
    
        wp_enqueue_style( 'bahotel-l-slick' , BAHOTEL_L_URI . '/js/slick/slick.css', false, BAHOTEL_L_VERSION );
        
        wp_enqueue_style( 'bahotel-l-slick-theme' , BAHOTEL_L_URI . '/js/slick/slick-theme.css', false, BAHOTEL_L_VERSION );
        
        //Load comment reply js
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
    
        $scripts = array(
			'html5' => 'html5.js',
			'skip-link-focus-fix' => 'skip-link-focus-fix.js',
            'popper' => 'popper.min.js',
			'bootstrap' => 'bootstrap.min.js',
            'slick' => 'slick/slick.min.js',
			'theme' => 'theme'.(BAHOTEL_L_DEV ? '' : '.min').'.js'
		);

		foreach ( $scripts as $id => $script ) {
			wp_enqueue_script( 'bahotel-l-'.$id, BAHOTEL_L_URI .'/js/'. $script, array( 'jquery' ), BAHOTEL_L_VERSION, true );
		}
        
        wp_script_add_data( 'bahotel-l-html5', 'conditional', 'lt IE 9' );        
    
}

///////////////////////////////

add_filter( 'style_loader_src',  'bahotel_l_remove_ver_css', 9999, 2 );
/**
 * Clear version arg in google font css url
 *
 */
function bahotel_l_remove_ver_css($src, $handle) {
    
    if ($handle == 'bahotel-l-gfonts' && strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    
    return $src;
}

///////////////////////////////

//add_filter('script_loader_tag', 'bahotel_l_async_defer_scripts', 10, 3);
/**
 * Loads scripts as async or defer to improve site perfomance.
 *
 */
function bahotel_l_async_defer_scripts($tag, $handle, $src) {
   
   $scripts = array(
			'bahotel-l-html5' => 1,
			'bahotel-l-skip-link-focus-fix' => 1,
            'bahotel-l-photoswipe' => 1,
            'bahotel-l-photoswipe-ui' => 1,
   );
   
   if (isset($scripts[$handle])) {
       return str_replace(' src', ' defer="defer" src', $tag);
   }
     
   return $tag;
}

//////////////////////////////////

add_action( 'widgets_init', 'bahotel_l_widgets_init', 10 );

function bahotel_l_widgets_init(){
      
     foreach (Bahotel_L_Settings::$sidebars as $id => $sidebar){
        
        $h_tag = $id == 'left' || $id == 'right' ? 'h3' : 'h2';
        
        register_sidebar(
			array(
				'id' => $id,
				'name' => esc_html( $sidebar['name'] ),
				'description' => (isset($sidebar['desc']) ? esc_html($sidebar['desc']) : ''),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget' => '</section>',
				'before_title' => '<'.$h_tag.' class="widget-title">',
				'after_title' => '</'.$h_tag.'><div class="sidebar_sub_title_bottom"><div class="sidebar_sub_title_bottom_line"></div></div>'
			)
		);
     }   
        
     return;
}

////////////////////

if ( ! isset( $content_width ) ) $content_width = 900;

//////////////////////////////////////////////////

add_filter( 'wp_get_attachment_image_attributes', 'bahotel_l_post_thumbnail_sizes_attr', 10, 3 );

/**
 * Add custom sizes attribute to responsive image functionality for post thumbnails.
 *
 * @param array        $attr       Attributes for the image markup.
 * @param WP_Post      $attachment Image attachment post.
 * @param string|array $size       Requested size. Image size or array of width and height values
 *                                 (in that order). Default 'thumbnail'.
 * 
 * @return string Value for use in post thumbnail 'sizes' attribute.
 */
function bahotel_l_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {

	if ( is_admin() ) {
		return $attr;
	}
    
    if ($size == 'bahotel_thumbnail_sm'){
        
        $attr['sizes'] = '(max-width: 420px) 420px, (max-width: 870px) 870px, 100vw';
        
    } elseif ( ! is_singular() ) {
		$attr['sizes'] = '100vw';
	}

	return $attr;
}

//////////////////////////////////////////////////

include_once BAHOTEL_L_DIR . '/includes/class-settings.php';

include_once BAHOTEL_L_DIR . '/includes/class-page-options.php';

include_once BAHOTEL_L_DIR . '/includes/class-nav-menu.php';

/**
 * Recommended plugins.
 */
include_once BAHOTEL_L_DIR . '/includes/functions-plugins.php';

/**
 * Theme administration.
 */
if ( is_admin() ) {
	
	include_once BAHOTEL_L_DIR . '/includes/class-redux.php';
	include_once BAHOTEL_L_DIR . '/includes/class-cmb2-admin.php';
    
    include_once BAHOTEL_L_DIR . '/includes/customizer.php';
}

////////////////////////////////////////////////////////////
//// Functions section.
////////////////////////////////////////////////////////////

include_once BAHOTEL_L_DIR . '/includes/template-functions.php';     
