<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/*
 * Remove CA widgets options
 */
$list_widgets = get_option( 'sidebars_widgets', null );
if ( ! empty( $list_widgets ) ) {
	foreach ( $list_widgets as $k => $list_widget ) {
		foreach ( $list_widget as $key => $widget ) {
			if ( preg_match( '/^(chargeafter_widget-)(\d)/', $widget ) ) {
				unset( $list_widgets[ $k ][ $key ] );
			}
		}
	}

	update_option( 'sidebars_widgets', $list_widgets );
}

/*
 * Remove options
 */
$charge_key  = 'wc_chargeafter_gateway';
$setting_key = 'woocommerce_' . $charge_key;

delete_option( $setting_key . '_settings' );
