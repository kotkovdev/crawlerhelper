<?php

use Slim\Http\Request;
use Slim\Http\Response;

if (!isset($_SESSION['id'])) {
    $app->any('/', App\Controllers\UserController::class);
    $app->get('/login', \App\Controllers\UserController::class);
    $app->get('/forgot', '\App\Controllers\UserController:forgot');
    $app->post('/login', '\App\Controllers\UserController:login');
    $app->post('/forgot', '\App\Controllers\UserController:forgot');
} else {

    /*
     * GET
     */
    $app->any('/', App\Controllers\AnalyzeController::class);
    $app->get('/download', App\Controllers\SaveController::class);
    $app->get('/list', App\Controllers\ListController::class);
    $app->get('/jobs', App\Controllers\JobsController::class);
    $app->get('/jobs/log', 'App\Controllers\JobsController:log');
    $app->get('/login', \App\Controllers\UserController::class);
    $app->get('/forgot', '\App\Controllers\UserController:forgot');
    $app->get('/instlist', function ($req, $res, $args){
        global $container;
        $instances = new \App\Controllers\InstancesController($container->get('db')->table('instances'));
        return $instances->__invoke($req, $res);
    });
    $app->get('/process', function($req, $res, $args){
        global $container;
        $processController = new App\Controllers\ProcessController($container->get('db')->table('queue'), $container->get('db')->table('instances'));
        return $processController->process();
    });
    /*
     * POST
     */
    $app->post('/process', App\Controllers\ProcessController::class);
    $app->post('/login', '\App\Controllers\UserController:login');
    $app->post('/forgot', '\App\Controllers\UserController:forgot');
    $app->post('/save', 'App\Controllers\AnalyzeController:save');
    $app->post('/download/save', 'App\Controllers\SaveController:save');
    $app->post('/list/save', 'App\Controllers\ListController:save');
    $app->post('/jobs/remove', 'App\Controllers\JobsController:remove');
    $app->post('/instlist/remove', function ($req, $res, $args){
        global $container;
        $instances = new \App\Controllers\InstancesController($container->get('db')->table('instances'));
        return $instances->remove($req, $res);
    });
}
