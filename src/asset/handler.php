<?php
/**
 * Asset Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Asset
 * @since       1.0.1
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Asset;

use function LearningCurve\BeansVisualHookGuide\_get_plugin_url;
use function LearningCurve\BeansVisualHookGuide\_get_plugin_version;
use function LearningCurve\BeansVisualHookGuide\is_set_to_show_bvhg;

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_markup_id_scraper_script', 1 );
/**
 * Enqueue and localize the markup IDs scraper script.
 *
 * This script does the following:
 *      1.  Scrapes all data-markup-id values into an array.
 *      2.  Adds all data-markup-id values as an additional class to their elements - to be used later to change
 *          css on the fly.
 *      3. Sends values to be used by Ajax call used to receive the POSTed markup array.
 *
 * @since 1.0.0
 *
 * @return void
 */
function enqueue_markup_id_scraper_script() {

	if ( is_customize_preview() ) {
		return;
	}

	wp_enqueue_script(
		'scrape-the-markup-ids',
		_get_plugin_url() . '/assets/js/scrape_markup_ids.js',
		array( 'jquery' ),
		_get_plugin_version(),
		true
	);

	wp_localize_script(
		'scrape-the-markup-ids',
		'myAjax',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'my-special-string' )
		)
	);
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_stylesheet', 1 );
/**
 * Enqueue the stylesheet when not in Customizer preview, Beans is in Development Mode, and
 * the Beans Visual Hook Guide is set to show.
 *
 * @since 1.0.0
 *
 * @return void
 */
function enqueue_stylesheet() {

	if ( is_customize_preview() ) {
		return;
	}

	if ( ! _beans_is_html_dev_mode() ) {
		return;
	};

	if ( ! is_set_to_show_bvhg() ) {
		return;
	}

	wp_enqueue_style( 'bvhg_styles', _get_plugin_url() . '/assets/css/bvhg_styles.css' );
}

/**
 * Enqueue the "CSS-on-the-fly" script for chosen hooks.
 *
 * @since 1.0.0
 *
 * @global $markup_array_for_individual_css_changes
 *
 * @return void
 */
function enqueue_css_on_the_fly_for_chosen_hooks_only() {

	global $markup_array_for_individual_css_changes;

	add_action( 'wp_enqueue_scripts', function () use ( $markup_array_for_individual_css_changes ) {
		_enqueue_css_on_the_fly_script( $markup_array_for_individual_css_changes );
	}, 1, 999 );
}
/**
 * Enqueue the "CSS-on-the-fly" script for all of the markup IDs.
 *
 * @since 1.0.0
 *
 * @param array $markup_ids Array of data-markup-id values.
 *
 * @return void
 */
function enqueue_css_on_the_fly_for_all( $markup_ids ) {
	add_action( 'wp_enqueue_scripts', function () use ( $markup_ids ) {
		_enqueue_css_on_the_fly_script( $markup_ids );
	}, 1, 999 );
}

/**
 * Enqueue script to make CSS changes on fly.
 *
 * 1. Add orange border around selected elements.
 * 2. Change wp_admin toolbar menu item to yellow for elements that are currently selected to be displayed.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @param array $markup_ids Array of data-markup-id values.
 *
 * @return void
 */
function _enqueue_css_on_the_fly_script( $markup_ids ) {

	if ( empty( $markup_ids ) ) {
		return;
	}

	wp_enqueue_script(
		'element-id-css-changes',
		_get_plugin_url() . '/assets/js/element_id_css.js',
		array( 'jquery' ),
		_get_plugin_version(),
		true
	);

	wp_localize_script(
		'element-id-css-changes',
		'element',
		array( 'elementClass' => $markup_ids )
	);
}
