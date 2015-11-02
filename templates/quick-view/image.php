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

	$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
	$image       = get_the_post_thumbnail(
		$post->ID,
		apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ),
		array(
			'title' => $image_title,
			'alt'   => $image_title,
		)
	);

	echo $image;

} else {

	echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );

}
