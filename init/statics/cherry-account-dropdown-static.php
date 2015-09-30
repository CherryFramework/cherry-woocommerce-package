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

if ( ! class_exists( 'cherry_register_static' ) ) {
	return;
}

class cherry_wc_account_static extends cherry_register_static {

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
new cherry_wc_account_static(
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
		)
	)
);