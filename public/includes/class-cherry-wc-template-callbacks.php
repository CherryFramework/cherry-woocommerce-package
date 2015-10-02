<?php
/**
 * Template callback functions for Cherry WooCOmmerce shortcodes
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Template_Callbacks' ) ) {

	/**
	 * Define template callbacks class
	 */
	class Cherry_WC_Template_Callbacks {

		/**
		 * Shortcode attributes array
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $atts = array();

		/**
		 * Current object holder
		 *
		 * @since 1.0.0
		 * @var object
		 */
		public $object = null;

		/**
		 * Constructor for the class
		 */
		function __construct( $atts ) {
			$this->atts = $atts;
		}

		/**
		 * Set current processed object data
		 *
		 * @since 1.0.0
		 * @param object $obj processed object.
		 * @return void
		 */
		public function set_object( $obj ) {
			$this->object = $obj;
		}

		/**
		 * Clear current object data
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function clear_object() {
			$this->object = null;
		}

		/**
		 * Get category featured image
		 *
		 * @since 1.0.0
		 * @param  string $size image size.
		 * @return string
		 */
		public function get_cat_image( $size = 'full' ) {

			if ( ! function_exists( 'get_woocommerce_term_meta' ) ) {
				return '';
			}

			$thumbnail_id = get_woocommerce_term_meta( $this->object->term_id, 'thumbnail_id', true );
			$image        = false;

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_image_src( $thumbnail_id, $size );
				$image = $image[0];
			} elseif ( function_exists( 'wc_placeholder_img_src' ) ) {
				$image = wc_placeholder_img_src();
			}

			if ( $image ) {

				// Prevent esc_url from breaking spaces in urls for image embeds
				// Ref: http://core.trac.wordpress.org/ticket/23605
				$image = str_replace( ' ', '%20', $image );

				return sprintf( '<img src="%1$s" alt="%2$s" />', esc_url( $image ), esc_attr( $this->object->name ) );
			} else {
				return '';
			}

		}

		/**
		 * Get category products count
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function get_cat_count() {
			$format = apply_filters( 'cherry_woocommerce_cat_counts_format', '%s', $this->object->count );
			return sprintf( $format, $this->object->count );
		}

		/**
		 * Get category products count
		 *
		 * @since 1.0.0
		 * @param string $wrapper info about description HTML wrapper. Must be in follow format - html_tag.css_class.
		 * @return string
		 */
		public function get_cat_desc( $wrapper = 'div.cat-list_desc' ) {

			if ( ! $this->object->description ) {
				return '';
			}

			$wrapper = explode( '.', $wrapper );

			if ( empty( $wrapper ) ) {
				$tag   = 'div';
				$class = 'cat-list_desc';
			}

			if ( 3 <= count( $wrapper ) ) {
				$tag     = $wrapper[0];
				$classes = array_slice( $wrapper, 1 );
				$class   = implode( ' ', $classes );
			} else {
				$tag   = $wrapper[0];
				$class = $wrapper[1];
			}

			return sprintf(
				'<%2$s class="%3$s">%1$s</%2$s>',
				$this->object->description, esc_attr( $tag ), esc_attr( $class )
			);

		}

		/**
		 * Get category name
		 *
		 * @since 1.0.0
		 * @param  string $linked image size.
		 * @return string
		 */
		public function get_cat_name( $linked = 'linked' ) {

			if ( 'linked' == $linked ) {
				$link = $this->get_cat_url();
				return sprintf( '<a href="%2$s">%1$s</a>', $this->object->name, esc_url( $link ) );
			} else {
				return $this->object->name;
			}

		}

		/**
		 * Get category name
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function get_cat_url() {

			if ( empty( $this->object->link ) ) {
				$this->object->link = get_term_link( $this->object, 'product_cat' );
			}

			return $this->object->link;

		}
	}

}
