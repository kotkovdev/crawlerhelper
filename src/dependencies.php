<?php
use App\Controllers\AnalyzeController;
use App\Controllers\DownloadController;
// DIC configuration

$container = $app->getContainer();


$container['view'] = function($container) {
    $view = new \Slim\Views\Twig(__DIR__ .'/../templates/', [
        //'cache' => __DIR__ . '/../cache/'
    ]);

    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};


// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};



// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Service factory for the ORM
$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container[App\Controllers\AnalyzeController::class] = function ($c) {
    $table = $c->get('db')->table('settings');
    return new App\Controllers\AnalyzeController($table);
};

$container[App\Controllers\SaveController::class] = function ($c) {
    $table = $c->get('db')->table('settings');
    return new App\Controllers\SaveController($table);
};

$container[App\Controllers\ListController::class] = function ($c) {
    $table = $c->get('db')->table('settings');
    return new App\Controllers\ListController($table);
};

$container[App\Controllers\UserController::class] = function ($c) {
    $table = $c->get('db')->table('users');
    return new App\Controllers\UserController($table);
};

$container[App\Controllers\ProcessController::class] = function($c) {
    $jobs = $c->get('db')->table('queue');
    $instances = $c->get('db')->table('instances');
    return new App\Controllers\ProcessController($jobs, $instances);
};

$container[App\Controllers\JobsController::class] = function($c) {
    $jobs = $c->get('db')->table('queue');
    return new App\Controllers\JobsController($jobs);
};

$container[App\Controllers\InstanceController::class] = function($c) {
    $instances = $c->get('db')->table('instances');
    return new App\Controllers\InstancesController($instances);
};

