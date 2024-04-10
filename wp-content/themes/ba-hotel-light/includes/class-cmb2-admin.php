<?php
/**
 * CMB2 hooks
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
}

//////////////////////////////////////////////////

class Bahotel_L_CMB2_Admin {

	//////////////////////////////////////////////////
	/**
	 * Setup function.
	 *
	 */
    public static function init() {
		
		add_action( 'cmb2_admin_init', array( __CLASS__, 'taxonomies_metabox' ), 10, 1 );
		
		add_action( 'cmb2_render_fontawesome_icon', array( __CLASS__, 'cmb2_render_fontawesome_icon' ), 10, 5 );
		add_action( 'cmb2_sanitize_fontawesome_icon', array( __CLASS__, 'cmb2_sanitize_fontawesome_icon' ), 10, 2 );
		
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_assets' ), 10, 1 );
        
        add_action( 'cmb2_service_before_service_type', array( __CLASS__, 'cmb2_service' ), 10, 2 );
	}
	
	
	
	////////////////////////////////////////////////////////////
	//// Init section.
	////////////////////////////////////////////////////////////

	//////////////////////////////////////////////////
	/**
	 * Loads required styles and scripts.
	 */
    public static function load_assets() {
		
		if ( ! class_exists( 'BABE_Post_types' ) ) {
			return;
		}
		
		if ( isset( $_GET['post_type'] ) && isset( $_GET['taxonomy'] ) && $_GET['post_type'] == BABE_Post_types::$booking_obj_post_type ) {
			
			// Scripts.
			wp_enqueue_script( 'bahotel-l-fontawesome-picker', BAHOTEL_L_URI . '/js/fontawesome-iconpicker.min.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'bahotel-l-fontawesome-picker-init', BAHOTEL_L_URI . '/js/fontawesome-picker-init.js', array( 'bahotel-l-fontawesome-picker' ), '1.0', true );
			
			// Styles.
            wp_enqueue_style( 'bahotel-l-fontawesome' , BAHOTEL_L_URI . '/fonts/fontawesome-free/css/all.min.css', false, '5.5.0' );
			wp_enqueue_style( 'bahotel-l-bootstrap-popovers', BAHOTEL_L_URI . '/admin/css/bootstrap-popovers.css' );
			wp_enqueue_style( 'bahotel-l-fontawesome-picker', BAHOTEL_L_URI . '/admin/css/fontawesome-iconpicker.min.css', array( 'bahotel-l-bootstrap-popovers' ), '1.0' );
			wp_enqueue_style( 'bahotel-l-fontawesome-picker-fixes', BAHOTEL_L_URI . '/admin/css/cmb2-fixes.css', array( 'bahotel-l-fontawesome-picker' ), '1.0' );
		}
	}

	//////////////////////////////////////////////////
	/**
	 * Registers custom taxonomies extra fields.
	 * 
	 * @return
	 */
	public static function taxonomies_metabox() {
		
		if ( ! class_exists( 'BABE_Post_types' ) ) {
			
			return;
		}
		
		$taxonomies_arr = array();
		
        foreach( BABE_Post_types::$taxonomies_list as $taxonomy_id => $taxonomy ) {
			
			$taxonomies_arr[] = $taxonomy['slug'];
		}
		
		if ( ! empty( $taxonomies_arr ) ) {
			
			$cmb_term = new_cmb2_box( array(
				'id'               => 'custom_taxonomies_font_icons',
				'title'            => __( 'Icon fonts Metabox', 'ba-hotel-light' ), // Doesn't output for term boxes
				'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
				'taxonomies'       => $taxonomies_arr, // Tells CMB2 which taxonomies should have these fields
				//'new_term_section' => true, // Will display in the "Add New Category" section
			) );
            
            $cmb_term->add_field( array(
				'name' => esc_html__( 'Linearicons icon class', 'ba-hotel-light' ),
				'desc' => sprintf(
                  /* translators: %1$s: URL */
                  __( 'Exapmle: lnr lnr-tag<br> Find icon classes list on <a href="%1$s">Linearicons site</a>', 'ba-hotel-light' ),
                  'https://linearicons.com/free/'
                ),
				'id'   => 'lnr_class',
				'type' => 'text',
			) );
            
            $cmb_term->add_field( array(
				'name' => esc_html__( 'Eleganticons icon class', 'ba-hotel-light' ),
				'desc' => sprintf(
                    /* translators: %1$s: URL */
                    __( 'Example: eleganticon icon_folder-alt<br> Find icon classes list on <a href="%1$s">Elegant Themes site</a>', 'ba-hotel-light' ),
                    'https://www.elegantthemes.com/blog/resources/elegant-icon-font'
                ),
				'id'   => 'el_class',
				'type' => 'text',
			) );
			
			$cmb_term->add_field( array(
				'name' => esc_html__( 'Fontawesome 5 icon class', 'ba-hotel-light' ),
				'desc' => sprintf(
                     /* translators: %1$s: URL */
                     __( 'Exapmle: fas fa-wifi<br> Find icon classes list on <a href="%1$s">Fontawesome site</a>', 'ba-hotel-light' ),
                     'https://fontawesome.com/cheatsheet/'
                ),
				'id'   => 'fa_class',
				'type' => 'fontawesome_icon',
			) );
		}
	}
	
	//////////////////////////////////////////////////
	/**
	 * Outputs an additional CMB custom field
	 * to allow the FontAwesome Icon selection.
	 * 
	 * @return
	 */
	public static function cmb2_render_fontawesome_icon( $field, $escaped_value, $object_id, $object_type, $field_type ) {
	   
        $output = $field_type->input( array( 'type' => 'text', 'class' => 'fontawesome-icon-select regular-text' ) );
        $output = apply_filters( 'bahotel_l_cmb2_render_fontawesome_icon', $output, $field, $field_type );
		
		echo wp_kses( $output, Bahotel_L_Settings::$wp_allowedposttags );
	}
	
	//////////////////////////////////////////////////
	/**
	 * Sanitizes icon class name.
	 * 
	 * @return string
	 */
	public static function cmb2_sanitize_fontawesome_icon( $sanitized_val, $val ) {
		
		if ( ! empty( $val ) ) {
			return  sanitize_text_field( $val );
		}
		
		return $sanitized_val;
	}
    
    //////////////////////////////////////////////////
	/**
	 * Add extra fields to service posts
	 * 
     * @param object $cmb
     * @param string $prefix
     * 
	 * @return
	 */
	public static function cmb2_service($cmb, $prefix) {
		
		if ( ! class_exists( 'BABE_Post_types' ) ) {
			return;
		}
        
        $cmb->add_field( array(
         'name' => __( 'Service subtitle', 'ba-hotel-light' ),
         'id'   => $prefix . 'service_subtitle',
         'type'    => 'text',
        ) );
        
    }    
	
	////////////////////////////////////////////////////////////
	//// End of our class.
	////////////////////////////////////////////////////////////
}

//////////////////////////////////////////////////
/**
 * Calling to setup class.
 */
Bahotel_L_CMB2_Admin::init();
