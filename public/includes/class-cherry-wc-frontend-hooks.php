<?php
/**
 * Define callbacks for frontend-related hooks
 *
 * @package   Cherry_WooCommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Frontend_Hooks' ) ) {

	/**
	 * Define callbacks for frontend-related hooks
	 */
	class Cherry_WC_Frontend_Hooks {

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
			add_filter( 'loop_shop_per_page', array( $this, 'products_per_page' ) );
		}

		/**
		 * Set products number per shop page from theme options
		 *
		 * @since  1.0.0
		 * @param  int $num default per page number.
		 * @return int
		 */
		function products_per_page( $num ) {
			$per_page = cherry_wc_options()->get_option( 'shop-per-page', 8 );
			return $per_page;
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

	Cherry_WC_Frontend_Hooks::get_instance();

}
