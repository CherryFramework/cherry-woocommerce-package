<?php
/**
 * Account dropdown static registration
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_WC_Account_Static' ) ) {
	return;
}

/**
 * Account dropdown static class
 */
class Cherry_WC_Account_Static extends cherry_register_static {

	/**
	 * Callback-method for registered static.
	 *
	 * @since 1.0.0
	 */
	public function callback() {
		do_action( 'cherry_wc_account_dropdown' );
	}
}

/**
 * Register for Account dropdown static for editor
 */
new Cherry_WC_Account_Static(
	array(
		'id'      => 'shop-account-dropdown',
		'name'    => __( 'Shop Account Dropdown', 'cherry-woocommerce-package' ),
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
