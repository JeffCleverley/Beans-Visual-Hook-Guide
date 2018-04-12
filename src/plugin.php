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

add_action( 'beans_head', __NAMESPACE__ . '\beans_hooker' );
/**
 * Hook in Beans Visual Hook Guide Functionality
 * 1. Execute function that adds action hooks and toolbar nodes for markup hooks that have been selected individually
 * 2. Check if show all hooks has been chosen, if so, enqueue css script with markup for chosen hooks only, then return.
 * 3. Execute function to add all action hooks for all possible markup
 * 4. Enqueue css for all possible markup hooks.
 */
function beans_hooker() {

	if ( ! _beans_is_html_dev_mode() || is_customize_preview() ) {
		return;
	}

	$markup_array = get_transient( 'beans_html_markup_transient' );

	if ( ! $markup_array || 'show' != isset( $_GET['bvhg_enable'] ) ) {
		return;
	}

	$escaped_markup_array = array();
	foreach ( $markup_array as $markup_string ) {
		$escaped_markup_array[] = esc_attr( $markup_string );
	}

	add_action_hooks_toolbar_nodes_for_individual_markup_hooks( $escaped_markup_array );

	if ( 'show' != isset( $_GET['bvhg_enable_every_html_hook'] ) ) {
		Asset\enqueue_css_script_with_markup_array_for_chosen_hooks_only();

		return;
	}

	add_action_hooks_for_all_markup_hooks( $escaped_markup_array );
	Asset\enqueue_css_script_with_markup_array_for_all_markup_hooks( $escaped_markup_array );

}

/**
 * Loop through the markup array and:
 * 1. Strip all square brackets so the array values can be used as query args.
 * 2. Execute function to add toolbar nodes for all possible markup hooks that can be chosen.
 * 3. Execute function that adds actions to display markup on all chosen action hooks.
 *
 * @param $markup_array     array   Array of all data-markup-id values scraped from the site and stored as transient.
 */
function add_action_hooks_toolbar_nodes_for_individual_markup_hooks( $markup_array ) {

	foreach ( $markup_array as $markup ) {
		$markup_stripped_of_opening_square_bracket = str_replace( '[', '', $markup );
		$markup_stripped_of_all_square_brackets    = str_replace( ']', '', $markup_stripped_of_opening_square_bracket );
		Admin\add_toolbar_nodes_for_individual_markup_hooks( $markup, $markup_stripped_of_all_square_brackets );
		add_action_hooks_for_individually_chosen_markup_hooks( $markup, $markup_stripped_of_all_square_brackets );
	}
}
