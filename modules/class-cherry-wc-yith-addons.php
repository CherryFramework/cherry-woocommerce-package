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
		 * Custom addons argumnets for products loop
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		private $loop_args = array();

		/**
		 * Custom addons argumnets for single product page
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		private $single_args = array();

		/**
		 * Check if compare is avaliable
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $compare = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {

			$this->compare = array(
				'loop'   => get_option( 'yith_woocompare_compare_button_in_products_list' ),
				'single' => get_option( 'yith_woocompare_compare_button_in_product_page' ),
			);

			add_action( 'wp_head', array( $this, 'compare_css' ), 99 );

			$this->loop_args = apply_filters(
				'cherry_wc_addons_loop_args',
				array(
					'before' => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 25,
						'format'   => '<div class="product-addons">',
					),
					'after' => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 26,
						'format'   => '</div>',
					),
					'wishlist' => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 25,
						'format'   => '%s',
					),
					'compare'  => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 26,
						'format'   => '%s',
					),
				)
			);

			$this->single_args = apply_filters(
				'cherry_wc_addons_single_args',
				array(
					'before' => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 25,
						'format'   => '<div class="product-addons">',
					),
					'after' => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 26,
						'format'   => '<div class="product-addons">',
					),
					'wishlist' => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 25,
						'format'   => '%s',
					),
					'compare'  => array(
						'hook'     => 'woocommerce_after_shop_loop_item',
						'priority' => 26,
						'format'   => '%s',
					),
				)
			);

			add_action( 'init', array( $this, 'attach_loop_hooks' ) );
		}

		/**
		 * Attach related actions to hooks
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function attach_loop_hooks() {

			if ( 'yes' === $this->compare['loop'] ) {
				$this->_remove_filter(
					'woocommerce_after_shop_loop_item',
					'YITH_Woocompare_Frontend',
					'add_compare_link'
				);
			}

			$data = array(
				'before'   => 'add_loop_open',
				'wishlist' => 'add_loop_wishlist',
				'compare'  => 'add_loop_compare',
				'after'    => 'add_loop_close',
			);

			foreach ( $data as $where => $callback ) {

				if ( empty( $this->loop_args[ $where ] ) ) {
					continue;
				}

				add_action(
					$this->loop_args[ $where ]['hook'],
					array( $this, $callback ),
					$this->loop_args[ $where ]['priority']
				);
			}

		}

		/**
		 * Print loop open wrap
		 *
		 * @since  1.0.0
		 * @return void|bool false
		 */
		public function add_loop_open() {

			if ( ! isset( $this->loop_args['before']['format'] ) ) {
				return false;
			}

			echo $this->loop_args['before']['format'];

		}

		/**
		 * Print loop close wrap
		 *
		 * @since  1.0.0
		 * @return void|bool false
		 */
		public function add_loop_close() {

			if ( ! isset( $this->loop_args['after']['format'] ) ) {
				return false;
			}

			echo $this->loop_args['after']['format'];

		}

		/**
		 * Print wishlist button at products loop
		 *
		 * @since  1.0.0
		 * @return void|bool false
		 */
		public function add_loop_wishlist() {

			if ( ! defined( 'YITH_WCWL' ) ) {
				return false;
			}

			$content = do_shortcode(
				apply_filters( 'cherry_wc_wishlist_shortcode', '[yith_wcwl_add_to_wishlist]' )
			);

			printf( $this->loop_args['wishlist']['format'], $content );

		}

		/**
		 * Print compare button in products loop
		 *
		 * @since 1.0.0
		 * @return void|bool false
		 */
		public function add_loop_compare() {

			if ( 'yes' !== $this->compare['loop'] ) {
				return false;
			}

			$this->custom_compare_link();

		}

		/**
		 * Print custom compare link
		 *
		 * @since 1.0.0
		 * @return void|null
		 */
		public function custom_compare_link() {

			global $product;
			$product_id = isset( $product->id ) ? $product->id : 0;

			// return if product doesn't exist
			if ( empty( $product_id )
				|| apply_filters( 'yith_woocompare_remove_compare_link_by_cat', false, $product_id )
			) {
				return null;
			}

			$is_button = get_option( 'yith_woocompare_is_button', 'button' );

			if ( ! isset( $button_text ) || 'default' == $button_text ) {

				$button_text = get_option(
					'yith_woocompare_button_text',
					__( 'Compare', 'cherry-woocommerce-package' )
				);

				$button_text = function_exists( 'icl_translate' )
								? icl_translate( 'Plugins', 'plugin_yit_compare_button_text', $button_text )
								: $button_text;
			}

			$action_add = 'yith-woocompare-add-product';
			$url_args   = array(
				'action' => $action_add,
				'id'     => $product_id,
			);

			$url = apply_filters(
				'yith_woocompare_add_product_url',
				wp_nonce_url( esc_url_raw( add_query_arg( $url_args ) ), $action_add )
			);

			global $wp_query;

			$wp_query->query_vars['cherry_wc_compare_button'] = array(
				'url'         => $url,
				'is_button'   => $is_button,
				'product_id'  => $product_id,
				'button_text' => $button_text,
			);

			cherry_wc_templater()->get_template_part( 'compare-button' );
			unset( $wp_query->query_vars['cherry_wc_compare_button'] );

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
		 * Remove attached filter from hook if function passed as method
		 *
		 * @since 1.0.0
		 * @param string $tag hook name.
		 * @param string $class class name.
		 * @param string $method method name.
		 * @return bool
		 */
		public function _remove_filter( $tag, $class, $method ) {

			$filters = $GLOBALS['wp_filter'][ $tag ];

			if ( empty( $filters ) ) {
				return true;
			}

			foreach ( $filters as $priority => $filter ) {

				foreach ( $filter as $identifier => $function ) {

					if ( is_array( $function )
						&& is_a( $function['function'][0], $class )
						&& $method === $function['function'][1]
					) {
						remove_filter(
							$tag,
							array( $function['function'][0], $method ),
							$priority
						);

						return true;
					}
				}
			}

			return false;

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
