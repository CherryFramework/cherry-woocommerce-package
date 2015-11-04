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
		 * Holder for shop sidebar ID
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public $shop_sidebar_id = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			$this->shop_sidebar_id = apply_filters( 'cherry_wc_shop_sidebar_id', 'sidebar-shop' );

			add_filter( 'loop_shop_per_page', array( $this, 'loop_per_page' ) );
			add_filter( 'loop_shop_columns', array( $this, 'loop_columns' ) );
			add_filter( 'woocommerce_before_shop_loop', array( $this, 'loop_wrapper_open' ), 99 );
			add_filter( 'woocommerce_after_single_product_summary', array( $this, 'loop_wrapper_open' ), 19 );
			add_filter( 'woocommerce_after_shop_loop', array( $this, 'loop_wrapper_close' ), 0 );
			add_filter( 'woocommerce_after_single_product_summary', array( $this, 'loop_wrapper_close' ), 21 );
			add_filter( 'cherry_current_object_id', array( $this, 'set_shop_page_id' ) );
			add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
			add_filter( 'cherry_wc_product_gallery_layout', array( $this, 'product_gallery_layout' ) );
			add_filter( 'cherry_get_page_layout', array( $this, 'shop_page_layouts' ) );
			add_filter( 'wp_get_attachment_image_attributes', array( $this, 'remove_srcset' ), 10 ,3 );

			add_action( 'woocommerce_before_template_part', array( $this, 'open_qty_wrap' ), 10, 4 );
			add_action( 'woocommerce_after_template_part', array( $this, 'close_qty_wrap' ), 10, 4 );

			add_action( 'widgets_init', array( $this, 'register_shop_sidebar' ) );

			add_filter( 'cherry_get_main_sidebar', array( $this, 'show_shop_sidebar' ) );

		}

		/**
		 * Temporary remove srcset attribute from single shop image.
		 *
		 * @since  1.0.0
		 * @param  array  $atts       default attributes array.
		 * @param  object $attachment image attachment post object.
		 * @param  array  $size       image size array.
		 * @return array
		 */
		public function remove_srcset( $atts, $attachment, $size ) {

			if ( ! isset( $atts['srcset'] )
				 || ! isset( $atts['data-is-shop-single'] )
				 || true !== $atts['data-is-shop-single'] ) {
				return $atts;
			}

			unset( $atts['srcset'] );

			return $atts;
		}

		/**
		 * Open quantity block wrappers.
		 * Hooked to woocommerce_before_template_part and processed only if is qty template
		 *
		 * @since  1.0.0
		 * @param  string $template_name current template name.
		 * @param  string $template_path current template path.
		 * @param  string $located       current template location.
		 * @param  array  $args          additional args, passed into template.
		 * @return void
		 */
		public function open_qty_wrap( $template_name, $template_path, $located, $args ) {

			if ( 'global/quantity-input.php' !== $template_name ) {
				return;
			}
			?>
			<div class="quantity-wrap">
				<div class="qty-controls">
					<span class="qty-controls-add"></span>
					<span class="qty-controls-remove"></span>
				</div>
			<?php
		}

		/**
		 * Close quantity block wrappers.
		 * Hooked to woocommerce_after_template_part and processed only if is qty template
		 *
		 * @since  1.0.0
		 * @param  string $template_name current template name.
		 * @param  string $template_path current template path.
		 * @param  string $located       current template location.
		 * @param  array  $args          additional args, passed into template.
		 * @return void
		 */
		public function close_qty_wrap( $template_name, $template_path, $located, $args ) {

			if ( 'global/quantity-input.php' !== $template_name ) {
				return;
			}
			?>
			</div>
			<?php
		}

		/**
		 * Rwegister sidabar for shop pages
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function register_shop_sidebar() {

			if ( function_exists( 'cherry_register_sidebar' ) ) {
				cherry_register_sidebar(
					array(
						'id'           => $this->shop_sidebar_id,
						'name'         => __( 'Shop Sidebar', 'cherry-wcoocommerce-package' ),
						'description'  => __( 'Main Shop Sidebar', 'cherry-wcoocommerce-package' ),
						'before_title' => '<h5 class="widget-title">',
						'after_title'  => '</h5>',
					)
				);
			}

		}

		/**
		 * Pass shop sidebar as main page sidebar for WooCommerce pages
		 *
		 * @since  1.0.0
		 * @param  string $sidebar mcurrent main sidebar name.
		 * @return string
		 */
		public function show_shop_sidebar( $sidebar ) {

			if ( ! function_exists( 'is_woocommerce' ) || ! is_woocommerce() ) {
				return $sidebar;
			}

			return $this->shop_sidebar_id;

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
			if ( is_product_taxonomy() ) {
				$per_page = cherry_wc_options()->get_option( 'shop-per-cat-page', 8 );
			} else {
				$per_page = cherry_wc_options()->get_option( 'shop-per-page', 8 );
			}
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

			if ( in_array( $layout, array( "$s-$c-$s", "$s-$s-$c", "$c-$s-$s" ) ) ) {
				$this->loop_cols = 2;
				return $this->loop_cols;
			}

			$this->loop_cols = $cols;

			return $this->loop_cols;

		}

		/**
		 * Get product gallery layout class
		 *
		 * @since  1.0.0
		 * @param  string $layout current layout.
		 * @return string
		 */
		public function product_gallery_layout( $layout ) {

			$cols = $this->get_shop_columns( 4 );

			switch ( $cols ) {
				case 2:
					return 'two-sidebars';

				case 3:
					return 'single-sidebar';

				case 4:
					return 'fullwidth';

				default:
					return $layout;
			}

		}

		/**
		 * Set shop page id from options as current page object for shop and product categories
		 *
		 * @since  1.0.0
		 * @param  int $id current object id.
		 * @return int
		 */
		public function set_shop_page_id( $id ) {

			if ( ! is_shop() ) {
				return $id;
			}

			return wc_get_page_id( 'shop' );

		}

		/**
		 * Get specific layouts from options for shop-related pages
		 *
		 * @since  1.0.0
		 * @param  string $layout layout type.
		 * @return string
		 */
		public function shop_page_layouts( $layout ) {

			if ( is_shop() ) {
				$layout = cherry_wc_options()->get_option( 'shop-loop-layout', 'no-sidebar' );
			} elseif ( is_product_taxonomy() ) {
				$layout = cherry_wc_options()->get_option( 'shop-category-layout', 'no-sidebar' );
			} elseif ( is_singular( 'product' ) ) {
				$layout = cherry_wc_options()->get_option( 'shop-single-layout', 'no-sidebar' );
			}

			return $layout;

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
