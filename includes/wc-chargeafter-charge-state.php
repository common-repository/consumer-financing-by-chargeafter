<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface WC_ChargeAfter_ChargeState
 * ChargeAfter state`s
 */
class WC_ChargeAfter_ChargeState {

	const AUTHORIZED                           = 'AUTHORIZED';
	const SETTLED                              = 'SETTLED';
	const PARTIALLY_SETTLED                    = 'PARTIALLY_SETTLED';
	const VOIDED                               = 'VOIDED';
	const REFUNDED                             = 'REFUNDED';
	const PARTIALLY_SETTLED_REFUNDED           = 'PARTIALLY_SETTLED_REFUNDED';
	const FULLY_REFUNDED                       = 'FULLY_REFUNDED';
	const PARTIALLY_REFUNDED                   = 'PARTIALLY_REFUNDED';
	const SETTLED_PARTIALLY_REFUNDED           = 'SETTLED_PARTIALLY_REFUNDED';
	const PARTIALLY_SETTLED_PARTIALLY_REFUNDED = 'PARTIALLY_SETTLED_PARTIALLY_REFUNDED';
	const CHARGEBACK                           = 'CHARGEBACK';
}
