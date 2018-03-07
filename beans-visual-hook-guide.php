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

register_activation_hook( __FILE__, 'bvhg_environment_check' );
add_action( 'switch_theme', 'bvhg_environment_check' );
/**
 * Check active theme is Beans:
 * 1. Before Activation
 * 2. After Switching Themes
 *
 * If not:
 * 1. Don't allow activation and throw a die message
 * 2. Disable plugin
 */
function bvhg_environment_check() {

	$is_beans          = in_array( wp_get_theme()->Template, BEANS_FLAVORS );
	$deactivate_plugin = deactivate_plugins( plugin_basename( __FILE__ ) );

	if ( ! $is_beans && current_filter() != 'switch_theme' ) {
		$deactivate_plugin;
		wp_die( 'Sorry, you can\'t activate unless you have installed Beans</a>' );
	} elseif ( ! $is_beans ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$deactivate_plugin;
	}
}

add_action( 'admin_notices', 'bvhg_active_notice' );
/**
 * Notices to display when plugin active:
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

add_action( 'admin_bar_menu', 'bvhg_toolbar_top_level_links', 100 );
/**
 * Add the Admin Toolbar Top Level Link conditionally:
 * 1. Execute function to add Link to Beans Theme Settings if Development mode is inactive.
 * 2. Execute function to enable Visual Guide if Development mode is active.
 * 3. Execute function to add Visual Guide Top level link if Visual Guide is enabled.
 */
function bvhg_toolbar_top_level_links() {

	if ( is_admin() ) {
		return;
	}

	$toolbar_top_link_args = array(
	        'development_mode_disabled' => array(
		        'id'       => 'bvhg_hooks',
		        'title'    => __( 'Beans Visual Hook Guide requires development mode to be enabled!', 'beans-visual-hook-guide' ),
		        'href'     => get_site_url() . '/wp-admin/themes.php?page=beans_settings',
            ),
            'enable_visual_hook_guide' => array(
	            'id'       => 'bvhg_hooks',
	            'title'    => __( 'Enable Beans Visual Hook Guide', 'beans-visual-hook-guide' ),
	            'href'     => esc_url( add_query_arg( 'bvhg_enable', 'show' ) ),
            ),
            'add_visual_hook_guide' => array(
	            'id'       => 'bvhg_html',
	            'title'    => __( 'Beans Visual Hook Guide', 'beans-visual-hook-guide' ),
	            'href'     => '',
            ),
    );

	if ( ! _beans_is_html_dev_mode() ) {
	    bvhg_add_toolbar_top_link( $toolbar_top_link_args['development_mode_disabled'] );
		return;
	}

	if ( 'show' != isset( $_GET['bvhg_enable'] ) ) {
		bvhg_add_toolbar_top_link( $toolbar_top_link_args['enable_visual_hook_guide'] );
	} elseif ( 'show' == isset( $_GET['bvhg_enable'] ) ) {
		bvhg_add_toolbar_top_link( $toolbar_top_link_args['add_visual_hook_guide'] );
	}
}

/**
 *  Function to add Beans Visual Hook Guide Top level links
 *
 * 1. Generate link to Beans Setting if Development mode is disabled
 * 2. Generate a link to enable Beans Visual Hook Guide
 * 3. Generate Top Level Menu for configuration drop-downs
 *
 * @param $menu_args    array   values to generate the required link.
 */
function bvhg_add_toolbar_top_link( $menu_args ){

	global $wp_admin_bar;

	$wp_admin_bar->add_node(
		array(
			'id'       => $menu_args['id'],
			'title'    => $menu_args['title'],
			'href'     => $menu_args['href'],
			'position' => 0,
		)
	);
}

add_action( 'admin_bar_menu', 'bvhg_toolbar_second_level_link_prep', 101 );
/**
 * Add the Admin Toolbar 2nd Level Links - to appear in drop-down:
 * 1. Execute function to show all possible HTML Hooks in a Submenu to allow them to be selected individually.
 * 2. Execute function to show all possible HTML hooks on screen at once (Crazy Mode).
 * 3. Execute function to clear the display of all currently selected hooks.
 * 4. Execute function to disable Visual Hook guide and clear the display of all currently selected hooks.
 */
function bvhg_toolbar_second_level_link_prep() {

	$markup_array_query_args = get_transient( 'beans_html_markup_transient' );

	if ( ! $markup_array_query_args ) {
		return;
	}

	$bvhg_main_query_args = array(
		'bvhg_html_hooks',
		'bvhg_enable',
		'bvhg_enable_every_html_hook'
	);

	global $markup_array_query_args_stripped;

	bvhg_strip_markup_query_args_of_square_brackets( $markup_array_query_args );

	$bvhg_query_args_to_clear = array_merge( $markup_array_query_args_stripped, $bvhg_main_query_args );

	$markup_array_query_args_stripped[] = 'bvhg_enable_every_html_hook';

	bvhg_toolbar_second_level_link_arg_generation( $bvhg_query_args_to_clear );
}

/**
 * Create a multidimensional array of all the args required to add the admin nodes,
 * then loop through them and execute a function with each arg array passed.
 *
 * @param $bvhg_query_args_to_clear     array   Query args array to be used to clear the display
 */
function bvhg_toolbar_second_level_link_arg_generation( $bvhg_query_args_to_clear ) {

	global $markup_array_query_args_stripped;

    $toolbar_drop_down_links_args = array(
            'html_list' => array(
                 'id'      =>  'bvhg_html_list',
	            'title'    => __( 'All HTML API Hooks List - Show Individually', 'beans-visual-hook-guide' ),
	            'href'     => '',
            ),
            'show_all' => array(
                    'id'  =>  'bvhg_show_all_html',
	            'title'    => __( 'Show ALL HTML API Hooks (Crazy Mode)', 'beans-visual-hook-guide' ),
	            'href'     => esc_url( add_query_arg( 'bvhg_enable_every_html_hook', 'show' ) ),
            ),
            'clear' => array(
                    'id'    => 'bvhg_html_clear',
	            'title'    => __( 'Clear all displayed Hooks', 'beans-visual-hook-guide' ),
	            'href'     => esc_url( remove_query_arg( $markup_array_query_args_stripped ) ),
            ),
            'clear_disable' => array(
                    'id'    => 'bvhg_html_clear_disable',
	            'title'    => __( 'Disable Beans HTML API Visual Hook Guide', 'beans-visual-hook-guide' ),
	            'href'     => esc_url( remove_query_arg( $bvhg_query_args_to_clear ) ),
            )
    );

    foreach ( $toolbar_drop_down_links_args as $toolbar_drop_down_links_arg ) {
	    bvhg_toolbar_generate_second_level_links( $toolbar_drop_down_links_arg );
    }
}

/**
 * Function to strip the square brackets from the markup array,
 * So the array can be used to generate query args.
 *
 * Once stripped they are added to a global array.
 *
 * @param $markup_array_query_args  array   array of all HTML API data-markup-id scraped from the DOM
 */
function bvhg_strip_markup_query_args_of_square_brackets( $markup_array_query_args ) {

	global $markup_array_query_args_stripped;

	foreach ( $markup_array_query_args as $markup_array_query_arg ) {
		$markup_array_query_arg_first_strip = str_replace( '[', '', $markup_array_query_arg );
		$markup_array_query_args_stripped[] = str_replace( ']', '', $markup_array_query_arg_first_strip );
	}
}

/**
 * Function used to generate the toolbar second level links, using the args generated and passed to it.
 *
 * @param $toolbar_drop_down_links_arg      array   Array of the args required to generate each link.
 */
function bvhg_toolbar_generate_second_level_links( $toolbar_drop_down_links_arg ) {

	global $wp_admin_bar;

	$wp_admin_bar->add_node(
		array(
			'id'       => $toolbar_drop_down_links_arg['id'],
			'parent'   => 'bvhg_html',
			'title'    => $toolbar_drop_down_links_arg['title'],
			'href'     => $toolbar_drop_down_links_arg['href'],
			'position' => 10,
		)
	);
}

add_action( 'wp_enqueue_scripts', 'bvhg_script_to_scrape_markup_on_page_Load', 1 );
add_action( 'wp_enqueue_scripts', 'bvhg_enqueue_css_if_guide_enabled', 1 );
/**
 * Enqueue Script on page load that:
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

/**
 * Enqueue CSS only if BeansVisual Hook Guide is enabled
 */
function bvhg_enqueue_css_if_guide_enabled() {

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
 * Hook in Beans Visual Hook Guide Functionality
 * 1. Execute function that adds action hooks and toolbar nodes for markup hooks that have been selected individually
 * 2. Check if show all hooks has been chosen, if so, enqueue css script with markup for chosen hooks only, then return.
 * 3. Execute function to add all action hooks for all possible markup
 * 4. Enqueue css for all possible markup hooks.
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

/**
 * Loop through the markup array and:
 * 1. Strip all square brackets so the array values can be used as query args.
 * 2. Execute function to add toolbar nodes for all possible markup hooks that can be chosen.
 * 3. Execute function that adds actions to display markup on all chosen action hooks.
 *
 * @param $markup_array     array   Array of all data-markup-id values scraped from the site and stored as transient.
 */
function bvhg_add_action_hooks_toolbar_nodes_for_individual_markup_hooks( $markup_array ) {

	foreach ( $markup_array as $markup ) {
		$markup_stripped_of_opening_square_bracket = str_replace( '[', '', $markup );
		$markup_stripped_of_all_square_brackets    = str_replace( ']', '', $markup_stripped_of_opening_square_bracket );
		bvhg_add_toolbar_nodes_for_individual_markup_hooks( $markup, $markup_stripped_of_all_square_brackets );
		bvhg_add_action_hooks_for_individually_chosen_markup_hooks( $markup, $markup_stripped_of_all_square_brackets );
	}
}


/**
 * Function to add toolbar nodes for all possible markup hooks that can be chosen
 *
 * @param $markup                               array   array of all data-markup-id values
 * @param $markup_stripped_of_square_brackets   array   array of all data-markup-id values stripped of square brackets
 *                                                      to be used as query args
 */
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

/**
 * Function that adds actions to display markup on all chosen action hooks
 * Add the current markup query arg to the global markup array for making individual css changes.
 *
 * @param $markup                               array   array of all data-markup-id values
 * @param $markup_stripped_of_square_brackets   array   array of all data-markup-id values stripped of square brackets
 *                                                      to be used as query args
 *
 */
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

/**
 * Function to enqueue css script with markup for chosen hooks only.
 */
function bvhg_enqueue_css_script_with_markup_array_for_chosen_hooks_only() {

	global $markup_array_for_individual_css_changes;

	add_action( 'wp_enqueue_scripts', function () use ( $markup_array_for_individual_css_changes ) {
		bvhg_enqueue_element_id_script( $markup_array_for_individual_css_changes );
	}, 1, 999 );
}

/**
 * Function to add all action hooks for all possible markup
 *
 * @param $markup_array     array   Array of all data-markup-id values - used to add actions to all possible hooks.
 */
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

/**
 * Function to add script to enqueue scripts hook, and provide array for localization.
 *
 * @param $markup_array     array   Array of all data-markup-id values - used to add actions to all possible hooks.
 */
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
 * @param $markup_array     array   depending on query_arg displaying, will either be an array of just the chosen elements,
 *                          or every single element with a data-markup-id value.
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






