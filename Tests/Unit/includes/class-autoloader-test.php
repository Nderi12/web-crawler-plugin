<?php
namespace WpMediaCrawler\Tests\Unit;

use WpMediaCrawler\Tests\Unit\TestCase as TestCase;
use \Brain\Monkey\Functions;
use WPMedia\Web\Crawler\Autoloader as Autoloader;
use Actions\Crawler as Crawler;

class AutoloaderTest extends TestCase {
	/**
	 * Method to test get instance.
	 * @return void
	 */
	public function test_get_instance() {

		Autoloader::get_instance();
		$this->assertNotNull( Autoloader::$instance );

	}
	/**
	 * Method to test plugin setup.
	 * @return void
	 */
	public function test_plugin_setup() {

		Functions\expect( 'plugins_url' )
			->with( '/', __FILE__ )
			->andReturn( true );
		Functions\expect( 'plugin_dir_path' )
			->with( __FILE__ )
			->andReturn( true );
		Functions\expect( 'load_plugin_textdomain' )
			->with( 'wp-media-web-crawler' )
			->andReturn( true );

		
		$autoloader_mock = \Mockery::mock( Autoloader::class );
		$autoloader_mock->shouldReceive( 'load_language' )->with('wp-media-web-crawler');
		$autoloader_mock->shouldReceive( 'autoload' ); 
		$crawler_mock = \Mockery::mock( Crawler::class );
		$crawler_mock->shouldReceive( 'init_hooks' );
		$autoloader = Autoloader::get_instance(); 
		$autoloader->plugin_setup();
	}
	/**
	 * Method to test load_language.
	 * @return void
	 */
	public function test_load_language() {
		Functions\expect( 'load_plugin_textdomain' )
				->with( 'wp-media-web-crawler', false, "plugin_path" )
				->andReturn( true );	
		$autoloader = Autoloader::get_instance(); 
		$autoloader->load_language('wp-media-web-crawler');

	}
	/**
	 * Method to test plugin_activation.
	 * @return void
	 */
	public function test_plugin_activation() {
		Functions\expect( 'flush_rewrite_rules' )
					->andReturn( true );	
		$autoloader = Autoloader::get_instance(); 
		$autoloader->plugin_activation();

	}
	/**
	 * Method to test plugin_deactivation.
	 * @return void
	 */
	public function test_plugin_deactivation() {
		Functions\expect( 'flush_rewrite_rules' )
					->andReturn( true );	
		Functions\expect( 'delete_transient' )
					->with( 'wpmedia_crawler_info' )
					->andReturn( true );	
		Functions\expect( 'delete_option' )
					->with( 'wpmedia_crawler_started' )
					->andReturn( true );
		Functions\expect( 'wp_clear_scheduled_hook' )
					->with( 'crawler_task' )
					->andReturn( true );
		$unlink_result = false;
		\Patchwork\replace(
			'unlink',
			function ($file_path) use ( &$unlink_result )
			{
				$unlink_result = true;
			}
		);
		$autoloader = Autoloader::get_instance(); 
		$autoloader->plugin_deactivation();
		$this->assertTrue($unlink_result);

	}


}
