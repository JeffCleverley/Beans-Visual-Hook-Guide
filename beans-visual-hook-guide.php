<?php
/**
 * Loads the Beans Visual Hook Guide plugin.
 *
 * @package    LearningCurve\BeansVisualHookGuide
 * @since      1.0.0
 * @author     Jeff Cleverley
 * @link       https://learningcurve.xyz
 * @license    GNU-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:     Beans Visual Hook Guide
 * Plugin URI:      https://github.com/JeffCleverley/Beans-Visual-Hook-Guide
 * Description:     Find Beans Hooks (HTML API created action hooks only at the moment) quickly and easily by seeing
 * their actual locations inside your theme.
 * Version:         1.0.1
 * Author:          Jeff Cleverley
 * Author URI:      https://learningcurve.xyz
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     beans-visual-hook-guid
 * Requires WP:     4.8
 * Requires PHP:    5.6
 */

/**
 * Thanks to:
 *
 * This plugin was inspired by Christopher Cochran's Genesis Visual Hook Guide, one of my favourite plugins for
 * Genesis Development. I started with his plugin and went from there.... Thank you very much Christopher!
 * https://genesistutorials.com/visual-hook-guide/
 * https://github.com/christophercochran/Genesis-Visual-Hook-Guide
 * http://christophercochran.me
 */

namespace LearningCurve\BeansVisualHookGuide;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Hello, Hello, Hello, what\'s going on here then?' );
}

define( 'BVHG_BEANS_PLUGIN_URL', plugins_url( null, __FILE__ ) );

register_activation_hook( __FILE__, __NAMESPACE__ . '\environment_check' );
add_action( 'switch_theme', __NAMESPACE__ . '\environment_check' );
/**
 * Check active theme is Beans:
 * 1. Before Activation
 * 2. After Switching Themes
 *
 * If not:
 * 1. Don't allow activation and throw a die message
 * 2. Disable plugin
 */
function environment_check() {

	$is_beans          = in_array( wp_get_theme()->Template, array( 'beans', 'tm-beans' ) );
	$deactivate_plugin = deactivate_plugins( plugin_basename( __FILE__ ) );

	if ( ! $is_beans && current_filter() != 'switch_theme' ) {
		$deactivate_plugin;
		wp_die( 'Sorry, you can\'t activate unless you have installed Beans</a>' );
	} elseif ( ! $is_beans ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$deactivate_plugin;
	}
}

/**
 * Autoload the plugin's files.
 *
 * @since 1.0.1
 *
 * @return void
 */
function autoload_files() {
	$files = array(
		'markup.php',
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
 * @since 1.0.0
 *
 * @return void
 */
function launch() {
	autoload_files();
}

launch();
