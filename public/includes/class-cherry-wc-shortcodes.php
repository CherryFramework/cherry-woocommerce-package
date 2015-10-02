<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Shortcodes' ) ) {

	/**
	 * Define plugin specific shortcodes
	 */
	class Cherry_WC_Shortcodes {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * The array of arguments for template file.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		private $macros_data = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {

			// Register shortcode by default to use it without cherry shortcodes
			add_action( 'init', array( $this, 'register_shortcode' ) );

			// Add shortcode to cherry shortcodes dialog
			add_filter( 'cherry_shortcodes/data/shortcodes', array( $this, 'register_shortcodes' ) );
			add_filter( 'cherry_templater/data/shortcodes',  array( $this, 'shortcodes' ) );

			// Register shortcode for templater
			add_filter( 'cherry_templater_target_dirs', array( $this, 'add_target_dir' ), 11 );
			add_filter( 'cherry_templater_macros_buttons', array( $this, 'add_macros_buttons' ), 11, 2 );

		}

		/**
		 * Registers the Cherry WooCommerce shortcodes
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register_shortcode() {
			add_shortcode( 'shop_categories', array( $this, 'categories' ) );
		}

		/**
		 * Register shop shortcodes for editor
		 *
		 * @since 1.0.0
		 * @param array $shortcodes existing shortcodes array.
		 * @return array
		 */
		public function register_shortcodes( $shortcodes ) {

			$shop_shortcodes = array(
				'shop_categories' => array(
					'name'    => __( 'Shop Categories', 'cherry-woocommerce-package' ),
					'type'    => 'single',
					'group'   => 'content',
					'atts'    => array(
						'only_top'   => array(
							'type'    => 'bool',
							'default' => 'yes',
							'name'    => __( 'Show only parent', 'cherry-woocommerce-package' ),
							'desc'    => __( 'Show only 1st level categories', 'cherry-woocommerce-package' ),
						),
						'number'     => array(
							'type'    => 'number',
							'min'     => -1,
							'max'     => 100,
							'step'    => 1,
							'default' => -1,
							'name'    => __( 'Categories number', 'cherry-woocommerce-package' ),
							'desc'    => __( 'Specify number of categories that you want to show. Enter -1 to get all', 'cherry-woocommerce-package' ),
						),
						'orderby'    => array(
							'type'   => 'select',
							'values' => array(
								'none'  => __( 'None', 'cherry-woocommerce-package' ),
								'id'    => __( 'Category ID', 'cherry-woocommerce-package' ),
								'count' => __( 'Products count', 'cherry-woocommerce-package' ),
								'name'  => __( 'Category name', 'cherry-woocommerce-package' ),
								'slug'  => __( 'Category slug', 'cherry-woocommerce-package' ),
							),
							'default' => 'name',
							'name'    => __( 'Order by', 'cherry-woocommerce-package' ),
							'desc'    => __( 'Order categories by', 'cherry-woocommerce-package' ),
						),
						'order'      => array(
							'type'   => 'select',
							'values' => array(
								'desc' => __( 'Descending', 'cherry-woocommerce-package' ),
								'asc'  => __( 'Ascending', 'cherry-woocommerce-package' ),
							),
							'default' => 'DESC',
							'name'    => __( 'Order', 'cherry-woocommerce-package' ),
							'desc'    => __( 'Categories order', 'cherry-woocommerce-package' ),
						),
						'columns'    => array(
							'type'    => 'number',
							'min'     => 1,
							'max'     => 6,
							'step'    => 1,
							'default' => 3,
							'name'    => __( 'Columns number', 'cherry-woocommerce-package' ),
							'desc'    => __( 'Specify number of columns to show', 'cherry-woocommerce-package' ),
						),
						'hide_empty' => array(
							'type'    => 'bool',
							'default' => 'yes',
							'name'    => __( 'Hide empty categories', 'cherry-woocommerce-package' ),
							'desc'    => __( 'Show only 1st level categories', 'cherry-woocommerce-package' ),
						),
						'parent'     => array(
							'default' => '',
							'name'    => __( 'Categories from', 'cherry-shortcodes' ),
							'desc'    => __( 'Show direct children of selected category post (enter category ID)', 'cherry-woocommerce-package' ),
						),
						'ids'        => array(
							'default' => '',
							'name'    => __( 'Categories ID\'s', 'cherry-woocommerce-package' ),
							'desc'    => __( 'Enter comma separated ID\'s of the categories that you want to show', 'cherry-woocommerce-package' ),
						),
						'template' => array(
							'type'   => 'select',
							'values' => array(
								'default.tmpl' => 'default.tmpl',
							),
							'default' => 'default.tmpl',
							'name'    => __( 'Template', 'cherry-woocommerce-package' ),
							'desc'    => __( 'Shortcode template', 'cherry-woocommerce-package' ),
						),
					),
					'desc'     => __( 'Show product categories list', 'cherry-woocommerce-package' ),
					'icon'     => 'folder-open',
					'function' => array( $this, 'categories' ),
				),
			);

			return array_merge( $shortcodes, $shop_shortcodes );

		}

		/**
		 * Adds Cherry WooCommerce template directory to shortcodes templater
		 *
		 * @since 1.0.0
		 * @param array $target_dirs existing target dirs.
		 * @return array
		 */
		public function add_target_dir( $target_dirs ) {

			array_push( $target_dirs, cherry_wc_package()->plugin_dir() );
			return $target_dirs;

		}

		/**
		 * Add shortcods macros buttons to templater
		 *
		 * @since 1.0.0
		 * @param array $macros_buttons current buttons array.
		 * @param string $shortcode shortcode name.
		 * @return array
		 */
		public function add_macros_buttons( $macros_buttons, $shortcode ) {

			switch ( $shortcode ) {
				case 'shop_categories':
					$macros_buttons = array(
						'catimage' => array(
							'id'    => 'cherry_image',
							'value' => __( 'Featured image', 'cherry-woocommerce-package' ),
							'open'  => '%%CAT_IMAGE%%',
							'close' => '',
						),
						'catname' => array(
							'id'    => 'cherry_name',
							'value' => __( 'Name', 'cherry-woocommerce-package' ),
							'open'  => '%%CAT_]NAME%%',
							'close' => '',
						),
						'cat_desc' => array(
							'id'    => 'cherry_desc',
							'value' => __( 'Description', 'cherry-woocommerce-package' ),
							'open'  => '%%CAT_DESC%%',
							'close' => '',
						),
						'cat_count' => array(
							'id'    => 'cherry_count',
							'value' => __( 'Products count', 'cherry-woocommerce-package' ),
							'open'  => '%%CAT_COUNT%%',
							'close' => '',
						),
						'cat_url' => array(
							'id'    => 'cherry_cat_url',
							'value' => __( 'Category permalink', 'cherry-woocommerce-package' ),
							'open'  => '%%CAT_URL%%',
							'close' => '',
						),
					);
					break;

			}

			return $macros_buttons;

		}

		/**
		 * Categories shortcode callback function
		 *
		 * @since 1.0.0
		 * @param array  $atts shortcode attributes array.
		 * @param string $content shortcode inner content.
		 * @return string
		 */
		public function categories( $atts, $content = null ) {

			$atts = shortcode_atts(
				array(
					'only_top'   => 'yes',
					'number'     => -1,
					'orderby'    => 'name',
					'order'      => 'asc',
					'columns'    => 3,
					'hide_empty' => 'yes',
					'parent'     => '',
					'ids'        => '',
					'template'   => 'default.tmpl',
				),
				$atts,
				'shop_categories'
			);

			if ( ! empty( $atts['ids'] ) ) {
				$ids = explode( ',', $atts['ids'] );
				$ids = array_map( 'trim', $ids );
			} else {
				$ids = array();
			}

			$hide_empty = ( 'yes' === $atts['hide_empty'] ) ? 1 : 0;
			$parent     = ( ! empty( $atts['parent'] ) ) ? absint( $atts['parent'] ) : '';
			if ( -1 !== $atts['number'] ) {
				$number = absint( $atts['number'] );
			} else {
				$number = -1;
			}

			$args = array(
				'orderby'    => esc_attr( $atts['orderby'] ),
				'order'      => esc_attr( $atts['order'] ),
				'hide_empty' => $hide_empty,
				'include'    => $ids,
				'pad_counts' => true,
				'child_of'   => $parent,
			);

			if ( 'yes' === $atts['only_top'] ) {
				$args['parent'] = 0;
			}

			$product_categories = get_terms( 'product_cat', $args );

			if ( 0 <= $number ) {
				$product_categories = array_slice( $product_categories, 0, $number );
			}

			ob_start();

			$macros    = '/%%([a-zA-Z_]+[^%]{2})(=[\'\"]([a-zA-Z0-9-_\s]+)[\'\"])?%%/';
			$callbacks = $this->setup_template_data( $atts );
			$template  = cherry_wc_templater()->get_tmpl_content( esc_attr( $atts['template'] ), 'shop_categories' );

			/**
			 * Fires before start main categories shortcode output
			 */
			do_action( 'cherry_wc_before_categories_list' );

			echo '<div class="cat-list">';

			foreach ( $product_categories as $cat ) {
				$callbacks->set_object( $cat );
				$content = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $template );
				$callbacks->clear_object();

				echo $content;
			}

			echo '</div>';

			/**
			 * Fires immediately after main categories shortcode output ended
			 */
			do_action( 'cherry_wc_after_categories_list' );

			return ob_get_clean();

		}

		/**
		 * Callback to replace macros with data
		 *
		 * @since 1.0.0
		 * @param array $matches found macros.
		 * @return string
		 */
		public function replace_callback( $matches ) {

			if ( ! is_array( $matches ) ) {
				return '';
			}

			if ( empty( $matches ) ) {
				return '';
			}

			$key = strtolower( $matches[1] );

			// If key not found in data -return nothing
			if ( ! isset( $this->macros_data[ $key ] ) ) {
				return '';
			}

			$callback = $this->macros_data[ $key ];

			if ( ! is_callable( $callback ) ) {
				return;
			}

			// If found parameters and has correct callback - process it
			if ( isset( $matches[3] ) ) {
				return call_user_func( $callback, $matches[3] );
			}

			return call_user_func( $callback );

		}

		/**
		 * Prepare template data to replace
		 *
		 * @since 1.0.0
		 * @param array $atts output attributes.
		 * @return object
		 */
		public function setup_template_data( $atts ) {

			require_once(
				cherry_wc_package()->plugin_dir( 'public/includes/class-cherry-wc-template-callbacks.php' )
			);

			$callbacks = new Cherry_WC_Template_Callbacks( $atts );

			$data = array(
				'cat_image' => array( $callbacks, 'get_cat_image' ),
				'cat_name'  => array( $callbacks, 'get_cat_name' ),
				'cat_desc'  => array( $callbacks, 'get_cat_desc' ),
				'cat_count' => array( $callbacks, 'get_cat_count' ),
				'cat_url'   => array( $callbacks, 'get_cat_url' ),
			);

			$this->macros_data = apply_filters( 'cherry_woocommerce_data_callbacks', $data, $atts );

			return $callbacks;

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

	Cherry_WC_Shortcodes::get_instance();

}
