<?php
/**
 * View file for admin error notice.
 *
 * @package LearningCurve\BeansVisualHookGuide\Admin
 *
 * @since   1.0.1
 */

?>

<div class="notice notice-error" >
	<p><?php
		printf(
			// translators: The %s is a placeholder for the Beans Settings page's URL.
			__(
				'Beans Visual Hook Guide is currently installed, but it also requires <a href="%s">Development</a> mode to be active before it can be enabled on the toolbar.',
				'beans-visual-hook-guide'
			),
			esc_url( get_site_url() . '/wp-admin/themes.php?page=beans_settings' )
		);
		?>
	</p>
</div>
