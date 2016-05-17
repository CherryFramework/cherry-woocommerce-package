<?php
/**
 * Quick View module
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Quick_View' ) ) {

	/**
	 * Product quick view
	 */
	class Cherry_WC_Quick_View {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Module init arguments array
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		private $args = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {

			$this->args = apply_filters(
				'cherry_wc_quick_view_args',
				array(
					'before' => array(
						'hook'     => 'woocommerce_before_shop_loop_item_title',
						'priority' => 0,
						'format'   => '<div class="cherry-thumb-wrap">',
					),
					'after' => array(
						'hook'     => 'woocommerce_before_shop_loop_item_title',
						'priority' => 100,
						'format'   => '</div>',
					),
					'button' => array(
						'hook'     => 'woocommerce_before_shop_loop_item_title',
						'priority' => 99,
						'format'   => '<span class="btn %3$s" data-product="%1$s">%2$s</span>',
					),
				)
			);

			$this->append_wrapper();
			$this->append_button();

		}

		/**
		 * Add open and close wrapper functions to related hooks
		 *
		 * @since  1.0.0
		 * @return void
		 */
		private function append_wrapper() {

			if ( ! empty( $this->args['before'] ) ) {
				add_action(
					$this->args['before']['hook'],
					array( $this, 'print_open_wrap' ),
					$this->args['before']['priority']
				);
			}

			if ( ! empty( $this->args['after'] ) ) {
				add_action(
					$this->args['after']['hook'],
					array( $this, 'print_close_wrap' ),
					$this->args['after']['priority']
				);
			}
		}

		/**
		 * Add show button function to related hook
		 *
		 * @since  1.0.0
		 * @return void
		 */
		private function append_button() {

			if ( empty( $this->args['button'] ) ) {
				return;
			}

			add_action(
				$this->args['button']['hook'],
				array( $this, 'print_button' ),
				$this->args['button']['priority']
			);

		}

		/**
		 * Append open wrapper for quick view
		 *
		 * @since 1.0.0
		 */
		public function print_open_wrap() {
			echo $this->args['before']['format'];
		}

		/**
		 * Append open wrapper for quick view
		 *
		 * @since 1.0.0
		 */
		public function print_close_wrap() {
			echo $this->args['after']['format'];
		}

		/**
		 * Append quick view button to product listing template
		 *
		 * @since 1.0.0
		 */
		public function print_button() {

			global $post, $product;

			$btn_txt = apply_filters( 'cherry_wc_quick_view_text', __( 'Quick view', 'cherry-woocommerce-package' ) );
			$trigger = 'cherry-quick-view';

			printf( $this->args['button']['format'], $product->id, $btn_txt, $trigger );

			Cherry_WC_Assets::enqueue_local_js( array( 'cherry-woocommerce', 'magnific-popup' ) );
		}

		/**
		 * Process AJAX request and return quick view popup content
		 *
		 * @since 1.0.0
		 */
		public function ajax_callback() {

			$nonce = isset( $_REQUEST['_wpnonce'] ) ? esc_attr( $_REQUEST['_wpnonce'] ) : false;

			if ( ! wp_verify_nonce( $nonce, 'cherry_woocommerce' ) ) {
				die( 'Nonce not verifyed' );
			}

			$product_id = isset( $_REQUEST['product'] ) ? $_REQUEST['product'] : false;

			if ( ! $product_id ) {
				die( 'Product not found' );
			}

			if ( ! class_exists( 'WC_Product_Factory' ) ) {
				die( 'Product not found' );
			}

			add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'cherry_wc_product_add_to_cart_url' ), 10, 2 );

			global $product, $woocommerce, $post;

			$product_factory = new WC_Product_Factory();

			$post    = get_post( $product_id );
			$product = $product_factory->get_product( $product_id );

			setup_postdata( $post );

			cherry_wc_templater()->get_template_part( 'quick-view' );

			wp_reset_postdata();

			die();

		}

		public function cherry_wc_product_add_to_cart_url( $url, $product ) {

			$type = $product->product_type;
			$href = isset( $_REQUEST['href'] ) ? $_REQUEST['href'] : false;

			if ( 'simple' == $type ) {

				$url = $product->is_purchasable() && $product->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $product->id, $href ) ) : get_permalink( $product->id );

			} else if ( 'variation' == $type ) {

				$variation_data = array_map( 'urlencode', $product->variation_data );
				$url            = $product->is_purchasable() && $product->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( array_merge( array( 'variation_id' => $product->variation_id, 'add-to-cart' => $product->id ), $variation_data, $href ) ) ) : get_permalink( $product->id );

			} else if ( 'external' == $type ) {

				$url = $product->get_product_url();
			}
			return $url;
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
