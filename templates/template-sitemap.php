<?php
/**
 * Template Name: Sitemap Template
 *
 * @package WordPress
 * @subpackage SEO Web Page Crawler
 * @since 1.0
 */

get_header();

if ( ! empty( $sitemap_markup ) ) {
	echo $sitemap_markup;
} else {
	echo '<div>' . __( 'No Links found', 'wp-media-web-crawler' ) . '</div>';
}
get_footer();
