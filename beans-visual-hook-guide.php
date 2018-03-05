<?php
/*
Plugin Name: Beans HTML API Visual Hook Guide
Plugin URI: https://github.com/JeffCleverley/Beans-Visual-Hook-Guide
Description: Find Beans Hooks (HTML API created action hooks only) quickly and easily by seeing their actual locations inside your theme.
Version: 1.0.0
Author: Jeff R Cleverley
Author URI: https://learningcurve.xyz
Text Domain: beans-visual-hook-guide
License: GPLv2

This plugin was inspired by Christopher Cochran's Genesis Visual Hook Guide, one of my favourite plugins for Genesis Development.
I started with his plugin and went from there.... Thank you very much Christopher!
https://genesistutorials.com/visual-hook-guide/
https://github.com/christophercochran/Genesis-Visual-Hook-Guide
http://christophercochran.me
*/

$beans_flavors = array(
	'beans',
	'tm-beans',
);

define( 'BEANS_FLAVORS', $beans_flavors );
define( 'BEANS_PLUGIN_URL', plugins_url( null, __FILE__ ) );

register_activation_hook( __FILE__, 'bvhg_activation_check' );
/**
 * Check active theme is Beans before allowing activation
 */
function bvhg_activation_check() {

	if ( ! in_array( wp_get_theme()->Template, BEANS_FLAVORS ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( 'Sorry, you can\'t activate unless you have installed Beans</a>' );
	}
}

add_action( 'switch_theme', 'bvhg_disable_itself_if_not_beans' );
/**
 * Deactivate plugin if Beans is no longer the active framework after switching themes
 */
function bvhg_disable_itself_if_not_beans() {

	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if ( ! in_array( wp_get_theme()->Template, BEANS_FLAVORS ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
}

add_action( 'admin_notices', 'bvhg_active_notice' );
/**
 * Notices to display when plugin active.
 * Notice 1 - Displays when Development Mode isn't active, but plugin is.
 * Notice 2 - Displays when Development Mode and Plugin are active.
 */
function bvhg_active_notice() {

	if ( ! _beans_is_html_dev_mode() ) {
		echo '<div class="notice notice-warning" >'; ?>
        <p><?php _e( 'Beans Visual Hook Guide is currently active, but it also requires Development mode to be active. If this is a production site, remember to deactivate both after use.', 'beans-visual-hook-guide' ) ?></p><?php
		echo '</div>';
	} else {
		echo '<div class="notice notice-warning" >'; ?>
        <p><?php _e( 'Beans Visual Hook Guide and Development mode are both active. If this is a production site, remember to deactivate both after use.', 'beans-visual-hook-guide' ) ?></p><?php
		echo '</div>';
	}
}

add_action( 'admin_bar_menu', 'bvhg_admin_initial_links', 100 );
/**
 * Add the Admin Bar Links conditionally
 * If development mode isn't active, link is directed to Beans Settings to enable.
 * If development mode is active, displays drop-down links for features and clearance.
 * Set different query_args to enable different functions.
 */
function bvhg_admin_initial_links() {

	global $wp_admin_bar;

	if ( is_admin() ) {
		return;
	}

	if ( ! _beans_is_html_dev_mode() ) {

		$wp_admin_bar->add_node(
			array(
				'id'       => 'bvhg_hooks',
				'title'    => __( 'Beans Visual Hook Guide requires development mode to be enabled!', 'beans-visual-hook-guide' ),
				'href'     => get_site_url() . '/wp-admin/themes.php?page=beans_settings',
				'position' => 0,
			)
		);

		return;
	}

	if ( 'show' != isset( $_GET['bvhg_enable'] ) ) {

		$wp_admin_bar->add_node(
			array(
				'id'       => 'bvhg_hooks',
				'title'    => __( 'Enable Beans Visual Hook Guide', 'beans-visual-hook-guide' ),
				'href'     => esc_url( add_query_arg( 'bvhg_enable', 'show' ) ),
				'position' => 0,
			)
		);
	} elseif ( 'show' == isset( $_GET['bvhg_enable'] ) ) {

		$wp_admin_bar->add_node(
			array(
				'id'       => 'bvhg_html',
				'title'    => __( 'Beans Visual Hook Guide', 'beans-visual-hook-guide' ),
				'href'     => '',
				'position' => 0,
			)
		);
	}

	$bvhg_main_query_args = array(
		'bvhg_html_hooks',
		'bvhg_enable',
		'bvhg_enable_every_html_hook'
	);

	$markup_array_query_args = get_transient( 'beans_html_markup_transient' );

	$markup_array_query_args_stripped = array();

	if ( ! $markup_array_query_args ) {
		return;
	}

	$i = 0;
	foreach ( $markup_array_query_args as $markup_array_query_arg ) {
		$markup_array_query_arg_first_strip     = str_replace( '[', '', $markup_array_query_arg );
		$markup_array_query_args_stripped[ $i ] = str_replace( ']', '', $markup_array_query_arg_first_strip );
		$i ++;
	}

	$bvhg_query_args_to_clear           = array_merge( $markup_array_query_args_stripped, $bvhg_main_query_args );
	$markup_array_query_args_stripped[] = 'bvhg_enable_every_html_hook';

	$wp_admin_bar->add_node(
		array(
			'id'       => 'bvhg_html_list',
			'parent'   => 'bvhg_html',
			'title'    => __( 'All HTML API Hooks List - Show Individually', 'beans-visual-hook-guide' ),
			'href'     => '',
			'position' => 10,
		)
	);

	$wp_admin_bar->add_node(
		array(
			'id'       => 'bvhg_show_all_html',
			'parent'   => 'bvhg_html',
			'title'    => __( 'Show ALL HTML API Hooks (Crazy Mode)', 'beans-visual-hook-guide' ),
			'href'     => esc_url( add_query_arg( 'bvhg_enable_every_html_hook', 'show' ) ),
			'position' => 10,
		)
	);

	$wp_admin_bar->add_node(
		array(
			'id'       => 'bvhg_html_clear',
			'parent'   => 'bvhg_html',
			'title'    => __( 'Clear all displayed Hooks', 'beans-visual-hook-guide' ),
			'href'     => esc_url( remove_query_arg( $markup_array_query_args_stripped ) ),
			'position' => 10,
		)
	);

	$wp_admin_bar->add_node(
		array(
			'id'       => 'bvhg_html_clear_disable',
			'parent'   => 'bvhg_html',
			'title'    => __( 'Disable Beans HTML API Visual Hook Guide', 'beans-visual-hook-guide' ),
			'href'     => esc_url( remove_query_arg( $bvhg_query_args_to_clear ) ),
			'position' => 10,
		)
	);
}

add_action( 'wp_enqueue_scripts', 'bvhg_script_to_scrape_markup_on_page_Load', 1 );
add_action( 'wp_enqueue_scripts', 'bvhg_enqueue_css_if_guide_enabled', 1 );
/**
 * Enqueue CSS assets.
 *
 * Enqueue Script that:
 * 1. Scrapes all data-markup-id values into an array.
 * 2. Adds all data-markup-id values as an additional class to their elements - to be used later to change css on the fly.
 * 3. Localizes script - sends values to be used by Ajax call used to receive the POSTed markup array.
 */
function bvhg_script_to_scrape_markup_on_page_Load() {

	if ( is_customize_preview() ) {
		return;
	};

	wp_enqueue_script(
		'scrape-the-markup-ids',
		BEANS_PLUGIN_URL . '/js/scrape_markup_ids.js',
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

function bvhg_enqueue_css_if_guide_enabled(){

	if ( is_customize_preview() ) {
		return;
	};

    if ( 'show' == isset( $_GET['bvhg_enable'] ) ) {
		wp_enqueue_style( 'bvhg_styles', BEANS_PLUGIN_URL . '/css/bvhg_styles.css' );
	}
}

add_action( 'wp_ajax_bvhg_pass_markup_id_array', 'bvhg_pass_markup_id_array_callback' );
/**
 * AJAX call back
 *
 * Receive Array containing all the data-markup-id attributes displayed by Beans Development Mode.
 * Check if transient exists, if so delete it, then save the received array as transient.
 *
 * Always die out of an AJAX call
 */
function bvhg_pass_markup_id_array_callback() {

	check_ajax_referer( 'my-special-string', 'security' );

	$markup_array_from_ajax     = $_POST['markup'];
	$markup_array_for_transient = array_unique( $markup_array_from_ajax );

	if ( get_transient( 'beans_html_markup_transient' ) ) {
		delete_transient( 'beans_html_markup_transient' );
	}
	set_transient( 'beans_html_markup_transient', $markup_array_for_transient, 12 * HOUR_IN_SECONDS );

	die();
}


add_action( 'beans_head', 'bvhg_beans_hooker' );
/**
 * Check Query_Args and add actions conditionally.
 *
 * HTML API actions         - To add div elements to display the full HTML API Dynamic Markup Hooks in place
 * Enqueue Script actions   - To enqueue script conditionally depending on which elements hooks have been chosen.
 *
 * Actions use closures to pass the arrays to callbacks
 */
function bvhg_beans_hooker() {

	if ( is_customize_preview() ) {
		return;
	}

	$markup_array = get_transient( 'beans_html_markup_transient' );

	if ( ! $markup_array || 'show' != isset( $_GET['bvhg_enable'] ) ) {
		return;
	}

	bvhg_add_action_hooks_toolbar_nodes_for_individual_markup_hooks( $markup_array );

	if ( 'show' != isset( $_GET['bvhg_enable_every_html_hook'] ) ) {
		bvhg_enqueue_css_script_with_markup_array_for_chosen_hooks_only();
		return;
	}

	bvhg_add_action_hooks_for_all_markup_hooks( $markup_array );
	bvhg_enqueue_css_script_with_markup_array_for_all_markup_hooks( $markup_array );

}

function bvhg_add_action_hooks_toolbar_nodes_for_individual_markup_hooks( $markup_array ) {

	foreach ( $markup_array as $markup ) {
		$markup_stripped_of_opening_square_bracket = str_replace( '[', '', $markup );
		$markup_stripped_of_all_square_brackets  = str_replace( ']', '', $markup_stripped_of_opening_square_bracket );

		bvhg_add_toolbar_nodes_for_individual_markup_hooks( $markup, $markup_stripped_of_all_square_brackets );
		bvhg_add_action_hooks_for_individually_chosen_markup_hooks( $markup, $markup_stripped_of_all_square_brackets );
	}
}

function bvhg_add_toolbar_nodes_for_individual_markup_hooks( $markup, $markup_stripped_of_square_brackets ) {

	global $wp_admin_bar;

	$wp_admin_bar->add_node(
		array(
			'id'       => "bvhg_html_{$markup}hook",
			'parent'   => 'bvhg_html_list',
			'title'    => $markup,
			'href'     => esc_url( add_query_arg( "{$markup_stripped_of_square_brackets}", 'show' ) ),
			'position' => 10,
		)
	);
}

function bvhg_add_action_hooks_for_individually_chosen_markup_hooks( $markup, $markup_stripped_of_square_brackets ) {

	global $markup_array_for_individual_css_changes;

	if ( 'show' == isset( $_GET[ $markup_stripped_of_square_brackets ] ) ) {

		$markup_array_for_individual_css_changes[] = $markup;

		add_action( "{$markup}_before_markup", function () use ( $markup ) {
			bvhg_beans_before_markup( $markup );
		}, 1 );
		add_action( "{$markup}_prepend_markup", function () use ( $markup ) {
			bvhg_beans_prepend_markup( $markup );
		}, 1 );
		add_action( "{$markup}_append_markup", function () use ( $markup ) {
			bvhg_beans_append_markup( $markup );
		}, 1 );
		add_action( "{$markup}_after_markup", function () use ( $markup ) {
			bvhg_beans_after_markup( $markup );
		}, 1 );
	}
}

function bvhg_enqueue_css_script_with_markup_array_for_chosen_hooks_only() {

    global  $markup_array_for_individual_css_changes;

	add_action( 'wp_enqueue_scripts', function () use ( $markup_array_for_individual_css_changes ) {
		bvhg_enqueue_element_id_script( $markup_array_for_individual_css_changes );
	}, 1, 999 );
}

function bvhg_add_action_hooks_for_all_markup_hooks( $markup_array ) {

	foreach ( $markup_array as $markup ) {

		add_action( "{$markup}_before_markup", function () use ( $markup ) {
			bvhg_beans_before_markup( $markup );
		}, 1 );
		add_action( "{$markup}_prepend_markup", function () use ( $markup ) {
			bvhg_beans_prepend_markup( $markup );
		}, 1 );
		add_action( "{$markup}_append_markup", function () use ( $markup ) {
			bvhg_beans_append_markup( $markup );
		}, 1 );
		add_action( "{$markup}_after_markup", function () use ( $markup ) {
			bvhg_beans_after_markup( $markup );
		}, 1 );
	}
}

function bvhg_enqueue_css_script_with_markup_array_for_all_markup_hooks( $markup_array ) {

	add_action( 'wp_enqueue_scripts', function () use ( $markup_array ) {
		bvhg_enqueue_element_id_script( $markup_array );
	}, 1, 999 );
}

/**
 * Enqueue script to make css changes on fly
 *
 * 1. Add orange border around selected elements.
 * 2. Change wp_admin toolbar menu item to yellow for elements that are currently selected to be displayed.
 *
 * @param $markup_array     array   depending on query_arg displaying, will either be an array of just the chosen elements or every single element with a data-markup-id value.
 */
function bvhg_enqueue_element_id_script( $markup_array ) {

	wp_enqueue_script(
		'element-id-css-changes',
		BEANS_PLUGIN_URL . '/js/element_id_css.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);

	wp_localize_script(
		'element-id-css-changes',
		'element',
		array(
			'elementClass' => $markup_array,
		)
	);
}

/**
 * Callback to be run on every possible $markup_before_markup html action hook
 *
 * Displays a div element containing the full dynamic action hook id
 *
 * @param   $markup string  data-markup-id attribute
 */
function bvhg_beans_before_markup( $markup ) {
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
function bvhg_beans_prepend_markup( $markup ) {
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
function bvhg_beans_append_markup( $markup ) {
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
function bvhg_beans_after_markup( $markup ) {
	echo '<div class="bvhg-hook-after-markup-cue" data-bvhg-hook-cue="' . $markup . '_after_markup">';
	echo $markup . '_after_markup</div>';
}






