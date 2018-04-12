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

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\script_to_scrape_markup_on_page_Load', 1 );
/**
 * Enqueue Script on page load that:
 * 1. Scrapes all data-markup-id values into an array.
 * 2. Adds all data-markup-id values as an additional class to their elements - to be used later to change css on the fly.
 * 3. Localizes script - sends values to be used by Ajax call used to receive the POSTed markup array.
 */
function script_to_scrape_markup_on_page_Load() {

	if ( is_customize_preview() ) {
		return;
	};

	wp_enqueue_script(
		'scrape-the-markup-ids',
		BVHG_BEANS_PLUGIN_URL . '/js/scrape_markup_ids.js',
		array( 'jquery' ),
		'1.0.0',
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

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_css_if_guide_enabled', 1 );
/**
 * Enqueue CSS only if BeansVisual Hook Guide is enabled
 */
function enqueue_css_if_guide_enabled() {

	if ( ! _beans_is_html_dev_mode() || is_customize_preview() ) {
		return;
	};

	if ( 'show' == isset( $_GET['bvhg_enable'] ) ) {
		wp_enqueue_style( 'bvhg_styles', BVHG_BEANS_PLUGIN_URL . '/css/bvhg_styles.css' );
	}
}

/**
 * Function to enqueue css script with markup for chosen hooks only.
 */
function enqueue_css_script_with_markup_array_for_chosen_hooks_only() {

	global $markup_array_for_individual_css_changes;

	add_action( 'wp_enqueue_scripts', function () use ( $markup_array_for_individual_css_changes ) {
		enqueue_element_id_script( $markup_array_for_individual_css_changes );
	}, 1, 999 );
}
/**
 * Function to add script to enqueue scripts hook, and provide array for localization.
 *
 * @param $markup_array     array   Array of all data-markup-id values - used to add actions to all possible hooks.
 */
function enqueue_css_script_with_markup_array_for_all_markup_hooks( $markup_array ) {
	add_action( 'wp_enqueue_scripts', function () use ( $markup_array ) {
		enqueue_element_id_script( $markup_array );
	}, 1, 999 );
}

/**
 * Enqueue script to make css changes on fly
 *
 * 1. Add orange border around selected elements.
 * 2. Change wp_admin toolbar menu item to yellow for elements that are currently selected to be displayed.
 *
 * @param $markup_array     array   depending on query_arg displaying, will either be an array of just the chosen elements,
 *                          or every single element with a data-markup-id value.
 */
function enqueue_element_id_script( $markup_array ) {

	if ( ! $markup_array ) {
		return;
	}

	wp_enqueue_script(
		'element-id-css-changes',
		BVHG_BEANS_PLUGIN_URL . '/js/element_id_css.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);

	wp_localize_script(
		'element-id-css-changes',
		'element',
		array( 'elementClass' => $markup_array )
	);
}
