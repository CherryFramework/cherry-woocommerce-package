<?php
/**
 * Define callbacks for admin-related hooks
 *
 * @package   Cherry_WooCommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Admin_Hooks' ) ) {

	/**
	 * Define callbacks for frontend-related hooks
	 */
	class Cherry_WC_Admin_Hooks {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {

			add_filter( 'cherry_dm_export_database_tables', array( $this, 'export_termmmeta' ) );
			add_filter( 'cherry_data_manager_export_options', array( $this, 'shop_options_to_export' ) );
			add_filter( 'cherry_data_manager_options_ids', array( $this, 'shop_options_ids' ) );

		}

		/**
		 * Pass woocommerce_termmeta into exported database tables
		 *
		 * @since  1.0.0
		 * @param  array $tables tables array to export.
		 * @return array
		 */
		public function export_termmmeta( $tables ) {

			$tables[] = 'woocommerce_termmeta';
			return $tables;

		}

		/**
		 * Pass option id's to rewrite
		 *
		 * @since  1.0.0
		 * @param  array $options default options array.
		 * @return array
		 */
		public function shop_options_ids( $options ) {

			$options[] = 'woocommerce_shop_page_id';
			$options[] = 'woocommerce_shop_page_display';
			$options[] = 'woocommerce_cart_page_id';
			$options[] = 'woocommerce_checkout_page_id';
			$options[] = 'woocommerce_terms_page_id';
			$options[] = 'woocommerce_myaccount_page_id';
			$options[] = 'yith_wcwl_wishlist_page_id';

			return $options;

		}

		/**
		 * Pass shop options to contant exposrter
		 *
		 * @since  1.0.0
		 * @param  array $options default options to export list.
		 * @return array
		 */
		public function shop_options_to_export( $options ) {

			$options[] = 'woocommerce_enable_myaccount_registration';
			$options[] = 'woocommerce_weight_unit';
			$options[] = 'woocommerce_dimension_unit';
			$options[] = 'woocommerce_enable_review_rating';
			$options[] = 'woocommerce_enable_review_rating';
			$options[] = 'yith_woocompare_is_button';
			$options[] = 'yith_woocompare_compare_button_in_product_page';
			$options[] = 'yith_woocompare_compare_button_in_products_list';
			$options[] = 'yith_woocompare_auto_open';
			$options[] = 'yith_woocompare_price_end';
			$options[] = 'yith_woocompare_add_to_cart_end';
			$options[] = 'yith_woocompare_image_size[width]';
			$options[] = 'yith_woocompare_image_size[height]';
			$options[] = 'woocommerce_enable_lightbox';
			$options[] = 'woocommerce_default_country';
			$options[] = 'woocommerce_currency';
			$options[] = 'woocommerce_currency_pos';
			$options[] = 'woocommerce_enable_ajax_add_to_cart';
			$options[] = 'shop_catalog_image_size';
			$options[] = 'shop_single_image_size';
			$options[] = 'shop_thumbnail_image_size';
			$options[] = 'woocommerce_price_display_suffix';
			$options[] = 'woocommerce_logout_endpoint';

			return $options;

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

	Cherry_WC_Admin_Hooks::get_instance();

}
