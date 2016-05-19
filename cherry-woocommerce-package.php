<?php
/**
 * Plugin Name: Cherry WooCommerce package
 * Plugin URI:  http://www.cherryframework.com/
 * Description: Extends Cherryframework for WooCommerce
 * Version:     1.0.3
 * Author:      Cherry Team
 * Author URI:  http://www.cherryframework.com/
 * Text Domain: cherry-woocommerce-package
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 * @package  Cherry WooCommerce Package
 * @category Core
 * @author   Cherry Team
 * @license  GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Woocommerce_Package' ) ) {

	/**
	 * Main plugin class
	 */
	class Cherry_Woocommerce_Package {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Trigger checks is woocoomerce active or not
		 *
		 * @since 1.0.0
		 * @var   bool
		 */
		public $has_woocommerce = null;

		/**
		 * Holder for plugin folder URL
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public $plugin_url = null;

		/**
		 * Holder for plugin folder path
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public $plugin_dir = null;

		/**
		 * Plugin version
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		public $version = '1.0.3';

		/**
		 * Class constructor
		 */
		function __construct() {

			if ( ! $this->has_woocommerce() ) {
				return false;
			}

			// Internationalize the text strings used.
			add_action( 'plugins_loaded', array( $this, 'lang' ), 2 );

			$this->load_core();

		}

		/**
		 * Load plugin core files
		 *
		 * @since  1.0.0
		 * @return void
		 */
		private function load_core() {

			$this->load_admin();
			$this->load_public();

		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		public function lang() {
			load_plugin_textdomain(
				'cherry-woocommerce-package',
				false, dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
		}

		/**
		 * Load admin-only required files
		 *
		 * @since  1.0.0
		 * @return void
		 */
		private function load_admin() {

			if ( ! is_admin() ) {
				return;
			}

			require_once $this->plugin_dir( 'admin/includes/class-cherry-wc-admin-hooks.php' );
			require_once $this->plugin_dir( 'admin/includes/class-cherry-update/class-cherry-plugin-update.php' );

			$Cherry_Plugin_Update = new Cherry_Plugin_Update();
			$Cherry_Plugin_Update->init(
				array(
					'version'         => $this->version,
					'slug'            => 'cherry-woocommerce-package',
					'repository_name' => 'cherry-woocommerce-package',
				)
			);
		}

		/**
		 * Load globally included files
		 *
		 * @since  1.0.0
		 * @return void
		 */
		private function load_public() {

			require_once $this->plugin_dir( 'public/includes/class-cherry-wc-options.php' );
			require_once $this->plugin_dir( 'public/includes/class-cherry-wc-assets.php' );
			require_once $this->plugin_dir( 'public/includes/class-cherry-wc-templater.php' );
			require_once $this->plugin_dir( 'public/includes/class-cherry-wc-shortcodes.php' );
			require_once $this->plugin_dir( 'public/includes/class-cherry-wc-frontend-hooks.php' );
			require_once $this->plugin_dir( 'modules/class-cherry-wc-modules-loader.php' );
		}

		/**
		 * Get plugin URL (or some plugin dir/file URL)
		 *
		 * @since  1.0.0
		 * @param  string $path dir or file inside plugin dir.
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			if ( null != $path ) {
				return $this->plugin_url . $path;
			}

			return $this->plugin_url;

		}

		/**
		 * Get plugin dir path (or some plugin dir/file path)
		 *
		 * @since  1.0.0
		 * @param  string $path dir or file inside plugin dir.
		 * @return string
		 */
		public function plugin_dir( $path = null ) {

			if ( ! $this->plugin_dir ) {
				$this->plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			if ( null != $path ) {
				return $this->plugin_dir . $path;
			}

			return $this->plugin_dir;

		}

		/**
		 * Check if WooCommerce is active
		 *
		 * @since  1.0.0
		 * @return bool
		 */
		public function has_woocommerce() {

			if ( null == $this->has_woocommerce ) {
				$this->has_woocommerce = in_array(
					'woocommerce/woocommerce.php',
					apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
				);
			}

			return $this->has_woocommerce;

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
	 * Get base plugin class instance
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function cherry_wc_package() {
		return Cherry_Woocommerce_Package::get_instance();
	}

	/**
	 * Create plugin instance
	 */
	cherry_wc_package();

}
