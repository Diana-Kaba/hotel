<?php
/**
 * Individual page template options handeling.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
}

//////////////////////////////////////////////////
/**
 * Individual page template options handeling.
 *
 */
class Bahotel_L_Page_Options {

	//////////////////////////////////////////////////
	/**
	 * Internal variables.
	 */
	private static $data = array(
			'settings_id'  => 'bahotel_l_page_options',
			'support_id'   => 'bahotel_l_page_options',
			'nonce_id'     => '_bahotel_l_page_options_nonce',
			'nonce_action' => 'save_page_options',
	);
    
    /// cache
    private static $page_options = array();
	
	//////////////////////////////////////////////////
	/**
	 * Setup function.
	 * 
	 * @return
	 */
	static function init() {
		
		// Setup page options.
		add_action( 'init', array( __CLASS__, 'init_options' ), 10, 1 );
		add_action( 'init', array( __CLASS__, 'add_page_options_support' ), 30, 1 );
		
		// Handle meta boxes.
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ), 10, 1 );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 1 );
		
		// Provide data.
		add_filter( 'bahotel_l_page_option', array( __CLASS__, 'get_page_option' ), 10, 2 );
        
	}
	
	//////////////////////////////////////////////////
	/**
	 * Sets default data.
	 *
	 * @return
	 */
	static function init_options() {
	   
       $options = Bahotel_L_Settings::$layouts;
       $options[0] = __( 'Theme default', 'ba-hotel-light' );
		
		// Options set.
		self::$data['options_set'] = array(
			'layout' => array(
				'type'        => 'select',
				'label'       => __( 'Page layout', 'ba-hotel-light' ),
				'description' => __( 'Specific layout for this page.', 'ba-hotel-light' ),
                'options' => $options,
			),
			'page_title' => array(
				'type'           => 'checkbox',
				'label'          => __( 'Page title', 'ba-hotel-light' ),
				'checkbox_label' => __( 'Enable', 'ba-hotel-light' ),
				'description'    => __( 'Display page title.', 'ba-hotel-light' ),
			),
			'header_margin' => array(
				'type'           => 'checkbox',
				'label'          => __( 'Header bottom margin', 'ba-hotel-light' ),
				'checkbox_label' => __( 'Enable', 'ba-hotel-light' ),
				'description'    => __( 'Display the margin below the header.', 'ba-hotel-light' ),
			),
		);
		
		
		// Default values.
		self::$data['options_defaults'] = array(
			'layout' => 0,
			'page_title' => 1,
			'header_margin' => 1,
		);
        
        self::$data = apply_filters('bahotel_l_page_options_init', self::$data);
        
        return;
	}
	
	//////////////////////////////////////////////////
	/**
	 * Add page settings support for the post types.
	 *
	 * @return
	 */
	static function add_page_options_support() {
		
		// Get builtin post types.
		$post_types = array(
			'page' => 'page',
			'post' => 'post',
		);
		
		// Get custom post types.
		$custom_post_types = get_post_types( array(
			'public' => true,
			'_builtin' => false,
		) );
		
		// Merge lists.
		$post_types = wp_parse_args( $post_types, $custom_post_types );
        
		// Register support.
		foreach ( $post_types as $post_type ) {
			add_post_type_support( $post_type, self::$data['support_id'] );
		}
	}
	
	//////////////////////////////////////////////////
	/**
	 * Returns current page specific option.
	 *
	 * @param mixed $value - get from filter
	 * @param string $option_id Option ID.
	 * 
	 * @return array
	 */
	static function get_page_option( $value, $option_id ) {
		
		$post_id = get_the_ID();
		
		if ( ! $post_id ) {
			return $value;
		}
        
        if (!isset(self::$page_options[$post_id])){
            self::$page_options[$post_id] = self::get_page_options_values($post_id);
        }
		
		$value = isset( self::$page_options[$post_id][ $option_id ] ) ? self::$page_options[$post_id][ $option_id ] : $value;
		
		return $value;
	}
	
	//////////////////////////////////////////////////
	/**
	 * Returns page options values.
	 *
	 * @param int $post_id Post ID.
	 * 
	 * @return array
	 */
	static function get_page_options_values( $post_id ) {
		
		$options = get_post_meta( $post_id, self::$data['settings_id'], 1 );
        
        $options = empty($options) ? self::$data['options_defaults'] : wp_parse_args($options, self::$data['options_defaults']);		
		
		return $options;
	}
	
	////////////////////////////////////////////////////////////
	//// Handle meta boxes.
	////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////
	/**
	 * Add meta box for the page settings.
	 *
	 * @param string $post_type Post type ID.
	 *
	 * @return
	 */
	static function add_meta_box( $post_type ) {
		
		if ( ! empty( $post_type ) && post_type_supports( $post_type, self::$data['support_id'] ) ) {
			
			add_meta_box(
				self::$data['settings_id'],
				__( 'Page template options', 'ba-hotel-light' ),
				array( __CLASS__, 'show_meta_box' ),
				$post_type,
				'side'
			);
		}
		
		return;
	}
	
	//////////////////////////////////////////////////
	/**
	 * Shows meta box for the page settings.
	 *
     * @param WP_Post $post
	 *
	 * @return
	 */
	static function show_meta_box( $post ) {
		
		$options_set = self::$data['options_set'];
		
		$values = self::get_page_options_values($post->ID);
		
		$settings_id = self::$data['settings_id'];
		
		do_action( 'bahotel_l_page_options_before_meta_box', $post );		

		// Output settings form.
		foreach( $options_set as $id => $field ) {

			?>
			<p><label for="<?php echo esc_attr( $settings_id ); ?>_<?php echo esc_attr( $id ); ?>"><strong><?php echo esc_html( $field['label'] ); ?></strong></label></p>
			<?php

			switch( $field['type'] ) {

				case 'select':
					?>
					<select name="<?php echo esc_attr( $settings_id ); ?>[<?php echo esc_attr( $id ) ?>]" id="<?php echo esc_attr( $settings_id ); ?>_<?php echo esc_attr( $id ) ?>">
						<?php foreach( $field['options'] as $field_option_id => $filed_option_title ) : ?>
							<option value="<?php echo esc_attr( $field_option_id ); ?>" <?php selected( $values[ $id ], $field_option_id ); ?>><?php echo esc_html( $filed_option_title ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php

					break;

					
				case 'checkbox':
					?>
					<label><input type="checkbox" name="<?php echo esc_attr( $settings_id ); ?>[<?php echo esc_attr( $id ); ?>]" <?php checked( $values[ $id ], 1 ); ?> value="1" /><?php echo esc_html( $field['checkbox_label'] ); ?></label>
					<?php
					break;

					
				case 'text':
				default :
					?><input type="text" name="<?php echo esc_attr( $settings_id ); ?>[<?php echo esc_attr( $id ); ?>]" id="<?php echo esc_attr( $settings_id ); ?>_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $values[ $id ] ); ?>" /><?php
					break;

			}

			if ( ! empty( $field['description'] ) ) {
				
				?><p class="description"><?php echo esc_html( $field['description'] ); ?></p>
				<?php
			}
		}

		// Add nounce.
		wp_nonce_field( self::$data['nonce_action'], self::$data['nonce_id'] );
		
		do_action( 'bahotel_l_page_options_after_meta_box', $post );
        
        return;
	}
	
	//////////////////////////////////////////////////
	/**
	 * Save page options from meta boxes.
	 *
	 * @param string $post_id Post ID.
	 *
	 * @return
	 */
	static function save_post( $post_id ) {
		
		$settings_id = self::$data['settings_id'];
		$nonce_id = self::$data['nonce_id'];
		$nonce_action = self::$data['nonce_action'];
        
        if ( 
			( ! current_user_can( 'edit_post', $post_id ) ) ||
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			( empty( $_POST[ $nonce_id ] ) ) ||
			( ! wp_verify_nonce( wp_unslash($_POST[ $nonce_id ]), $nonce_action ) ) || // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			( empty( $_POST[ $settings_id ] ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		) {
			
			return;
		}
		
		$settings = (array) wp_unslash( $_POST[ $settings_id ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        
        //// sanitize settings in loop
		
		$options_set = self::$data['options_set'];

		foreach( $options_set as $id => $field ) {
			
			switch( $field['type'] ) {
				
				case 'select' :
					if ( ! isset( $field['options'][$settings[ $id ]]) ) {
						$settings[ $id ] = self::$data['options_defaults'][$id];
					}
					break;

				case 'checkbox' :
					$settings[ $id ] = isset($settings[ $id ]) && $settings[ $id ] ? 1 : 0;
					break;

				case 'text' :
				default :
					$settings[ $id ] = sanitize_text_field( $settings[ $id ] );
					break;
			}
		}
		
		update_post_meta( $post_id, $settings_id, $settings );
        
        // clear cache
        unset(self::$page_options[$post_id]);
        
        return;
        
	}
	
	////////////////////////////////////////////////////////////
	//// End of our class.
	////////////////////////////////////////////////////////////
}

//////////////////////////////////////////////////
/**
 * Calling to setup class.
 */
Bahotel_L_Page_Options::init();

