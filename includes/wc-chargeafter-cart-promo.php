<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_Cart_Promo
 */
class WC_ChargeAfter_Cart_Promo {


	/**
	 * WC_ChargeAfter_Cart_Promo Instance
	 *
	 * @var WC_ChargeAfter_Cart_Promo
	 */
	private static $instance;

	/**
	 * WC_ChargeAfter_Cart_Promo constructor
	 */
	public function __construct() {
		if (
			WC_ChargeAfter_Helper::is_enabled() &&
			WC_ChargeAfter_Helper::is_enabled_promo_cart()
		) {
			add_action( 'woocommerce_proceed_to_checkout', array( $this, 'init' ), 5 );
			add_shortcode( 'chargeafter-promo-cart', array( $this, 'init' ) );
		}
	}

	/**
	 * Get Instance
	 *
	 * @return WC_ChargeAfter_Cart_Promo
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Init Promo
	 */
	public function init() {
		try {
			$widget_type        = WC_ChargeAfter_Helper::get_promo_type();
			$widget_tag         = WC_ChargeAfter_Helper::get_promo_tag();
			$financial_page_url = WC_ChargeAfter_Helper::get_promo_url();

			$cart_sku   = 'cart_sku';
			$cart_price = 0;

			if ( null != WC()->cart ) {
				$cart_price = floatval( WC()->cart->total );
			}

			if ( empty( $cart_price ) ) {
				throw new Exception( 'Cart price is invalid' );
			}

			WC_ChargeAfter_Widget::init_promo_script();
			WC_ChargeAfter_Helper::build_promotional_widget( $widget_type, $cart_sku, $cart_price, $widget_tag, $financial_page_url );
		} catch ( Exception $exception ) {
			return;
		}
	}
}

/**
 * Init WC_ChargeAfter_Cart_Promo
 */
WC_ChargeAfter_Cart_Promo::get_instance();
