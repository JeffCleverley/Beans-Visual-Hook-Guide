<?php
/**
 * Loads the Beans Visual Hook Guide plugin.
 *
 * @package    LearningCurve\BeansVisualHookGuide
 * @since      1.1.0
 * @author     Jeff Cleverley
 * @link       https://learningcurve.xyz
 * @license    GNU-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:     Beans Visual Hook Guide
 * Plugin URI:      https://github.com/JeffCleverley/Beans-Visual-Hook-Guide
 * Description:     Find Beans Hooks (HTML API created action hooks only at the moment) quickly and easily by seeing
 * their actual locations inside your theme.
 * Version:         1.1.0
 * Author:          Jeff Cleverley
 * Author URI:      https://learningcurve.xyz
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     beans-visual-hook-guide
 * Requires WP:     4.6
 * Requires PHP:    5.6
 */

/**
 * Special thanks to:
 *
 * This plugin was inspired by Christopher Cochran's Genesis Visual Hook Guide, one of my favourite plugins for
 * Genesis Development. I started with his plugin and went from there.... Thank you very much Christopher!
 *
 * Links to Christopher:
 * - https://genesistutorials.com/visual-hook-guide/
 * - https://github.com/christophercochran/Genesis-Visual-Hook-Guide
 * - http://christophercochran.me
 */

namespace LearningCurve\BeansVisualHookGuide;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Hello, Hello, Hello, what\'s going on here then?' );
}

/**
 * Gets this plugin's absolute directory path.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_plugin_directory() {
	return __DIR__;
}

/**
 * Gets this plugin's URL.
 *
 * @since  1.1.0
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_plugin_url() {
	static $plugin_url;

	if ( empty( $plugin_url ) ) {
		$plugin_url = plugins_url( null, __FILE__ );
	}

	return $plugin_url;
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\deactivate_when_beans_not_activated_theme' );
add_action( 'switch_theme', __NAMESPACE__ . '\deactivate_when_beans_not_activated_theme' );
/**
 * If Beans is not the activated theme, deactivate this plugin and pop a die message when not switching themes.
 *
 * @since 1.0.0
 *
 * @return void
 */
function deactivate_when_beans_not_activated_theme() {
	// If Beans is the active theme, bail out.
	$theme = wp_get_theme();
	if ( in_array( $theme->template, array( 'beans', 'tm-beans' ), true ) ) {
		return;
	}

	deactivate_plugins( plugin_basename( __FILE__ ) );

	if ( current_filter() !== 'switch_theme' ) {
		$message = __( 'Sorry, you can\'t activate this plugin unless the <a href="https://www.getbeans.io" target="_blank">Beans</a> framework is installed and a child theme is activated.', 'beans-visual-hook-guide' );
		wp_die( wp_kses_post( $message ) );
	}
}

/**
 * Autoload the plugin's files.
 *
 * @since 1.1.0
 *
 * @return void
 */
function autoload_files() {
	$files = array(
		'markup/handler.php',
		'admin/admin-bar.php',
		'admin/notices.php',
		'asset/ajax.php',
		'asset/handler.php',
		'plugin.php',
	);

	foreach ( $files as $file ) {
		require __DIR__ . '/src/' . $file;
	}
}

/**
 * Launch the plugin.
 *
 * @since 1.1.0
 *
 * @return void
 */
function launch() {
	autoload_files();
}

launch();
