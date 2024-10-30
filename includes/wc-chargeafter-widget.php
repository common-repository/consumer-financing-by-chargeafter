<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_ChargeAfter_Widget
 */
class WC_ChargeAfter_Widget extends WP_Widget {

	/**
	 * Allow CA widget types
	 *
	 * @var array
	 */
	private $widget_allow_types = array(
		'default',
		'banner-horizontal',
		'banner-vertical',
	);

	/**
	 * WC_ChargeAfter_Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			'chargeafter_widget',
			__( 'ChargeAfter Promo Banner', 'chargeafter-extension' ),
			array(
				'description' => 'Display the ChargeAfter banner.',
			)
		);
	}

	/**
	 * Override Widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		self::init_promo_script();
		$widget_type = isset( $instance['ca_widget_type'] ) ? $instance['ca_widget_type'] : null;

		?>
		<div class="ca-promotional-widget"
			data-widget-type="<?php echo esc_html( ! empty( $widget_type ) ? $widget_type : '' ); ?>"></div>
		<?php
	}

	/**
	 * Init Promo Script
	 */
	public static function init_promo_script() {
		wp_enqueue_script(
			'chargeafter_promo',
			plugins_url( '/assets/js/promo.js', CHARGEAFTER_MAIN_FILE ),
			array( 'chargeafter_sdk' ),
			CHARGEAFTER_VERSION,
			true
		);
	}

	/**
	 * Override form
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$widget_type = isset( $instance['ca_widget_type'] ) ? $instance['ca_widget_type'] : $this->widget_allow_types[0];

		?>
		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'ca_widget_type' ) ); ?> "><?php __( 'ChargeAfter Widget Type:', 'chargeafter-extension' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'ca_widget_type' ) ); ?>"
					name="<?php echo esc_html( $this->get_field_name( 'ca_widget_type' ) ); ?>">
				<?php foreach ( $this->widget_allow_types as $allow_type ) : ?>
					<option
						value="<?php echo esc_attr( $allow_type ); ?>" <?php echo( $widget_type == $allow_type ? 'selected="selected"' : '' ); ?>>
						<?php echo esc_attr( $allow_type ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Override update
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$ca_widget_type = '';
		if (
			! empty( $new_instance['ca_widget_type'] ) &&
			in_array( $new_instance['ca_widget_type'], $this->widget_allow_types )
		) {
			$ca_widget_type = $new_instance['ca_widget_type'];
		}

		$instance['ca_widget_type'] = $ca_widget_type;

		return $instance;
	}
}

/**
 * Widget register
 */
// @codingStandardsIgnoreLine
function wpb_load_chargeafter_widget() {
	register_widget( 'WC_ChargeAfter_Widget' );
}

add_action( 'widgets_init', 'wpb_load_chargeafter_widget' );
