<?php
/** Core Helper file
 *
 * @package Wpmedia_Web_Page_Crawler
 */

namespace Helper;

/**
 * Represents all methods related of Core Helper.
 */
class Core {

	/**
	 * Method to get internal links of home page.
	 *
	 * @return mixed
	 */
	public static function get_all_internal_links() {
		$links_array = [];
		$home_url    = home_url();
		$html        = file_get_contents( $home_url );
		// Create a new DOM document.
		$dom = new \DOMDocument();
		$dom->loadHTML( $html );
		// Saving home page as html file.
		$dom->save( WP_MEDIA_CRAWLER_PLUGIN_DIR . '/storage/homepage.html' );
		// Get all links.
		$links = $dom->getElementsByTagName( 'a' );

		// Iterate over the extracted links and display their URLs.
		foreach ( $links as $link ) {
			// Extract and show the "href" attribute.
			$url = $link->getAttribute( 'href' );
			$url = filter_var( $url, FILTER_SANITIZE_URL );
			// validate url and only considering internal links.
			if ( ! filter_var( $url, FILTER_VALIDATE_URL ) === false && strpos( $url, $home_url ) !== false && ! in_array( $url, $links_array, true ) ) {

				$links_array[] = $url;
			}
		}
		return $links_array;
	}
}
