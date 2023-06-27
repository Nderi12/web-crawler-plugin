<?php
/** WP Web Crawler Autoloader file
 *
 * @package WPMedia\Web\Crawler
 * @author Nderi Kamau <nderikamau1212@gmail.com>
 */

namespace WPMedia\Web\Crawler;

use Actions\Crawler;

/**
 * Represents all methods related to autoloading of plugin.
 * @author Nderi Kamau <nderikamau1212@gmail.com>
 */
class Autoloader {

	/**
	 * Plugin instance.
	 *
	 * @var $instance
	 * @see get_instance()
	 * @type object
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public static $instance = null;
	/**
	 * URL to this plugin's directory.
	 *
	 * @var $plugin_url
	 * @type string
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public $plugin_url = '';
	/**
	 * Path to this plugin's directory.
	 *
	 * @var $plugin_path
	 * @type string
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public $plugin_path = '';

	/**
	 * Access this plugin’s working instance
	 *
	 * @wp-hook plugins_loaded
	 * @return  object of this class
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Used for regular plugin work.
	 *
	 * @wp-hook plugins_loaded
	 * @return  void
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public function plugin_setup() {
		$this->plugin_url  = plugins_url( '/', __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->load_language( 'wp-media-web-crawler' );

		spl_autoload_register( [ $this, 'autoload' ] );
		
		// Initiate the Crawler.
		Crawler::init_hooks();
	}

	/**
	 * Loads translation file.
	 *
	 * Accessible to other classes to load different language files (admin and
	 * front-end for example).
	 *
	 * @wp-hook init
	 * @param   string $domain Domain String.
	 * @return  void
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public function load_language( $domain ) {
		load_plugin_textdomain( $domain, false, $this->plugin_path . '/languages' );
	}

	/**
	 * Method to autoload all plugin files.
	 *
	 * @param string $class ClassName.
	 * @return void
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public function autoload( $class ) {

		$class = str_replace( '\\', DIRECTORY_SEPARATOR, $class );
		if ( ! class_exists( $class ) ) {
			$class_arr       = explode( '\\', $class );
			$class_full_path = $this->plugin_path . 'includes/';
			foreach ( $class_arr as $key => $folder ) {
				if ( ( count( $class_arr ) - 1 ) === $key ) {
					$class_full_path .= 'class-' . strtolower( $folder ) . '.php';
				} else {
					$class_full_path .= $folder . '/';
				}
			}
			if ( file_exists( $class_full_path ) ) {
				require_once $class_full_path;
			}
		}
	}

	/**
	 * Callback method for plugin activation.
	 *
	 * @return void
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public function plugin_activation() {
		// clear the permalinks to remove custom url from the database.
		flush_rewrite_rules();
	}

	/**
	 * Callback method for plugin deactivation.
	 *
	 * @return void
	 * @author Nderi Kamau <nderikamau1212@gmail.com>
	 */
	public function plugin_deactivation() {
		// clear the permalinks to remove custom url from the database.
		flush_rewrite_rules();

		// Delete all data and cron schedules related to this.
		delete_transient( 'wpmedia_crawler_info' );
		delete_option( 'wpmedia_crawler_started' );
		wp_clear_scheduled_hook( 'crawler_task' );
		// Delete sitemap.html file.
		$sitemap_path = WP_MEDIA_CRAWLER_PLUGIN_DIR . '/storage/sitemap.html';
		unlink( $sitemap_path );
	}
}
