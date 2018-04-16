<?php
/**
 * Asset Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Asset
 * @since       1.1.0
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Asset;

use function LearningCurve\BeansVisualHookGuide\_get_plugin_directory;
use function LearningCurve\BeansVisualHookGuide\_get_plugin_url;
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
		'markup_id_scraper_script',
		_get_plugin_url() . '/assets/js/markup-id-scraper.js',
		array( 'jquery' ),
		_get_asset_version( '/assets/js/markup-id-scraper.js' ),
		true
	);

	wp_localize_script(
		'markup_id_scraper_script',
		'scraperParams',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'my-special-string' ),
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

	wp_enqueue_style(
		'bvhg_styles',
		_get_plugin_url() . '/assets/css/bvhg_styles.css',
		array(),
		_get_asset_version( '/assets/css/bvhg_styles.css' )
	);
}

/**
 * Get the Singleton instance of Css_On_The_Fly.
 *
 * @since 1.1.0
 *
 * @return Css_On_The_Fly
 */
function css_on_the_fly() {
	require_once __DIR__ . '/class-css-on-the-fly.php';

	return Css_On_The_Fly::get_instance();
}

/**
 * Get's the asset file's version number by using it's modification timestamp.
 *
 * @since 1.1.0
 *
 * @param string $relative_path Relative path to the asset file.
 *
 * @return bool|int
 */
function _get_asset_version( $relative_path ) {
	return filemtime( _get_plugin_directory() . $relative_path );
}
