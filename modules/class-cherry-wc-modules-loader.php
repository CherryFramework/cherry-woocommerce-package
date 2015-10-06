<?php
/**
 * Modules loader
 *
 * @package   Cherry_WooCommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Modules_Loader' ) ) {

	/**
	 * Existing modules loader class
	 */
	class Cherry_WC_Modules_Loader {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Existing modules
		 * @var array
		 */
		private $modules = array();

		/**
		 * Modules loading queue
		 * @var array
		 */
		private $queue = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {

			// register existing modules
			$this->modules = array(
				'quick-view' => array(
					'load_on' => 'woocommerce_before_shop_loop_item',
					'file'    => cherry_wc_package()->plugin_dir( 'modules/class-cherry-wc-quick-view.php' ),
					'class'   => 'Cherry_WC_Quick_View',
					'ajax'    => array(
						'action'   => 'cherry_wc_quick_view',
						'callback' => 'ajax_callback',
					),
				),
				'video-tab' => array(
					'load_on' => 'init',
					'file'    => cherry_wc_package()->plugin_dir( 'modules/class-cherry-wc-product-video-tab.php' ),
					'class'   => 'Cherry_WC_Product_Video',
					'ajax'    => false,
				),
				'yith-addons' => array(
					'load_on' => 'after_setup_theme',
					'file'    => cherry_wc_package()->plugin_dir( 'modules/class-cherry-wc-yith-addons.php' ),
					'class'   => 'Cherry_WC_YITH_Addons',
					'ajax'    => false,
				),
				'account-dropdown' => array(
					'load_on' => 'after_setup_theme',
					'file'    => cherry_wc_package()->plugin_dir( 'modules/class-cherry-wc-account-dropdown.php' ),
					'class'   => 'Cherry_WC_Account_Dropdown',
					'ajax'    => false,
				),
				'cart-dropdown' => array(
					'load_on' => 'after_setup_theme',
					'file'    => cherry_wc_package()->plugin_dir( 'modules/class-cherry-wc-cart-dropdown.php' ),
					'class'   => 'Cherry_WC_Cart_Dropdown',
					'ajax'    => false,
				),
				'product-gallery' => array(
					'load_on' => 'woocommerce_before_single_product',
					'file'    => cherry_wc_package()->plugin_dir( 'modules/class-cherry-wc-single-product-gallery.php' ),
					'class'   => 'Cherry_WC_Single_Product_Gallery',
					'ajax'    => false,
				),
				'product-sharing' => array(
					'load_on' => 'woocommerce_before_single_product',
					'file'    => cherry_wc_package()->plugin_dir( 'modules/class-cherry-wc-product-sharing.php' ),
					'class'   => 'Cherry_WC_Product_Sharing',
					'ajax'    => false,
				),
			);

			$this->add_ajax_handlers();
			$this->load_modules();
		}

		/**
		 * Add required AJAX handlers for loaded modules
		 *
		 * @since  1.0.0
		 */
		function add_ajax_handlers() {

			foreach ( $this->modules as $module => $data ) {

				if ( empty( $data['ajax'] ) ) {
					continue;
				}
				if ( $this->is_ajax_action( $data['ajax']['action'] ) ) {

					include_once $data['file'];
					$callback = $data['class']::get_instance();

					add_action(
						'wp_ajax_' . $data['ajax']['action'],
						array( $callback, $data['ajax']['callback'] )
					);
					add_action(
						'wp_ajax_nopriv_' . $data['ajax']['action'],
						array( $callback, $data['ajax']['callback'] )
					);
				}
			}

		}

		/**
		 * Load selected modue
		 *
		 * @since  1.0.0
		 * @param string $module module name to load.
		 * @return object         module class instance
		 */
		public function load( $module = null ) {

			if ( ! $module ) {
				return;
			}

			if ( ! array_key_exists( $module, $this->modules ) ) {
				return;
			}

			$data = $this->modules[ $module ];

			if ( ! file_exists( $data['file'] ) ) {
				return;
			}

			include_once $data['file'];

			return $data['class']::get_instance();

		}

		/**
		 * Check if is specific AJAX action
		 *
		 * @since 1.0.0
		 * @param string $action action name.
		 * @return boolean
		 */
		public function is_ajax_action( $action = null ) {

			// false if not is AJAX request
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				return false;
			}

			// true if is AJAX and action not provided
			if ( null == $action ) {
				return true;
			}

			// true if is provided action doing right now
			if ( isset( $_REQUEST['action'] ) && $action == $_REQUEST['action'] ) {
				return true;
			}

			return false;

		}

		/**
		 * Load all modules
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function load_modules() {
			foreach ( $this->modules as $module => $data ) {
				$this->queue[ $data['load_on'] ][] = $module;
				add_action( $data['load_on'], array( $this, 'load_current' ) );
			}
		}

		/**
		 * Load current module
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function load_current() {

			$current = current_filter();

			if ( empty( $this->queue[ $current ] ) ) {
				return;
			}

			foreach ( $this->queue[ $current ] as $module ) {
				$instance = $this->load( $module );
			}

			// Prevent from fires the same action couple times
			remove_filter( $current, array( $this, 'load_current' ) );

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

	/**
	 * Get instance of module loader class
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function cherry_wc_modules_loader() {
		return Cherry_WC_Modules_Loader::get_instance();
	}

	cherry_wc_modules_loader();
}
