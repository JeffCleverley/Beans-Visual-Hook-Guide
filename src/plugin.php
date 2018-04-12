<?php
/**
 * Plugin Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide
 * @since       1.0.1
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide;

add_action( 'beans_head', __NAMESPACE__ . '\hook_into_beans' );
/**
 * Hook into Beans to enable the Beans Visual Hook Guide functionality.  This callback does the following:
 *      1. Execute function that adds action hooks and toolbar nodes for markup hooks that have been selected
 *      individually
 *      2. Check if show all hooks has been chosen, if so, enqueue css script with markup for chosen hooks only, then
 *      return.
 *      3. Execute function to add all action hooks for all possible markup
 *      4. Enqueue css for all possible markup hooks.
 *
 * @since 1.0.0
 *
 * @return void
 */
function hook_into_beans() {

	if ( ! _beans_is_html_dev_mode() || is_customize_preview() ) {
		return;
	}

	$markup_ids = get_transient( 'beans_html_markup_transient' );

	if ( ! $markup_ids || 'show' != isset( $_GET['bvhg_enable'] ) ) {
		return;
	}

	$escaped_markup_array = array();
	foreach ( $markup_ids as $markup_string ) {
		$escaped_markup_array[] = esc_attr( $markup_string );
	}

	process_individual_markup_hooks( $escaped_markup_array );

	if ( 'show' != isset( $_GET['bvhg_enable_every_html_hook'] ) ) {
		Asset\enqueue_css_script_with_markup_array_for_chosen_hooks_only();

		return;
	}

	add_action_hooks_for_all_markup_hooks( $escaped_markup_array );
	Asset\enqueue_css_script_with_markup_array_for_all_markup_hooks( $escaped_markup_array );

}

/**
 * Process the scraped markup IDs to add each to the admin bar and hook into Beans for rendering
 * the visual guide.
 *
 * @since 1.0.0
 * @since 1.0.1 Renamed the function.
 *
 * @param array $markup_ids Array of scraped data-markup-id values.
 *
 * @return void
 */
function process_individual_markup_hooks( array $markup_ids ) {

	foreach ( $markup_ids as $markup ) {
		$clean_markup = remove_square_brackets( $markup );
		Admin\add_toolbar_nodes_for_individual_markup_hooks( $markup, $clean_markup );
		add_action_hooks_for_individually_chosen_markup_hooks( $markup, $clean_markup );
	}
}

/**
 * Remove the opening and closing square brackets from the given string.
 *
 * @since 1.0.1
 *
 * @param string $string Given string to clean.
 *
 * @return string
 */
function remove_square_brackets( $string ) {
	$string = str_replace( '[', '', $string );

	return str_replace( ']', '', $string );
}
