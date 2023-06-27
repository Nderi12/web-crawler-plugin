<?php
/** Crawler file
 *
 * @package Controllers
 * @author Nderi Kamau <nderikamau1212@gmail.com>
 */

namespace Controllers;

/**
 * A controller to handle all web crawler functions
 * 
 * @author Nderi Kamau <nderikamau1212@gmail.com>
 */
class Crawler
{
    /**
     * Path to the class.
     *
     * @var $class_path
     * @type string
     */
    protected static $class_path = WP_MEDIA_CRAWLER_NAMESPACE . '\Crawler';

	public static function init_hooks() {
		add_action( 'wp_ajax_start_crawler', [ self::$class_path, 'start_crawler' ] );
	}

    /**
	 * Using ajax to initialize our web crawler
	 *
	 * @return void
     * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public static function start_crawler() {

		$links = self::run_crawler_tasks( 1 );

		// Scheduling task to run for every one hour.
		if ( ! wp_next_scheduled( 'crawler_task' ) ) {
			wp_schedule_event( time(), '1hour', 'crawler_task' );
		}
		// Set flag for starting crawler.
		add_option( 'wpmedia_crawler_started', 1 );
		if ( ! empty( $links ) ) {
			// Display Links.
			$template_path = WP_MEDIA_CRAWLER_PLUGIN_DIR . 'resources/links-list.php';
			include_once $template_path;
		} else {
			$markup = '<div class = \'no-links-msg\' >' . __( 'No links found.', 'wp-media-web-crawler' ) . '</div>';
			echo $markup;
		}
		die();
	}

	/**
	 * Using ajax to list links for admin
	 *
	 * @return void
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public static function view_links() {
		$links = json_decode( get_transient( 'wpmedia_crawler_info' ), true );
		if ( false !== $links && ! empty( $links ) ) {
			$template_path = WP_MEDIA_CRAWLER_PLUGIN_DIR . 'resources/links-list.php';
			include_once $template_path;
		} else {
			$markup = '<div class = \'no-links-msg\'>' . __( 'No links found.', 'wp-media-web-crawler' ) . '</div>';
			echo $markup;
		}
		die();
	}

	/**
	 * Ajax method to reset the crawler.
	 *
	 * @return void
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public static function reset_crawler() {
		delete_transient( 'wpmedia_crawler_info' );
		delete_option( 'wpmedia_crawler_started' );
		wp_clear_scheduled_hook( 'crawler_task' );
		// Delete sitemap.html file.
		$sitemap_path = WP_MEDIA_CRAWLER_PLUGIN_DIR . '/storage/sitemap.html';
		unlink( $sitemap_path );
		$markup = '<div class = \'reset-msg\'>' . __( 'Resetting done. Click on Start Crawler to schedule the task again.', 'wp-media-web-crawler' ) . '</div>';
		echo $markup;
		die();
	}

	/**
	 * Method to set cron schedules.
	 *
	 * @param array $schedules Schedules array.
	 * @return schedules array
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public static function cron_schedules( $schedules ) {
		if ( ! isset( $schedules['1hour'] ) ) {
			$schedules['1hour'] = [
				'interval' => 60 * 60,
				'display'  => __( 'Once every 1 hour', 'wp-media-web-crawler' ),
			];
		}
		return $schedules;
	}
}