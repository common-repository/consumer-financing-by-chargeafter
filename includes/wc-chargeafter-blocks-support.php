<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Automattic\WooCommerce\StoreApi\Payments\PaymentContext;
use Automattic\WooCommerce\StoreApi\Payments\PaymentResult;

final class WC_ChargeAfter_Blocks_Support extends AbstractPaymentMethodType {


	/**
	 * Payment method name defined by payment methods extending this class.
	 *
	 * @var string
	 */
	protected $name = CHARGEAFTER_ID;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_rest_checkout_process_payment_with_context', array( $this, 'add_payment_request_order_meta' ), 8, 2 );
	}

	/**
	 * Initializes the payment method type.
	 */
	public function initialize() {
		$this->settings = WC_ChargeAfter_Helper::get_all_settings();
	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return WC_ChargeAfter_Helper::is_allow_gateway();
	}

	/**
	 * Get payment method scripts
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		$asset_path = __DIR__ . '/../assets/blocks/wc_chargeafter_gateway.asset.php';

		$version      = false;
		$dependencies = array();

		if ( file_exists( $asset_path ) ) {
			$asset = require_once $asset_path;

			if ( is_array( $asset ) && isset( $asset['version'] ) ) {
				$version = $asset['version'];
			}

			if ( is_array( $asset ) && isset( $asset['dependencies'] ) ) {
				$dependencies = $asset['dependencies'];
			}
		}

		wp_register_script(
			'wc-chargeafter-gateway-integration',
			plugins_url( '/assets/blocks/wc_chargeafter_gateway.js', CHARGEAFTER_MAIN_FILE ),
			$dependencies,
			$version,
			true
		);

		wp_enqueue_style(
			'wc-chargeafter-gateway-integration',
			plugins_url( '/assets/blocks/wc_chargeafter_gateway.css', CHARGEAFTER_MAIN_FILE ),
			array(),
			$version
		);

		wp_set_script_translations( 'wc-chargeafter-gateway-integration', 'chargeafter-extension' );

		return array( 'wc-chargeafter-gateway-integration' );
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return array(
			'title'          => $this->get_title(),
			'supports'       => $this->get_supported_features(),
			'buttonType'     => $this->get_button_type(),
			'publicKey'      => $this->get_public_key(),
			'environment'    => $this->get_environment(),
			'withDataUpdate' => $this->get_with_data_update(),
		);
	}

	/**
	 * Returns the title string to use in the UI (customisable via admin settings screen).
	 *
	 * @return string Title / label string
	 */
	public function get_title() {
		return $this->get_setting( 'title' );
	}

	/**
	 * Returns an array of supported features.
	 *
	 * @return array
	 */
	public function get_supported_features() {
		$gateway = new WC_ChargeAfter_Gateway();
		return array_filter( $gateway->supports, array( $gateway, 'supports' ) );
	}

	/**
	 * Returns the button type to use in the UI (customisable via admin settings screen).
	 *
	 * @return string Button type
	 */
	public function get_button_type() {
		return $this->get_setting( 'checkout_brand_type' );
	}

	/**
	 * Returns the environment of the payment method.
	 *
	 * @return string
	 */
	public function get_environment() {
		return 'yes' !== $this->get_setting( 'is_production' ) ? 'sandbox' : 'production';
	}

	/**
	 * Returns the public key of the payment method.
	 *
	 * @return mixed
	 */
	private function get_public_key() {
		return 'production' !== $this->get_environment()
			? $this->get_setting( 'sandbox_public_key' )
			: $this->get_setting( 'production_public_key' );
	}

	/**
	 * Returns the with data update of the payment method.
	 *
	 * @return bool
	 */
	private function get_with_data_update() {
		return 'yes' == $this->get_setting( 'enabled_data_update' );
	}

	/**
	 * Add payment request order meta
	 *
	 * @param PaymentContext $context
	 * @param PaymentResult $result
	 *
	 * @return void
	 * @throws Exception
	 */
	public function add_payment_request_order_meta( $context, &$result ) {
		$order = $context->order;

		if ( $order ) {
			$api_urls        = WC_ChargeAfter_Helper::get_api_action_urls();
			$payment_details = array_merge(
				isset( $result->payment_details ) ? $result->payment_details : array(),
				array(
					'confirmation_endpoint'  => $api_urls['create_charge'],
					'checkout_data_endpoint' => $api_urls['get_checkout_data'],
					'data_update_endpoint'   => $api_urls['on_data_update'],
					'order_nonce'            => WC_ChargeAfter_Helper::create_order_nonce( $order->get_id() ),
				)
			);

			$result->set_payment_details( $payment_details );
			$result->set_status( 'success' );
		}
	}
}
