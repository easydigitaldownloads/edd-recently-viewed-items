<?php
/**
 * Recently Viewed Items Widget
 *
 * Displays the specified number of recently viewed downloads.
 *
 * @author John Parris
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * Register the Recently Viewed Items widget
 */
function edd_rvi_widget() {
	register_widget( 'EDD_RVI_Widget' );
}
add_action( 'widgets_init', 'edd_rvi_widget' );

/**
 * Recently Viewed Items Widget Class
 */
class EDD_RVI_Widget extends WP_Widget {


	/**
	 * Holds widget settings defaults. Populated in __construct().
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set default options and create widget.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		$this->defaults = array(
			'title'          => '',
			'number'         => 3,
			'show_thumbnail' => true,
		);

		$widget_ops = array(
			'classname'   => 'edd-rvi-widget',
			'description' => sprintf( __( 'Displays a list of %s the current visitor has recently viewed.', 'edd-rvi' ), edd_get_label_plural() ),
		);

		$control_ops = array(
			'id_base' => 'edd-rvi-widget',
			'width'   => 380,
			'height'  => 350,
		);

		parent::__construct( 'edd-rvi-widget', sprintf( __( 'Recently Viewed %s', 'edd-rvi' ), edd_get_label_plural() ), $widget_ops, $control_ops );
	}

	/**
	 * Display the widget content.
	 *
	 * @since 1.0
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		// Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		// Return early if no number is somehow specified.
		if( ! isset( $instance['number'] ) ) {
			return;
		}

		$downloads = EDD_RVI()->get_recently_viewed_downloads( (int) $instance['number'] );

		if ( ! $downloads || ( $downloads && ! $downloads->have_posts() ) ) {
			return;
		}

		echo $args['before_widget'];

		// Widget title
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
		}

		if( $downloads && $downloads->have_posts() ) { ?>
			<div class="edd-rvi-wrapper-widget">
				<ul class="edd-rvi-items-list">

				<?php while( $downloads->have_posts() ) {
					$downloads->the_post();
					$item_id = get_the_ID();
					?>
					<li class="edd-rvi-item<?php if ( has_post_thumbnail( $item_id ) ) { echo ' image'; } ?>">
						<a href="<?php the_permalink(); ?>">
							<?php if ( $instance['show_thumbnail'] && has_post_thumbnail( $item_id ) ) {
								echo get_the_post_thumbnail( $item_id, apply_filters( 'edd_rvi_widget_image_size', array( 115, 115 ) ) );
							} ?>
							<?php the_title(); ?>
						</a>
					</li>
				<?php } ?>

				</ul>
			</div>
			<?php
		}

		wp_reset_postdata();

		echo $args['after_widget'];
	}

	/**
	 * Updates an instance.
	 *
	 * This function checks that $new_instance is set correctly.
	 * The newly calculated value of $instance is returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {

		$instance                   = $old_instance;
		$instance['title']          = strip_tags( $new_instance['title']);
		$instance['number']         = (int) $new_instance['number'];
		$instance['show_thumbnail'] = (bool) $new_instance['show_thumbnail'];

		return $instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {

		// Merge the current settings with the defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'edd-rvi' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<div class="edd-rvi-widget-column">

			<div class="edd-rvi-widget-column-box edd-rvi-widget-column-box-top">

				<p>
					<label for="<?php echo $this->get_field_id( 'number' ); ?>">
						<?php printf( __( 'Maximum number of recently viewed %s to show', 'edd-rvi' ), strtolower( edd_get_label_plural() ) ); ?>
					</label>
					<select id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>">
						<option value="1" <?php selected( '1', $instance['number'] ); ?>><?php _e( '1', 'edd-rvi' ); ?></option>
						<option value="2" <?php selected( '2', $instance['number'] ); ?>><?php _e( '2', 'edd-rvi' ); ?></option>
						<option value="3" <?php selected( '3', $instance['number'] ); ?>><?php _e( '3', 'edd-rvi' ); ?></option>
						<option value="4" <?php selected( '4', $instance['number'] ); ?>><?php _e( '4', 'edd-rvi' ); ?></option>
						<option value="5" <?php selected( '5', $instance['number'] ); ?>><?php _e( '5', 'edd-rvi' ); ?></option>
						<option value="6" <?php selected( '6', $instance['number'] ); ?>><?php _e( '6', 'edd-rvi' ); ?></option>
					</select>
				</p>

				<p>
					<input <?php checked( $instance['show_thumbnail'], true ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumbnail' ) ); ?>" type="checkbox" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>">
						<?php _e( 'Show the featured images?', 'edd-rvi' ); ?>
					</label>
				</p>

			</div>

		</div>

		<?php

	}
}
