<?php
/**
 * Plugin Name
 *
 * @package           WPMedia\Web\Crawler
 * @author            Nderi Kamau
 * @copyright         2020 Nderi Kamau
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Web Crawler
 * Plugin URI:        https://github.com/Nderi12/wp-media-web-crawler-plugin
 * Description:       A plugin to crawl all internal links of home page of website.
 * Version:           1.0.0
 * Author:            Nderi Kamau
 * Text Domain:       wp-media-web-crawler
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

/*
SEO Web Page Crawler is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

SEO Web Page Crawler is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SEO Web Page Crawler. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
*/

/**
 * Make sure we don't expose any info if called directly.
 * 
 * @author Nderi Kamau <nderikamau1212@gmail.com>
 */
if ( ! function_exists( 'add_action' ) ) {
	echo 'Invalid request';
	exit;
}

define( 'WP_MEDIA_CRAWLER_NAMESPACE', 'Actions' );
define( 'WP_MEDIA_CRAWLER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_MEDIA_CRAWLER_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

require_once WP_MEDIA_CRAWLER_PLUGIN_DIR . 'autoloader.php';

use WPMedia\Web\Crawler\Autoloader as Autoloader;
add_action( 'plugins_loaded', [ Autoloader::get_instance(), 'plugin_setup' ] );
register_activation_hook( __FILE__, [ Autoloader::get_instance(), 'plugin_activation' ] );
register_deactivation_hook( __FILE__, [ Autoloader::get_instance(), 'plugin_deactivation' ] );
