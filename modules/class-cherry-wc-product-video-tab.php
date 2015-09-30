<?php
/**
 * Add product video tab to product meta box
 *
 * @package   Cherry_WooCommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Menu badges management class
 *
 * @since 1.0.0
 */
class Cherry_WC_Product_Video {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	public $key = '_cherry_wc_video_url';

	/**
	 * Costructor for the class
	 */
	function __construct() {

		if ( is_admin() ) {
			$this->backend();
		} else {
			$this->frontend();
		}
	}

	/**
	 * Run backed actions
	 *
	 * @since  1.0.0
	 */
	public function backend() {
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_admin_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'admin_tab_panel' ) );
		$product_type = isset( $_POST['product-type'] ) ? sanitize_title( stripslashes( $_POST['product-type'] ) ) : 'simple';
		$product_type = empty( $product_type ) ? 'simple' : $product_type;
		add_action( 'woocommerce_process_product_meta_' . $product_type, array( $this, 'save_video_meta' ) );
	}

	/**
	 * Run frontend actions
	 *
	 * @since  1.0.0
	 */
	public function frontend() {
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_frontend_tab' ) );
	}

	/**
	 * Add frontend Product Videotab
	 *
	 * @since  1.0.0
	 * @param  array  $tabs  existing tabs
	 */
	public function add_frontend_tab( $tabs ) {

		global $product, $post;
		$video = get_post_meta( $post->ID, $this->key, true );

		if ( !$video ) {
			return $tabs;
		}

		$label = __( 'Video', 'cherry-woocommerce-package' );

		$tabs['cherry_wc_video'] = array(
			'title'    => $label,
			'priority' => 90,
			'callback' => array( $this, 'frontend_tab_callback' )
		);

		return $tabs;
	}

	/**
	 * Show frontend video tab content
	 *
	 * @since 1.0.0
	 */
	public function frontend_tab_callback() {

		global $product, $post;
		$video = get_post_meta( $post->ID, $this->key, true );

		if ( !$video ) {
			return $tabs;
		}

		$video_code = wp_oembed_get( $video, array( 'width' => 1140 ) );
		$video_code = preg_replace_callback( '/src=[\'\"]([\S+]+)[\'\"]/', array( $this, 'add_wmode' ), $video_code );

		printf( '<div class="video-tab-wrap">%s</div>', $video_code );
	}

	/**
	 * Callback function to add vmode
	 *
	 * @since 1.0.0
	 */
	public function add_wmode( $matches ) {

		if ( is_array( $matches ) && isset( $matches[1] ) ) {
			return sprintf( 'src="%s"', add_query_arg( 'wmode', 'transparent', $matches[1] ) );
		}

		return $matches[0];
	}

	/**
	 * Add video tab to admin product metabox
	 *
	 * @since  1.0.0
	 * @param  array $tabs existing tabs
	 */
	public function add_admin_tab( $tabs ) {

		$tabs['cherry_wc_video'] = array(
			'label'  => __( 'Product video', 'cherry-woocommerce-package' ),
			'target' => 'cherry_wc_product_video',
			'class'  => array(),
		);

		return $tabs;

	}

	/**
	 * Show admin tab panel markup
	 *
	 * @since 1.0.0
	 */
	function admin_tab_panel() {
		global $post;

		?>
		<div id="cherry_wc_product_video" class="panel woocommerce_options_panel">

			<?php

			echo '<div class="options_group">';

				// menu_order
				if ( function_exists( 'woocommerce_wp_text_input' ) ) {
					woocommerce_wp_text_input(
						array(
							'id'                => $this->key,
							'label'             => __( 'YouTube video link', 'cherry-woocommerce-package' ),
							'desc_tip'          => 'true',
							'description'       => __( 'Paste YouTube video URL', 'cherry-woocommerce-package' ),
							'type'              => 'text',
							'custom_attributes' => array(),
						)
					);
				}

			echo '</div>';
			?>

		</div>
		<?php
	}

	/**
	 * Save video tab meta data to data base
	 *
	 * @since  1.0.0
	 * @param  int  $post_id saved post ID
	 */
	public function save_video_meta( $post_id ) {

		$url = isset( $_POST[ $this->key ] ) ? $_POST[ $this->key ] : '';

		if ( ! $url ) {
			delete_post_meta( $post_id, $this->key );
		} else {
			update_post_meta( $post_id, $this->key, esc_url( $url ) );
		}

	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

}