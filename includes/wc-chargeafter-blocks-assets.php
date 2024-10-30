<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ChargeAfter_Blocks_Assets {


	/**
	 * WC_ChargeAfter_Blocks_Assets constructor.
	 */
	public function __construct() {
		$hook = is_admin() ? 'admin_print_footer_scripts' : 'wp_print_footer_scripts';

		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( $hook, array( $this, 'enqueue_asset_data' ), 1 );
	}

	/**
	 * Initialize the class.
	 *
	 * @return WC_ChargeAfter_Blocks_Assets
	 */
	public static function init() {
		return new WC_ChargeAfter_Blocks_Assets();
	}

	/**
	 * Register assets for the block.
	 *
	 * @return void
	 */
	public function register_assets() {
		$script_asset_path = __DIR__ . '/../assets/blocks/wc-charge-after-blocks-settings.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => CHARGEAFTER_VERSION,
			);

		wp_register_script(
			'wc-chargeafter-blocks-settings',
			plugins_url( '/assets/blocks/wc-charge-after-blocks-settings.js', CHARGEAFTER_MAIN_FILE ),
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
	}

	/**
	 * Enqueue asset data for the block.
	 * This data will be used to localize the block script.
	 *
	 * @return void
	 */
	public function enqueue_asset_data() {
		if ( wp_script_is( 'wc-chargeafter-blocks-settings', 'enqueued' ) ) {
			$data = rawurlencode(
				wp_json_encode(
					array(
						'widget-type'        => WC_ChargeAfter_Helper::get_promo_type(),
						'widget-tag'         => WC_ChargeAfter_Helper::get_promo_tag(),
						'financial-page-url' => WC_ChargeAfter_Helper::get_promo_url(),
					)
				)
			);

			$settings_script = 'var wcChargeAfterBlocksSettings = wcChargeAfterBlocksSettings || ' .
				"JSON.parse( decodeURIComponent( '" . esc_js( $data ) . "' ) );";

			wp_add_inline_script(
				'wc-chargeafter-blocks-settings',
				$settings_script,
				'before'
			);
		}
	}
}
