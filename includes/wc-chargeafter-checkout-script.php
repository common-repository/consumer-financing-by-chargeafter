<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_Checkout_Script
 */
class WC_ChargeAfter_Checkout_Script {

	/**
	 * Get Checkout Data
	 *
	 * @param WC_Order $order
	 *
	 * @return string
	 */
	public static function checkout_data( $order ) {
		/*
		 * Billing && Shipping Address
		 */
		$billing_address = self::address(
			$order->get_billing_address_1(),
			$order->get_billing_address_2(),
			$order->get_billing_city(),
			$order->get_billing_state(),
			$order->get_billing_postcode()
		);

		$shipping_address = self::address(
			$order->get_shipping_address_1(),
			$order->get_shipping_address_2(),
			$order->get_shipping_city(),
			$order->get_shipping_state(),
			$order->get_shipping_postcode()
		);

		$shipping_address_empty = empty(
			self::array_filter_recursive( $shipping_address, 'WC_ChargeAfter_Helper::filter_empty_string' )
		);

		$shipping_address = $shipping_address_empty ? $billing_address : $shipping_address;

		/*
		 * Items
		 */
		$items = array();
		if ( $order->get_items() ) {
			foreach ( $order->get_items() as $item_id => $item ) {
				$product_variation_id = $item['variation_id'];

				if ( $product_variation_id ) {
					$sku = get_post_meta( $item['variation_id'], '_sku', true );
				} else {
					$product = new WC_Product( $item['product_id'] );
					$sku     = $product->get_sku();
				}

				if ( ! $sku ) {
					$sku = $item_id;
				}

				$leasable = get_post_meta( $item['product_id'], 'wc_chargeafter_' . NON_LEASABLE_META, true ) !== 'yes';
				$warranty = get_post_meta( $item['product_id'], 'wc_chargeafter_' . WARRANTY_META, true ) == 'yes';

				$item_details = array(
					'name'     => strval( $item['name'] ),
					'price'    => floatval( $order->get_item_subtotal( $item ) ),
					'sku'      => strval( $sku ),
					'quantity' => intval( $item['qty'] ),
					'leasable' => boolval( $leasable ),
				);

				if ( $warranty ) {
					$item_details['warranty'] = array(
						'name'  => strval( $item['name'] ),
						'price' => floatval( 0 ),
						'sku'   => strval( $sku ),
					);
				}

				array_push( $items, $item_details );
			}
		}

		if ( $order->get_items( 'fee' ) ) {
			foreach ( $order->get_items( 'fee' ) as $item_id => $item_fee ) {
				$item_details = array(
					'name'     => strval( $item_fee->get_name() ),
					'price'    => floatval( $item_fee->get_total() ),
					'sku'      => strval( 'fee_' . $item_id ),
					'quantity' => 1,
					'leasable' => true,
				);

				array_push( $items, $item_details );
			}
		}

		/*
		 * Discounts
		 */
		$discounts = array();
		foreach ( $order->get_items( 'coupon' ) as $coupon_item_id => $coupon_item ) {
			$coupon_line = array(
				'name'   => strval( $coupon_item['name'] ),
				'amount' => floatval( $coupon_item['discount_amount'] ),
			);

			array_push( $discounts, $coupon_line );
		}

		/*
		 * Checkout Data
		 */
		$checkout_data = array(
			'consumerDetails' => array(
				'firstName'          => strval( $order->get_billing_first_name() ),
				'lastName'           => strval( $order->get_billing_last_name() ),
				'email'              => strval( $order->get_billing_email() ),
				'mobilePhoneNumber'  => strval( $order->get_billing_phone() ),
				'merchantConsumerId' => strval( $order->get_user_id() ),
				'billingAddress'     => $billing_address,
				'shippingAddress'    => $shipping_address,
			),
			'cartDetails'     => array(
				'items'          => $items,
				'taxAmount'      => floatval( $order->get_total_tax() ),
				'shippingAmount' => floatval( $order->get_shipping_total() ),
				'totalAmount'    => floatval( $order->get_total() ),
			),
			'channel'         => 'E_COMMERCE',
		);

		if ( ! empty( get_locale() ) ) {
			$checkout_data['preferences'] = array(
				'language' => strval( get_locale() ),
			);
		}

		if ( ! empty( $order->get_currency() ) ) {
			$checkout_data['currency'] = strval( $order->get_currency() );
		}

		if ( count( $discounts ) > 0 ) {
			$checkout_data['cartDetails']['discounts'] = $discounts;
		}

		$filtered_data = self::array_filter_recursive( $checkout_data, 'WC_ChargeAfter_Helper::filter_empty_string' );

		return json_encode( $filtered_data );
	}

	/**
	 * Generate Address array
	 *
	 * @param $line1
	 * @param $line2
	 * @param $city
	 * @param $state
	 * @param $zip_code
	 *
	 * @return array
	 */
	public static function address( $line1, $line2, $city, $state, $zip_code ) {
		return array(
			'line1'   => strval( $line1 ),
			'line2'   => strval( $line2 ),
			'city'    => strval( $city ),
			'state'   => strval( $state ),
			'zipCode' => strval( $zip_code ),
		);
	}

	/**
	 * Override array_filter_recursive
	 *
	 * @param $input
	 * @param null $callback
	 *
	 * @return array
	 */
	public static function array_filter_recursive( $input, $callback = null ) {
		foreach ( $input as &$value ) {
			if ( is_array( $value ) ) {
				$value = self::array_filter_recursive( $value, $callback );
			}
		}

		return array_filter( $input, $callback );
	}

	/**
	 * Start checkout script
	 *
	 * @return void
	 */
	public static function enqueue_checkout_script() {
		if ( ! WC_ChargeAfter_Helper::is_enabled() || ! is_checkout() ) {
			return;
		}

		wp_enqueue_script(
			'chargeafter_checkout',
			plugins_url( 'assets/js/checkout.js', CHARGEAFTER_MAIN_FILE ),
			array( 'chargeafter_sdk' ),
			CHARGEAFTER_VERSION,
			true
		);

		wp_enqueue_style(
			'chargeafter_checkout',
			plugins_url( 'assets/css/checkout.css', CHARGEAFTER_MAIN_FILE ),
			array(),
			CHARGEAFTER_VERSION
		);

		$chargeafter_checkout_data = array(
			'settings' => array(
				'with_data_update' => WC_ChargeAfter_Helper::is_enabled_data_update(),
			),
			'url'      => WC_ChargeAfter_Helper::get_api_action_urls( home_url() ),
		);

		wp_localize_script(
			'chargeafter_checkout',
			'chargeAfterCheckoutData',
			$chargeafter_checkout_data
		);
	}
}
