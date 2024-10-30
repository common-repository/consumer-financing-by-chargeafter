<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_Order_Note
 */
class WC_ChargeAfter_Order_Note {

	/**
	 * Note messages
	 */
	const NEW_AUTH_NOTE           = 'ChargeAfter Charge authorized (Charge ID: [UUID])';
	const NEW_ORDER_NOTE          = 'Order created via ChargeAfter';
	const VOID_AUTH_NOTE          = 'ChargeAfter Charge voided (Void ID: [UUID])';
	const SETTLE_NOTE             = 'ChargeAfter Charge settled (Settle ID: [UUID])';
	const REFUND_NOTE             = 'ChargeAfter Charge refunded [Amount] [Currency] (Refund ID: [UUID])';
	const SETTLE_FAILED_NOTE      = 'ChargeAfter error: unable to settle charge. Charge ID [UUID] is already voided';
	const GENERAL_FAILED_NOTE     = 'ChargeAfter error: could not complete operation';
	const AMOUNT_FAILED_NOTE      = "Unable to settle Charge. You can't settle a Charge for an amount different than authorized ([Amount] [Currency])";
	const REFUNDED_FAILED_NOTE    = 'ChargeAfter error: unsettled payment cannot be refunded. Confirm or cancel the charge';
	const TAX_UPDATED_NOTE        = 'ChargeAfter: Order tax changed (Charge ID: [UUID])';
	const TAX_UPDATED_FAILED_NOTE = 'ChargeAfter error: unable to update tax for the order';

	/**
	 * Add settle note
	 *
	 * @param string $order_id
	 * @param string $settle_transaction_id
	 *
	 * @return void
	 */
	public static function add_settle_note( $order_id, $settle_transaction_id ) {
		self::add_order_note(
			$order_id,
			function ( $order ) use ( $settle_transaction_id ) {
				return str_replace( '[UUID]', $settle_transaction_id, self::SETTLE_NOTE );
			}
		);
	}

	/**
	 * Add settleFailed note
	 *
	 * @param string $order_id
	 *
	 * @return void
	 */
	public static function add_settle_failed_note( $order_id ) {
		self::add_order_note(
			$order_id,
			function ( $order ) {
				return str_replace( '[UUID]', $order->get_transaction_id(), self::SETTLE_FAILED_NOTE );
			}
		);
	}

	/**
	 * Add voidAuth note
	 *
	 * @param string $order_id
	 * @param string $void_transaction_id
	 *
	 * @return void
	 */
	public static function add_void_auth_note( $order_id, $void_transaction_id ) {
		self::add_order_note(
			$order_id,
			function ( $order ) use ( $void_transaction_id ) {
				return str_replace( '[UUID]', $void_transaction_id, self::VOID_AUTH_NOTE );
			}
		);
	}

	/**
	 * Add refund note
	 *
	 * @param string $order_id
	 * @param $amount
	 * @param $refund_transaction_id
	 *
	 * @return void
	 */
	public static function add_refund_note( $order_id, $amount, $refund_transaction_id ) {
		self::add_order_note(
			$order_id,
			function ( $order ) use ( $amount, $refund_transaction_id ) {
				$note = str_replace( '[UUID]', $refund_transaction_id, self::REFUND_NOTE );
				$note = str_replace( '[Amount]', $amount, $note );
				return str_replace( '[Currency]', $order->get_currency(), $note );
			}
		);
	}

	/**
	 * Add newAuth note
	 *
	 * @param string $order_id
	 *
	 * @return void
	 */
	public static function add_new_auth_note( $order_id ) {
		self::add_order_note(
			$order_id,
			function ( $order ) {
				return str_replace( '[UUID]', $order->get_transaction_id(), self::NEW_AUTH_NOTE );
			}
		);
	}

	/**
	 * Add newOrder note
	 *
	 * @param string $order_id
	 * @param string|null $lender_name
	 * @param string|null $lease_id
	 *
	 * @return void
	 */
	public static function add_new_order_note( $order_id, $lender_name = null, $lease_id = null ) {
		self::add_order_note(
			$order_id,
			function ( $order ) use ( $lease_id, $lender_name ) {
				$note = self::NEW_ORDER_NOTE;

				if ( is_string( $lender_name ) && null != $lender_name ) {
					$note .= "\n Lender: {$lender_name}";
				}

				if ( is_string( $lease_id ) && null != $lease_id ) {
					$note .= "\n Lease ID: {$lease_id}";
				}

				return $note;
			}
		);
	}

	/**
	 * Add generalFailed note
	 *
	 * @param string $order_id
	 *
	 * @return void
	 */
	public static function add_general_failed_note( $order_id ) {
		self::add_order_note( $order_id, self::GENERAL_FAILED_NOTE );
	}

	/**
	 * Add amountFailed note
	 *
	 * @param string $order_id
	 * @param $amount
	 *
	 * @return void
	 */
	public static function add_amount_failed_note( $order_id, $amount ) {
		self::add_order_note(
			$order_id,
			function ( $order ) use ( $amount ) {
				$note = str_replace( '[Amount]', $amount, self::AMOUNT_FAILED_NOTE );
				return str_replace( '[Currency]', $order->get_currency(), $note );
			}
		);
	}

	/**
	 * Add refundedFailed note
	 *
	 * @param string $order_id
	 *
	 * @return void
	 */
	public static function add_refund_failed_note( $order_id ) {
		self::add_order_note( $order_id, self::REFUNDED_FAILED_NOTE );
	}

	/**
	 * Add tax updated note
	 *
	 * @param string $order_id
	 *
	 * @return void
	 */
	public static function add_tax_updated_note( $order_id ) {
		self::add_order_note(
			$order_id,
			function ( $order ) {
				return str_replace( '[UUID]', $order->get_transaction_id(), self::TAX_UPDATED_NOTE );
			}
		);
	}

	/**
	 * Add tax updated failed note
	 *
	 * @param string $order_id
	 *
	 * @return void
	 */
	public static function add_tax_updated_failed_note( $order_id ) {
		self::add_order_note( $order_id, self::TAX_UPDATED_FAILED_NOTE );
	}

	/**
	 * Add order note
	 *
	 * @param string $order_id
	 * @param string|callable $note
	 *
	 * @return void
	 */
	private static function add_order_note( $order_id, $note ) {
		$order = wc_get_order( $order_id );

		if ( is_callable( $note ) ) {
			$note = $note( $order );
		}

		$order->add_order_note( $note );
	}
}
