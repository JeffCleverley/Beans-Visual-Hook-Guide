<?php
/**
 * Admin notices handler.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Admin
 * @since       1.1.0
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Admin;

add_action( 'admin_notices', __NAMESPACE__ . '\render_admin_notice' );
/**
 * Renders the admin notice.  If in Development mode, a warning notice is rendered; else, the error warning
 * is rendered.
 *
 * @since 1.1.0
 *
 * @return void
 */
function render_admin_notice() {
	$which_notice = _beans_is_html_dev_mode() ? 'warning' : 'error';

	require __DIR__ . "/views/notice-{$which_notice}.php";
}
