<?php
/**
 * Main plugin file.
 *
 * @package    dorzki\WooCommerce\Buy_Now
 * @subpackage Plugin
 * @author     Dor Zuberi <webmaster@dorzki.co.il>
 * @link       https://www.dorzki.co.il
 * @version    1.0.0
 */

namespace dorzki\WooCommerce\Buy_Now;

// Block if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class Plugin
 *
 * @package dorzki\WooCommerce\Buy_Now
 */
class Plugin {

	/**
	 * Plugin instance.
	 *
	 * @var null|Plugin
	 */
	private static $instance = null;


	/* ------------------------------------------ */


	/**
	 * Plugin constructor.
	 */
	public function __construct() {

		add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'product_page_button' ] );
		add_action( 'woocommerce_after_shop_loop_item', [ $this, 'product_archive_button' ], 20 );

		add_action( 'wp_loaded', [ $this, 'handle_buy_now_request' ] );

	}


	/* ------------------------------------------ */


	/**
	 * Inject buy now button to product page.
	 */
	public function product_page_button() {

		global $product;

		printf( '<button type="submit" name="buy-now" value="%d" class="single_add_to_cart_button buy_now_button button alt">%s</button>', $product->get_ID(), esc_html__( 'Buy Now', 'dorzki-wc-buy-now' ) );

	}


	/**
	 * Inject buy now button to product archive page.
	 *
	 * @return bool
	 */
	public function product_archive_button() {

		global $product;

		if ( ! $product->is_type( 'simple' ) ) {
			return false;
		}

		printf( '<a href="%s?buy-now=%s" data-quantity="1" class="button product_type_simple add_to_cart_button  buy_now_button" data-product_id="%s" rel="nofollow">%s</a>', wc_get_checkout_url(), $product->get_ID(), $product->get_ID(), esc_html__( 'Buy Now', 'dorzki-wc-buy-now' ) );

	}


	/* ------------------------------------------ */


	/**
	 * Handle the click on buy now button.
	 *
	 * @return bool
	 */
	public function handle_buy_now_request() {

		if ( ! isset( $_REQUEST['buy-now'] ) ) {
			return false;
		}

		WC()->cart->empty_cart();

		$product_id = absint( $_REQUEST['buy-now'] );

		if ( isset( $_REQUEST['variation_id'] ) ) {

			$variation_id = absint( $_REQUEST['variation_id'] );

			WC()->cart->add_to_cart( $product_id, 1, $variation_id );

		} else {

			WC()->cart->add_to_cart( $product_id );

		}

		wp_safe_redirect( wc_get_checkout_url() );
		exit;

	}


	/* ------------------------------------------ */


	/**
	 * Retrieve plugin instance.
	 *
	 * @return Plugin|null
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {

			self::$instance = new self();

		}

		return self::$instance;

	}

}

// initiate plugin.
Plugin::get_instance();
