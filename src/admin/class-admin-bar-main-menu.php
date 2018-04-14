<?php
/**
 * Admin Bar Main Menu Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Admin
 * @since       1.1.0
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Admin;

use function LearningCurve\BeansVisualHookGuide\is_set_to_show_bvhg;

/**
 * Class Admin_Bar_Main_Menu
 *
 * @package LearningCurve\BeansVisualHookGuide\Admin
 */
class Admin_Bar_Main_Menu {

	/**
	 * Array of runtime parameters.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Admin_Bar_Main_Menu constructor.
	 *
	 * @param array $config Array of parameters.
	 */
	public function __construct( array $config ) {
		$this->config = $config;
	}

	/**
	 * Initializes by hooking into the "admin_bar_menu" event.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_bar_menu', array( $this, 'add_menu_callback' ), 100 );
	}

	/**
	 * Handles adding the menu.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function add_menu_callback() {

		if ( _beans_is_html_dev_mode() ) {
			$key = is_set_to_show_bvhg() ? 'add_visual_hook_guide' : 'enable_visual_hook_guide';
		} else {
			$key = 'development_mode_disabled';
		}

		$this->add_main_menu_item( $this->config[ $key ] );
	}

	/**
	 * Add the main menu item.
	 *
	 * @since 1.1.0
	 *
	 * @param array $menu_args Array of menu arguments.
	 *
	 * @return void
	 */
	private function add_main_menu_item( $menu_args ) {
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
}
