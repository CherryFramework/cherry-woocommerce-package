<?php
/**
 * Manage plugin assets
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Assets' ) ) {

	class Cherry_WC_Assets {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Localized data holder
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		public static $localized = array();

		function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 15 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_global_assets' ), 20 );

			add_filter( 'cherry_compiler_static_css', array( $this, 'add_style_to_compiler' ) );
			add_action( 'cherry_dynamic_styles_before', array( $this, 'add_dynamic_styles' ) );

			add_action( 'init', array( $this, 'disable_woo_default_styles' ) );

		}

		/**
		 * Disable default WooCommerce styling, if theme force it
		 *
		 * @since  1.0.0
		 * @return bool false|void
		 */
		public function disable_woo_default_styles() {

			$disabled = apply_filters( 'cherry_woocommerce_disbale_default_css', false );

			if ( true !== $disabled ) {
				return false;
			}

			add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		}

		/**
		 * Register reauired assets to include when needed
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function register_assets() {

			// styles
			foreach ( $this->get_styles() as $style ) {
				wp_register_style( $style['handle'], $style['url'], $style['deps'], $style['ver'] );
			}

			// scripts
			foreach ( $this->get_scripts() as $script ) {

				if ( wp_script_is( $script['handle'], 'registered' ) ) {
					continue;
				}

				wp_register_script( $script['handle'], $script['url'], $script['deps'], $script['ver'], true );
			}


			self::$localized = array(
				'cherry-woocommerce' => apply_filters(
					'cherry_woocommerce_localized_strings',
					array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'nonce'   => wp_create_nonce( 'cherry_woocommerce' ),
						'loading' => __( 'Loading...', 'cherry-woocommerce-package' ),
					)
				),
			);

		}

		/**
		 * Get styles array to register
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_styles() {

			if ( file_exists( get_stylesheet_directory() . '/assets/css/cherry-woocommerce-theme.css' ) ) {
				$theme_css = get_stylesheet_directory_uri() . '/assets/css/cherry-woocommerce-theme.css';
			} else {
				$theme_css = cherry_wc_package()->plugin_url( 'public/assets/css/cherry-woocommerce-theme.css' );
			}

			$styles = array(
				array(
					'handle' => 'cherry-woocommerce-core',
					'url'    => cherry_wc_package()->plugin_url( 'public/assets/css/cherry-woocommerce-core.css' ),
					'deps'   => false,
					'ver'    => cherry_wc_package()->version,
				),
				array(
					'handle' => 'cherry-woocommerce-theme',
					'url'    => $theme_css,
					'deps'   => false,
					'ver'    => cherry_wc_package()->version,
				),
			);

			return $styles;

		}

		/**
		 * Get scripts array to register
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_scripts() {

			$scripts = array(
				array(
					'handle' => 'cherry-woocommerce',
					'url'    => cherry_wc_package()->plugin_url( 'public/assets/js/min/cherry-woocommerce-script.min.js' ),
					'deps'   => array( 'jquery' ),
					'ver'    => cherry_wc_package()->version,
				),
				array(
					'handle' => 'magnific-popup',
					'url'    => cherry_wc_package()->plugin_url( 'public/assets/js/min/jquery.magnific-popup.min.js' ),
					'deps'   => array( 'jquery' ),
					'ver'    => '1.0.0',
				),
				array(
					'handle' => 'cherry-cycle2',
					'url'    => cherry_wc_package()->plugin_url( 'public/assets/js/min/jquery.cycle2.min.js' ),
					'deps'   => array( 'jquery' ),
					'ver'    => '2.1.6',
				),
				array(
					'handle' => 'cherry-cycle2-carousel',
					'url'    => cherry_wc_package()->plugin_url( 'public/assets/js/min/jquery.cycle2.carousel.min.js' ),
					'deps'   => array( 'jquery' ),
					'ver'    => '2.1.6',
				),
				array(
					'handle' => 'cherry-elevatezoom',
					'url'    => cherry_wc_package()->plugin_url( 'public/assets/js/min/jquery.elevatezoom.min.js' ),
					'deps'   => array( 'jquery' ),
					'ver'    => '2.1.6',
				),
			);

			return $scripts;
		}

		/**
		 * Load WooCommerce specific dynamic CSS
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function add_dynamic_styles() {
			include cherry_wc_package()->plugin_dir( 'public/assets/css/dynamic-style.css' );
		}

		/**
		 * Enqueue assets that appers on each page
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function enqueue_global_assets() {
			wp_enqueue_style( 'cherry-woocommerce-core' );
			wp_enqueue_style( 'cherry-woocommerce-theme' );
		}

		/**
		 * Enqueue scripts by call
		 *
		 * @param  array  $handles required scripts to enqueue
		 * @return void
		 */
		public static function enqueue_local_js( $handles = array() ) {

			if ( ! is_array( $handles ) ) {
				return;
			}

			foreach ( $handles as $handle ) {
				wp_enqueue_script( $handle );

				if ( array_key_exists( $handle, self::$localized ) ) {
					wp_localize_script(
						$handle,
						str_replace( ' ', '', ucwords( str_replace( '-', ' ', $handle ) ) ),
						self::$localized[ $handle ]
					);
				}
			}

		}

		/**
		 * Pass chart style handle to CSS compiler
		 *
		 * @since  1.0.0
		 * @param  array $handles CSS handles to compile
		 */
		public function add_style_to_compiler( $handles ) {

			$to_compiler = array();

			foreach ( $this->get_styles() as $style ) {
				$to_compiler[ $style['handle'] ] = $style['url'];
			}

			$handles = array_merge( $to_compiler, $handles );

			return $handles;
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

	Cherry_WC_Assets::get_instance();

}
