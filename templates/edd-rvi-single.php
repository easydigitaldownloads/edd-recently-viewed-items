<?php
/**
 * The template for displaying recently viewed items on single download pages.
 */
$downloads = EDD_RVI()->get_recently_viewed_downloads();

if ( $downloads && $downloads->have_posts() ) { ?>

	<div class="edd-rvi-wrapper-single">
		<h4 class="edd-rvi-heading">
			<?php echo esc_html( edd_get_option( 'edd_rvi_single_heading', sprintf( __( 'Your Recently Viewed %s', 'edd-rvi' ), edd_get_label_plural() ) ) ); ?>
		</h4>

		<ul class="edd-rvi-items-list">

		<?php while( $downloads->have_posts() ) {
			$downloads->the_post();

			$item_id = get_the_ID();

			do_action( 'edd_rvi_single_item_before', $item_id ); ?>

			<li class="edd-rvi-item<?php if ( has_post_thumbnail( $item_id ) ) { echo ' image'; } ?>">
				<?php do_action( 'edd_rvi_single_item_top', $item_id ); ?>

				<a href="<?php the_permalink(); ?>">
					<?php if ( has_post_thumbnail( $item_id ) ) {
						echo get_the_post_thumbnail( $item_id, apply_filters( 'edd_rvi_single_image_size', array( 115, 115 ) ) );
					}
					the_title(); ?>
				</a>

				<?php do_action( 'edd_rvi_single_item_bottom', $item_id ); ?>
			</li>

			<?php do_action( 'edd_rvi_single_item_after', $item_id );
		} ?>

		</ul>
	</div>

	<?php wp_reset_postdata();
}
