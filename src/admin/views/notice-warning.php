<?php
/**
 * View file for admin warning notice.
 *
 * @package LearningCurve\BeansVisualHookGuide\Admin
 *
 * @since   1.1.0
 */

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- The variable is within function scope.
$message = __( 'Beans Visual Hook Guide and Development mode are both active. If this is a production site, remember to deactivate both after use.', 'beans-visual-hook-guide' );

?>

<div class="notice notice-warning" >
	<p><?php echo wp_kses_post( $message ); ?></p>
</div>
