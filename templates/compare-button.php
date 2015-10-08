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
?>
<a href="<?php echo esc_url( $cherry_wc_compare_button['url'] ); ?>" class="compare <?php echo esc_attr( $cherry_wc_compare_button['is_button'] ); ?>" data-product_id="<?php echo absint( $cherry_wc_compare_button['product_id'] ); ?>">
	<i class="fa fa-retweet"></i>
	<?php echo sanitize_text_field( $cherry_wc_compare_button['button_text'] ); ?>
</a>
