<?php
/**
* Admin Bar Handler.
*
* @package     LearningCurve\BeansVisualHookGuide\Admin
* @since       1.0.1
* @author      Jeff Cleverley
* @link        https://learningcurve.xyz
* @license     GNU-2.0+
*/

namespace LearningCurve\BeansVisualHookGuide\Admin;

add_action( 'admin_bar_menu', __NAMESPACE__ . '\toolbar_top_level_links', 100 );
/**
 * Add the Admin Toolbar Top Level Link conditionally:
 * 1. Execute function to add Link to Beans Theme Settings if Development mode is inactive.
 * 2. Execute function to enable Visual Guide if Development mode is active.
 * 3. Execute function to add Visual Guide Top level link if Visual Guide is enabled.
 */
function toolbar_top_level_links() {

	if ( is_admin() ) {
		return;
	}

	$toolbar_top_link_args = array(
		'development_mode_disabled' => array(
			'id'    => 'bvhg_hooks',
			'title' => __( 'Beans Visual Hook Guide requires development mode to be enabled!', 'beans-visual-hook-guide' ),
			'href'  => esc_url( get_site_url() . '/wp-admin/themes.php?page=beans_settings' ),
		),
		'enable_visual_hook_guide'  => array(
			'id'    => 'bvhg_hooks',
			'title' => __( 'Enable Beans Visual Hook Guide', 'beans-visual-hook-guide' ),
			'href'  => esc_url( add_query_arg( 'bvhg_enable', 'show' ) ),
		),
		'add_visual_hook_guide'     => array(
			'id'    => 'bvhg_html',
			'title' => __( 'Beans Visual Hook Guide', 'beans-visual-hook-guide' ),
			'href'  => '',
		),
	);

	if ( ! _beans_is_html_dev_mode() ) {
		add_toolbar_top_link( $toolbar_top_link_args['development_mode_disabled'] );

		return;
	}

	if ( 'show' != isset( $_GET['bvhg_enable'] ) ) {
		add_toolbar_top_link( $toolbar_top_link_args['enable_visual_hook_guide'] );
	} elseif ( 'show' == isset( $_GET['bvhg_enable'] ) ) {
		add_toolbar_top_link( $toolbar_top_link_args['add_visual_hook_guide'] );
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
function add_toolbar_top_link( $menu_args ) {

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

add_action( 'admin_bar_menu', __NAMESPACE__ . '\toolbar_second_level_link_prep', 101 );
/**
 * Add the Admin Toolbar 2nd Level Links - to appear in drop-down:
 * 1. Execute function to show all possible HTML Hooks in a Submenu to allow them to be selected individually.
 * 2. Execute function to show all possible HTML hooks on screen at once (Crazy Mode).
 * 3. Execute function to clear the display of all currently selected hooks.
 * 4. Execute function to disable Visual Hook guide and clear the display of all currently selected hooks.
 */
function toolbar_second_level_link_prep() {

	$markup_array_query_args = get_transient( 'beans_html_markup_transient' );

	if ( ! $markup_array_query_args ) {
		return;
	}

	$main_query_args = array(
		'bvhg_html_hooks',
		'bvhg_enable',
		'bvhg_enable_every_html_hook'
	);

	global $markup_array_query_args_stripped;

	strip_markup_query_args_of_square_brackets( $markup_array_query_args );

	$query_args_to_clear = array_merge( $markup_array_query_args_stripped, $main_query_args );

	$markup_array_query_args_stripped[] = 'bvhg_enable_every_html_hook';

	toolbar_second_level_link_arg_generation( $query_args_to_clear );
}

/**
 * Create a multidimensional array of all the args required to add the admin nodes,
 * then loop through them and execute a function with each arg array passed.
 *
 * @param $query_args_to_clear     array   Query args array to be used to clear the display
 */
function toolbar_second_level_link_arg_generation( $query_args_to_clear ) {

	global $markup_array_query_args_stripped;

	$toolbar_drop_down_links_args = array(
		'html_list'     => array(
			'id'    => 'bvhg_html_list',
			'title' => __( 'All HTML API Hooks List - Show Individually', 'beans-visual-hook-guide' ),
			'href'  => '',
		),
		'show_all'      => array(
			'id'    => 'bvhg_show_all_html',
			'title' => __( 'Show ALL HTML API Hooks (Crazy Mode)', 'beans-visual-hook-guide' ),
			'href'  => esc_url( add_query_arg( 'bvhg_enable_every_html_hook', 'show' ) ),
		),
		'clear'         => array(
			'id'    => 'bvhg_html_clear',
			'title' => __( 'Clear all displayed Hooks', 'beans-visual-hook-guide' ),
			'href'  => esc_url( remove_query_arg( $markup_array_query_args_stripped ) ),
		),
		'clear_disable' => array(
			'id'    => 'bvhg_html_clear_disable',
			'title' => __( 'Disable Beans HTML API Visual Hook Guide', 'beans-visual-hook-guide' ),
			'href'  => esc_url( remove_query_arg( $query_args_to_clear ) ),
		)
	);

	foreach ( $toolbar_drop_down_links_args as $toolbar_drop_down_links_arg ) {
		toolbar_generate_second_level_links( $toolbar_drop_down_links_arg );
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
function strip_markup_query_args_of_square_brackets( $markup_array_query_args ) {

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
function toolbar_generate_second_level_links( $toolbar_drop_down_links_arg ) {

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

/**
 * Function to add toolbar nodes for all possible markup hooks that can be chosen
 *
 * @param $markup                                       array   array of all data-markup-id values
 * @param $markup_stripped_of_square_brackets           array   array of all data-markup-id values stripped of square brackets
 *                                                      to be used as query args
 */
function add_toolbar_nodes_for_individual_markup_hooks( $markup, $markup_stripped_of_square_brackets ) {

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
