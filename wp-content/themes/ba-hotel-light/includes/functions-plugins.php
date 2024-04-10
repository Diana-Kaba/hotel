<?php
/**
 * Required plugins handeling.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	
	exit;
}

include_once BAHOTEL_L_DIR . '/includes/class-tgm-plugin-activation.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

add_action( 'tgmpa_register', 'bahotel_l_register_required_plugins', 10, 1 );
	/**
	 * Registers the required plugins.
	 *
	 * The variables passed to the `tgmpa()` function should be:
	 * - an array of plugin arrays;
	 * - optionally a configuration array.
	 *
	 * This function is hooked into `tgmpa_register`, which is
	 * fired on the WP `init` action on priority 10.
	 *
	 * @see http://tgmpluginactivation.com/configuration/
	 *
	 * @return
	 */
	function bahotel_l_register_required_plugins() {
		
		//////////////////////////////////////////////////
		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(
        array(
		  'name'               => 'Redux Framework',
		  'slug'               => 'redux-framework',
		  'source'             => '', // The plugin source.
		  'required'           => false, // If false, the plugin is only 'recommended' instead of required.
		  'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
		  'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
		  'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
		  'external_url'       => '', // If set, overrides default API URL and points to an external URL.
          'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        ),
        array(
		  'name'               => 'BA Book Everything',
		  'slug'               => 'ba-book-everything',
		  'required'           => false,
        ),
        array(
		  'name'               => 'Yoast SEO',
		  'slug'               => 'wordpress-seo',
		  'required'           => false,
        ),
        array(
		  'name'               => 'Contact Form 7',
		  'slug'               => 'contact-form-7',
		  'required'           => false,
        ),
        array(
		  'name'               => 'Sassy Social Share',
		  'slug'               => 'sassy-social-share',
		  'required'           => false,
        ),
        array(
		  'name'               => 'Social Icons',
		  'slug'               => 'social-icons',
		  'required'           => false,
        ),
        array(
		  'name'               => 'MailChimp for WordPress',
		  'slug'               => 'mailchimp-for-wp',
		  'required'           => false,
        ),
        );
		
		//////////////////////////////////////////////////
		/**
		 * Array of configuration settings.
		 */
		$config = array(
			'id'           => 'bahotel',              // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                         // Default absolute path to bundled plugins.
			'menu'         => 'install-required-plugins', // Menu slug.
			'has_notices'  => true,                       // Show admin notices or not.
			'dismissable'  => true,                       // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                         // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                      // Automatically activate plugins after installation or not.
			'message'      => '',                         // Message to output right before the plugins table.
		);
		
		//////////////////////////////////////////////////
		/**
		 * Register plugins.
		 */
		tgmpa( $plugins, $config );
	}


