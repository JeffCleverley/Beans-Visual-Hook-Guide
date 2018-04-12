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
 * Function that adds actions to display markup on all chosen action hooks
 * Add the current markup query arg to the global markup array for making individual css changes.
 *
 * @param $markup                                       array   array of all data-markup-id values
 * @param $markup_stripped_of_square_brackets           array   array of all data-markup-id values stripped of square brackets
 *                                                      to be used as query args
 *
 */
function add_action_hooks_for_individually_chosen_markup_hooks( $markup, $markup_stripped_of_square_brackets ) {

	global $markup_array_for_individual_css_changes;

	if ( 'show' == isset( $_GET[ $markup_stripped_of_square_brackets ] ) ) {

		$markup_array_for_individual_css_changes[] = $markup;

		add_action( "{$markup}_before_markup", function () use ( $markup ) {
			beans_before_markup( $markup );
		}, 1 );
		add_action( "{$markup}_prepend_markup", function () use ( $markup ) {
			beans_prepend_markup( $markup );
		}, 1 );
		add_action( "{$markup}_append_markup", function () use ( $markup ) {
			beans_append_markup( $markup );
		}, 1 );
		add_action( "{$markup}_after_markup", function () use ( $markup ) {
			beans_after_markup( $markup );
		}, 1 );
	}
}

/**
 * Function to add all action hooks for all possible markup
 *
 * @param $markup_array     array   Array of all data-markup-id values - used to add actions to all possible hooks.
 */
function add_action_hooks_for_all_markup_hooks( $markup_array ) {

	foreach ( $markup_array as $markup ) {
		add_action( "{$markup}_before_markup", function () use ( $markup ) {
			beans_before_markup( $markup );
		}, 1 );
		add_action( "{$markup}_prepend_markup", function () use ( $markup ) {
			beans_prepend_markup( $markup );
		}, 1 );
		add_action( "{$markup}_append_markup", function () use ( $markup ) {
			beans_append_markup( $markup );
		}, 1 );
		add_action( "{$markup}_after_markup", function () use ( $markup ) {
			beans_after_markup( $markup );
		}, 1 );
	}
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