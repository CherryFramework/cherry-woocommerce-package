<?php
/**
 * Cart dropdown template
 *
 * @author      Cherry Team
 * @category    Core
 * @package     cherry-woocommerce-package/templates
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$cart_dropdown = Cherry_WC_Cart_Dropdown::get_instance();
?>
<div class="cherry-wc-cart cart-content" data-dropdown="box" data-dropdown-active="false">
	<a class="cherry-wc-cart_link" href="#" data-dropdown="trigger"><?php

		// print cart title
		printf(
			apply_filters( 'cherry_woocommerce_cart_title_format', '<span>%s</span>' ),
			$cart_dropdown->cart_options['shop-cart-title']
		);

		// print cart count
		printf(
			apply_filters( 'cherry_woocommerce_cart_count_format', '<span class="cherry-wc-cart_count">%s</span>' ),
			WC()->cart->cart_contents_count
		);
	?></a>
	<div class="cherry-wc-cart_content" data-dropdown="content">
		<?php cherry_wc_templater()->get_template_part( 'cart/cart-content' ); ?>
	</div>
</div>
