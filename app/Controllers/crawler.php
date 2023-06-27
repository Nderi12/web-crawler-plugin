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
}