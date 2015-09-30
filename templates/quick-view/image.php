<?php
/**
 * Loop Add to Cart
 *
 * @author      Cherry Team
 * @category    Core
 * @package     cherry-woocommerce-package/templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

if ( has_post_thumbnail() ) {

	$image_title    = esc_attr( get_the_title( get_post_thumbnail_id() ) );
	$image_caption  = get_post( get_post_thumbnail_id() )->post_excerpt;
	$image_link     = wp_get_attachment_url( get_post_thumbnail_id() );
	$image          = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
		'title' => $image_title,
		'alt'   => $image_title
		) );

	echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" class="woocommerce-main-image zoom" title="%s" >%s</a>', $image_link, $image_caption, $image ), $post->ID );

} else {

	echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );

}
