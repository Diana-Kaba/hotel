<?php
/**
 * Theme administration.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'ReduxFramework' ) && file_exists( BAHOTEL_L_DIR . '/admin/ReduxCore/framework.php' ) ) {
    require_once BAHOTEL_L_DIR . '/admin/ReduxCore/framework.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
}

// Redux Framefork is required.
if ( ! class_exists( 'Redux' ) ) {
	return;
}

//////////////////////////////////////////////////
/**
 * Theme administration.
 *
 */
class Bahotel_L_Redux {

	//////////////////////////////////////////////////
	/**
	 * Setup function.
	 * 
	 * @return null
	 */
	static function init() {
		
		// Setup settings.
		add_action( 'after_setup_theme', array( __CLASS__, 'set_redux' ), 30, 1 );
		add_action( 'after_setup_theme', array( __CLASS__, 'set_help' ), 33, 1 );
		add_action( 'after_setup_theme', array( __CLASS__, 'set_settings' ), 36, 1 );
        
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_assets' ), 10, 1 );
        
        add_filter( 'redux/options/' . Bahotel_L_Settings::$option_name . '/sections', array( __CLASS__, 'sections_altering' ) );
        
		// Save WP site options.
		add_filter( 'redux/options/' . Bahotel_L_Settings::$option_name . '/ajax_save/response', array( __CLASS__, 'after_ajax_save_redux' ), 10, 1 );

		// Cleaning, as it's embeded in theme
		add_action( 'redux/extensions/before', array( __CLASS__, 'remove_dev_mode' ), 100, 1 );
        add_action( 'admin_menu', array( __CLASS__, 'remove_redux_page'), 99 );
        add_action( 'init', array( __CLASS__, 'remove_demo_mode' ), 10 );
	}
    
    //////////////////////////////////////////////////
	/**
	 * Loads required styles and scripts.
	 */
    public static function load_assets() {
        
        global $pagenow;
        
        if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && $_GET['page'] == 'bah_options' ) {
			
			wp_enqueue_style( 'bahotel-l-redux', BAHOTEL_L_URI . '/admin/css/admin.css' , false, BAHOTEL_L_VERSION );
            
		}
	}
    
    /////////////////////////////////////////////////
	
    /**
	 * Get update notification.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	static function get_update_notification() {
		$current = get_site_transient( 'update_themes' );
		
        $theme = wp_get_theme();
		$theme_name = $theme->get( 'Name' );
		$message_html = $theme->get( 'Version' );
        
		if ( isset( $current->response['ba-hotel-light'] ) ) {
			$message_html = '<span class="update-message">'.esc_html__('New update available!', 'ba-hotel-light').'</span>
				<span class="update-actions">'.esc_html__('Version ', 'ba-hotel-light').$current->response['ba-hotel-light']['new_version'].': <a href="'.esc_url(admin_url( 'update-core.php' )).'">'.esc_html__('Update', 'ba-hotel-light').'</a></span>';
		}

		return $message_html;
	}
	
	//////////////////////////////////////////////////
	/**
	 * Redux validation.
	 *
	 * @param array $data Redux array.
	 *
	 * @return
	 */
	static function validate_redux( $data ) {
		
		error_log( '$data validation array: ' . print_r( $data, 1 ) );
	}
	
	//////////////////////////////////////////////////
	/**
	 * Fills site settings with Redux options array
	 *
	 * @param array $return_array Redux array.
	 *
	 * @return array
	 */
	static function after_ajax_save_redux( $return_array ) {
		
		$attachment_id = isset( $return_array['options']['logo']['id'] ) ? $return_array['options']['logo']['id'] : '';
        
        set_theme_mod( 'custom_logo', $attachment_id );
		
		
		if ( isset( $return_array['options']['blogname'] ) ) {
			
			$blogname = get_option( 'blogname' );
			
			if ( $blogname != $return_array['options']['blogname'] ) {
				
			    update_option( 'blogname', $return_array['options']['blogname'] );
			}
		}
		
		
		if ( isset( $return_array['options']['blogdescription'] ) ) {
			
			$blogdescription = get_option( 'blogdescription' );
			
			if ( $blogdescription != $return_array['options']['blogdescription'] ) {
				
			    update_option( 'blogdescription', $return_array['options']['blogdescription'] );
			}
		}
		
		return $return_array;
	}
    
	
	//////////////////////////////////////////////////
	/**
	 * Sets all the Redux arguments.
	 *
	 * @return null
	 */
	static function set_redux() {
		
		/**
		 * All the possible arguments for Redux.
		 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
		 */
         
		$promo = '<a href="' . BAHOTEL_L_AUTHOR_URL . '" target="_blank"><img src="' . apply_filters( 'bahotel_l_admin_image_url', '', 'ba-logo.png' ) . '" title="' . BAHOTEL_L_AUTHOR . '" alt="' . BAHOTEL_L_AUTHOR . '" /></a>';

		$args = array(
			// TYPICAL -> Change these values as you need/desire
			'opt_name'             => Bahotel_L_Settings::$option_name,
			// This is where your data is stored in the database and also becomes your global variable name.
			'display_name'         => sprintf(
                /* translators: %1$s: opening tag <a>, %2$s: closing tag <a> */
                __( 'BA Hotel light options %1$sTheme Documentation%2$s', 'ba-hotel-light' ),
                '<a href="https://ba-booking.com/ba-hotel/documentation/introduction/" target="_blank">',
                '</a>'
            ),
			// Name that appears at the top of your panel
			'display_version'      => self::get_update_notification(),
			// Version that appears at the top of your panel
			'menu_type'            => 'menu',
			//Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
			'allow_sub_menu'       => true,
			// Show the sections below the admin menu item or not
			'menu_title'           => esc_html__( 'Theme Options', 'ba-hotel-light' ),
			'page_title'           => esc_html__( 'BA Hotel light options', 'ba-hotel-light' ),
			// You will need to generate a Google API key to use this feature.
			// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
			'google_api_key'       => '',
			// Set it you want google fonts to update weekly. A google_api_key value is required.
			'google_update_weekly' => false,
			// Must be defined to add google fonts to the typography module
			'async_typography'     => true,
			// Use a asynchronous font on the front end or font string
			//'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
			'admin_bar'            => true,
			// Show the panel pages on the admin bar
			'admin_bar_icon'       => 'dashicons-admin-generic',
			// Choose an icon for the admin bar menu
			'admin_bar_priority'   => 50,
			// Choose an priority for the admin bar menu
			'global_variable'      => '',
			// Set a different name for your global variable other than the opt_name
            'disable_tracking' => true,
			'dev_mode'             => false,
			// Show the time the page took to load, etc
			'update_notice'        => false,
			// If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
			'customizer'           => false,
			// Enable basic customizer support
			//'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
			//'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

			// OPTIONAL -> Give you extra features
			'page_priority'        => 56,
			// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
			'page_parent'          => 'themes.php',
			// For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
			'page_permissions'     => 'manage_options',
			// Permissions needed to access the options panel.
			'menu_icon'            => '',
			// Specify a custom URL to an icon
			'last_tab'             => '',
			// Force your panel to always open to a specific tab (by id)
			'page_icon'            => 'icon-themes',
			// Icon displayed in the admin panel next to your menu_title
			'page_slug'            => 'bah_options',
			// Page slug used to denote the panel
			'save_defaults'        => true,
			// On load save the defaults to DB before user clicks save or not
			'default_show'         => false,
			// If true, shows the default value next to each field that is not the default value.
			'default_mark'         => '',
			// What to print by the field's title if the value shown is default. Suggested: *
			'show_import_export'   => true,
			// Shows the Import/Export panel when not used as a field.

			// CAREFUL -> These options are for advanced use only
			'transient_time'       => 60 * MINUTE_IN_SECONDS,
			'output'               => true,
			// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
			'output_tag'           => true,
			// Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
			// 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

			// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
			'database'             => '',
			// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!

			'use_cdn'              => true,
			// If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

			//'compiler'             => true,

			// HINTS
			'hints'                => array(
				'icon'          => 'el el-question-sign',
				'icon_position' => 'right',
				'icon_color'    => 'lightgray',
				'icon_size'     => 'normal',
				'tip_style'     => array(
					'color'   => 'light',
					'shadow'  => true,
					'rounded' => false,
					'style'   => '',
				),
				'tip_position'  => array(
					'my' => 'top left',
					'at' => 'bottom right',
				),
				'tip_effect'    => array(
					'show' => array(
						'effect'   => 'slide',
						'duration' => '500',
						'event'    => 'mouseover',
					),
					'hide' => array(
						'effect'   => 'slide',
						'duration' => '500',
						'event'    => 'click mouseleave',
					),
				),
			)
		);
		
		// ADMIN BAR LINKS -> Setup custom links in the admin bar menu as external items.
		$args['admin_bar_links'][] = array(
			'id'    => 'bahotel-l-docs',
			'href'  => esc_url('https://ba-booking.com/ba-hotel/documentation/introduction/'),
			'title' => esc_html__( 'Documentation', 'ba-hotel-light' ),
		);
		
		Redux::setArgs( Bahotel_L_Settings::$option_name, $args );
	}
	
	//////////////////////////////////////////////////
	/**
	 * Sets help panels.
	 *
	 * @return
	 */
	static function set_help() {
		
		$tabs = array();
		Redux::setHelpTab( Bahotel_L_Settings::$option_name, $tabs );
		
		$content = '';
		Redux::setHelpSidebar( Bahotel_L_Settings::$option_name, $content );
	}
	
	//////////////////////////////////////////////////
	/**
	 * Sets theme settings set.
	 *
	 * @return
	 */
	static function set_settings() {
		
		$sections = array();
		
		//////////////////////////////////////////////////
		/**
		 * Site identity.
		 */
		$flag_update_options = false;
		
		$options = Bahotel_L_Settings::$settings;
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$logo_wp = wp_get_attachment_image_src( $custom_logo_id , 'full' );
		$logo_wp_thumbnail = wp_get_attachment_image_src( $custom_logo_id , 'thumbnail' );   
		
		if ( isset( $options['logo']['id'] ) && $options['logo']['id'] != $custom_logo_id ) {
			$options['logo']['url'] = isset( $logo_wp[0] ) ? $logo_wp[0] : '';
			$options['logo']['width'] = isset( $logo_wp[1] ) ? $logo_wp[1] : '';
			$options['logo']['height'] = isset( $logo_wp[2] ) ? $logo_wp[2] : '';
			$options['logo']['thumbnail'] = isset( $logo_wp_thumbnail[0] ) ? $logo_wp_thumbnail[0] : '';
			$options['logo']['title'] = '';
			$options['logo']['caption'] = '';
			$options['logo']['alt'] = '';
			$options['logo']['description'] = '';
			$options['logo']['id'] = $custom_logo_id;
			$flag_update_options = true; 
		}
		
		// Logo field.
		$field_logo = array(
			'id'         => 'logo',
			'type'       => 'media',
			'full_width' => false,
			'title'      => esc_html__( 'Logo image', 'ba-hotel-light' ),
		);
		
		if ( isset( $logo_wp[0] ) ) {
			
			$field_logo['default'] = array( 'url' => $logo_wp[0] );
		}
		
		// Blog name.
		$blogname = get_option( 'blogname' );
		
		$field_blogname = array(
			'id'         => 'blogname',
			'type'       => 'text',
			'full_width' => false,
			'title'      => esc_html__( 'Site Title', 'ba-hotel-light' ),
			'default'    => $blogname,
		);
		
		if ( ! isset( $options['blogname'] ) || ( isset( $options['blogname'] ) && $options['blogname'] != $blogname ) ) {
			
			$options['blogname'] = $blogname;
			$flag_update_options = true;
		}
		
		/// Blog description.
		$blogdescription = get_option( 'blogdescription' );
		
		$field_blogdescription = array(
			'id'         => 'blogdescription',
			'type'       => 'text',
			'full_width' => false,
			'title'      => esc_html__( 'Tagline', 'ba-hotel-light' ),
			'default'    => $blogdescription,
		);
		
		if ( ! isset( $options['blogdescription'] ) || ( isset( $options['blogdescription'] ) && $options['blogdescription'] != $blogdescription ) ) {
			
			$options['blogdescription'] = $blogdescription;
			$flag_update_options = true;
		}
		
		// Update theme options from WP data.
		if ( $flag_update_options ) {
			update_option( Bahotel_L_Settings::$option_name, $options );
		}
		
		$sections[] = array(
			'title'  => esc_html__( 'Site Identity', 'ba-hotel-light' ),
			'id'     => 'site_identity',
			'desc'   => '',
			'icon'   => 'el el-star',
			'fields' => array(
				$field_logo,
				$field_blogname,
				$field_blogdescription,
                array(
                    'id'         => 'footer_logo',
                    'type'       => 'media',
                    'full_width' => false,
                    'title'      => esc_html__( 'Footer logo image', 'ba-hotel-light' ),
				),
			),
		);
        
        ////////////////presets//////////////
        
        $layout_options = array();
		
		foreach ( Bahotel_L_Settings::$layouts as $layout_id => $layout_title ) {
			
			$preview = isset(Bahotel_L_Settings::$layout_previews[$layout_id]) ? Bahotel_L_Settings::$layout_previews[$layout_id] : '';
			
			$layout_options[ $layout_id ] = array(
				'alt' => '',
				'title' => $layout_title,
				'img' => $preview,
			);
		}
		
		//////////////////////////////////////////////////
        /**
		 * Header.
		 */
		$sections[] = array(
			'title'  => esc_html__( 'General', 'ba-hotel-light' ),
			'id'     => 'header',
			'desc'   => '',
			'icon'   => 'el el-cogs',
			'fields' => array(
                array(
					'id'         => 'header_info',
					'type'       => 'section',
					'subtitle' => '',
					'title'      => esc_html__( 'Header settings', 'ba-hotel-light' ),
					'indent' => true,
				),
                array(
					'id'         => 'header_bg_default',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Use default header background pattern', 'ba-hotel-light' ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'header_bg_default' ),
				),
                array(
                    'id'         => 'header_bg_image',
                    'type'       => 'media',
                    'required' => array('header_bg_default','!=','1'),
                    'full_width' => false,
                    'title'      => esc_html__( 'Header background pattern image', 'ba-hotel-light' ),
				),
                array(
					'id'         => 'header_transparent',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Header with transparent background (over header image)', 'ba-hotel-light' ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'header_transparent' ),
				),
                array(
					'id'         => 'header_sticky',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Sticky header', 'ba-hotel-light' ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'header_sticky' ),
				),
                array(
					'id'         => 'gallery_info',
					'type'       => 'section',
					'subtitle' => '',
					'title'      => esc_html__( 'Gallery settings', 'ba-hotel-light' ),
					'indent' => true,
				),
                array(
					'id'         => 'gallery_style',
					'type'       => 'radio',
					'full_width' => false,
					'title'      => esc_html__( 'Gallery style', 'ba-hotel-light' ),
                    'description' => '',
                    'options' => array(
                         'masonry' => esc_html__( 'Masonry', 'ba-hotel-light' ),
                         'col_3' => esc_html__( '3 columns', 'ba-hotel-light' ),
                    ),
					'default'    => 'masonry',
				),
			),
		);
        
        //////////////////////////////////////////////////
		/**
		 * Layout.
		 */
		
        $sections[] = array(
			'title'  => esc_html__( 'Layouts', 'ba-hotel-light' ),
			'id'     => 'layouts',
			'desc'   => '',
			'icon'   => 'el el-website',
			'fields' => array(
				array(
					'id'          => 'layout',
					'type'        => 'image_select',
                    'icon'  => 'el-icon-info-sign',
                    'full_width'  => true,
					'title'       => esc_html__( 'Default page/post layout', 'ba-hotel-light' ),
					'description' => esc_html__( 'This layout will be applied if no specific layout has been assigned to the current page/post.', 'ba-hotel-light' ),
                    'options'     => $layout_options,
                    'default'     => apply_filters( 'bahotel_l_option', '', 'layout' ),
				),
			),
		);
		
		/////////////////////////////////////////////
		/**
		 * Front Page.
		 */
		
        $sections[] = array(
			'title'  => esc_html__( 'Front Page', 'ba-hotel-light' ),
			'id'     => 'front-page',
			'desc'   => '',
			'icon'   => 'el el-home',
			'fields' => array(
                array(
					'id'          => 'front_info',
					'type'        => 'info',
                    'style' => 'warning',
                    'icon'  => 'el-icon-info-sign',
					'title'       => sprintf(
                    /* translators: 1: open 'a' html tag, 2: close 'a' html tag, 3: open 'a' html tag, 4: close 'a' html tag. */
                    __( 'To use shortcodes like on %1$sDemo site%2$s, you need to download from theme\'s site and install our free %3$sBA Theme core plugin%4$s.', 'ba-hotel-light' ), '<a href="https://ba-booking.com/ba-hotel-demo/">', '</a>', '<a href="https://ba-booking.com/ba-hotel/wp-content/uploads/sites/9/2019/10/ba-theme-core.zip">', '</a>'),
					'description' => '',
				),
                array(
					'id'         => 'header_slideshow',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Use slideshow', 'ba-hotel-light' ),
                    'description' => esc_html__( 'If it\'s enabled, Slide posts will be used to get images and titles for slideshow. Otherwise, the page featured image will be displayed.', 'ba-hotel-light' ),
					'default'    => false,
				),
			),
		);
        
        //////////////////////////////////////////////////
		/**
		 * Room Page.
		 */
		
        $sections[] = array(
			'title'  => esc_html__( 'Room Page', 'ba-hotel-light' ),
			'id'     => 'room-page',
			'desc'   => '',
			'icon'   => 'fas fa-hotel',
			'fields' => array(
                array(
					'id'          => 'room_layout',
					'type'        => 'image_select',
                    'full_width'  => false,
					'title'       => esc_html__( 'Room post layout', 'ba-hotel-light' ),
                    'options'     => $layout_options,
                    'default'     => apply_filters( 'bahotel_l_option', '', 'room_layout' ),
				),
                array(
					'id'         => 'room_slideshow_thumbnails',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Show thumbnails in slideshow', 'ba-hotel-light' ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'room_slideshow_thumbnails' ),
				),
                array(
					'id'         => 'room_rating',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Show star rating in room details?', 'ba-hotel-light' ),
                    'default'    => (isset(Bahotel_L_Settings::$settings['room_rating']) ? Bahotel_L_Settings::$settings['room_rating'] : ''),
				),
                array(
					'id'         => 'room_booknow_button',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Show book now button', 'ba-hotel-light' ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'room_booknow_button' ),
				),
                array(
					'id'         => 'room_features',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Show features section', 'ba-hotel-light' ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'room_features' ),
				),
                array(
					'id'         => 'room_booking_form',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Show booking form at the page bottom', 'ba-hotel-light' ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'room_booking_form' ),
				),
                array(
					'id'         => 'room_reviews',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Show reviews section', 'ba-hotel-light' ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'room_reviews' ),
				),
                array(
					'id'         => 'room_reviews_open',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Show "add review" form', 'ba-hotel-light' ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'room_reviews_open' ),
				),
            ),
		);
        
        //////////////////////////////////////////////////
		/**
		 * Blog Page.
		 */
		
        $sections[] = array(
			'title'  => esc_html__( 'Blog', 'ba-hotel-light' ),
			'id'     => 'blog-page',
			'desc'   => '',
			'icon'   => 'fas fa-file-alt',
			'fields' => array(
                array(
					'id'         => 'blog_header_default',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Use default header image', 'ba-hotel-light' ),
					'default'    => 1,
				),
                array(
                    'id'         => 'blog_header_image',
                    'type'       => 'media',
                    'required' => array('blog_header_default','!=','1'),
                    'full_width' => false,
                    'title'      => esc_html__( 'Blog Header image', 'ba-hotel-light' ),
				),
                array(
					'id'          => 'blog_layout',
					'type'        => 'image_select',
                    'full_width'  => false,
					'title'       => esc_html__( 'Archive layout', 'ba-hotel-light' ),
                    'options'     => $layout_options,
                    'default'     => apply_filters( 'bahotel_l_option', '', 'blog_layout' ),
				),
                array(
					'id'         => 'blog_columns',
					'type'       => 'radio',
					'full_width' => false,
					'title'      => esc_html__( 'Blog archive columns', 'ba-hotel-light' ),
                    'description' => esc_html__( 'Only for full width layouts', 'ba-hotel-light' ),
                    'options' => array(
                         1 => esc_html__( '1 column', 'ba-hotel-light' ),
                         2 => esc_html__( '2 columns', 'ba-hotel-light' ),
                    ),
					'default'    => 1,
				),
                array(
					'id'          => 'blog_post_layout',
					'type'        => 'image_select',
                    'full_width'  => false,
					'title'       => esc_html__( 'Single post layout', 'ba-hotel-light' ),
                    'options'     => $layout_options,
                    'default'     => apply_filters( 'bahotel_l_option', '', 'blog_post_layout' ),
				),
			),
		);
        
        //////////////////////////////////////////////////
		/**
		 * Archive Page.
		 */
		
        $sections[] = array(
			'title'  => esc_html__( 'Archive Page', 'ba-hotel-light' ),
			'id'     => 'archive-page',
			'desc'   => '',
			'icon'   => 'far fa-folder',
			'fields' => array(
                array(
					'id'         => 'archive_header_default',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Use default header image', 'ba-hotel-light' ),
					'default'    => 1,
				),
                array(
                    'id'         => 'archive_header_image',
                    'type'       => 'media',
                    'required' => array('archive_header_default','!=','1'),
                    'full_width' => false,
                    'title'      => esc_html__( 'Archive Header image', 'ba-hotel-light' ),
				),
                array(
					'id'          => 'archive_layout',
					'type'        => 'image_select',
                    'full_width'  => false,
					'title'       => esc_html__( 'Archive layout', 'ba-hotel-light' ),
                    'options'     => $layout_options,
                    'default'     => apply_filters( 'bahotel_l_option', '', 'archive_layout' ),
				),
			),
		);
        
        //////////////////////////////////////////////////
		/**
		 * Events
		 */
         
        $sections[] = array(
			'title'  => esc_html__( 'Events', 'ba-hotel-light' ),
			'id'     => 'events-page',
			'desc'   => '',
			'icon'   => 'fas fa-theater-masks',
			'fields' => array(
                array(
					'id'          => 'events_info',
					'type'        => 'info',
                    'style' => 'warning',
                    'icon'  => 'el-icon-info-sign',
					'title'       => sprintf(
                    /* translators: 1: open 'a' html tag, 2: close 'a' html tag, 3: open 'a' html tag, 4: close 'a' html tag. */
                    __( 'To use event post type and shortcodes like on %1$sDemo site%2$s, you need to download from theme\'s site and install our free %3$sBA Theme core plugin%4$s.', 'ba-hotel-light' ), '<a href="https://ba-booking.com/ba-hotel-demo/">', '</a>', '<a href="https://ba-booking.com/ba-hotel/wp-content/uploads/sites/9/2019/10/ba-theme-core.zip">', '</a>'),
					'description' => '',
				),
                array(
					'id'         => 'events_header_default',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Use default header image', 'ba-hotel-light' ),
					'default'    => 1,
				),
                array(
                    'id'         => 'events_header_image',
                    'type'       => 'media',
                    'required' => array('events_header_default','!=','1'),
                    'full_width' => false,
                    'title'      => esc_html__( 'Events Header image', 'ba-hotel-light' ),
				),
                array(
					'id'          => 'events_layout',
					'type'        => 'image_select',
                    'full_width'  => false,
					'title'       => esc_html__( 'Archive layout', 'ba-hotel-light' ),
                    'options'     => $layout_options,
                    'default'     => apply_filters( 'bahotel_l_option', '', 'events_layout' ),
				),
                array(
					'id'         => 'events_orderby',
					'type'       => 'radio',
					'full_width' => false,
					'title'      => esc_html__( 'Archive events order by', 'ba-hotel-light' ),
                    'options' => array(
                         'date_event' => esc_html__( 'Event date', 'ba-hotel-light' ),
                         'modified' => esc_html__( 'Post modified date', 'ba-hotel-light' ),
                         'title' => esc_html__( 'Title', 'ba-hotel-light' ),
                    ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'events_orderby' ),
				),
                array(
					'id'         => 'events_order',
					'type'       => 'radio',
					'full_width' => false,
					'title'      => esc_html__( 'Archive events order', 'ba-hotel-light' ),
                    'options' => array(
                         'DESC' => esc_html__( 'DESC', 'ba-hotel-light' ),
                         'ASC' => esc_html__( 'ASC', 'ba-hotel-light' ),
                    ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'events_order' ),
				),
                array(
					'id'          => 'events_post_layout',
					'type'        => 'image_select',
                    'full_width'  => false,
					'title'       => esc_html__( 'Single post layout', 'ba-hotel-light' ),
                    'options'     => $layout_options,
                    'default'     => apply_filters( 'bahotel_l_option', '', 'events_post_layout' ),
				),
                array(
                    'id'         => 'events_post_dateformat',
                    'type'       => 'text',
                    'full_width' => false,
                    'title'      => esc_html__( 'Front end event date format', 'ba-hotel-light' ),
                    'description' => esc_html__( 'See more in PHP docs, https://www.php.net/manual/en/function.date.php', 'ba-hotel-light' ),
                    'default'     => apply_filters( 'bahotel_l_option', '', 'events_post_dateformat' ),
				),
                array(
                    'id'         => 'events_related_title',
                    'type'       => 'text',
                    'full_width' => false,
                    'title'      => esc_html__( 'Title for related events section', 'ba-hotel-light' ),
                    'default'     => apply_filters( 'bahotel_l_option', '', 'events_related_title' ),
				),
                array(
                    'id'         => 'events_related_subtitle',
                    'type'       => 'text',
                    'full_width' => false,
                    'title'      => esc_html__( 'Subtitle for related events section', 'ba-hotel-light' ),
                    'default'     => apply_filters( 'bahotel_l_option', '', 'events_related_subtitle' ),
				),
                array(
					'id'         => 'events_related_orderby',
					'type'       => 'radio',
					'full_width' => false,
					'title'      => esc_html__( 'Related events order by', 'ba-hotel-light' ),
                    'options' => array(
                         'date_event' => esc_html__( 'Event date', 'ba-hotel-light' ),
                         'modified' => esc_html__( 'Post modified date', 'ba-hotel-light' ),
                         'title' => esc_html__( 'Title', 'ba-hotel-light' ),
                    ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'events_related_orderby' ),
				),
                array(
					'id'         => 'events_related_order',
					'type'       => 'radio',
					'full_width' => false,
					'title'      => esc_html__( 'Related events order', 'ba-hotel-light' ),
                    'options' => array(
                         'DESC' => esc_html__( 'DESC', 'ba-hotel-light' ),
                         'ASC' => esc_html__( 'ASC', 'ba-hotel-light' ),
                    ),
					'default'    => apply_filters( 'bahotel_l_option', '', 'events_related_order' ),
				),
			),
		);
		
		//////////////////////////////////////////////////
		/**
		 * Search form.
		 */
		
        $sections[] = array(
			'title'  => esc_html__( 'Search Form', 'ba-hotel-light' ),
			'id'     => 'search-form',
			'desc'   => '',
			'icon'   => 'el el-search',
			'fields' => array(
                array(
					'id'         => 'search_form_info',
					'type'       => 'info',
					'title'      => esc_html__( 'Setup fields in Search Form Builder', 'ba-hotel-light' ),
					'desc'    => '<a href="'.esc_url(get_admin_url().'edit.php?post_type=to_book&page=search_form').'" target="_blank">'.__( 'Search Form Builder', 'ba-hotel-light' ).'</a>',
				),
                array(
					'id'         => 'search_form_bg',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Use background on search form', 'ba-hotel-light' ),
                    'default'    => (isset(Bahotel_L_Settings::$settings['search_form_bg']) ? Bahotel_L_Settings::$settings['search_form_bg'] : ''),
				),
                array(
					'id'         => 'search_form_over_header',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Place search form over header image', 'ba-hotel-light' ),
                    'default'    => (isset(Bahotel_L_Settings::$settings['search_form_over_header']) ? Bahotel_L_Settings::$settings['search_form_over_header'] : ''),
				),
                array(
					'id'         => 'search_form_collapsible',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Make header search form collapsible on mobile screens', 'ba-hotel-light' ),
                    'default'    => (isset(Bahotel_L_Settings::$settings['search_form_collapsible']) ? Bahotel_L_Settings::$settings['search_form_collapsible'] : ''),
				),
            ),
		);
		
		//////////////////////////////////////////////////
		/**
		 * Search Results.
		 */
		
        $sections[] = array(
			'title'  => esc_html__( 'Search Results', 'ba-hotel-light' ),
			'id'     => 'search-results',
			'desc'   => '',
			'icon'   => 'el el-th-list',
			'fields' => array(
                array(
					'id'         => 'search_result_view',
					'type'       => 'radio',
					'full_width' => false,
					'title'      => esc_html__( 'Search result columns', 'ba-hotel-light' ),
                    'description' => '',
                    'options' => array(
                         'col1_s' => esc_html__( '1 column', 'ba-hotel-light' ),
                         'col2' => esc_html__( '2 columns', 'ba-hotel-light' ),
                    ),
					'default'    => (isset(Bahotel_L_Settings::$settings['search_result_view']) ? Bahotel_L_Settings::$settings['search_result_view'] : 'col1_s'),
				),
                array(
					'id'         => 'search_result_sortby',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Show "sort by" filter?', 'ba-hotel-light' ),
                    'default'    => (isset(Bahotel_L_Settings::$settings['search_result_sortby']) ? Bahotel_L_Settings::$settings['search_result_sortby'] : ''),
				),
                array(
					'id'         => 'search_result_rating',
					'type'       => 'switch',
					'full_width' => false,
					'title'      => esc_html__( 'Show star rating in room details?', 'ba-hotel-light' ),
                    'default'    => (isset(Bahotel_L_Settings::$settings['search_result_rating']) ? Bahotel_L_Settings::$settings['search_result_rating'] : ''),
				),
            ),
		);
		
		//////////////////////////////////////////////////
		/**
		 * Footer.
		 */
		$sections[] = array(
			'title'  => esc_html__( 'Footer', 'ba-hotel-light' ),
			'id'     => 'footer',
			'desc'   => '',
			'icon'   => 'far fa-copyright',
			'fields' => array(
				array(
					'id'         => 'copyright-section-start',
					'type'       => 'section',
					'full_width' => true,
					'title'      => esc_html__( 'Copyrights', 'ba-hotel-light' ),
					'indent'     => true,
				),
				array(
					'id'         => 'copyrights',
					'type'       => 'editor',
					'full_width' => true,
					'title'      => esc_html__( 'Copyrights text', 'ba-hotel-light' ),
					'default'    => __( 'Copyright &copy; {year}, {sitename}', 'ba-hotel-light' ),
					'args'       => array(
						'wpautop'       => false,
						'media_buttons' => false,
						'textarea_rows' => 5,
					),
				),
				array(
					'id'         => 'copyrigh-section-end',
					'type'       => 'section',
					'full_width' => true,
					'indent'     => false,
				),
			)
		);		
		
		//////////////////////////////////////////////////
		/**
		 * Font set.
		 */
		
		$sections[] = array(
			'title'  => esc_html__( 'Fonts', 'ba-hotel-light' ),
			'id'     => 'custom-fonts',
			'desc'   => '',
			'icon'   => 'el el-fontsize',
			'fields' => array(
				array(
					'id'          => 'fonts_info',
					'type'        => 'info',
                    'style' => 'warning',
                    'icon'  => 'el-icon-info-sign',
					'title'       => sprintf(
                    /* translators: 1: open 'a' html tag, 2: close 'a' html tag. */
                    __( 'These options are available in %1$sBA Hotel theme%2$s only.', 'ba-hotel-light' ), '<a href="https://ba-booking.com/ba-hotel/">', '</a>'),
					'description' => '',
				),
			),
		);
		
		//////////////////////////////////////////////////
		/**
		 * Color settings
		 */
        
        $sections[] = array(
			'title'  => esc_html__( 'Colors', 'ba-hotel-light' ),
			'id'     => 'custom-colors',
			'desc'   => '',
			'icon'   => 'el el-tint',
			'fields' => array(
				array(
					'id'          => 'colors_info',
					'type'        => 'info',
                    'style' => 'warning',
                    'icon'  => 'el-icon-info-sign',
					'title'       => sprintf(
                    /* translators: 1: open 'a' html tag, 2: close 'a' html tag. */
                    __( 'These options are available in %1$sBA Hotel theme%2$s only.', 'ba-hotel-light' ), '<a href="https://ba-booking.com/ba-hotel/">', '</a>'),
					'description' => '',
				),
			),
		);
		
		//////////////////////////////////////////////////
		/**
		 * Register sections.
		 */
		$sections = apply_filters( 'bahotel_l_theme_settings_sections', $sections );
        
		foreach ( $sections as $section ) {
			
			Redux::setSection( Bahotel_L_Settings::$option_name, $section );
		}
	}
	
	////////////////////////////////////////////////////////////
	//// BA Book Everything integration.
	////////////////////////////////////////////////////////////
	
    ////////////////////////////////////////////////////////////
	/**
	 * Returns theme settings set filled with BABE data.
	 *
	 * @param array $sections Redux sections array.
	 *
	 * @return array
	 */
	static function sections_altering( $sections ) {
		
		$babe_taxonomies = array();
		
		if ( ( class_exists( 'BABE_Post_types' ) ) && ( ! empty( BABE_Post_types::$taxonomies_list ) ) ) {
			
			foreach( BABE_Post_types::$taxonomies_list as $taxonomy_id => $taxonomy ) {
				$babe_taxonomies[ $taxonomy['slug'] ] = $taxonomy['name'];
			}
			
		} else {
			return $sections;
		}
		
		foreach( $sections as $section_key => $section_arr ) {
			
			switch ( $section_arr['id'] ) {
				
				//////////////////////////////////////////////////
				/**
				 * Search Form.
				 */
				case 'search-form':
					
					$fields = $sections[ $section_key ]['fields'];
					
					$fields[] = array(
					  'id'         => 'search_form_exclude_post_types',
					  'type'       => 'checkbox',
					  'full_width' => false,
					  'title'      => esc_html__( 'Exclude search form from post types', 'ba-hotel-light' ),
                      'description' => '',
                      'options' => self::get_post_types_options(),
				    );
                    
                    $sections[ $section_key ]['fields'] = $fields;
					
					break;
				
				//////////////////////////////////////////////////
				/**
				 * Search Results.
				 */
				case 'search-results':
					
					$fields = $sections[ $section_key ]['fields'];
					
					$fields[] = array(
						'id'          => 'search_res_preview_taxonomies',
						'type'        => 'checkbox',
						'full_width'  => false,
						'title'       => esc_html__( 'Add room terms', 'ba-hotel-light' ),
						'description' => esc_html__( 'Show terms in the room preview from selected taxonomies.', 'ba-hotel-light' ),
						'options'     => $babe_taxonomies,
					);
					
					$sections[ $section_key ]['fields'] = $fields;
					
					break;
				
				//////////////////////////////////////////////////
				/**
				 * Room Page.
				 */
				case 'room-page':
				
					$fields = $sections[ $section_key ]['fields'];
					
					$fields[] = array(
						'id'          => 'taxonomies_on_room_page',
						'type'        => 'checkbox',
						'full_width'  => false,
						'title'       => esc_html__( 'Room terms section', 'ba-hotel-light' ),
						'description' => esc_html__( 'Show term icons on the room page from selected taxonomies.', 'ba-hotel-light' ),
						'options'     => $babe_taxonomies,
					);
					
					$sections[ $section_key ]['fields'] = $fields;
					
					break;
                    
                //////////////////////////////////////////////////
				/**
				 * Services Page.
				 */
				case 'services-page':
				
					$fields = $sections[ $section_key ]['fields'];
                    
                    $fields_start = array_slice($fields, 0, 6);
                    $fields_end = array_slice($fields, 6);
					
					$fields_start[] = array(
						'id'          => 'taxonomies_on_services_page',
						'type'        => 'checkbox',
                        'required' => array('services_excerpt','=','1'),
						'full_width'  => false,
						'title'       => esc_html__( 'Archive excerpt term icons', 'ba-hotel-light' ),
						'description' => esc_html__( 'Show terms on the services archive from selected taxonomies.', 'ba-hotel-light' ),
						'options'     => $babe_taxonomies,
					);
					
					$sections[ $section_key ]['fields'] = array_merge($fields_start, $fields_end);
					
					break;    
			}
		}
        
		return $sections;
	}
	
    ///////////////////////////////////////////////////////////
	/**
	 * Outputs radio field for the BABE options.
	 *
	 * @param $args Redux field arguments.
	 *
	 * @return
	 */
	static function callback_radio_taxonomies( $args ) {
		
		$babe_taxonomies = array();
		
		if ( ( class_exists( 'BABE_Post_types' ) ) && ( ! empty( BABE_Post_types::$taxonomies_list ) ) ) {
			
			$babe_taxonomies[0] = __( 'None', 'ba-hotel-light' );
			
			foreach( BABE_Post_types::$taxonomies_list as $taxonomy_id => $taxonomy ) {
				
				$babe_taxonomies[ $taxonomy['slug'] ] = $taxonomy['name'];
			}
			
		} else {	
			return;
		}
		
        $current_checked = apply_filters( 'bahotel_l_option', null, $args['id'] );
		
        echo '
			<fieldset id="bahotel_l_settings-' . esc_attr( $args['id'] ). '" class="redux-field-container redux-field redux-field-init redux-container-radio ' . esc_attr( $args['class'] ) . '" data-id="' . esc_attr( $args['id'] ) . '" data-type="radio">
				<ul class="data-full">
		';
		
		foreach ( $babe_taxonomies as $key => $title ) {
			
			echo '<li><label for="' . esc_attr( $args['id'] . '_' . $key ) . '"><input type="radio" class="radio" id="' . esc_attr( $args['id'] . '_' . $key ) . '" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $key ) . '" ' . checked( $key, $current_checked, false ) . '><span>' . esc_html( $title ) . '</span></label></li>';
		}
		
		echo '
			</ul>
            <div class="description field-desc">'.esc_html__( 'Add terms from selected taxonomy to the search form filters.', 'ba-hotel-light' ).'</div>
        </fieldset>
		';
		
		return;
	}
    
    //////////////////////////////
    /**
	 * Get posts list
     * 
     * @param string $post_type
     * @param array $ids
     * 
     * @return array
	 */
    public static function get_posts_options( $post_type = 'page', $ids = array() ) {
      
      $args = array(
        'post_type'   => $post_type,
        'numberposts' => -1, // phpcs:ignore WPThemeReview.CoreFunctionality.PostsPerPage.posts_per_page_numberposts
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC',
      ); 
      
      if (!empty($ids)){
        $args['post__in'] = $ids;
      }
      
      $posts = get_posts( $args );
      
      $post_options = array();
      
      if ( $posts ) {
        foreach ( $posts as $post ) {
            
          $post_options[ $post->ID ] = $post->post_title;
          
        }
      }
      
      return $post_options;
    
    }
    
    ///////////////////////////////////////////////
    /**
	 * Get post types option list.
     * 
     * @return array
	 */
    public static function get_post_types_options() {
        
        $output = array();
        
        $args = array(
          'public'   => true,
        );
        
        $post_types = get_post_types( $args, 'objects');
        
        foreach ( $post_types as $post_type ) {
            
            if ( !$post_type->_builtin || ( $post_type->_builtin && $post_type->name == 'post' ) ){
                $output[ $post_type->name ] = $post_type->labels->singular_name;
            }
        }
        
        return $output;
    
    }
	
	//////////////////////////////////////////////////
	/**
	 * Clears developer mode notifications.
	 *
	 * @param object $redux_instance Redux instance.
	 *
	 * @return object
	 */
	static function remove_dev_mode( $redux_instance ) {
		
		$redux_instance->args['dev_mode'] = false;
		
		$GLOBALS['redux_notice_check'] = 1;
		
		return $redux_instance;	
	}
	
	//////////////////////////////////////////////////
	/**
	 * Remove redux framework admin page to avoid confusion of our users and unnecesarry support questions.
	 *
	 * @return
	 */
	static function remove_redux_page() {
		
		remove_submenu_page( 'tools.php', 'redux-about' );
	}
    
    ////////////////////remove_demo_mode//////////
	/**
	 * Remove redux framework demo link mode to avoid confusion of our users and unnecesarry support questions.
     * https://docs.reduxframework.com/core/the-basics/removing-demo-mode-and-notices/
	 *
	 * @return
	 */
	static function remove_demo_mode() {
		
		if ( class_exists('ReduxFrameworkPlugin') ) {
           remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2 );
           remove_action( 'admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );    
        }
    
	}
	
	////////////////////////////////////////////////////////////
	//// End of our class.
	////////////////////////////////////////////////////////////
}

//////////////////////////////////////////////////
/**
 * Calling to setup class.
 */
Bahotel_L_Redux::init();

