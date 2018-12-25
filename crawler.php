<?php
if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

define('PUBLIC_DIR', __DIR__ . '/public');

require __DIR__ . '/vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/src/settings.php';

$app = new \Slim\App($settings);
$container = $app->getContainer();

//Database
require __DIR__ . '/src/database.php';

// Set up dependencies
require __DIR__ . '/src/dependencies.php';

// Register middleware
require __DIR__ . '/src/middleware.php';

global $container;
$processController = new App\Controllers\ProcessController($container->get('db')->table('queue'), $container->get('db')->table('instances'));
return $processController->process();