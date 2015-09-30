<?php
/**
 * Add custom single product gallery
 *
 * @package   cherry_woocommerce_package
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Single_Product_Gallery' ) ) {

	/**
	 * Product single page gallery
	 */
	class Cherry_WC_Single_Product_Gallery {

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
			$this->remove_default_img();
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'add_gallery' ), 20 );
		}

		/**
		 * Unattach default imgae output hook
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function remove_default_img() {
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		}

		/**
		 * Add product images gallery
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_gallery() {

			Cherry_WC_Assets::enqueue_local_js(
				array(
					'cherry-woocommerce',
					'cherry-cycle2',
					'cherry-cycle2-carousel',
					'cherry-elevatezoom',
				)
			);

			cherry_wc_templater()->get_template_part( 'product-gallery/image' );
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

}
