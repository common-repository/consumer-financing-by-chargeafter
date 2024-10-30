<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_API
 * ChargeAfter Api
 */
class WC_ChargeAfter_API {

	/**
	 * Create Charge By CheckoutToken
	 *
	 * @param string $token
	 * @param string $order_id
	 *
	 * @return array|null
	 *
	 * @throws WC_ChargeAfter_API_Exception
	 */
	public static function api_create_charge( $token, $order_id ) {
		return self::api_generic_post_sale(
			'wp_safe_remote_post',
			'payment/charges',
			array(
				'confirmationToken' => $token,
				'merchantOrderId'   => $order_id,
			)
		);
	}

	/**
	 * Get Charge By ChargeId
	 *
	 * @param string $charge_id
	 *
	 * @return array|null
	 *
	 * @throws WC_ChargeAfter_API_Exception
	 */
	public static function api_get_charge_data( $charge_id ) {
		return self::api_generic_post_sale(
			'wp_safe_remote_get',
			'post-sale/charges/' . $charge_id
		);
	}

	/**
	 * Settle Charge By ChargeId
	 *
	 * @param string $charge_id
	 *
	 * @return array|null
	 *
	 * @throws WC_ChargeAfter_API_Exception
	 */
	public static function api_settle_charge( $charge_id ) {
		return self::api_post_sale( $charge_id, 'settles' );
	}

	/**
	 * Void Charge By ChargeId
	 *
	 * @param string $charge_id
	 *
	 * @return array|null
	 *
	 * @throws WC_ChargeAfter_API_Exception
	 */
	public static function api_void_charge( $charge_id ) {
		return self::api_post_sale( $charge_id, 'voids' );
	}

	/**
	 * Refund Charge Amount By ChargeId
	 *
	 * @param string $charge_id
	 * @param $amount
	 *
	 * @return array|null
	 *
	 * @throws WC_ChargeAfter_API_Exception
	 */
	public static function api_refund_amount( $charge_id, $amount ) {
		return self::api_post_sale(
			$charge_id,
			'refunds',
			array( 'amount' => floatval( $amount ) )
		);
	}

	/**
	 * Post Sale
	 *
	 * @param string $charge_id
	 * @param string $action
	 * @param array $data
	 *
	 * @return array|null
	 *
	 * @throws WC_ChargeAfter_API_Exception
	 */
	private static function api_post_sale( $charge_id, $action, $data = array() ) {
		return self::api_generic_post_sale(
			'wp_safe_remote_post',
			'post-sale/charges/' . $charge_id . '/' . $action,
			$data
		);
	}

	/**
	 * Generic post request
	 *
	 * @param $http_callable
	 * @param string $endpoint
	 * @param array $data
	 *
	 * @return array|null
	 *
	 * @throws WC_ChargeAfter_API_Exception
	 */
	private static function api_generic_post_sale( $http_callable, $endpoint, $data = array() ) {
		$url  = self::get_api_endpoint() . $endpoint;
		$args = wp_parse_args(
			array(
				'body' => json_encode( $data ),
			),
			self::get_default_request_args( $url )
		);

		$response = $http_callable( $url, array_filter( $args, 'WC_ChargeAfter_Helper::filter_empty_string' ) );
		$body     = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body ) || self::is_http_error_status( $response ) ) {
			$metadata = array(
				'endpoint' => $url,
				'response' => array(
					'code'    => wp_remote_retrieve_response_code( $response ),
					'message' => wp_remote_retrieve_response_message( $response ),
					'body'    => ! empty( $body ) ? $body : array(),
				),
			);

			$message = __( 'Unable to execute API request.', 'chargeafter-extension' );

			WC_ChargeAfter_Logger::log( $message . ' Details: ' . PHP_EOL . PHP_EOL . print_r( $metadata, true ) );

			throw new WC_ChargeAfter_API_Exception( $message );
		}

		return $body;
	}

	/**
	 * Get Default Request Arguments
	 *
	 * @param string $url
	 *
	 * @return array
	 */
	private static function get_default_request_args( $url ) {
		return array(
			'headers' => array(
				'Authorization' => 'Bearer ' . WC_ChargeAfter_Helper::get_private_key(),
				'Content-Type'  => 'application/json',
			),
			/**
			 * Filter the timeout for the HTTP request.
			 *
			 * @since 1.0.0
			 * @param int $timeout Timeout in seconds.
			 */
			'timeout' => apply_filters( 'wc_chargeafter_http_request_timeout', 180, $url ),
		);
	}

	/**
	 * Get Api Prefix
	 *
	 * @return string
	 */
	private static function get_api_endpoint() {
		return 'https://api' . WC_ChargeAfter_Helper::get_url_suffix() . '/v2/';
	}

	/**
	 * Check http error status
	 *
	 * @param $response
	 *
	 * @return bool
	 */
	private static function is_http_error_status( $response ) {
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( is_int( $response_code ) && $response_code >= 400 && $response_code < 600 ) {
			return true;
		}

		return false;
	}
}
