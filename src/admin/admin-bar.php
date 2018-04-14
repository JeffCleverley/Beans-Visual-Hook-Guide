<?php
/**
 * Admin Bar Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Admin
 * @since       1.1.0
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Admin;

use function LearningCurve\BeansVisualHookGuide\_get_plugin_directory;

add_action( 'init', __NAMESPACE__ . '\create_admin_bar_menus' );
/**
 * Create the menus for the Admin Bar.
 *
 * @since 1.1.0
 *
 * @return void
 */
function create_admin_bar_menus() {
	$config = require_once _get_plugin_directory() . '/config/admin-bar.php';
	require_once __DIR__ . '/class-admin-bar-main-menu.php';
	require_once __DIR__ . '/class-admin-bar-submenu.php';

	( new Admin_Bar_Main_Menu( $config ) )->init();
	( new Admin_Bar_Submenu() )->init();
}

/**
 * Add the given markup IDs to the Admin Bar's "Show Individually" submenu.  This creates a third level menu.
 *
 * @since 1.0.0
 *
 * @param array $markup_ids Array of all data-markup-id values.
 * @param array $query_args Array of query args to add to the URL.
 *
 * @return void
 */
function add_individual_markups_to_admin_bar( $markup_ids, $query_args ) {
	global $wp_admin_bar;

	$wp_admin_bar->add_node(
		array(
			'id'       => "bvhg_html_{$markup_ids}hook",
			'parent'   => 'bvhg_html_list',
			'title'    => $markup_ids,
			'href'     => esc_url( add_query_arg( "{$query_args}", 'show' ) ),
			'position' => 10,
		)
	);
}
