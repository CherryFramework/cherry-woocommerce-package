<?php
/**
 * Class description
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Templater' ) ) {

	/**
	 * Plugin templates management
	 */
	class Cherry_WC_Templater {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Get template part (for templates like the shop-loop).
		 *
		 * @since 1.0.0
		 * @param  string  $name  template name.
		 * @return void
		 */
		public function get_template_part( $name, $from_wc = false ) {

			if ( !$name ) {
				return false;
			}

			$template = '';

			// Look in yourtheme/name.php and yourtheme/woocommerce/name.php
			$template = locate_template( array( "{$name}.php", "/woocommerce/{$name}.php" ) );

			// look in woocommerce templates if needed
			if ( $from_wc && ! $template ) {
				$template = WC()->plugin_path() . "/templates/{$name}.php";
			}

			// Get template file from plugin templates
			if ( ! $template ) {
				$template = cherry_wc_package()->plugin_dir( "templates/{$name}.php" );
			}
			// Allow 3rd party plugin filter template file from their plugin
			$template = apply_filters( 'cherry_woocommerce_get_template_part', $template, $name );

			if ( $template && file_exists( $template ) ) {
				load_template( $template, false );
			}

		}

		/**
		 * Register new static by name
		 *
		 * @since 1.0.0
		 * @param string $filename static filname to register.
		 * @return void
		 */
		public function register_static( $filename = null ) {

			if ( ! $filename ) {
				return;
			}

			$static_file = apply_filters( 'cherry_wc_static_' . $filename, $filename );

			if ( defined( 'CHILD_DIR' ) ) {
				$child_dir = CHILD_DIR;
			} else {
				$child_dir = get_stylesheet_directory();
			}

			$static_file_path = preg_replace( '#/+#', '/', trailingslashit( $child_dir ) . $static_file );

			// If file found in child theme - include it and break function.
			if ( file_exists( $static_file_path ) ) {
				require_once $static_file_path;
				return;
			}

			// If file was not found in child theme - search it in parent.
			if ( defined( 'PARENT_DIR' ) ) {
				$parent_dir = PARENT_DIR;
			} else {
				$parent_dir = get_template_directory();
			}

			$static_file_path = preg_replace( '#/+#', '/', trailingslashit( $parent_dir ) . $static_file );

			if ( file_exists( $static_file_path ) ) {
				require_once $static_file_path;
				return;
			}

			require_once cherry_wc_package()->plugin_dir( 'init/statics/' . $static_file );
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

	/**
	 * Get instance of templater class
	 *
	 * @since  1.0.0
	 * @return jbject
	 */
	function cherry_wc_templater() {
		return Cherry_WC_Templater::get_instance();
	}

	cherry_wc_templater();
}
