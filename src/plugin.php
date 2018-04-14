<?php
/**
 * Plugin Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide
 * @since       1.1.0
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
 * @since 1.1.0 Renamed the function.
 *
 * @return void
 */
function hook_into_beans() {

	if ( ! _beans_is_html_dev_mode() || is_customize_preview() ) {
		return;
	}

	$markup_ids = get_transient( 'beans_html_markup_transient' );

	if ( empty( $markup_ids ) || ! is_set_to_show_bvhg() ) {
		return;
	}

	$markup_ids = array_map( 'esc_attr', $markup_ids );

	process_individual_markup_hooks( $markup_ids );

	if ( ! is_set_to_show_every_html_hook() ) {
		return Asset\css_on_the_fly()->enqueue_for_selected_ids();
	}

	Markup\hook_into_beans_for_all( $markup_ids );
	Asset\css_on_the_fly()->enqueue_for_all_hooks( $markup_ids );
}

/**
 * Process the scraped markup IDs to add each to the admin bar and hook into Beans for rendering
 * the visual guide.
 *
 * @since 1.0.0
 * @since 1.1.0 Renamed the function.
 *
 * @param array $markup_ids Array of scraped data-markup-id values.
 *
 * @return void
 */
function process_individual_markup_hooks( array $markup_ids ) {

	foreach ( $markup_ids as $markup ) {
		$clean_markup = remove_square_brackets( $markup );
		Admin\add_individual_markups_to_admin_bar( $markup, $clean_markup );
		Markup\hook_into_beans_for_selected( $markup, $clean_markup );
	}
}

/**
 * Remove the opening and closing square brackets from the given string.
 *
 * @since 1.1.0
 *
 * @param string $string Given string to clean.
 *
 * @return string
 */
function remove_square_brackets( $string ) {
	$string = str_replace( '[', '', $string );

	return str_replace( ']', '', $string );
}

/**
 * Checks if the admin bar has been set to display the Beans Visual Hook Guide.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function is_set_to_show_bvhg() {
	return is_query_arg_set_show( 'bvhg_enable' );
}

/**
 * Checks if the admin bar has been set to display every HTML hook.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function is_set_to_show_every_html_hook() {
	return is_query_arg_set_show( 'bvhg_enable_every_html_hook' );
}

/**
 * Checks if the given query argument exists and if yes, is set to "show."
 *
 * @since 1.1.0
 *
 * @param string $query_arg Given query arg.
 *
 * @return bool
 */
function is_query_arg_set_show( $query_arg ) {

	if ( ! isset( $_GET[ $query_arg ] ) ) { // phpcs::ignore WordPress.CSRF.NonceVerification.NoNonceVerification -- Not needed for this use case.
		return false;
	}

	return 'show' === $_GET[ $query_arg ]; // phpcs::ignore WordPress.CSRF.NonceVerification.NoNonceVerification -- Not needed for this use case.
}
