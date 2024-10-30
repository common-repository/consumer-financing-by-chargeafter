<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ChargeAfter Gateway ID
 */
define( 'CHARGEAFTER_ID', 'wc_chargeafter_gateway' );

/**
 * Class WC_ChargeAfter_Gateway Extends WC_Payment_Gateway
 * ChargeAfter Gateway
 */
class WC_ChargeAfter_Gateway extends WC_Payment_Gateway {

	/**
	 * WC_ChargeAfter_Gateway constructor.
	 */
	public function __construct() {
		/*
		 * Payment Data
		 */
		$this->id                 = CHARGEAFTER_ID;
		$this->has_fields         = false;
		$this->method_title       = __( 'Consumer Financing by ChargeAfter', 'chargeafter-extension' );
		$this->method_description = __( 'ChargeAfter is a market-leading platform that connects retailers and lenders to offer consumers personalized Point of Sale Financing options.', 'chargeafter-extension' );

		/*
		 * Init forms
		 */
		$this->init_form_fields();
		$this->init_settings();

		/*
		 * Payment Data
		 */
		$this->enabled           = $this->get_option( 'enabled' );
		$this->title             = $this->get_option( 'title' );
		$this->description       = null;
		$this->order_button_text = __( 'Continue to payment', 'chargeafter-extension' );
		$this->supports          = array( 'refunds', 'products' );

		$this->icon = null;

		/*
		 * Actions
		 */
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'wp_enqueue_scripts', array( WC_ChargeAfter_Checkout_Script::class, 'enqueue_checkout_script' ) );
		add_action( 'woocommerce_api_wc_chargeafter_get_checkout_data', array( $this, 'get_checkout_data_api_callback' ) );
		add_action( 'woocommerce_api_wc_chargeafter_create_charge', array( $this, 'create_charge' ) );
		add_action( 'woocommerce_api_wc_chargeafter_on_data_update', array( $this, 'on_data_update' ) );
		add_action( 'woocommerce_api_wc_chargeafter_on_error_url', array( $this, 'on_error_url' ) );
		add_action( 'woocommerce_review_order_after_submit', array( $this, 'on_review_order_after_submit' ) );

		/*
		 * Filters
		 */
		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'change_payment_complete_order_status' ), 10, 2 );
		add_filter( 'woocommerce_get_sections_checkout', array( $this, 'add_checkout_sections' ) );
	}

	/**
	 * Init Form Settings
	 */
	public function init_form_fields() {
		$this->form_fields = require __DIR__ . '/admin/chargeafter-settings.php';
	}

	/**
	 * Override Settings Html
	 *
	 * @param array $form_fields
	 * @param bool  $echo
	 *
	 * @return string
	 */
	public function generate_settings_html( $form_fields = array(), $echo = true ) {
		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields();
		}

		foreach ( $form_fields as $key => $field ) {
			if ( isset( $field['parent_element'] ) ) {
				foreach ( $field['parent_element'] as $setting => $value ) {
					$setting_param = WC_ChargeAfter_Helper::get_setting_param( $setting );
					if ( ! is_null( $setting_param ) && $setting_param != $value ) {
						$form_fields[ $key ]['custom_attributes']['readonly'] = 'readonly';
					}
				}
			}
		}

		?>
		<script>
			function toogleFieldDisabled(e) {
				let d = document.getElementById("<?php echo esc_html( 'woocommerce_' . CHARGEAFTER_ID . '_' ); ?>" + e);
				d.toggleAttribute("readonly");
			}
		</script>
		<?php

		return parent::generate_settings_html( $form_fields, $echo );
	}

	/**
	 * Generate Divide HTML.
	 *
	 * @param string $key Field key.
	 * @param array  $data Field data.
	 *
	 * @return string
	 */
	public function generate_divide_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'       => '',
			'description' => '',
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<td scope="row" colspan="2" style="padding:0;margin:0;">
				<h3><?php echo esc_html( $data['title'] ); ?></h3>
				<?php if ( $data['description'] ) : ?>
					<p><?php echo esc_html( $data['description'] ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get Checkout Api Data
	 */
	public function get_checkout_data_api_callback() {
		$nonce    = ( isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : null );
		$order_id = ( isset( $_GET['orderId'] ) ? sanitize_text_field( wp_unslash( $_GET['orderId'] ) ) : null );

		if ( WC_ChargeAfter_Helper::verify_order_nonce( $nonce, $order_id ) ) {
			status_header( 200 );

			$order = wc_get_order( $order_id );
			echo wp_kses_post( WC_ChargeAfter_Checkout_Script::checkout_data( $order ) );
		} else {
			status_header( 401 );
		}

		exit;
	}

	/**
	 * Filter woocommerce payment complete
	 *
	 * @param $status
	 * @param int    $order_id
	 *
	 * @return string
	 */
	public function change_payment_complete_order_status( $status, $order_id = 0 ) {
		return WC_ChargeAfter_Helper::is_chargeafter_gateway( $order_id ) ? 'on-hold' : $status;
	}

	/**
	 * Ovveride Get Title
	 *
	 * @return string
	 */
	public function get_title() {
		if ( is_checkout() ) {
			ob_start();
			WC_ChargeAfter_Helper::get_checkout_brand_label( $this->title );

			return ob_get_clean();
		}

		return parent::get_title();
	}

	/**
	 * Adds an "all payment methods" and a "wc_chargeafter_gateway" section to the gateways settings page
	 *
	 * @param array $sections the sections for the payment gateways tab.
	 *
	 * @return array
	 */
	public function add_checkout_sections( array $sections ) {
		$sections[ $this->id ] = $this->method_title;

		// unsetting and setting again, so it appears last in the array.
		unset( $sections[''] );

		// @codingStandardsIgnoreLine
		$sections[''] = __( 'All payment methods', 'woocommerce-payments' );

		return $sections;
	}

	/**
	 * Override Process Payment.
	 *
	 * @param int $order_id
	 *
	 * @return array
	 *
	 * @throws WC_Data_Exception
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			$order->set_payment_method_title( sanitize_text_field( $this->get_option( 'title' ) ) );
			$order->save();
		}

		$hash = '#checkoutfinance=1;orderid=' . $order_id . ';nonce=' . WC_ChargeAfter_Helper::create_order_nonce( $order_id );
		return array(
			'result'   => 'success',
			'redirect' => wc_get_checkout_url() . $hash,
		);
	}

	/**
	 * Action Create ChargeAfter
	 */
	public function create_charge() {
		global $woocommerce;

		$nonce    = ( isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : null );
		$order_id = ( isset( $_GET['orderId'] ) ? sanitize_text_field( wp_unslash( $_GET['orderId'] ) ) : null );

		$token = ( isset( $_POST['confirmationToken'] ) ? sanitize_text_field( wp_unslash( $_POST['confirmationToken'] ) ) : null );
		// @codingStandardsIgnoreLine
		$data  = ( isset( $_POST['data'] ) ? WC_ChargeAfter_Helper::recursive_sanitize_text_field( $_POST['data'] ) : array() );

		if ( WC_ChargeAfter_Helper::verify_order_nonce( $nonce, $order_id ) && ! empty( $token ) ) {
			$order = wc_get_order( $order_id );

			try {
				WC_ChargeAfter_Order_Handler::try_authorize_order( $order_id, $token, $data );

				if ( null != $woocommerce->cart ) {
					$woocommerce->cart->empty_cart();
				}

				echo json_encode(
					array(
						'redirect' => esc_url_raw( $this->get_return_url( $order ) ),
					)
				);
			} catch ( WC_ChargeAfter_API_Exception $e ) {
				$order->update_status( 'pending', __( 'Pending payment', 'chargeafter-extension' ) );

				status_header( 424 );

				if ( $woocommerce->session ) {
					wc_add_notice( 'Failed to finalize payment', 'error' );
				}

				echo json_encode(
					array(
						'redirect' => esc_url_raw( wc_get_checkout_url() ),
					)
				);
			}
		} else {
			status_header( 401 );

			if ( $woocommerce->session ) {
				wc_add_notice( 'Failed to create checkout', 'error' );
			}

			echo json_encode(
				array(
					'redirect' => esc_url_raw( wc_get_checkout_url() ),
				)
			);
		}

		exit;
	}

	/**
	 * Update Callback
	 */
	public function on_data_update() {
		$nonce    = ( isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : null );
		$order_id = ( isset( $_GET['orderId'] ) ? sanitize_text_field( wp_unslash( $_GET['orderId'] ) ) : null );

		if ( WC_ChargeAfter_Helper::verify_order_nonce( $nonce, $order_id ) ) {
			// @codingStandardsIgnoreLine
			$consumer_details = ( isset( $_POST['consumerDetails'] ) ? WC_ChargeAfter_Helper::recursive_sanitize_text_field( $_POST['consumerDetails'], 'consumerDetails' ) : array() );
			$order            = wc_get_order( $order_id );

			if ( ! empty( $consumer_details ) ) {
				$consumer_address = array(
					'first_name' => $consumer_details['firstName'],
					'last_name'  => $consumer_details['lastName'],
					'address_1'  => $consumer_details['billingAddress']['line1'],
					'address_2'  => $consumer_details['billingAddress']['line2'],
					'city'       => $consumer_details['billingAddress']['city'],
					'state'      => $consumer_details['billingAddress']['state'],
					'postcode'   => $consumer_details['billingAddress']['zipCode'],
					'country'    => 'US',
					'email'      => $consumer_details['email'],
					'phone'      => $consumer_details['mobilePhoneNumber'],
				);

				$order->set_address( $consumer_address, 'billing' );
				$order->set_address( $consumer_address, 'shipping' );
			}

			$order->calculate_totals();
			$new_amounts = array(
				'taxAmount'      => floatval( $order->get_total_tax() ),
				'shippingAmount' => floatval( $order->get_shipping_total() ),
				'totalAmount'    => floatval( $order->get_total() ),
			);

			status_header( 200 );

			echo json_encode( $new_amounts );
		} else {
			status_header( 401 );
		}
		exit;
	}

	/**
	 * Error url Callback
	 */
	public function on_error_url() {
		$error      = ( isset( $_GET['errorMsg'] ) ? sanitize_text_field( wp_unslash( $_GET['errorMsg'] ) ) : null );
		$error_code = ( isset( $_GET['errorCode'] ) ? sanitize_text_field( wp_unslash( $_GET['errorCode'] ) ) : null );

		$response = new WP_HTTP_Response( array( 'redirect' => esc_url_raw( wc_get_checkout_url() ) ), 200 );

		if ( ! in_array( $error_code, array( 'BACK_TO_STORE', 'CONSUMER_CANCELLED' ) ) ) {
			wc_add_notice( $this->title . ' - ' . $error, 'error' );
		}

		echo json_encode( $response->get_data() );
		status_header( $response->get_status() );

		exit;
	}

	/**
	 * Do action woocommerce_review_order_after_payment
	 */
	public function on_review_order_after_submit() {
		?>
		<script>
			window.dispatchEvent(new CustomEvent('ca_update_checkout_payment_method'));
		</script>
		<?php
	}

	/**
	 * Override Process refund.
	 *
	 * @param int    $order_id
	 * @param null   $amount
	 * @param string $reason
	 *
	 * @return bool
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		return ( WC_ChargeAfter_Order_Handler::process_refund_amount( $order_id, $amount ) );
	}
}
