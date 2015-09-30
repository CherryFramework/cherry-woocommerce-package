<?php
/**
 * Add sharing buttons to single product page
 *
 * @package   cherry_woocommerce_package
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Product_Sharing' ) ) {

	/**
	 * Product sharing class
	 */
	class Cherry_WC_Product_Sharing {

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
			add_filter( 'cherry_woocommerce_localized_strings', array( $this, 'localize_js' ) );
			add_action( 'woocommerce_share', array( $this, 'sharing_frontend' ) );
		}

		/**
		 * Loacalize sharing-related strings
		 *
		 * @since 1.0.0
		 * @param array $strings localized strings.
		 * @return array
		 */
		public function localize_js( $strings ) {

			$strings['sharing_title'] = __( 'Share this', 'cherry-woocommerce-package' );
			return $strings;
		}

		/**
		 * Print product share block
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function sharing_frontend() {
			$sharedata = apply_filters(
				'cherry_wocommerce_sharing_services',
				array(
					'facebook'    => 'https://www.facebook.com/sharer/sharer.php?u=%1$s',
					'twitter'     => 'https://twitter.com/intent/tweet?url=%1$s&status=%2$s',
					'google-plus' => 'https://plus.google.com/share?url=%1$s',
					'pinterest'   => 'https://pinterest.com/pin/create/bookmarklet/?media=%3$s&url=%1$s&is_video=false&description=%2$s',
				)
			);

			$format = apply_filters(
				'cherry_wocommerce_share_button_format',
				'<div class="share-buttons_item"><a href="#" data-url="%2$s" class="share-buttons_link link-%1$s"><i class="fa fa-%1$s"></i></a></div>'
			);

			$url   = urlencode( get_permalink() );
			$text  = urlencode( get_the_title() . ' - ' . get_permalink() );
			$media = false;

			if ( has_post_thumbnail() ) {
				$media = wp_get_attachment_url( get_post_thumbnail_id() );
				$media = urlencode( $media );
			}

			echo '<div class="share-buttons">';
			foreach ( $sharedata as $net => $link ) {
				$link = sprintf( $link, $url, $text, $media );
				printf( $format, $net, $link );
			}
			echo '</div>';
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
