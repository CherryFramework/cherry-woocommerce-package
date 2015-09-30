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

	class Cherry_WC_Quick_View {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		function __construct() {
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'append_open_wrap' ), 0 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'append_close_wrap' ), 100 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'append_button' ), 99 );
		}

		/**
		 * Append open wrapper for quick view
		 *
		 * @since 1.0.0
		 */
		function append_open_wrap() {
			echo '<div class="cherry-thumb-wrap">';
		}

		/**
		 * Append open wrapper for quick view
		 *
		 * @since 1.0.0
		 */
		function append_close_wrap() {
			echo '</div>';
		}

		/**
		 * Append quick view button to product listing template
		 *
		 * @since 1.0.0
		 */
		function append_button() {

			global $post, $product;

			$btn_tex = apply_filters( 'cherry_wc_quick_view_text', __( 'Quick view', 'cherry-woocommerce-package' ) );

			echo '<span class="btn cherry-quick-view" data-product="' . $product->id . '">' . $btn_tex . '</span>';

			Cherry_WC_Assets::enqueue_local_js( array( 'cherry-woocommerce', 'magnific-popup' ) );
		}

		/**
		 * Process AJAX request and return quick view popup content
		 *
		 * @since 1.0.0
		 */
		function ajax_callback() {

			$nonce = isset( $_REQUEST['_wpnonce'] ) ? esc_attr( $_REQUEST['_wpnonce'] ) : false;

			if ( ! wp_verify_nonce( $nonce, 'cherry_woocommerce' ) ) {
				die( 'Nonce not verifyed' );
			}

			$product_id = isset( $_REQUEST['product'] ) ? $_REQUEST['product'] : false;

			if ( ! $product_id ) {
				die( 'Product not found' );
			}

			if ( !class_exists( 'WC_Product_Factory' ) ) {
				die( 'Product not found' );
			}

			global $product, $woocommerce, $post;

			$product_factory = new WC_Product_Factory();

			$post    = get_post( $product_id );
			$product = $product_factory->get_product( $product_id );

			setup_postdata( $post );

			cherry_wc_templater()->get_template_part( 'quick-view' );

			wp_reset_postdata();

			die();

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance )
				self::$instance = new self;
			return self::$instance;
		}

	}

}
