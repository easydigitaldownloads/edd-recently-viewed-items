<?php
/**
 * Recently Viewed Items Shortcodes
 *
 * @since 1.0
 */
function edd_rvi_shortcode( $atts, $content = null ) {

	$atts = shortcode_atts( array(
		'heading' => sprintf( __( 'Your Recently Viewed %s', 'edd-rvi' ), edd_get_label_plural() ),
		'image'   => true,
		'number'  => 3
	), $atts );

	$downloads = EDD_RVI()->get_recently_viewed_downloads( $atts['number'] );

	if( $downloads && $downloads->have_posts() ) { ?>

		<div class="edd-rvi-wrapper-shortcode">
			<?php if ( 'false' != $atts['heading'] ) { ?>
				<h4 class="edd-rvi-heading">
					<?php echo esc_html( $atts['heading'] ); ?>
				</h4>
			<?php } ?>

			<ul class="edd-rvi-items-list">
				<?php while ( $downloads->have_posts() ) : $downloads->the_post();

					$item_id = get_the_ID();
					if ( ! edd_item_in_cart( $item_id ) ) { ?>
						<li class="edd-rvi-item<?php if ( 'true' == $atts['image'] && has_post_thumbnail( $item_id ) ) { echo ' image'; } ?>">
							<a href="<?php the_permalink(); ?>">
								<?php if ( 'true' == $atts['image'] && has_post_thumbnail( $item_id ) ) {
									echo get_the_post_thumbnail( $item_id, apply_filters( 'edd_rvi_shortcode_image_size', array( 115, 115 ) ) );
								}
								the_title(); ?>
							</a>
						</li>
					<?php }

				endwhile;

				wp_reset_postdata(); ?>
			</ul>
		</div>

		<?php
	}
}
add_shortcode( 'edd_rvi', 'edd_rvi_shortcode' );