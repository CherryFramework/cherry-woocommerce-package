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
		 * Holder for shop columns number
		 *
		 * @since 1.0.0
		 * @var   int
		 */
		private $loop_cols = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {
			add_filter( 'loop_shop_per_page', array( $this, 'loop_per_page' ) );
			add_filter( 'loop_shop_columns', array( $this, 'loop_columns' ) );
			add_filter( 'woocommerce_before_shop_loop', array( $this, 'loop_wrapper_open' ), 99 );
			add_filter( 'woocommerce_after_single_product_summary', array( $this, 'loop_wrapper_open' ), 19 );
			add_filter( 'woocommerce_after_shop_loop', array( $this, 'loop_wrapper_close' ), 0 );
			add_filter( 'woocommerce_after_single_product_summary', array( $this, 'loop_wrapper_close' ), 21 );
			add_filter( 'cherry_current_object_id', array( $this, 'set_shop_page_id' ) );
			add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
		}

		/**
		 * Add open shop loop wrapper with column numbers
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function loop_wrapper_open() {
			echo '<div class="product-cols-' . $this->get_shop_columns( 4 ) . '">';
		}

		/**
		 * Add close shop loop wrapper with column numbers
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function loop_wrapper_close() {
			echo '</div>';
		}

		/**
		 * Set products number per shop page from theme options
		 *
		 * @since  1.0.0
		 * @param  int $num default per page number.
		 * @return int
		 */
		public function loop_per_page( $num ) {
			$per_page = cherry_wc_options()->get_option( 'shop-per-page', 8 );
			return $per_page;
		}

		/**
		 * Set columns number per shop loop page
		 *
		 * @since  1.0.0
		 * @param  int $cols current columns number.
		 * @return int
		 */
		public function loop_columns( $cols ) {

			if ( ! function_exists( 'cherry_current_page' ) ) {
				return $cols;
			}

			return $this->get_shop_columns( $cols );

		}

		/**
		 * Change related products number according to page layout
		 *
		 * @since  1.0.0
		 * @param  array $args current related products arguments array.
		 * @return array
		 */
		function related_products_args( $args ) {

			$args['posts_per_page'] = $this->get_shop_columns( 4 );
			$args['columns']        = $this->get_shop_columns( 4 );

			return $args;
		}

		/**
		 * Get shop columns number
		 *
		 * @since  1.0.0
		 * @param  int $cols initiall columns number.
		 * @return int
		 */
		public function get_shop_columns( $cols = 4 ) {

			if ( null !== $this->loop_cols ) {
				return $this->loop_cols;
			}

			$layout = cherry_current_page()->get_property( 'layout' );

			$s = 'sidebar';
			$c = 'content';

			if ( in_array( $layout, array( "$s-$c", "$c-$s" ) ) ) {
				$this->loop_cols = 3;
				return $this->loop_cols;
			}

			if ( in_array( $layout, array( "$s-$c-$s", "$s-$s-$c", "%c-$s-$s" ) ) ) {
				return $this->loop_cols;
			}

			$this->loop_cols = $cols;

			return $cols;

		}

		/**
		 * Set shop page id from options as current page object for shop and product categories
		 *
		 * @since  1.0.0
		 * @param  int $id current object id.
		 * @return int
		 */
		public function set_shop_page_id( $id ) {

			if ( ! is_shop() && ! is_product_taxonomy() ) {
				return $id;
			}

			return wc_get_page_id( 'shop' );

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
