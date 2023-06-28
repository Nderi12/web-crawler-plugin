<?php
namespace WpMediaCrawler\Tests\Unit;

use WpMediaCrawler\Tests\Unit\TestCase as TestCase;
use \Brain\Monkey\Functions;
use Actions\Crawler as Crawler;

class CrawlerTest extends TestCase {
	/**
	 * Method to test init_hooks.
	 * 
	 * @return void
	 */
	public function test_init_hooks() {

		Crawler::init_hooks();
		$this->assertTrue( has_action( 'admin_menu', [ Crawler::class, 'register_options_page' ] ) );
		$this->assertTrue( has_action( 'admin_enqueue_scripts', [ Crawler::class, 'enqueue_scripts_and_styles' ] ) );
		$this->assertTrue( has_action( 'wp_ajax_start_crawler', [ Crawler::class, 'start_crawler' ] ) );
		$this->assertTrue( has_action( 'wp_ajax_view_links', [ Crawler::class, 'view_links' ] ) );
		$this->assertTrue( has_action( 'wp_ajax_reset_crawler', [ Crawler::class, 'reset_crawler' ] ) );
		$this->assertTrue( has_filter( 'cron_schedules', [ Crawler::class, 'cron_schedules' ] ) );
		$this->assertTrue( has_action( 'crawler_task', [ Crawler::class, 'run_crawler_tasks' ] ) );
		$this->assertTrue( has_action( 'init', [ Crawler::class, 'add_sitemap_endpoint' ], 99 ) );
		$this->assertTrue( has_filter( 'request', [ Crawler::class, 'sitemap_filter_request' ] ) );
		$this->assertTrue( has_action( 'template_redirect', [ Crawler::class, 'load_sitemap_template' ] ) );
	}
	/**
	 * Method to test enqueue_scripts_and_styles.
	 * 
	 * @return void
	 */
	public function test_enqueue_scripts_and_styles() {
		Functions\expect( 'wp_register_script' )
			->with( 'wpmedia-crawler-js', 'main.js', [ 'jquery' ], '1.0.0', true )
			->andReturn( true );
		Functions\expect( 'wp_localize_script' )
			->with( 'wpmedia-crawler-js', 'myAjax', [ 'ajaxurl' => "admin-ajax.php" ] )
			->andReturn( true );
		Functions\expect( 'wp_enqueue_script' )
			->with( 'wpmedia-crawler-js' )
			->andReturn( true );
		Functions\expect( 'wp_register_style' )
			->with( 'wpmedia-crawler-css', 'main.css', false, '1.0.0' )
			->andReturn( true );
		Functions\expect( 'wp_enqueue_style' )
			->with( 'wpmedia-crawler-css' )
			->andReturn( true );
		Functions\expect( 'admin_url' )
			->with( 'admin-ajax.php' )
			->andReturn( true );

		Crawler::enqueue_scripts_and_styles();
	}
	/**
	 * Method to test register_options_page.
	 * 
	 * @return void
	 */
	public function test_register_options_page() {
		Functions\expect( 'add_options_page' )
			->with( 'Web Crawler', 'Web Crawler', 'manage_options', 'wpmediacrawler', [ Crawler::class, 'options_page_callback' ] )
			->andReturn( true );

		Crawler::register_options_page();
	}
	/**
	 * Method to test options_page_callback.
	 * 
	 * @return void
	 */
	public function test_options_page_callback() {
		$include_result = false;
		\Patchwork\replace(
            'include_once',
            function ($file_path) use ( &$include_result )
            {
				$include_result = true;
            }
		);
		Crawler::options_page_callback();
		$this->assertTrue($include_result);
		
	}
	/**
	 * Method to test cron_schedules.
	 * 
	 * @return void
	 */
	public function test_cron_schedules() {
		$expected_schedule_array = array('interval' => 60 * 60,
									'display'  => 'Once every 1 hour' );

		$schedules = Crawler::cron_schedules(array());
		$this->assertTrue(isset($schedules['1hour']));
		$this->assertEquals($schedules['1hour'],$expected_schedule_array);
	}
	/**
	 * Method to test add_sitemap_endpoint.
	 * 
	 * @return void
	 */
	public function test_add_sitemap_endpoint() {
		define('EP_ROOT', 'root');
		Functions\expect( 'flush_rewrite_rules' )
			->andReturn( true );
		Functions\expect( 'add_rewrite_endpoint' )
			->with('wmsitemap', EP_ROOT)
			->andReturn( true );
		Crawler::add_sitemap_endpoint();
	}
	/**
	 * Method to test sitemap_filter_request.
	 * 
	 * @return void
	 */
	public function test_sitemap_filter_request() {
		$result = Crawler::sitemap_filter_request(array( "wmsitemap" => "" ));
		$this->assertTrue( $result['wmsitemap'] );
	}
}
