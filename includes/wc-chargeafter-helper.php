<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_Helper
 */
class WC_ChargeAfter_Helper {

	/**
	 * Filter empty string
	 *
	 * @param $var
	 *
	 * @return bool
	 */
	public static function filter_empty_string( $var ) {
		if ( is_string( $var ) ) {
			return ( '' !== $var );
		} elseif ( is_array( $var ) ) {
			return count( $var ) > 0;
		}

		return true;
	}

	/**
	 * Recursive sanitation for an array
	 *
	 * @param $array
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function recursive_sanitize_text_field( $array, $key = null ) {
		if ( ! empty( $key ) ) {
			$array[ $key ] = $array;
		}

		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = self::recursive_sanitize_text_field( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}

		return $array;
	}

	/**
	 * Get Url Suffix
	 *
	 * @return string
	 */
	public static function get_url_suffix() {
		return self::is_sandbox() ? '-sandbox.ca-dev.co' : '.chargeafter.com';
	}

	/**
	 * Check sandbox flag
	 *
	 * @return bool
	 */
	public static function is_sandbox() {
		return 'yes' !== self::get_setting_param( 'is_production' );
	}

	/**
	 * Get setting param
	 *
	 * @param $key
	 *
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public static function get_setting_param( $key, $default = null ) {
		$settings = self::get_all_settings();

		return key_exists( $key, $settings ) ? $settings[ $key ] : $default;
	}

	/**
	 * Get All settings
	 *
	 * @return mixed|void
	 */
	public static function get_all_settings() {
		return get_option( 'woocommerce_' . CHARGEAFTER_ID . '_settings', array() );
	}

	/**
	 * Check ChargeAfter Gateway for order
	 *
	 * @param $order_id
	 *
	 * @return bool
	 */
	public static function is_chargeafter_gateway( $order_id ) {
		$order = wc_get_order( $order_id );

		return ! empty( $order ) && ( CHARGEAFTER_ID == $order->get_payment_method() );
	}

	/**
	 * Check promo enabled
	 *
	 * @return bool
	 */
	public static function is_enabled_promo() {
		return 'yes' == self::get_setting_param( 'enabled_promo' );
	}

	/**
	 * Check promo enabled for product page
	 *
	 * @return bool
	 */
	public static function is_enabled_promo_product() {
		$value = self::get_setting_param( 'enabled_promo_product' );
		// value === null - for old versions; migration
		return ( null === $value || 'yes' == $value ) && self::is_enabled_promo();
	}

	/**
	 * Check promo enabled for cart
	 *
	 * @return bool
	 */
	public static function is_enabled_promo_cart() {
		return self::is_enabled_promo() && 'yes' == self::get_setting_param( 'enabled_promo_cart' );
	}

	/**
	 * Check promo enabled for mini cart
	 *
	 * @return bool
	 */
	public static function is_enabled_promo_mini_cart() {
		return self::is_enabled_promo() && 'yes' == self::get_setting_param( 'enabled_promo_mini_cart' );
	}

	/**
	 * Check promo enabled for checkout
	 *
	 * @return bool
	 */
	public static function is_enabled_promo_checkout() {
		return self::is_enabled_promo() && 'yes' == self::get_setting_param( 'enabled_promo_checkout' );
	}

	/**
	 * Get promo type
	 *
	 * @return mixed|null
	 */
	public static function get_promo_type() {
		return self::get_setting_param( 'promo_type', 'product-widget-line-of-credit' );
	}

	/**
	 * Get promo tag
	 *
	 * @return mixed|null
	 */
	public static function get_promo_tag() {
		return self::get_setting_param( 'promo_tag' );
	}

	/**
	 * Get promo url
	 *
	 * @return mixed|null
	 */
	public static function get_promo_url() {
		return self::get_setting_param( 'promo_url', '/' );
	}

	/**
	 * Allow gateway
	 *
	 * @return bool
	 */
	public static function is_allow_gateway() {
		return self::is_enabled() && ! empty( self::get_public_key() ) && ! empty( self::get_private_key() );
	}

	/**
	 * Check enabled flag
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return 'yes' == self::get_setting_param( 'enabled' );
	}

	/**
	 * Get public key
	 *
	 * @return mixed
	 */
	public static function get_public_key() {
		return self::is_sandbox() ? self::get_setting_param( 'sandbox_public_key' )
			: self::get_setting_param( 'production_public_key' );
	}

	/**
	 * Get private key
	 *
	 * @return mixed
	 */
	public static function get_private_key() {
		return self::is_sandbox() ? self::get_setting_param( 'sandbox_private_key' )
			: self::get_setting_param( 'production_private_key' );
	}

	/**
	 * Get checkout brand type
	 *
	 * @return string
	 */
	public static function get_checkout_brand_type() {
		return self::get_setting_param( 'checkout_brand_type', 'medium-generic' );
	}

	/**
	 * Get checkout brand
	 *
	 * @return void
	 */
	public static function get_checkout_brand_label( $title ) {
		?>
		<span class="wc-chargeafter-components-payment-method-legacy">
			<span class="ca-checkout-button"
					data-button-type="<?php echo esc_html( self::get_checkout_brand_type() ); ?>"></span>
			<span><?php echo esc_html( $title ); ?></span>
		</span>
		<?php
	}

	/**
	 * Build promotional widget
	 *
	 * @param $widget_type
	 * @param $cart_sku
	 * @param $cart_price
	 * @param $widget_tag
	 * @param $financial_page_url
	 *
	 * @return void
	 */
	public static function build_promotional_widget( $widget_type, $cart_sku, $cart_price, $widget_tag, $financial_page_url ) {
		?>
		<div class="ca-promotional-widget"
			data-widget-type="<?php echo esc_html( $widget_type ); ?>"
			data-widget-item-sku="<?php echo esc_html( $cart_sku ); ?>"
			data-widget-item-price="<?php echo esc_html( $cart_price ); ?>"
			<?php echo $widget_tag ? 'data-widget-item-tag="' . esc_html( $widget_tag ) . '"' : ''; ?>
			data-widget-financing-page-url="<?php echo esc_url( $financial_page_url ); ?>"
			style="margin-top: 5px; margin-bottom: 5px"
		></div>
		<?php
	}

	/**
	 * Check OnDataUpdate enabled
	 *
	 * @return bool
	 */
	public static function is_enabled_data_update() {
		return 'yes' == self::get_setting_param( 'enabled_data_update' );
	}

	/**
	 * Check Auto-settle enabled
	 *
	 * @return bool
	 */
	public static function is_enabled_auto_settle() {
		return 'sale' == self::get_setting_param( 'payment_action', '' );
	}

	/**
	 * Create order nonce
	 *
	 * @param $order_id
	 * @return string
	 */
	public static function create_order_nonce( $order_id ) {
		return wp_create_nonce( self::order_nonce_key( $order_id ) );
	}

	/**
	 * Verify order nonce
	 *
	 * @param $nonce
	 * @param $order_id
	 *
	 * @return false|int
	 */
	public static function verify_order_nonce( $nonce, $order_id ) {
		return wp_verify_nonce( $nonce, self::order_nonce_key( $order_id ) );
	}

	/**
	 * Get nonce key
	 *
	 * @param $order_id
	 *
	 * @return string
	 */
	private static function order_nonce_key( $order_id ) {
		return 'chargeafter-extension_' . $order_id;
	}

	/**
	 * Get API Action URLs
	 *
	 * @param string $base_url
	 *
	 * @return array
	 */
	public static function get_api_action_urls( $base_url = '' ) {
		$get_checkout_data_url = $base_url . '?wc-api=wc_chargeafter_get_checkout_data';
		$create_on_update_url  = $base_url . '?wc-api=wc_chargeafter_on_data_update';
		$create_charge_url     = $base_url . '?wc-api=wc_chargeafter_create_charge';
		$on_error_url          = $base_url . '?wc-api=wc_chargeafter_on_error_url';

		return array(
			'get_checkout_data' => $get_checkout_data_url,
			'on_data_update'    => $create_on_update_url,
			'create_charge'     => $create_charge_url,
			'get_error'         => $on_error_url,
		);
	}
}
