<?php
/**
 * Adapter for YITH Compare and Wishlist plugins
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_YITH_Addons' ) ) {

	/**
	 * YITH addons extra options
	 */
	class Cherry_WC_YITH_Addons {

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
			add_action( 'wp_head', array( $this, 'compare_css' ), 99 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_wishlist_to_loop' ), 25 );
		}

		/**
		 * Print add to wishlist button inside products loop
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_wishlist_to_loop() {

			if ( ! defined( 'YITH_WCWL' ) ) {
				return;
			}

			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		}

		/**
		 * Print dynamic CSS in compare popup
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function compare_css() {

			if ( ! isset( $_GET['action'] ) || 'yith-woocompare-view-table' != $_GET['action'] ) {
				return;
			}
			$css_compiler = cherry_css_compiler::get_instance();
			$dynamic_css  = $css_compiler->prepare_dynamic_css();

			?>
			<style type="text/css">
				<?php echo $dynamic_css; ?>
			</style>
			<?php

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
