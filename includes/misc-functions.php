<?php
/**
 * Miscellaneous functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
 * Gets a template part
 *
 * @since 1.0
 *
 * This function adapted from bbPress
 *
 * @param string $slug
 * @param string $name Optional. Default null
 * @uses edd_rvi_locate_template()
 * @uses load_template()
 * @uses get_template_part()
 */
function edd_rvi_get_template_part( $slug, $name = null ) {

	// Execute code for this part
	do_action( 'get_template_part_' . $slug, $slug, $name );

	// Setup possible parts
	$templates = array();
	if ( isset( $name ) ) {
		$templates[] = $slug . '-' . $name . '.php';
	}

	$templates[] = $slug . '.php';

	// Allow template parts to be filtered
	$templates = apply_filters( 'edd_rvi_get_template_part', $templates, $slug, $name );

	// Return the part that is found
	return edd_rvi_locate_template( $templates, true, false );
}



/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the child theme before parent theme so that themes which
 * inherit from a parent theme can just overload one file. If the template is
 * not found in either of those, it looks in the plugin templates folder last.
 *
 * @since 1.0
 *
 * This function adapted from bbPress.
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @param bool $require_once Whether to require_once or require. Default true. Has no effect if $load is false.
 * @return string The template filename if one is located.
 */
function edd_rvi_locate_template( $template_names, $load = false, $require_once = true ) {

	// No file found yet
	$located = false;

	// Try to find a template file
	foreach ( (array) $template_names as $template_name ) {

		// Continue if template is empty
		if ( empty( $template_name ) ) {
			continue;
		}

		// Trim off any slashes from the template name
		$template_name  = ltrim( $template_name, '/' );

		// Check child theme first
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'edd_templates/' . $template_name ) ) {
			$located = trailingslashit( get_stylesheet_directory() ) . 'edd_templates/' . $template_name;
			break;

		// Check parent theme next
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . 'edd_templates/' . $template_name ) ) {
			$located = trailingslashit( get_template_directory() ) . 'edd_templates/' . $template_name;
			break;

		// Check plugin directory last
		} elseif ( file_exists( trailingslashit( EDD_RVI_PLUGIN_DIR ) . 'templates/' . $template_name ) ) {
			$located = trailingslashit( EDD_RVI_PLUGIN_DIR ) . 'templates/' . $template_name;
			break;
		}
	}

	if ( ( true == $load ) && ! empty( $located ) )
		load_template( $located, $require_once );

	return $located;
}