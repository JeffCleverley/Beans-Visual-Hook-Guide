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

use function LearningCurve\BeansVisualHookGuide\Asset\css_on_the_fly;

/**
 * Add Beans HTML API actions to display markup on all chosen action hooks.
 *
 * Add the current markup query arg to the global markup array for making individual css changes.
 *
 * @since 1.0.0
 *
 * @param string $markup_id     Markup ID from Beans' data-markup-id value.
 * @param string $raw_markup_id The raw data-markup-id without the square brackets.
 *
 * @return void
 */
function add_action_hooks_for_individually_chosen_markup_hooks( $markup_id, $raw_markup_id ) {

	if ( ! is_query_arg_set_show( $raw_markup_id ) ) {
		return;
	}

	css_on_the_fly()->add_markup_id( $markup_id );

	_hook_into_beans_markup( $markup_id );
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
 * @since  1.0.1
 * @ignore
 * @access private
 *
 * @param string $markup_id Markup ID.
 *
 * @return void
 */
function _hook_into_beans_markup( $markup_id ) {
	add_action( "{$markup_id}_before_markup", function() use ( $markup_id ) {
		_render_markup( $markup_id, 'before' );
	}, 1 );

	add_action( "{$markup_id}_prepend_markup", function() use ( $markup_id ) {
		_render_markup( $markup_id, 'prepend' );
	}, 1 );

	add_action( "{$markup_id}_append_markup", function() use ( $markup_id ) {
		_render_markup( $markup_id, 'append' );
	}, 1 );

	add_action( "{$markup_id}_after_markup", function() use ( $markup_id ) {
		_render_markup( $markup_id, 'after' );
	}, 1 );
}

/**
 * Renders the Guide's markup out to the browser.
 *
 * @since  1.0.1
 * @ignore
 * @access private
 *
 * @param string $markup_id Markup ID.
 * @param string $type Beans' hook type, i.e. before, prepend, append, after
 *
 * @return void
 */
function _render_markup( $markup_id, $type ) {
	$markup_id = esc_attr( $markup_id );
	$type      = esc_attr( $type );

	require __DIR__ . '/views/markup.php';
}