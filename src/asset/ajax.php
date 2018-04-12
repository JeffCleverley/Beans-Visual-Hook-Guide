<?php
/**
 * AJAX Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Asset
 * @since       1.0.1
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Asset;

add_action( 'wp_ajax_bvhg_pass_markup_id_array', __NAMESPACE__ . '\pass_markup_id_array_callback' );
/**
 * AJAX call back
 *
 * Receive Array containing all the data-markup-id attributes displayed by Beans Development Mode.
 * Check if transient exists, if so delete it, then save the received array as transient.
 *
 * Always die out of an AJAX call
 */
function pass_markup_id_array_callback() {

	check_ajax_referer( 'my-special-string', 'security' );

	if ( ! isset( $_POST['markup'] ) ) {
		return;
	}

	$non_sanitized_markup_array_from_ajax = $_POST['markup'];
	$non_sanitized_markup_array           = array_unique( $non_sanitized_markup_array_from_ajax );

	$sanitized_markup_array = array();
	foreach ( $non_sanitized_markup_array as $non_sanitized_markup ) {
		$sanitized_markup_array[] = sanitize_text_field( $non_sanitized_markup );
	}

	if ( get_transient( 'beans_html_markup_transient' ) ) {
		delete_transient( 'beans_html_markup_transient' );
	}

	set_transient( 'beans_html_markup_transient', $sanitized_markup_array, 12 * HOUR_IN_SECONDS );

	die();
}