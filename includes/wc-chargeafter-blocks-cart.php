<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

class WC_ChargeAfter_Blocks_Cart implements IntegrationInterface {


	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wc-chargeafter-gateway-cart';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {
		$this->register_main_integration();
	}

	/**
	 * Registers the main JS file required to add filters and Slot/Fills.
	 */
	private function register_main_integration() {
		$script_asset_path = __DIR__ . '/../assets/blocks/blocks-cart-frontend.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => CHARGEAFTER_VERSION,
			);

		wp_register_script(
			'wc-chargeafter-gateway-blocks-cart-frontend',
			plugins_url( '/assets/blocks/blocks-cart-frontend.js', CHARGEAFTER_MAIN_FILE ),
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		add_filter(
			'render_block',
			function ( $content, $block ) {
				if ( in_array( $block['blockName'], array( 'woocommerce/cart', 'woocommerce/checkout' ) ) ) {
					WC_ChargeAfter_Widget::init_promo_script();

					$custom_block = '<div data-block-name="' . $this->get_name() . '" class="wp-block-woocommerce-wc-chargeafter-gateway-cart-promo"></div>';

					if ( 'woocommerce/cart' == $block['blockName'] ) {
						$content = preg_replace( '/(<div data-block-name="woocommerce\/proceed-to-checkout-block" class="wp-block-woocommerce-proceed-to-checkout-block[\w\s-]*">)(.*)/', $custom_block . '$1$2', $content );
					}

					if ( 'woocommerce/checkout' == $block['blockName'] ) {
						$content = preg_replace( '/(<div data-block-name="woocommerce\/checkout-order-summary-block" class="wp-block-woocommerce-checkout-order-summary-block[\w\s-]*">)(.*)/', $custom_block . '$1$2', $content );
					}
				}

				return $content;
			},
			10,
			2
		);
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return array( 'wc-chargeafter-gateway-blocks-cart-frontend' );
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return array();
	}

	/**
	 * Returns an array of script data to localize in the frontend context.
	 *
	 * @return array
	 */
	public function get_script_data() {
		return array();
	}
}
