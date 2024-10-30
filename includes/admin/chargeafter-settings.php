<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter ChargeAfter settings
 *
 * @since 1.0.0
 * @param array $settings Settings structure.
 */
return apply_filters(
	'wc_chargeafter_settings',
	array(
		'enabled'                 => array(
			'title'       => __( 'Enable/Disable', 'chargeafter-extension' ),
			'label'       => __( 'Enable Consumer Financing by ChargeAfter', 'chargeafter-extension' ),
			'type'        => 'checkbox',
			'description' => '',
			'default'     => 'yes',
		),
		'title'                   => array(
			'title'       => __( 'Title', 'chargeafter-extension' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.', 'chargeafter-extension' ),
			'default'     => __( 'Checkout Finance', 'chargeafter-extension' ),
			'desc_tip'    => true,
		),
		'is_production'           => array(
			'title'       => __( 'Production Mode', 'chargeafter-extension' ),
			'label'       => __( 'Enable Production Mode', 'chargeafter-extension' ),
			'type'        => 'checkbox',
			'description' => 'The production mode uses the production ChargeAfter keys and switches the script to the production environment. Don`t activate for testing!',
			'default'     => 'no',
			'desc_tip'    => true,
		),
		'sandbox_public_key'      => array(
			'title'       => __( 'Sandbox Public Key', 'chargeafter-extension' ),
			'type'        => 'text',
			'description' => __( 'Set your sandbox public key provided by ChargeAfter.', 'chargeafter-extension' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'sandbox_private_key'     => array(
			'title'       => __( 'Sandbox Private Key', 'chargeafter-extension' ),
			'type'        => 'password',
			'description' => __( 'Set your sandbox private key provided by ChargeAfter.', 'chargeafter-extension' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'production_public_key'   => array(
			'title'       => __( 'Production Public Key', 'chargeafter-extension' ),
			'type'        => 'text',
			'description' => __( 'Set your production public key provided by ChargeAfter.', 'chargeafter-extension' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'production_private_key'  => array(
			'title'       => __( 'Production Private Key', 'chargeafter-extension' ),
			'type'        => 'password',
			'description' => __( 'Set your production private key provided by ChargeAfter.', 'chargeafter-extension' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'payment_action'          => array(
			'title'       => __( 'Transaction Type', 'chargeafter-extension' ),
			'type'        => 'select',
			'class'       => 'wc-enhanced-select',
			'description' => __( 'Choose transaction type: Capture the order immediately or authorize only.', 'chargeafter-extension' ),
			'default'     => 'authorization',
			'desc_tip'    => true,
			'options'     => array(
				'authorization' => __( 'Authorization', 'chargeafter-extension' ),
				'sale'          => __( 'Capture', 'chargeafter-extension' ),
			),
		),
		'divide_checkout'         => array(
			'title' => __( 'Checkout', 'chargeafter-extension' ),
			'type'  => 'divide',
		),
		'enabled_data_update'     => array(
			'title'       => __( 'Enable/Disable Data Update', 'chargeafter-extension' ),
			'label'       => __( 'Enable Customer Data Update', 'chargeafter-extension' ),
			'type'        => 'checkbox',
			'description' => __( 'Once you receive updated information from ChargeAfter Checkout Flow, the information will be updated in Woocommerce.', 'chargeafter-extension' ),
			'default'     => 'no',
			'desc_tip'    => true,
		),
		'checkout_brand_type'     => array(
			'title'       => __( 'Checkout Brand Type', 'chargeafter-extension' ),
			'type'        => 'select',
			'class'       => 'wc-enhanced-select',
			'description' => __( 'You can set the type attribute according the space available to you.', 'chargeafter-extension' ),
			'default'     => 'medium-generic',
			'desc_tip'    => true,
			'options'     => array(
				'small-generic'  => 'small-generic',
				'medium-generic' => 'medium-generic',
				'large-generic'  => 'large-generic',
				'small-square'   => 'small-square',
				'medium-square'  => 'medium-square',
				'large-square'   => 'large-square',
			),
		),
		'divide_promo'            => array(
			'title'       => __( 'Promotional Widgets', 'chargeafter-extension' ),
			'description' => __( 'The Promotional Widget is a powerful marketing tool to keep shoppers informed about financing options before making a purchase.', 'chargeafter-extension' ),
			'type'        => 'divide',
		),
		'enabled_promo'           => array(
			'title'             => __( 'Enable/Disable Promo', 'chargeafter-extension' ),
			'label'             => __( 'Enable Promotional Widget', 'chargeafter-extension' ),
			'type'              => 'checkbox',
			'description'       => __( 'This widget will display the offer with the lowest interest rate that a consumer can get.', 'chargeafter-extension' ),
			'default'           => 'yes',
			'desc_tip'          => true,
			'custom_attributes' => array(
				'onclick' => 'toogleFieldDisabled("promo_url");',
			),
		),
		'enabled_promo_product'   => array(
			'title'       => __( 'Display Promo', 'chargeafter-extension' ),
			'label'       => __( 'Display on Product Page', 'chargeafter-extension' ),
			'type'        => 'checkbox',
			'description' => __( 'Product Page; Under the product price.', 'chargeafter-extension' ),
			'default'     => 'yes',
		),
		'enabled_promo_cart'      => array(
			'label'       => __( 'Display on Cart Page', 'chargeafter-extension' ),
			'type'        => 'checkbox',
			'description' => __( 'Cart Page; Under the total amounts block.', 'chargeafter-extension' ),
			'default'     => 'yes',
		),
		'enabled_promo_mini_cart' => array(
			'label'       => __( 'Display on Mini Cart Preview', 'chargeafter-extension' ),
			'type'        => 'checkbox',
			'description' => __( 'Mini cart based on WooCommerce Blocks only; Above the total amounts block.', 'chargeafter-extension' ),
			'default'     => 'yes',
		),
		'enabled_promo_checkout'  => array(
			'label'       => __( 'Display on Checkout Page', 'chargeafter-extension' ),
			'type'        => 'checkbox',
			'description' => __( 'Checkout Page; Under the total amounts block.', 'chargeafter-extension' ),
			'default'     => 'yes',
		),
		'promo_type'              => array(
			'title'       => __( 'Promo Type', 'chargeafter-extension' ),
			'type'        => 'select',
			'class'       => 'wc-enhanced-select',
			'description' => __( 'You can choose one of widget types to display the financial offer available.', 'chargeafter-extension' ),
			'default'     => 'product-widget-line-of-credit',
			'desc_tip'    => true,
			'options'     => array(
				'product-widget-line-of-credit' => 'product-widget-line-of-credit',
				'default-template'              => 'default-template',
			),
		),
		'promo_tag'               => array(
			'title'       => __( 'Promo Tag', 'chargeafter-extension' ),
			'type'        => 'text',
			'description' => __( 'The promo tag is an attribute used for promotion targeting.', 'chargeafter-extension' ),
			'default'     => '',
			'desc_tip'    => true,
		),
		'promo_url'               => array(
			'title'          => __( 'Financing page URL', 'chargeafter-extension' ),
			'type'           => 'url',
			'description'    => __( 'The financing page URL is used to notify the user of more detailed funding information. Link can be absolute.', 'chargeafter-extension' ),
			'desc_tip'       => true,
			'default'        => home_url(),
			'parent_element' => array(
				'enabled_promo' => 'yes',
			),
		),
	)
);
