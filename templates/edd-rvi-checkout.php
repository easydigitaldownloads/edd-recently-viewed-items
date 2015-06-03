<?php
/**
 * The template for displaying recently viewed items on the checkout page.
 */
$downloads = EDD_RVI()->get_recently_viewed_downloads();

if ( $downloads && $downloads->have_posts() ) { ?>

	<div class="edd-rvi-wrapper-checkout">
		<h4 class="edd-rvi-heading">
			<?php echo esc_html( edd_get_option( 'edd_rvi_checkout_heading', sprintf( __( 'Your Recently Viewed %s', 'edd-rvi' ), edd_get_label_plural() ) ) ); ?>
		</h4>

		<ul class="edd-rvi-items-list">

		<?php while ( $downloads->have_posts() ) {
			$downloads->the_post();

			$item_id = get_the_ID();

			if ( ! edd_item_in_cart( $item_id ) ) { ?>
				<li class="edd-rvi-item<?php if ( has_post_thumbnail( $item_id ) ) { echo ' image'; } ?>">
					<a href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail( $item_id ) ) {
							echo get_the_post_thumbnail( $item_id, apply_filters( 'edd_rvi_checkout_image_size', array( 150, 150 ) ) );
						}
						the_title(); ?>
					</a>
					<?php if ( edd_has_variable_prices( $item_id ) ) {
						echo edd_get_purchase_link( array(
							'download_id' => $item_id,
							'price_id'    => edd_get_default_variable_price( $item_id ),
							'direct'      => false
						) );

					} else {
						echo edd_get_purchase_link( $item_id );
					} ?>
				</li>
				<?php
			}
		} ?>

		</ul>
	</div>

	<?php wp_reset_postdata();
}
