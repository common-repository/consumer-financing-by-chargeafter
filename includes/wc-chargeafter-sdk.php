<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_SDK
 */
class WC_ChargeAfter_SDK {

	/**
	 * WC_ChargeAfter_Checkout_Promo Instance
	 *
	 * @var WC_ChargeAfter_SDK
	 */
	private static $instance;

	/**
	 * WC_ChargeAfter_SDK constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
	}

	/**
	 * Enqueue script
	 *
	 * @return void
	 */
	public static function enqueue_script() {
		if ( ! WC_ChargeAfter_Helper::is_enabled() ) {
			return;
		}

		wp_enqueue_script(
			'chargeafter_sdk',
			plugins_url( 'assets/js/sdk.js', CHARGEAFTER_MAIN_FILE ),
			array( 'jquery' ),
			CHARGEAFTER_VERSION,
			true
		);

		wp_localize_script(
			'chargeafter_sdk',
			'chargeAfterSdkData',
			self::get_sdk_settings()
		);
	}

	/**
	 * Get SDK settings
	 *
	 * @return array
	 */
	private static function get_sdk_settings() {
		$public_key = WC_ChargeAfter_Helper::get_public_key();
		$cdn_url    = 'https://cdn' . WC_ChargeAfter_Helper::get_url_suffix();

		return array(
			'public_key' => $public_key,
			'cdn_url'    => $cdn_url,
		);
	}

	/**
	 * Get Class Instance
	 *
	 * @return WC_ChargeAfter_SDK
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

/**
 * Init WC_ChargeAfter_SDK
 */
WC_ChargeAfter_SDK::get_instance();
