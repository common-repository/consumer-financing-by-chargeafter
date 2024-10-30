<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_Widget
 */
class WC_ChargeAfter_Product_Promo {

	/**
	 * WC_ChargeAfter Instance
	 *
	 * @var WC_ChargeAfter_Product_Promo
	 */
	private static $instance;

	/**
	 * WC_ChargeAfter_Product_Promo constructor.
	 */
	public function __construct() {
		if (
			WC_ChargeAfter_Helper::is_enabled() &&
			WC_ChargeAfter_Helper::is_enabled_promo_product()
		) {
			add_action( 'woocommerce_single_product_summary', array( $this, 'init' ), 5 );
			add_shortcode( 'chargeafter-promo-product', array( $this, 'init' ) );
		}
	}

	/**
	 * Get Instance
	 *
	 * @return WC_ChargeAfter
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
			global $product;
			if ( empty( $product ) ) {
				throw new Exception( 'Empty product variable' );
			}

			$widget_type = WC_ChargeAfter_Helper::get_promo_type();
			$widget_tag  = WC_ChargeAfter_Helper::get_promo_tag();
			$ca_page_url = WC_ChargeAfter_Helper::get_promo_url();

			try {
				$product_sku = $product->get_sku();
			} catch ( Exception $exception ) {
				$product_sku = '';
			}

			try {
				$product_price = $product->get_price();
			} catch ( Exception $exception ) {
				$product_price = null;
			}

			try {
				$not_allow_product_type = (
					$product->is_type( 'external' ) || $product->is_type( 'grouped' )
				);
			} catch ( Exception $exception ) {
				$not_allow_product_type = true;
			}

			if ( $not_allow_product_type || empty( $product_price ) ) {
				throw new Exception( 'Used not all params' );
			}

			WC_ChargeAfter_Widget::init_promo_script();
			WC_ChargeAfter_Helper::build_promotional_widget( $widget_type, $product_sku, $product_price, $widget_tag, $ca_page_url );
		} catch ( Exception $exception ) {
			return;
		}
	}
}

/**
 * Init WC_ChargeAfter class
 */
WC_ChargeAfter_Product_Promo::get_instance();
