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
 * Get the Singleton instance of Css_On_The_Fly.
 *
 * @since 1.0.1
 *
 * @return Css_On_The_Fly
 */
function css_on_the_fly() {
	require_once __DIR__ . '/class-css-on-the-fly.php';

	return Css_On_The_Fly::getInstance();
}