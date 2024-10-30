<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Non Leasable Meta
 */
define( 'NON_LEASABLE_META', 'non-leasable' );

/**
 * Warranty Meta
 */
define( 'WARRANTY_META', 'warranty' );

/**
 * Class WC_ChargeAfter_Product_Meta
 */
class WC_ChargeAfter_Product_Meta {

	/**
	 * WC_ChargeAfter_Product_Meta Instance
	 *
	 * @var WC_ChargeAfter
	 */
	private static $instance;

	/**
	 * WC_ChargeAfter_Product_Meta construct
	 */
	public function __construct() {
		if ( WC_ChargeAfter_Helper::is_enabled() ) {
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_option_group' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_option_field' ) );

			add_action( 'woocommerce_product_bulk_edit_start', array( $this, 'add_bulk_edit_group' ) );
			add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'save_bulk_edit_field' ) );
		}
	}

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
	 * Echo custom option
	 *
	 * @return void
	 */
	public function add_option_group() {
		?>
		<div class="option_group">
			<?php
			foreach ( $this->get_meta_options() as $option ) {
				$option_name = $this->get_meta_post_option_name( $option );
				$option_data = $this->get_meta_post_option_data( $option );

				$checkbox_value = array(
					'id'          => $option_name,
					'value'       => get_post_meta( get_the_ID(), $option_name, true ),
					'label'       => $option_data['label'],
					'desc_tip'    => true,
					'description' => $option_data['description'],
				);

				woocommerce_wp_checkbox( $checkbox_value );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Save custom option
	 *
	 * @param $id
	 *
	 * @return void
	 */
	public function save_option_field( $id ) {
		foreach ( $this->get_meta_options() as $option ) {
			$option_name = $this->get_meta_post_option_name( $option );
			$value       = ( isset( $_POST[ $option_name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) ) : null );

			$this->update_meta_value( $option, $id, $value );
		}
	}

	/**
	 * Update post meta value
	 *
	 * @param $meta
	 * @param $id
	 * @param $value
	 *
	 * @return void
	 */
	public function update_meta_value( $meta, $id, $value ) {
		$filter_value = isset( $value ) && 'yes' === $value ? 'yes' : 'no';
		update_post_meta( $id, $this->get_meta_post_option_name( $meta ), $filter_value );
	}

	/**
	 * Add bulk edit action
	 *
	 * @return void
	 */
	public function add_bulk_edit_group() {
		foreach ( $this->get_meta_options() as $option ) {
			$option_name = $this->get_meta_post_option_name( $option );
			$option_data = $this->get_meta_post_option_data( $option );

			?>
			<div class="inline-edit-group">
				<label for="<?php echo esc_attr( $option_name ); ?>">
						<span class="title">
							<?php echo esc_attr( $option_data['label'] ); ?>
						</span>
					<span class="input-text-wrap">
							<select id="<?php echo esc_attr( $option_name ); ?>"
									name="<?php echo esc_attr( $option_name ); ?>">
								<?php
								$options = array(
									''    => __( '— No change —', 'chargeafter-extension' ),
									'yes' => __( 'Yes', 'chargeafter-extension' ),
									'no'  => __( 'No', 'chargeafter-extension' ),
								);

								foreach ( $options as $key => $value ) {
									echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
								}
								?>
							</select>
							<?php echo wc_help_tip( $option_data['description'] ); ?>
						</span>
				</label>
			</div>
			<?php
		}
	}

	/**
	 * Save bulk edit option
	 *
	 * @param $product
	 *
	 * @return void
	 */
	public function save_bulk_edit_field( $product ) {
		$post_id = $product->get_id();

		foreach ( $this->get_meta_options() as $option ) {
			$option_name = $this->get_meta_post_option_name( $option );
			$value       = ( isset( $_REQUEST[ $option_name ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $option_name ] ) ) : null );

			if ( $value ) {
				$this->update_meta_value( $option, $post_id, $value );
			}
		}
	}

	/**
	 * Get meta post option data
	 *
	 * @param $id
	 *
	 * @return mixed|null
	 */
	private function get_meta_post_option_data( $id ) {
		$data = array(
			NON_LEASABLE_META => array(
				'label'       => __( 'Non Leasable', 'chargeafter-extension' ),
				'description' => sprintf(
					'%s %s',
					__( 'Specifying whether a product is leasable for', 'chargeafter-extension' ),
					__( 'Consumer Financing by ChargeAfter', 'chargeafter-extension' )
				),
			),
			WARRANTY_META     => array(
				'label'       => __( 'Warranty', 'chargeafter-extension' ),
				'description' => sprintf(
					'%s %s',
					__( 'Specifying whether a product warranty for', 'chargeafter-extension' ),
					__( 'Consumer Financing by ChargeAfter', 'chargeafter-extension' )
				),
			),
		);

		return key_exists( $id, $data ) ? $data[ $id ] : null;
	}

	/**
	 * Get meta post option name
	 *
	 * @param $option
	 *
	 * @return string
	 */
	private function get_meta_post_option_name( $option ) {
		return 'wc_chargeafter_' . $option;
	}

	/**
	 * Get meta options
	 *
	 * @return array
	 */
	private function get_meta_options() {
		return array( NON_LEASABLE_META, WARRANTY_META );
	}
}

/**
 * Init WC_ChargeAfter_Order_Handler
 */
WC_ChargeAfter_Product_Meta::get_instance();
