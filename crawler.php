<?php
if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

define('PUBLIC_DIR', __DIR__ . '/public');
define('LOG_PATH', __DIR__ . '/logs');

require __DIR__ . '/vendor/autoload.php';

session_start();
$lockFileName = __DIR__ . '/process-start.txt';

if (file_exists($lockFileName)) {
    exit('Already runned' . PHP_EOL);
} else {
    file_put_contents($lockFileName, time());
}

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
$processController->process();
unlink($lockFileName);