<?php
/**
 * Account dropdown template
 *
 * @author      Cherry Team
 * @category    Core
 * @package     cherry-woocommerce-package/templates
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$cherry_wc_account_dropdown = Cherry_WC_Account_Dropdown::get_instance();

if ( is_user_logged_in() ) {
	$title = $cherry_wc_account_dropdown->account_options['shop-logged-label'];
} else {
	$title = $cherry_wc_account_dropdown->account_options['shop-not-logged-label'];
}

printf(
	apply_filters(
		'cherry_woocommerce_account_title_format',
		'<a class="cherry-wc-account_title" data-dropdown="trigger" href="#">%s</a>'
	),
	$title
);
?>
<div class="cherry-wc-account_content" data-dropdown="content"><?php
$cherry_wc_account_dropdown->show_account_list();
$cherry_wc_account_dropdown->show_account_auth();
?></div>
