<?php
use App\Controllers\AnalyzeController;
use App\Controllers\DownloadController;
// DIC configuration

$container = $app->getContainer();

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

$container['AnalyzeController'] = function() {
    return new App\Controllers\AnalyzeController();
};
$container['SaveController'] = function() {
    return new App\Controllers\SaveController();
};

