<?php
/**
 * Quick view content template
 *
 * @author      Cherry Team
 * @category    Core
 * @package     cherry-woocommerce-package/templates
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product, $woocommerce, $post;
?>

<div class="cherry-quick-view-wrap">
	<div class="cherry-quick-view-images">
		<?php cherry_wc_templater()->get_template_part( 'single-product/sale-flash', true ); ?>
		<?php cherry_wc_templater()->get_template_part( 'quick-view/image' ); ?>
	</div>
	<div class="cherry-quick-view-data">
		<?php cherry_wc_templater()->get_template_part( 'single-product/title', true ); ?>
		<?php cherry_wc_templater()->get_template_part( 'quick-view/rating' ); ?>
		<?php cherry_wc_templater()->get_template_part( 'single-product/price', true ); ?>
		<?php cherry_wc_templater()->get_template_part( 'single-product/short-description', true ); ?>
		<div class="cherry-quick-view-add-to-cart">
			<?php cherry_wc_templater()->get_template_part( 'loop/add-to-cart', true ); ?>
			<?php cherry_wc_templater()->get_template_part( 'quick-view/read-more' ); ?>
		</div>
	</div>
</div>