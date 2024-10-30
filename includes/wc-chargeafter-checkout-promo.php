<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_Checkout_Promo
 */
class WC_ChargeAfter_Checkout_Promo extends WC_ChargeAfter_Cart_Promo {


	/**
	 * WC_ChargeAfter_Checkout_Promo Instance
	 *
	 * @var WC_ChargeAfter_Checkout_Promo
	 */
	private static $instance;

	/**
	 * WC_ChargeAfter_Checkout_Promo constructor
	 */
	public function __construct() {
		if (
			WC_ChargeAfter_Helper::is_enabled() &&
			WC_ChargeAfter_Helper::is_enabled_promo_checkout()
		) {
			add_action( 'woocommerce_checkout_order_review', array( $this, 'init' ), 10 );
			add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'update_order_review_fragments' ), 10, 1 );
			add_shortcode( 'chargeafter-promo-checkout', array( $this, 'init' ) );
		}
	}

	/**
	 * Filter: Update Order Review Fragments
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	public function update_order_review_fragments( $fragments ) {
		ob_start();
		$this->init();

		$promotional = ob_get_clean();

		$fragments['.ca-promotional-widget[data-widget-item-sku="cart_sku"]'] = $promotional;

		return $fragments;
	}

	/**
	 * Get Instance
	 *
	 * @return WC_ChargeAfter_Checkout_Promo
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

/**
 * Init WC_ChargeAfter_Checkout_Promo
 */
WC_ChargeAfter_Checkout_Promo::get_instance();
