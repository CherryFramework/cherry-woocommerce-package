<?php
/**
 * Single Product Image
 *
 * @author 		Cherry Team
 * @package 	Cherry Woocommerce Package/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;

$layout_class = apply_filters( 'cherry_wc_product_gallery_layout', 'fullwidth' );

?>
<div class="product-images <?php printf( 'product-%s', esc_attr( $layout_class ) ); ?>">
	<?php

		$thumbnails = $product->get_gallery_attachment_ids();

		if ( has_post_thumbnail() ) {
			$thumbnails = array_merge( array( get_post_thumbnail_id() ), $thumbnails );
		}

		$thumb_count = count( $thumbnails );
		$thumb_class = '';
		$controls    = '';

		if ( $thumb_count > 4 ) {
			$thumb_class = ' cycle-slideshow';
			$controls = '<a href="#" class="product-thumbnails_prev"><i class="fa fa-caret-left"></i></a><a href="#" class="product-thumbnails_next"><i class="fa fa-caret-right"></i></a>';

		}

		if ( $thumb_count > 0 ) {
	?>
		<div class="product-large-image">
		<?php
			if ( has_post_thumbnail() ) {

				$product_title = get_the_title( $product->id );
				$image_link    = wp_get_attachment_url( get_post_thumbnail_id() );
				$thumb_link    = wp_get_attachment_image_src( get_post_thumbnail_id(), 'shop_single' );
				$thumb_link    = $thumb_link[0];
				$image         = get_the_post_thumbnail( $post->ID, 'shop_single', array(
					'itemprop'                 => 'image',
					'data-zoom-image'          => $image_link,
					'data-initial-thumb'       => $thumb_link,
					'data-initial-thumb-large' => $image_link,
					'title'                    => $product_title,
					'alt'                      => $product_title,
					'data-is-shop-single'      => true,
				) );

				echo $image;

			} else {

				echo apply_filters(
					'woocommerce_single_product_image_html',
					sprintf(
						'<img src="%s" alt="%s" />',
						wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' )
					),
					$post->ID
				);

			}
		?>
		</div>
		<div class="product-thumbnails">
			<div class="product-thumbnails_list<?php echo $thumb_class; ?>" data-cycle-fx="carousel" data-cycle-timeout="0" data-cycle-next=".product-thumbnails_next" data-cycle-prev=".product-thumbnails_prev" data-cycle-carousel-visible="4" data-allow-wrap=false>
			<?php
				foreach ( $thumbnails as $thumb_id ) {
					$image_link = wp_get_attachment_url( $thumb_id );
					if ( ! $image_link ) {
						continue;
					}
					$image_large = wp_get_attachment_image_src( $thumb_id, 'shop_single' );
					$image = wp_get_attachment_image( $thumb_id, 'shop_thumbnail' );
					echo '<div class="product-thumbnails_item" data-original-img="' . esc_url( $image_link ) . '" data-large-img="' . esc_url( $image_large[0] ) . '">' . $image . '</div>';
				}
			?>
			</div>
			<?php echo $controls; ?>
		</div>
	<?php } else {
		echo apply_filters(
			'woocommerce_single_product_image_html',
			sprintf(
				'<img src="%s" alt="%s" />',
				wc_placeholder_img_src(),
				__( 'Placeholder', 'woocommerce' )
			),
			$post->ID
		);
	} ?>
</div>
