<?php
// define fake WP_MEDIA_CRAWLER_NAMESPACE.
define( 'WP_MEDIA_CRAWLER_NAMESPACE', 'Actions' );
// define fake WP_MEDIA_CRAWLER_PLUGIN_DIR
define( 'WP_MEDIA_CRAWLER_PLUGIN_DIR', __DIR__ . '/../../' );
// define fake WP_MEDIA_CRAWLER_PLUGIN_URL
define( 'WP_MEDIA_CRAWLER_PLUGIN_URL', "plugin_url");

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../vendor/antecedent/patchwork/Patchwork.php';


require_once __DIR__ . '/../../autoloader.php';

require_once __DIR__ . '/../../includes/Actions/class-crawler.php';

// Include the class for TestCase
require_once __DIR__ . '/TestCase.php';
