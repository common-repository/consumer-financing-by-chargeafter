<?php
/**
 * Plugin Name: Consumer Financing by ChargeAfter
 * Requires Plugins: woocommerce
 * Plugin URI: https://docs.chargeafter.com/docs/woocommerce
 * Description: ChargeAfter connects merchants and lenders to offer consumers approved financing from multiple lenders across the entire credit spectrum.
 * Author: ChargeAfter
 * Author URI: http://www.chargeafter.com/
 * Version: 1.12.0
 * Requires at least: 4.8
 * Tested up to: 6.6
 *
 * WC requires at least: 3.0
 * WC tested up to: 9.1
 *
 * Text Domain: chargeafter-extension
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ChargeAfter Gateway Version
 */
define( 'CHARGEAFTER_VERSION', '1.12.0' );

/**
 * ChargeAfter Plugin Name
 */
define( 'CHARGEAFTER_MAIN_FILE', __FILE__ );

if ( ! class_exists( 'WC_ChargeAfter' ) ) :

	/**
	 * Main ChargeAfter Class
	 */
	class WC_ChargeAfter {

		/**
		 * WC_ChargeAfter Instance
		 *
		 * @var WC_ChargeAfter
		 */
		private static $instance;

		/**
		 * Get Instance
		 *
		 * @return WC_ChargeAfter
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * WC_ChargeAfter constructor.
		 */
		public function __construct() {
			add_action( 'before_woocommerce_init', array( $this, 'before_woocommerce_init' ), 1, 1 );
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		/**
		 * Init plugin
		 */
		public function init() {
			self::require_packages();

			// Actions
			add_action( 'woocommerce_blocks_loaded', array( $this, 'register_blocks_support' ) );

			// Filters
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateways' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		}

		/**
		 * Require plugin packages
		 *
		 * @return boolean
		 */
		public static function require_packages() {
			$root_include_path = __DIR__ . '/includes';

			require_once $root_include_path . '/wc-chargeafter-charge-state.php';
			require_once $root_include_path . '/wc-chargeafter-helper.php';
			require_once $root_include_path . '/wc-chargeafter-api.php';
			require_once $root_include_path . '/wc-chargeafter-api-exception.php';
			require_once $root_include_path . '/wc-gateway-chargeafter.php';
			require_once $root_include_path . '/wc-chargeafter-order-handler.php';
			require_once $root_include_path . '/wc-chargeafter-order-note.php';
			require_once $root_include_path . '/wc-chargeafter-widget.php';
			require_once $root_include_path . '/wc-chargeafter-checkout-script.php';
			require_once $root_include_path . '/wc-chargeafter-sdk.php';
			require_once $root_include_path . '/wc-chargeafter-product-promo.php';
			require_once $root_include_path . '/wc-chargeafter-cart-promo.php';
			require_once $root_include_path . '/wc-chargeafter-checkout-promo.php';
			require_once $root_include_path . '/wc-chargeafter-product-meta.php';
			require_once $root_include_path . '/wc-chargeafter-logger.php';

			return true;
		}

		/**
		 * Add Gateway
		 *
		 * @param $methods
		 *
		 * @return array
		 */
		public function add_gateways( $methods ) {
			if ( WC_ChargeAfter_Helper::is_allow_gateway() || is_admin() ) {
				$methods[] = 'WC_ChargeAfter_Gateway';
			}

			return $methods;
		}

		/**
		 * Register blocks support
		 *
		 * @return void
		 *
		 * @throws Exception
		 */
		public function register_blocks_support() {
			if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
				require_once __DIR__ . '/includes/wc-chargeafter-blocks-support.php';

				add_action(
					'woocommerce_blocks_payment_method_type_registration',
					function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
						$container = Automattic\WooCommerce\Blocks\Package::container();

						$container->register(
							WC_ChargeAfter_Blocks_Support::class,
							function () {
								return new WC_ChargeAfter_Blocks_Support();
							}
						);

						$payment_method_registry->register(
							$container->get( WC_ChargeAfter_Blocks_Support::class )
						);
					},
					5
				);
			}

			if ( WC_ChargeAfter_Helper::is_enabled() ) {

				$integrations = array();

				if ( WC_ChargeAfter_Helper::is_enabled_promo_mini_cart() ) {
					require_once __DIR__ . '/includes/wc-chargeafter-blocks-mini-cart.php';

					$integrations['woocommerce_blocks_mini-cart_block_registration'] = 'WC_ChargeAfter_Blocks_Mini_Cart';
				}

				if (
					WC_ChargeAfter_Helper::is_enabled_promo_cart() ||
					WC_ChargeAfter_Helper::is_enabled_promo_checkout()
				) {
					require_once __DIR__ . '/includes/wc-chargeafter-blocks-cart.php';

					if ( WC_ChargeAfter_Helper::is_enabled_promo_cart() ) {
						$integrations['woocommerce_blocks_cart_block_registration'] = 'WC_ChargeAfter_Blocks_Cart';
					}

					if ( WC_ChargeAfter_Helper::is_enabled_promo_checkout() ) {
						$integrations['woocommerce_blocks_checkout_block_registration'] = 'WC_ChargeAfter_Blocks_Cart';
					}
				}

				if ( ! empty( $integrations ) ) {

					require_once __DIR__ . '/includes/wc-chargeafter-blocks-assets.php';

					WC_ChargeAfter_Blocks_Assets::init();

					foreach ( $integrations as $action => $integration ) {
						add_action(
							$action,
							function ( $integration_registry ) use ( $integration ) {
								$integration_registry->register( new $integration() );
							},
							10,
							1
						);
					}
				}
			}
		}

		/**
		 * Add action links to Woocommerce Payment`s
		 *
		 * @param $links
		 *
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="admin.php?page=wc-settings&tab=checkout&section=wc_chargeafter_gateway">' .
				esc_html__( 'Settings', 'chargeafter-extension' ) .
				'</a>',
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Before WooCommerce init
		 */
		public function before_woocommerce_init() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}
	}

	/**
	 * Init WC_ChargeAfter class
	 * Check if WooCommerce is active
	 *
	 * @since 1.0.0
	 * @param array $active_plugins List of active plugins
	 */
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		WC_ChargeAfter::get_instance();
	}

endif;
