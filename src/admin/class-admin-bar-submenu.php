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

/**
 * Class Admin_Bar_Submenu
 *
 * @package LearningCurve\BeansVisualHookGuide\Admin
 */
class Admin_Bar_Submenu {

	/**
	 * Array of clean arguments.
	 *
	 * @var array
	 */
	private $clean_args;

	/**
	 * Array of baseline query arguments.
	 *
	 * @var array
	 */
	protected $query_args = array(
		'bvhg_html_hooks',
		'bvhg_enable',
		'bvhg_enable_every_html_hook',
	);

	/**
	 * Initializes by hooking into the "admin_bar_menu" event.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_bar_menu', array( $this, 'add_submenu_callback' ), 101 );
	}

	/**
	 * Handles adding the submenus.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function add_submenu_callback() {
		$query_args = $this->prep_query_args();

		if ( empty( $query_args ) ) {
			return;
		}

		foreach ( $this->generate_menu_args( $query_args ) as $menu_args ) {
			$this->add_submenu_item( $menu_args );
		}
	}

	/**
	 * Prepares the query args.
	 *
	 * @since 1.0.0
	 *
	 * @return array|bool
	 */
	private function prep_query_args() {
		$markup_ids = $this->get_markup_ids();

		if ( empty( $markup_ids ) ) {
			return false;
		}

		$this->remove_square_brackets( $markup_ids );

		$query_args         = array_merge( $this->clean_args, $this->query_args );
		$this->clean_args[] = 'bvhg_enable_every_html_hook';

		return $query_args;
	}

	/**
	 * Gets the markup IDs from the transient.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed
	 */
	private function get_markup_ids() {
		return get_transient( 'beans_html_markup_transient' );
	}

	/**
	 * Removes the square brackets from the given markup IDs.
	 *
	 * @since 1.1.0
	 *
	 * @param array $markup_ids Array of markup IDs.
	 *
	 * @return void
	 */
	private function remove_square_brackets( $markup_ids ) {
		$this->clean_args = array_map( 'LearningCurve\BeansVisualHookGuide\remove_square_brackets', $markup_ids );
	}

	/**
	 * Generate the submenu's arguments.
	 *
	 * @since 1.1.0
	 *
	 * @param array $clear_disable_query_args Array of query args to be stripped for the
	 *                                        "clear display" menu item's URL link.
	 *
	 * @return array
	 */
	private function generate_menu_args( $clear_disable_query_args ) {
		return array(
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
				'href'  => esc_url( remove_query_arg( $this->clean_args ) ),
			),
			'clear_disable' => array(
				'id'    => 'bvhg_html_clear_disable',
				'title' => __( 'Disable Beans HTML API Visual Hook Guide', 'beans-visual-hook-guide' ),
				'href'  => esc_url( remove_query_arg( $clear_disable_query_args ) ),
			),
		);
	}

	/**
	 * Adds each menu item to the submenu.
	 *
	 * @since 1.1.0
	 *
	 * @param array $menu_args Array of the menu args.
	 *
	 * @return void
	 */
	private function add_submenu_item( $menu_args ) {
		global $wp_admin_bar;

		$wp_admin_bar->add_node(
			array(
				'id'       => $menu_args['id'],
				'parent'   => 'bvhg_html',
				'title'    => $menu_args['title'],
				'href'     => $menu_args['href'],
				'position' => 10,
			)
		);
	}
}
