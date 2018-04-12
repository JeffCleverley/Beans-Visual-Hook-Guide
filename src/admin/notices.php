<?php
/**
 * Admin notices handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Admin
 * @since       1.0.1
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Admin;

add_action( 'admin_notices', __NAMESPACE__ . '\active_notice' );
/**
 * Notices to display when plugin active:
 * Notice 1 - Displays when Development Mode isn't active, but plugin is.
 * Notice 2 - Displays when Development Mode and Plugin are active.
 */
function active_notice() {

	if ( ! _beans_is_html_dev_mode() ) {
		echo '<div class="notice notice-error" ><p>';
		echo sprintf(
			__(
				'Beans Visual Hook Guide is currently installed, but it also requires <a href="%s">Development</a> mode to be active before it can be enabled on the toolbar.',
				'beans-visual-hook-guide'
			),
			esc_url( get_site_url() . '/wp-admin/themes.php?page=beans_settings' ) );
		echo '</p></div>';
	} else {
		echo '<div class="notice notice-warning" >'; ?>
		<p><?php _e( 'Beans Visual Hook Guide and Development mode are both active. If this is a production site, remember to deactivate both after use.', 'beans-visual-hook-guide' ) ?></p><?php
		echo '</div>';
	}
}