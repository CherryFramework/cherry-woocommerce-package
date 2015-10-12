<?php
/**
 * Adapter for YITH Compare plugin
 *
 * @package   cherry_woocommerce
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Account dropdown manager
 *
 * @since 1.0.0
 */
class Cherry_WC_Account_Dropdown {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Default account dropdown options array
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	public $account_options = array();

	/**
	 * Constructor for the class
	 */
	function __construct() {

		add_action( 'init', array( $this, 'register_static' ) );

		$this->account_options = array(
			'shop-show-acc'         => 'true',
			'shop-not-logged-label' => __( 'My Account', 'cherry-woocommerce-package' ),
			'shop-logged-label'     => __( 'My Account', 'cherry-woocommerce-package' ),
			'shop-acc-menu'         => '',
			'shop-show-auth'        => 'true',
			'shop-login-label'      => __( 'Log In/Register', 'cherry-woocommerce-package' ),
			'shop-logout-label'     => __( 'Logout', 'cherry-woocommerce-package' ),
		);

		add_action( 'cherry_wc_account_dropdown', array( $this, 'dropdown_frontend' ) );
	}

	/**
	 * Register static.
	 *
	 * @since 1.0.0
	 */
	public function register_static() {
		cherry_wc_templater()->register_static( 'cherry-account-dropdown-static.php' );
	}

	/**
	 * Show dropdown account content
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function dropdown_frontend() {

		$this->prepare_options();

		if ( 'true' !== $this->account_options['shop-show-acc'] ) {
			return;
		}
		?>
		<div class="cherry-wc-account" data-dropdown="box" data-dropdown-active="false">
			<?php cherry_wc_templater()->get_template_part( 'account-dropdown' ); ?>
		</div>
		<?php
	}

	/**
	 * Prepare account dropdown options
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_options() {

		foreach ( $this->account_options as $option_name => $option_val ) {
			$this->account_options[ $option_name ] = cherry_wc_options()->get_option( $option_name, $option_val );
		}

	}

	/**
	 * Show account items list
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function show_account_list() {
		if ( ! $this->account_options['shop-acc-menu'] ) {
			$this->default_account_list();
			return;
		}

		/**
		 * Displays a navigation menu
		 *
		 * @param array $args Arguments
		 */
		$args = apply_filters( 'cherry_wc_account_menu_args', array(
			'menu'       => $this->account_options['shop-acc-menu'],
			'menu_class' => 'cherry-wc-account_list',
			'depth'      => -1,
		) );

		wp_nav_menu( $args );

	}

	/**
	 * Show default account list (if menu in options not selected)
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function default_account_list() {
		$orders_page = get_option( 'woocommerce_myaccount_page_id' );
		if ( $orders_page ) {
			$orders_link = get_permalink( $orders_page );
		} else {
			$orders_link = '';
		}

		$items = array(
			'orders' => array(
				'link'  => esc_url( $orders_link ),
				'label' => __( 'Orders', 'cherry-woocommerce-package' ),
			),
		);

		if ( defined( 'YITH_WOOCOMPARE' ) ) {
			$items['cherry-compare'] = array(
				'link'  => '#',
				'label' => __( 'Compare', 'cherry-woocommerce-package' ),
			);
		}

		if ( defined( 'YITH_WCWL' ) ) {
			$wishlist_page = get_option( 'yith_wcwl_wishlist_page_id' );

			if ( $wishlist_page ) {
				$wishlist_link = get_permalink( $wishlist_page );
			} else {
				$wishlist_link = '';
			}

			$items['cherry-wishlist'] = array(
				'link'  => $wishlist_link,
				'label' => __( 'Wishlist', 'cherry-woocommerce-package' ),
			);
		}

		if ( ! $items ) {
			return;
		}
		?>
		<ul class="cherry-wc-account_list">
		<?php
		foreach ( $items as $item_class => $item_data ) {

			if ( empty( $item_data ) ) {
				continue;
			}
			echo '<li class="cherry-wc-account_list_item ' . $item_class . '"><a href="' . $item_data['link'] . '">' . $item_data['label'] . '</a></li>';
		}
		?>
		</ul>
		<?php
	}

	/**
	 * Show default account auth links
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function show_account_auth() {

		if ( 'hide' == $this->account_options['shop-show-auth'] ) {
			return;
		}

		$account_page = get_option( 'woocommerce_myaccount_page_id' );

		if ( ! $account_page ) {
			return;
		}

		echo '<div class="cherry-wc-account_auth">';

			$link_url   = get_permalink( $account_page );
			$link_text  = $this->account_options['shop-login-label'];
			$link_class = 'not-logged';

			if ( is_user_logged_in() ) {
				$link_url   = wp_logout_url( get_permalink( $account_page ) );
				$link_text  = $this->account_options['shop-logout-label'];
				$link_class = 'logged';
			}

			echo apply_filters(
				'cherry_wc_account_auth_html',
				'<a href="' . $link_url .'" class="' . $link_class . '">' . $link_text . '</a>',
				$link_url, $link_text, $link_class
			);

		echo '</div>';

	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
