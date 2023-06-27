<?php
/**
 * Template for the admin page of your WordPress plugin.
 */

// Prevent direct access to this file.
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="wrap" style="text-align: center;">
	<?php
	$wpmedia_start_disabled = '';
	$wpmedia_hide_view_btn  = true;
	if (false !== get_option('wpmedia_crawler_started')) {
		$wpmedia_start_disabled = 'disabled';
		$wpmedia_hide_view_btn  = false;
	}
	?>
	<div>
		<input type="button" name="start-crawler" id="start-crawler" class="button button-primary mx-3" value="<?php echo __('Start Crawler', 'wp-media-web-crawler'); ?>" <?php echo $wpmedia_start_disabled; ?> style="background-color: green;">
		<input type="button" name="view-links" id="view-links" class="button button-primary mx-3 <?php echo ($wpmedia_hide_view_btn) ? 'hide-view-btn' : ''; ?>" value="<?php echo __('View Links', 'wp-media-web-crawler'); ?>">
		<input type="button" name="reset-crawler" id="reset-crawler" class="button button-primary mx-3" value="<?php echo __('Reset Crawler', 'wp-media-web-crawler'); ?>" style="background-color: orange;">
	</div>
	<div>
		<img class="loader" src="<?php echo WP_MEDIA_CRAWLER_PLUGIN_URL . 'assets/img/loader.gif'; ?>">
	</div>
	<div id="links-section"></div>
</div>
