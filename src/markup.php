<?php
/**
 * Beans' Markup Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide
 * @since       1.0.1
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide;

/**
 * Add Beans HTML API actions to display markup on all chosen action hooks.
 *
 * Add the current markup query arg to the global markup array for making individual css changes.
 *
 * @since 1.0.0
 *
 * @param array $markup_ids    Array of Beans data-markup-id values.
 * @param array $raw_markup_ids   Array of the raw data-markup-id without the square brackets.
 *
 * @return void
 */
function add_action_hooks_for_individually_chosen_markup_hooks( array $markup_ids, array $raw_markup_ids ) {

	if ( ! is_query_arg_set_show( $raw_markup_ids ) ) {
		return;
	}

	global $markup_array_for_individual_css_changes;

	$markup_array_for_individual_css_changes[] = $markup_ids;

	_hook_into_beans_markup( $markup_ids );
}

/**
 * Add all action hooks for all possible markup.
 *
 * @since 1.0.0
 *
 * @param array $markup_ids Array of Beans data-markup-id values.
 *
 * @return void
 */
function add_action_hooks_for_all_markup_hooks( $markup_ids ) {

	foreach ( $markup_ids as $markup ) {
		_hook_into_beans_markup( $markup );
	}
}

/**
 * Hook the markup ID into Beans's HTML API.
 *
 * @since 1.0.0
 *
 * @param string $markup_id Markup ID.
 *
 * @return void
 */
function _hook_into_beans_markup( $markup_id ) {
	add_action( "{$markup_id}_before_markup", function() use ( $markup_id ) {
		beans_before_markup( $markup_id );
	}, 1 );

	add_action( "{$markup_id}_prepend_markup", function() use ( $markup_id ) {
		beans_prepend_markup( $markup_id );
	}, 1 );

	add_action( "{$markup_id}_append_markup", function() use ( $markup_id ) {
		beans_append_markup( $markup_id );
	}, 1 );

	add_action( "{$markup_id}_after_markup", function() use ( $markup_id ) {
		beans_after_markup( $markup_id );
	}, 1 );
}

/**
 * Callback to be run on every possible $markup_before_markup html action hook
 *
 * Displays a div element containing the full dynamic action hook id
 *
 * @param   $markup string  data-markup-id attribute
 */
function beans_before_markup( $markup ) {
	echo '<div class="bvhg-hook-before-markup-cue" data-bvhg-hook-cue="' . $markup . '_before_markup">';
	echo $markup . '_before_markup</div>';
}

/**
 * Callback to be run on every possible $markup_prepend_markup html action hook
 *
 * Displays a div element containing the full dynamic action hook id
 *
 * @param   $markup string  data-markup-id attribute
 */
function beans_prepend_markup( $markup ) {
	echo '<div class="bvhg-hook-prepend-markup-cue" data-bvhg-hook-cue="' . $markup . '_prepend_markup">';
	echo $markup . '_prepend_markup</div>';
}

/**
 * Callback to be run on every possible $markup_append_markup html action hook
 *
 * Displays a div element containing the full dynamic action hook id
 *
 * @param   $markup string  data-markup-id attribute
 */
function beans_append_markup( $markup ) {
	echo '<div class="bvhg-hook-append-markup-cue" data-bvhg-hook-cue="' . $markup . '_append_markup">';
	echo $markup . '_append_markup</div>';
}

/**
 * Callback to be run on every possible $markup_after_markup html action hook
 *
 * Displays a div element containing the full dynamic action hook id
 *
 * @param   $markup string  data-markup-id attribute
 */
function beans_after_markup( $markup ) {
	echo '<div class="bvhg-hook-after-markup-cue" data-bvhg-hook-cue="' . $markup . '_after_markup">';
	echo $markup . '_after_markup</div>';
}