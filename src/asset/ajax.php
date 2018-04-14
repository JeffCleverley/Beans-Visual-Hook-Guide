<?php
/**
 * AJAX Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Asset
 * @since       1.1.0
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Asset;

add_action( 'wp_ajax_bvhg_save_scraped_markup_ids', __NAMESPACE__ . '\pass_scraped_markup_ids_callback' );
/**
 * AJAX call back
 *
 * Receives an array of scraped markup IDs, i.e. from the data-markup-id attribute.  Process
 * the array and save as a transient.
 *
 * @since 1.0.0
 */
function pass_scraped_markup_ids_callback() {
	check_ajax_referer( 'my-special-string', 'security' );

	if ( ! isset( $_POST['markup'] ) ) {
		return;
	}

	_save_markup_ids_as_transient( _clean_scraped_markup_ids( $_POST['markup'] ) );

	die();
}

/**
 * Clean the given markup IDs.
 *
 * @since  1.1.0
 * @ignore
 * @access private
 *
 * @param array $markup_ids Markup IDs to clean.
 *
 * @return array
 */
function _clean_scraped_markup_ids( array $markup_ids ) {
	if ( empty( $markup_ids ) ) {
		return array();
	}

	$markup_ids = array_unique( $markup_ids );

	return array_map( 'sanitize_text_field', $markup_ids );
}

/**
 * Save the given markup IDs as a transient.
 *
 * @since  1.1.0
 * @ignore
 * @access private
 *
 * @param array $markup_ids Markup IDs to save.
 *
 * @return void
 */
function _save_markup_ids_as_transient( array $markup_ids ) {
	if ( get_transient( 'beans_html_markup_transient' ) ) {
		delete_transient( 'beans_html_markup_transient' );
	}

	set_transient( 'beans_html_markup_transient', $markup_ids, 12 * HOUR_IN_SECONDS );
}
