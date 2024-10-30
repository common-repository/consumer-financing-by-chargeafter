<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_API_Exception
 *
 * Exception for ChargeAfter API
 *
 * @since 1.10.0
 */
class WC_ChargeAfter_API_Exception extends Exception {

	/**
	 * WC_ChargeAfter_API_Exception constructor.
	 *
	 * @param string $message
	 */
	public function __construct( $message ) {
		parent::__construct( $message );
	}
}
