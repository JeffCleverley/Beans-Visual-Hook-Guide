<?php
/**
 * View file for displaying the markup.
 *
 * @package LearningCurve\BeansVisualHookGuide\Markup
 *
 * @since   1.1.0
 */

// phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped -- Variables are escaped in the rendering function. Why? Speed optimization - avoids having to call it over and over again for the same variable.
?>

<div class="bvhg-hook-<?php echo $type; ?>-markup-cue" data-bvhg-hook-cue="<?php echo $markup_id; ?>_<?php echo $type; ?>_markup">
	<?php echo $markup_id; ?>_<?php echo $type; ?>_markup
</div>
