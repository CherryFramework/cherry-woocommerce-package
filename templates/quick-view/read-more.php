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

if ( 'simple' !== $product->product_type ) {
	return;
}
?>
<a href="<?php esc_url( get_permalink( $post->ID ) ); ?>"><?php _e( 'Read More', 'cherry-woocommerce-package' ); ?></a>