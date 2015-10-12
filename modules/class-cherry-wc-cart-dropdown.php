<?php
/**
 * Cart Dropdown module
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Cart_Dropdown' ) ) {

	/**
	 * Cart Drodown class
	 */
	class Cherry_WC_Cart_Dropdown {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Cart-related theme options array
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $cart_options = array();

		/**
		 * Class constructor
		 */
		function __construct() {

			$this->cart_options = array(
				'shop-cart-title' => __( 'Shopping cart', 'cherry-woocommerce-package' ),
			);

			add_action( 'init', array( $this, 'register_static' ) );
			add_action( 'cherry_wc_cart_dropdown', array( $this, 'dropdown_frontend' ) );
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart_fragment' ) );
		}

		/**
		 * Prepare cart dropdown options
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function prepare_options() {

			foreach ( $this->cart_options as $option_name => $option_val ) {
				$this->cart_options[ $option_name ] = cherry_wc_options()->get_option( $option_name, $option_val );
			}

		}

		/**
		 * Register static.
		 *
		 * @since 1.0.0
		 */
		public function register_static() {
			cherry_wc_templater()->register_static( 'cherry-cart-dropdown-static.php' );
		}

		/**
		 * Print cart dropdown output
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function dropdown_frontend() {
			$this->prepare_options();
			?>
			<div class="cherry-wc-cart" data-dropdown="box" data-dropdown-active="false">
				<?php cherry_wc_templater()->get_template_part( 'cart-dropdown' ); ?>
			</div>
			<?php
		}

		/**
		 * AJAX callback for add to cart action
		 *
		 * @since 1.0.0
		 * @param array $fragments array of DOM fragments to replace.
		 * @return array
		 */
		function add_to_cart_fragment( $fragments ) {

			$this->prepare_options();
			ob_start();
			cherry_wc_templater()->get_template_part( 'cart-dropdown' );
			$fragments['div.cart-content'] = ob_get_clean();

			return $fragments;
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
