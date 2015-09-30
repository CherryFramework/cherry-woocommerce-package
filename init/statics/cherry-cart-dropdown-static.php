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

if ( ! class_exists( 'cherry_register_static' ) ) {
	return;
}

class cherry_wc_cart_static extends cherry_register_static {

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
new cherry_wc_cart_static(
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
		)
	)
);