<?php
/**
 * Settings for Recently Viewed Items
 */
function edd_rvi_settings( $settings ) {

	$rvi_settings = array(
		array(
			'id'   => 'edd_rvi_header',
			'name' => __( 'Recently Viewed Items', 'edd-rvi' ),
			'type' => 'header',
			'size' => 'regular'
		),

		array(
			'id'   => 'edd_rvi_disable_checkout',
			'name' => __( 'Disable Display on Checkout', 'edd-rvi' ),
			'desc' => __( 'Check this box if you do not want recently viewed items to be displayed on the checkout page.', 'edd-rvi' ),
			'type' => 'checkbox',
			'size' => 'regular',
		),

		array(
			'id'   => 'edd_rvi_checkout_heading',
			'name' => __( 'Checkout Heading Text', 'edd-rvi' ),
			'desc' => sprintf( __( 'The heading text displayed on the checkout. Default: "Your Recently Viewed %s".', 'edd-rvi' ), edd_get_label_plural() ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => sprintf( __( 'Your Recently Viewed %s', 'edd-rvi' ), edd_get_label_plural() ),
		),

		array(
			'id'   => 'edd_rvi_disable_single',
			'name' => sprintf( __( 'Disable Display on Single %s Pages', 'edd-rvi' ), edd_get_label_singular() ),
			'desc' => sprintf( __( 'Check this box if you do not want recently viewed items to be displayed on single %s pages.', 'edd-rvi' ), strtolower( edd_get_label_singular() ) ),
			'type' => 'checkbox',
			'size' => 'regular'
		),

		array(
			'id'   => 'edd_rvi_single_heading',
			'name' => sprintf( __( 'Single %s Heading Text', 'edd-rvi' ), edd_get_label_singular() ),
			'desc' => sprintf( __( 'The heading text displayed on single %1$s pages. Default: "Your Recently Viewed %2$s".', 'edd-rvi' ), strtolower( edd_get_label_singular() ), edd_get_label_plural() ),
			'type' => 'text',
			'size' => 'regular',
			'std'  => sprintf( __( 'Your Recently Viewed %s', 'edd-rvi' ), edd_get_label_plural() )
		),

		array(
			'id'   => 'edd_rvi_disable_css',
			'name' => __( 'Default Styles', 'edd-rvi' ),
			'desc' => __( 'Check this box to disable the included styles for the recently viewed items display. Useful if your theme already styles them appropriately.', 'edd-rvi' ),
			'type' => 'checkbox',
			'size' => 'regular'
		)
	);

	return array_merge( $settings, $rvi_settings );
}
add_filter( 'edd_settings_extensions', 'edd_rvi_settings' );