<?php
/**
 * Runtime configuration parameters for the Admin Bar.
 *
 * @package     LearningCurve\BeansVisualHookGuide\Admin
 * @since       1.1.0
 * @author      Jeff Cleverley
 * @link        https://learningcurve.xyz
 * @license     GNU-2.0+
 */

namespace LearningCurve\BeansVisualHookGuide\Admin;

return array(
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
