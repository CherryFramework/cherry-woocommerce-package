<?php
/**
 * Cart dropdown static registration
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Cart_Static' ) ) {
	return;
}

/**
 * Cart dropdown class
 */
class Cherry_WC_Cart_Static extends cherry_register_static {

	/**
	 * Callback-method for registered static.
	 *
	 * @since 1.0.0
	 */
	public function callback() {
		?>
		<div class="cherry-wc-cart_wrap woocommerce">
		<?php
			do_action( 'cherry_wc_cart_dropdown' );
		?>
		</div>
		<?php
	}
}

/**
 * Register Cart dropdown static for editor
 */
new Cherry_WC_Cart_Static(
	array(
		'id'      => 'shop-cart-dropdown',
		'name'    => __( 'Shop Cart Dropdown', 'cherry-woocommerce-package' ),
		'options' => array(
			'col-lg'   => 'none',
			'col-md'   => 'none',
			'col-sm'   => 'none',
			'col-xs'   => 'none',
			'class'    => '',
			'position' => 3,
			'area'     => 'available-statics',
		),
	)
);