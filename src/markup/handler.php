<?php
/**
 * Beans' Markup Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Markup
 * @since       1.1.0
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Markup;

use function LearningCurve\BeansVisualHookGuide\Asset\css_on_the_fly;
use function LearningCurve\BeansVisualHookGuide\is_query_arg_set_show;

/**
 * Hook into Beans to display the markup on all selected markup IDs.
 *
 * @since 1.0.0
 * @since 1.1.0 Renamed the function.
 *
 * @param string $markup_id     Markup ID from Beans' data-markup-id value.
 * @param string $raw_markup_id The raw data-markup-id without the square brackets.
 *
 * @return void
 */
function hook_into_beans_for_selected( $markup_id, $raw_markup_id ) {

	if ( ! is_query_arg_set_show( $raw_markup_id ) ) {
		return;
	}

	css_on_the_fly()->add_markup_id( $markup_id );

	_hook_into_beans_markup( $markup_id );
}

/**
 * Hook into Beans to display the markup for all possible markup IDs.
 *
 * @since 1.0.0
 * @since 1.1.0 Renamed the function.
 *
 * @param array $markup_ids Array of Beans data-markup-id values.
 *
 * @return void
 */
function hook_into_beans_for_all( $markup_ids ) {

	foreach ( $markup_ids as $markup ) {
		_hook_into_beans_markup( $markup );
	}
}

/**
 * Hook the markup ID into Beans's HTML API.
 *
 * @since  1.1.0
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
 * @since  1.1.0
 * @ignore
 * @access private
 *
 * @param string $markup_id Markup ID.
 * @param string $type      Beans' hook type, i.e. before, prepend, append, after.
 *
 * @return void
 */
function _render_markup( $markup_id, $type ) {
	$markup_id = esc_attr( $markup_id );
	$type      = esc_attr( $type );

	require __DIR__ . '/views/markup.php';
}
