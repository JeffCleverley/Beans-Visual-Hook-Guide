<?php
/**
 * "CSS on the Fly" Handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Asset
 * @since       1.1.0
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Asset;

use function LearningCurve\BeansVisualHookGuide\_get_plugin_url;

/**
 * Class Css_On_The_Fly
 *
 * @package LearningCurve\BeansVisualHookGuide\Asset
 */
class Css_On_The_Fly {

	/**
	 * Instance of the Singleton.
	 *
	 * @var static
	 */
	private static $instance;

	/**
	 * Array of selected markup IDs.
	 *
	 * @var array
	 */
	protected $selected_ids;

	/**
	 * Array of all markup IDs to be enqueued.
	 *
	 * @var array
	 */
	protected $ids_to_enqueue;

	/**
	 * Create or get the Singleton.
	 *
	 * @since 1.1.0
	 *
	 * @return static
	 */
	public static function get_instance() {

		if ( is_null( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Adds the given markup ID to be enqueued.
	 *
	 * @since 1.1.0
	 *
	 * @param string $markup_id Markup ID to store.
	 *
	 * @return void
	 */
	public function add_markup_id( $markup_id ) {
		$this->selected_ids[] = $markup_id;
	}

	/**
	 * Enqueue the given array of markup IDs.
	 *
	 * @since 1.1.0
	 *
	 * @param array $markup_ids Array of markup IDs.
	 *
	 * @return void
	 */
	public function enqueue_for_all_hooks( array $markup_ids ) {
		$this->ids_to_enqueue = $markup_ids;
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_callback' ), 1, 9999 );
	}

	/**
	 * Enqueue all of the previously selected markup IDs.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function enqueue_for_selected_ids() {
		$this->ids_to_enqueue = $this->selected_ids;
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_callback' ), 9999 );
	}

	/**
	 * Callback to enqueue the script.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function enqueue_callback() {

		if ( empty( $this->ids_to_enqueue ) ) {
			return;
		}

		$this->enqueue_script();
		$this->localize_script();
	}

	/**
	 * Enqueue script to make CSS changes on the fly.
	 *
	 *      1. Adds an orange border around selected elements.
	 *      2. Changes wp_admin toolbar menu item to yellow for elements that are currently selected to be displayed.
	 *
	 * @since  1.1.0
	 *
	 * @return void
	 */
	private function enqueue_script() {
		wp_enqueue_script(
			'css_on_the_fly_script',
			_get_plugin_url() . '/assets/js/css-on-the-fly.js',
			array( 'jquery' ),
			_get_asset_version( '/assets/js/css-on-the-fly.js' ),
			true
		);
	}

	/**
	 * Localizes the script.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	private function localize_script() {
		wp_localize_script(
			'css_on_the_fly_script',
			'cssOnTheFlyParams',
			array( 'classNames' => $this->ids_to_enqueue )
		);
	}

	/**
	 * Prevent a new instance of this Singleton via the "new" operator.
	 */
	private function __construct() {
		// nothing here.
	}

	/**
	 * Prevent cloning of this Singleton.
	 */
	private function __clone() {
		// nothing here.
	}

	/**
	 * Prevent unserializing of this Singleton.
	 */
	private function __wakeup() {
		// nothing here.
	}
}
