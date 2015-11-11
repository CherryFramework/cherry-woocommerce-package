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

			// Import-exposrt hooks
			add_filter( 'cherry_dm_export_database_tables', array( $this, 'export_termmmeta' ) );
			add_filter( 'cherry_data_manager_export_options', array( $this, 'shop_options_to_export' ) );
			add_filter( 'cherry_data_manager_options_ids', array( $this, 'shop_options_ids' ) );

			add_action( 'cherry_data_manager_update_featured_images', array( $this, 'update_term_thumb' ) );
			add_action( 'cherry_data_manager_process_terms', array( $this, 'remap_term_id' ) );

			// Admin UI hooks
			add_filter( 'cherry_document_link_attr', array( $this, 'change_documentation_link' ), 10 );

		}

		/**
		 * Change Cherry documentation link attributes for WooCommerce templates
		 *
		 * @since  1.0.0
		 * @param  array $link_attr default link attributes array.
		 * @return array
		 */
		function change_documentation_link( $link_attr ) {

			$link_attr['project']   = 'woocommerce';
			$link_attr['text_link'] = __( 'WooCommerce documentation', 'cherry-woocommerce-package' );

			return $link_attr;
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
		 * Update term thumbnails ID.
		 *
		 * @since  1.0.0
		 * @return void|bool false
		 */
		public function update_term_thumb() {

			$terms = get_terms( 'product_cat', array( 'hide_empty' => false ) );

			if ( empty( $terms ) ) {
				return false;
			}

			global $wpdb;
			$table = $wpdb->prefix . 'woocommerce_termmeta';

			foreach ( $terms as $term ) {

				$thumb = $wpdb->get_var( $wpdb->prepare(
					"
					SELECT meta_value
					FROM $table
					WHERE meta_key = %s AND woocommerce_term_id = %d
					",
					array( 'thumbnail_id', $term->term_id )
				) );

				if ( ! $thumb ) {
					continue;
				}

				if ( ! isset( $_SESSION['processed_posts'][ $thumb ] ) ) {
					continue;
				}

				$new_id = $_SESSION['processed_posts'][ $thumb ];

				$wpdb->update(
					$table,
					array( 'meta_value' => $new_id ),
					array( 'meta_key' => 'thumbnail_id', 'woocommerce_term_id' => $term->term_id ),
					array( '%d' ),
					array( '%s', '%d' )
				);

			}

		}

		/**
		 * Remap term IDs in wp_woocommerce_termmeta
		 *
		 * @since  1.0.0
		 * @return void|bool false
		 */
		public function remap_term_id() {

			global $wpdb;

			$table = $wpdb->prefix . 'woocommerce_termmeta';

			$termmeta = $wpdb->get_results(
				"
				SELECT *
				FROM $table
				"
			);

			if ( empty( $termmeta ) ) {
				return false;
			}

			foreach ( $termmeta as $row ) {

				if ( ! array_key_exists( $row->woocommerce_term_id, $_SESSION['processed_terms'] ) ) {
					continue;
				}

				$new_id = $_SESSION['processed_terms'][ $row->woocommerce_term_id ];

				$wpdb->update(
					$table,
					array( 'woocommerce_term_id' => $new_id ),
					array( 'meta_id' => $row->meta_id ),
					array( '%d' ),
					array( '%d' )
				);

			}

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
