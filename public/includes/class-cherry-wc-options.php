<?php
/**
 * Options management class
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Options' ) ) {

	/**
	 * Options management class
	 */
	class Cherry_WC_Options {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {
			add_filter( 'cherry_defaults_settings', array( $this, 'add_options' ) );
		}

		/**
		 * Add plugin options to Cherry options page
		 *
		 * @since 1.0.0
		 * @param array $sections existing section array.
		 * @return array
		 */
		public function add_options( $sections ) {

			$menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );

			$options_menus = array(
				'0' => __( 'Select menu', 'cherry-woocommerce-package' ),
			);

			foreach ( $menus as $menu ) {
				$options_menus[ $menu->term_id ] = $menu->name;
			}

			$shop_options = array(
				'shop-show-acc' => array(
					'type'  => 'switcher',
					'title' => __( 'Show account dropdown', 'cherry-woocommerce-package' ),
					'label' => '',
					'hint'  => array(
						'type'    => 'text',
						'content' => __( 'Show/hide account dropdown static content', 'cherry-woocommerce-package' ),
					),
					'value' => 'true',
				),
				'shop-not-logged-label' => array(
					'type'  => 'text',
					'title' => __( 'Guests account menu label', 'cherry-woocommerce-package' ),
					'label' => '',
					'hint'  => array(
						'type'    => 'text',
						'content' => __( 'Set label for account dropdwon for not logged in users', 'cherry-woocommerce-package' ),
					),
					'value' => __( 'My Account', 'cherry-woocommerce-package' ),
				),
				'shop-logged-label' => array(
					'type'  => 'text',
					'title' => __( 'Memebers account menu label', 'cherry-woocommerce-package' ),
					'label' => '',
					'hint'  => array(
						'type'    => 'text',
						'content' => __( 'Set label for account dropdwon for logged in users', 'cherry-woocommerce-package' ),
					),
					'value' => __( 'My Account', 'cherry-woocommerce-package' ),
				),
				'shop-acc-menu' => array(
					'type'    => 'select',
					'title'   => __( 'Menu to show in account dropdown', 'cherry' ),
					'label'   => '',
					'hint'    => array(
						'type'    => 'text',
						'content' => __( 'Select navigation menu to show it in account dropdown', 'cherry-woocommerce-package' ),
					),
					'value'   => '',
					'options' => $options_menus,
				),
				'shop-show-auth' => array(
					'type'  => 'switcher',
					'title' => __( 'Login/out links in account menu', 'cherry-woocommerce-package' ),
					'label' => '',
					'hint'  => array(
						'type'    => 'text',
						'content' => __( 'Show/hide authentication links in account dropdown', 'cherry-woocommerce-package' ),
					),
					'value' => 'true',
				),
				'shop-login-label' => array(
					'type'  => 'text',
					'title' => __( 'Log in link label', 'cherry-woocommerce-package' ),
					'label' => '',
					'hint'  => array(
						'type'    => 'text',
						'content' => __( 'Set label for log in/register link in account dropdwon', 'cherry-woocommerce-package' ),
					),
					'value' => __( 'Log In/Register', 'cherry-woocommerce-package' ),
				),
				'shop-logout-label' => array(
					'type'  => 'text',
					'title' => __( 'Logout link label', 'cherry-woocommerce-package' ),
					'label' => '',
					'hint'  => array(
						'type'    => 'text',
						'content' => __( 'Set label for log out link in account dropdwon', 'cherry-woocommerce-package' ),
					),
					'value' => __( 'Logout', 'cherry-woocommerce-package' ),
				),
			);

			$sections['shop-section'] = array(
				'name'         => __( 'Shop', 'cherry-woocommerce-package' ),
				'icon'         => 'dashicons dashicons-cart',
				'priority'     => 105,
				'options-list' => apply_filters( 'cherry_wc_package_options_list', $shop_options ),
			);

			return $sections;

		}

		/**
		 * Get shop option by name
		 *
		 * @since 1.0.0
		 * @uses cherry_get_option()
		 * @param string $option option name.
		 * @param mixed  $default default option value.
		 * @return mixed
		 */
		public function get_option( $option, $default = false ) {

			if ( ! function_exists( 'cherry_get_option' ) ) {
				return $default;
			}

			return cherry_get_option( $option, $default );

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

	/**
	 * Wrapper for Cherry_WC_Options class innstance
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function cherry_wc_options() {
		return Cherry_WC_Options::get_instance();
	}

	cherry_wc_options();

}
