<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_Order_Handler
 */
class WC_ChargeAfter_Order_Handler {

	/**
	 * Class Instance
	 *
	 * @var WC_ChargeAfter_Order_Handler
	 */
	private static $instance;

	/**
	 * WC_ChargeAfter_Order_Handler constructor.
	 */
	public function __construct() {
		/*
		 * Actions
		 */
		add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'order_on_hold_to_processing_or_completed' ) );
		add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'order_on_hold_to_processing_or_completed' ) );

		add_action( 'woocommerce_order_status_on-hold_to_cancelled', array( $this, 'order_on_hold_to_cancelled' ) );
		add_action( 'woocommerce_order_status_completed_to_cancelled', array( $this, 'order_processing_or_completed_to_cancelled' ) );

		add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 4 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'order_status_refunded' ), 9 );
	}

	/**
	 * Get instance
	 *
	 * @return WC_ChargeAfter_Order_Handler
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Try to authorize order
	 *
	 * @param string $order_id
	 * @param string $token
	 * @param array $data
	 *
	 * @throws WC_ChargeAfter_API_Exception
	 */
	public static function try_authorize_order( $order_id, $token, $data ) {
		$charge_data = WC_ChargeAfter_API::api_create_charge( $token, $order_id );

		if ( $charge_data ) {
			$order = wc_get_order( $order_id );
			$order->payment_complete( $charge_data['id'] );

			$lender_name = null;
			$offer       = key_exists( 'offer', $charge_data ) ? $charge_data['offer'] : null;

			if ( $offer && is_array( $offer ) ) {
				$lender = key_exists( 'lender', $offer ) ? $offer['lender'] : null;

				if ( $lender && is_array( $lender ) ) {
					$lender_name = key_exists( 'name', $lender ) ? sanitize_text_field( $lender['name'] ) : null;
				}
			}

			$lease_id = null;

			if (
				$data &&
				key_exists( 'lender', $data ) &&
				key_exists( 'information', $data['lender'] ) &&
				key_exists( 'leaseId', $data['lender']['information'] )
			) {
				$lease_id = $data['lender']['information']['leaseId'];
			}

			WC_ChargeAfter_Order_Note::add_new_order_note( $order_id, $lender_name, $lease_id );
			WC_ChargeAfter_Order_Note::add_new_auth_note( $order_id );

			$charge_amount = key_exists( 'totalAmount', $charge_data ) ? $charge_data['totalAmount'] : null;
			$tax_amount    = self::get_tax_total( $order );

			if ( $charge_amount && $tax_amount && $charge_amount < $order->get_total() ) {
				$diff_amount = wc_format_decimal( round( $order->get_total() - $charge_amount, wc_get_price_decimals() ) );

				if ( $diff_amount == $tax_amount ) {
					self::try_reset_order_tax( $order_id );
				}
			}

			self::trigger_auto_settle_order( $order_id );
		}
	}

	/**
	 * Hold to Processing OR Completed
	 *
	 * @param $order_id
	 */
	public function order_on_hold_to_processing_or_completed( $order_id ) {
		if ( WC_ChargeAfter_Helper::is_chargeafter_gateway( $order_id ) ) {
			$order       = wc_get_order( $order_id );
			$charge_data = self::get_charge_by_transaction_id( $order );

			if ( $charge_data ) {
				switch ( $charge_data['state'] ) {
					case WC_ChargeAfter_ChargeState::AUTHORIZED:
					case WC_ChargeAfter_ChargeState::PARTIALLY_SETTLED:
					case WC_ChargeAfter_ChargeState::CHARGEBACK:
						$this->try_settle_order( $order_id );
						break;

					case WC_ChargeAfter_ChargeState::VOIDED:
						WC_ChargeAfter_Order_Note::add_settle_failed_note( $order_id );
						$order->update_status( 'failed', __( 'Failed payment', 'chargeafter-extension' ) );
						break;
				}
			}
		}
	}

	/**
	 * Try settle order
	 *
	 * @param $order_id
	 */
	private function try_settle_order( $order_id ) {
		$order       = wc_get_order( $order_id );
		$charge_data = self::get_charge_by_transaction_id( $order );

		if ( $charge_data ) {
			$charge_amount = $charge_data['totalAmount'];

			if ( $order->get_total() == $charge_amount ) {
				try {
					$settle_data = WC_ChargeAfter_API::api_settle_charge( $order->get_transaction_id() );

					WC_ChargeAfter_Order_Note::add_settle_note( $order_id, $settle_data['id'] );
				} catch ( WC_ChargeAfter_API_Exception $e ) {
					WC_ChargeAfter_Order_Note::add_settle_failed_note( $order_id );

					$order->update_status( 'failed', __( 'Failed payment', 'chargeafter-extension' ) );
				}
			} else {
				WC_ChargeAfter_Order_Note::add_amount_failed_note( $order_id, $charge_amount );

				$order->update_status( 'failed', __( 'Failed payment', 'chargeafter-extension' ) );
			}
		}
	}

	/**
	 * Order on-hold to cancelled
	 *
	 * @param $order_id
	 */
	public function order_on_hold_to_cancelled( $order_id ) {
		if ( WC_ChargeAfter_Helper::is_chargeafter_gateway( $order_id ) ) {
			$order       = wc_get_order( $order_id );
			$charge_data = self::get_charge_by_transaction_id( $order );

			if ( $charge_data ) {
				if ( $charge_data['state'] == WC_ChargeAfter_ChargeState::AUTHORIZED ) {
					try {
						$void_data = WC_ChargeAfter_API::api_void_charge( $order->get_transaction_id() );

						WC_ChargeAfter_Order_Note::add_void_auth_note( $order_id, $void_data['id'] );
					} catch ( WC_ChargeAfter_API_Exception $e ) {
						WC_ChargeAfter_Order_Note::add_general_failed_note( $order_id );
					}
				}
			}
		}
	}

	/**
	 * Order status change. Fixing the previous status
	 *
	 * @param $order_id
	 * @param $status_from
	 * @param $status_to
	 * @param WC_Order $order
	 */
	public function order_status_changed( $order_id, $status_from, $status_to, $order ) {
		if (
			WC_ChargeAfter_Helper::is_chargeafter_gateway( $order_id )
			&& ( 'on-hold' == $status_from && 'refunded' == $status_to )
		) {
			$charge_data = self::get_charge_by_transaction_id( $order );

			if ( $charge_data && $charge_data['state'] == WC_ChargeAfter_ChargeState::AUTHORIZED ) {
				/*
				 * Last order note
				 */
				remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );
				$notes = get_comments(
					array(
						'post_id' => $order_id,
						'orderby' => 'comment_ID',
						'order'   => 'DESC',
						'approve' => 'approve',
						'type'    => 'order_note',
						'number'  => 1,
					)
				);
				add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

				/*
				 * Delete order note
				 */
				wc_delete_order_note( $notes[0]->comment_ID );

				/*
				 * Set status
				 */
				$order->set_status( $status_from );
				$order->save();
			}
		}
	}

	/**
	 * Order hold to refunded
	 *
	 * @param $order_id
	 */
	public function order_status_refunded( $order_id ) {
		if ( WC_ChargeAfter_Helper::is_chargeafter_gateway( $order_id ) ) {
			$order       = wc_get_order( $order_id );
			$charge_data = self::get_charge_by_transaction_id( $order );

			$allow_refund_state = array(
				WC_ChargeAfter_ChargeState::SETTLED,
				WC_ChargeAfter_ChargeState::REFUNDED,
				WC_ChargeAfter_ChargeState::FULLY_REFUNDED,
				WC_ChargeAfter_ChargeState::PARTIALLY_REFUNDED,
				WC_ChargeAfter_ChargeState::PARTIALLY_SETTLED_REFUNDED,
				WC_ChargeAfter_ChargeState::SETTLED_PARTIALLY_REFUNDED,
				WC_ChargeAfter_ChargeState::PARTIALLY_SETTLED_PARTIALLY_REFUNDED,
			);

			if ( $charge_data && ! in_array( $charge_data['state'], $allow_refund_state ) ) {
				/*
				 * Prevent action
				 */
				remove_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );

				WC_ChargeAfter_Order_Note::add_refund_failed_note( $order_id );
			}
		}
	}

	/**
	 * Order complete | processing to cancelled
	 *
	 * @param $order_id
	 */
	public function order_processing_or_completed_to_cancelled( $order_id ) {
		if ( WC_ChargeAfter_Helper::is_chargeafter_gateway( $order_id ) ) {
			$order       = wc_get_order( $order_id );
			$charge_data = self::get_charge_by_transaction_id( $order );

			if ( $charge_data ) {
				switch ( $charge_data['state'] ) {
					case WC_ChargeAfter_ChargeState::PARTIALLY_REFUNDED:
					case WC_ChargeAfter_ChargeState::SETTLED_PARTIALLY_REFUNDED:
					case WC_ChargeAfter_ChargeState::PARTIALLY_SETTLED_PARTIALLY_REFUNDED:
					case WC_ChargeAfter_ChargeState::SETTLED:
						self::process_refund_amount( $order_id, $charge_data['totalAmount'] );
						break;
				}
			}
		}
	}

	/**
	 * Refund Amount
	 *
	 * @param $order_id
	 * @param $amount
	 *
	 * @return bool
	 */
	public static function process_refund_amount( $order_id, $amount ) {
		$refund_state = false;

		$order       = wc_get_order( $order_id );
		$charge_data = self::get_charge_by_transaction_id( $order );

		if ( $charge_data ) {
			switch ( $charge_data['state'] ) {
				case WC_ChargeAfter_ChargeState::PARTIALLY_REFUNDED:
				case WC_ChargeAfter_ChargeState::SETTLED_PARTIALLY_REFUNDED:
				case WC_ChargeAfter_ChargeState::PARTIALLY_SETTLED_PARTIALLY_REFUNDED:
				case WC_ChargeAfter_ChargeState::SETTLED:
					if (
						in_array(
							$charge_data['state'],
							array(
								WC_ChargeAfter_ChargeState::PARTIALLY_REFUNDED,
								WC_ChargeAfter_ChargeState::SETTLED_PARTIALLY_REFUNDED,
								WC_ChargeAfter_ChargeState::PARTIALLY_SETTLED_PARTIALLY_REFUNDED,
							)
						)
						&& isset( $charge_data['refundedAmount'] ) && isset( $charge_data['totalAmount'] )
					) {
						$allow_refund_amount = ( $charge_data['totalAmount'] - $charge_data['refundedAmount'] );
						$amount              = ( $amount > $allow_refund_amount ? $allow_refund_amount : $amount );
					}

					try {
						$refund_data = WC_ChargeAfter_API::api_refund_amount( $order->get_transaction_id(), $amount );

						if ( key_exists( 'id', $refund_data ) ) {
							WC_ChargeAfter_Order_Note::add_refund_note( $order_id, $amount, $refund_data['id'] );
							$refund_state = true;
						}
					} catch ( WC_ChargeAfter_API_Exception $e ) {
						WC_ChargeAfter_Order_Note::add_general_failed_note( $order_id );
					}

					break;

				default:
					$refund_state = new WP_Error( 'error', 'ChargeAfter error: unsettled payment cannot be refunded. Confirm or cancel the charge' );
					break;
			}
		}

		return $refund_state;
	}

	/**
	 * Set tax amount = 0 to order
	 *
	 * @param $order_id
	 *
	 * @return void
	 */
	private static function try_reset_order_tax( $order_id ) {
		try {
			$order = wc_get_order( $order_id );

			if ( $order ) {
				$tax_callable = function ( $item ) use ( $order ) {
					if ( is_callable( array( $item, 'set_taxes' ) ) ) {
						$item->set_taxes( false );
					}

					if ( is_callable( array( $item, 'set_tax_class' ) ) ) {
						$item->set_tax_class( '' );
					}

					if ( is_callable( array( $item, 'set_tax_status' ) ) ) {
						$item->set_tax_status( 'none' );
					}

					if ( is_callable( array( $item, 'set_subtotal_tax' ) ) ) {
						$item->set_subtotal_tax( 0 );
					}

					if ( is_callable( array( $item, 'set_total_tax' ) ) ) {
						$item->set_total_tax( 0 );
					}

					if ( is_callable( array( $item, 'set_discount_tax' ) ) ) {
						$item->set_discount_tax( 0 );
					}

					$order->add_item( $item );
				};

				foreach ( $order->get_items( array( 'line_item', 'fee', 'shipping', 'coupon' ) ) as $value ) {
					$tax_callable( $value );
				}

				foreach ( $order->get_items( 'tax' ) as $key => $value ) {
					$order->remove_item( $key );
				}

				$order->set_discount_tax( 0 );
				$order->set_shipping_tax( 0 );
				$order->set_cart_tax( 0 );

				// Set VAT exempt
				$order->set_meta_data(
					array(
						'is_vat_exempt' => 'yes',
					)
				);

				$order->save();
				$order->calculate_totals( false );

				WC_ChargeAfter_Order_Note::add_tax_updated_note( $order_id );
			}
		} catch ( WC_Data_Exception $e ) {
			WC_ChargeAfter_Logger::log( $e->getMessage() );
			WC_ChargeAfter_Order_Note::add_tax_updated_failed_note( $order_id );
		}
	}

	/**
	 * Trigger auto settle order
	 *
	 * Trigger settle charge. Execute the woo action: woocommerce_order_status_on-hold_to_completed
	 *
	 * @param $order_id
	 */
	private static function trigger_auto_settle_order( $order_id ) {
		if ( WC_ChargeAfter_Helper::is_enabled_auto_settle() ) {
			$order = wc_get_order( $order_id );
			$order->update_status( 'completed' );
		}
	}

	/**
	 * Get charge by transaction id
	 *
	 * @param WC_Order $order
	 *
	 * @return array|null
	 */
	private static function get_charge_by_transaction_id( $order ) {
		try {
			return WC_ChargeAfter_API::api_get_charge_data( $order->get_transaction_id() );
		} catch ( WC_ChargeAfter_API_Exception $e ) {
			WC_ChargeAfter_Order_Note::add_general_failed_note( $order->get_id() );
			return null;
		}
	}

	/**
	 * Get order tax total amount
	 *
	 * @param WC_Order $order
	 *
	 * @return float
	 */
	private static function get_tax_total( $order ) {
		$tax_total = 0;

		if ( $order ) {
			$taxes = $order->get_taxes();

			if ( $taxes ) {
				foreach ( $taxes as $tax ) {
					$tax_total += (float) $tax->get_tax_total() + (float) $tax->get_shipping_tax_total();
				}
			}
		}

		return (float) $tax_total;
	}
}

/**
 * Init WC_ChargeAfter_Order_Handler
 */
WC_ChargeAfter_Order_Handler::get_instance();
