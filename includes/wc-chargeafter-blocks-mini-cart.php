<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

class WC_ChargeAfter_Blocks_Mini_Cart implements IntegrationInterface {


	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wc-chargeafter-gateway-mini-cart';
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
	public function register_main_integration() {
		$script_asset_path = __DIR__ . '/../assets/blocks/blocks-mini-cart-frontend.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => CHARGEAFTER_VERSION,
			);

		wp_register_script(
			'wc-chargeafter-gateway-blocks-mini-cart-frontend',
			plugins_url( '/assets/blocks/blocks-mini-cart-frontend.js', CHARGEAFTER_MAIN_FILE ),
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		add_filter(
			'render_block',
			function ( $content, $block ) {
				if ( ! empty( $block['blockName'] ) && 'woocommerce/mini-cart' === $block['blockName'] ) {
					WC_ChargeAfter_Widget::init_promo_script();

					$custom_block = '<div data-block-name="wc-chargeafter-gateway/mini-cart-promotional-block" class="wp-block-woocommerce-wc-chargeafter-gateway-mini-cart-promo"></div>';

					$content = preg_replace( '/(<div data-block-name="woocommerce\/mini-cart-footer-block" class="wp-block-woocommerce-mini-cart-footer-block[\w\s-]*">)(.*)/', '$1' . $custom_block . '$2', $content );
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
		return array( 'wc-chargeafter-gateway-blocks-mini-cart-frontend' );
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
