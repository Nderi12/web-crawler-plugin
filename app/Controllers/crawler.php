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
		add_action( 'admin_menu', [ self::$class_path, 'register_options_page' ] );
		add_action( 'admin_enqueue_scripts', [ self::$class_path, 'enqueue_scripts_and_styles' ] );
		add_action( 'wp_ajax_start_crawler', [ self::$class_path, 'start_crawler' ] );
		add_action( 'wp_ajax_view_links', [ self::$class_path, 'view_links' ] );
		add_action( 'wp_ajax_reset_crawler', [ self::$class_path, 'reset_crawler' ] );
		add_filter( 'cron_schedules', [ self::$class_path, 'cron_schedules' ] );
		add_action( 'crawler_task', [ self::$class_path, 'run_crawler_tasks' ] );
		add_action( 'init', [ self::$class_path, 'add_sitemap_endpoint' ], 99 );
		add_filter( 'request', [ self::$class_path, 'sitemap_filter_request' ] );
		add_action( 'template_redirect', [ self::$class_path, 'load_sitemap_template' ] );
	}

	/**
	 * Method to enqueue scripts and styles.
	 *
	 * @return void
	 */
	public static function enqueue_scripts_and_styles() {
		wp_register_script( 'wpmedia-crawler-js', WP_MEDIA_CRAWLER_PLUGIN_URL . '/assets/js/main.min.js', [ 'jquery' ], '1.0.0', true );
		wp_localize_script( 'wpmedia-crawler-js', 'myAjax', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
		wp_enqueue_script( 'wpmedia-crawler-js' );

		wp_register_style( 'wpmedia-crawler-css', WP_MEDIA_CRAWLER_PLUGIN_URL . '/assets/css/main.min.css', false, '1.0.0' );
		wp_enqueue_style( 'wpmedia-crawler-css' );

	}

	/**
	 * Method for registering options page.
	 *
	 * @return void
	 */
	public static function register_options_page() {
		add_options_page( 'Crawler', 'Crawler', 'manage_options', 'wpmediacrawler', [ self::$class_path, 'options_page_callback' ] );
	}

	/**
	 * Callback for options page.
	 *
	 * @return void
	 */
	public static function options_page_callback() {
		$template_path = WP_MEDIA_CRAWLER_PLUGIN_DIR . 'templates/admin/crawler-options.php';
		include_once $template_path;
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

	/**
	 * Method to register sitemap end point.
	 *
	 * @return void
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public static function add_sitemap_endpoint() {
		add_rewrite_endpoint( 'wmsitemap', EP_ROOT );
		flush_rewrite_rules();
	}

	/**
	 * Method to set query variable for custom endpoint to true.
	 *
	 * @param array $vars The vars array.
	 *
	 * @return vars array
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public static function sitemap_filter_request( $vars ) {
		if ( isset( $vars['wmsitemap'] ) && empty( $vars['wmsitemap'] ) ) {
			$vars['wmsitemap'] = true;
		}
		return $vars;
	}

	/**
	 * Method to load sitemap template.
	 *
	 * @return void
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public static function load_sitemap_template() {
		if ( get_query_var( 'wmsitemap' ) ) {
			$sitemap_markup = '';
			$sitemap_path   = WP_MEDIA_CRAWLER_PLUGIN_DIR . '/storage/sitemap.html';
			if ( file_exists( $sitemap_path ) ) {
				$sitemap_markup = file_get_contents( $sitemap_path );
			}
			$template_path = WP_MEDIA_CRAWLER_PLUGIN_DIR . 'resources/template-sitemap.php';
			include $template_path;
			exit();
		}

	}
}