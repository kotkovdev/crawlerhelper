<?php

use Slim\Http\Request;
use Slim\Http\Response;

/*
 * GET
 */

$app->any('/', App\Controllers\AnalyzeController::class);
$app->get('/download', App\Controllers\SaveController::class);
$app->get('/login', \App\Controllers\UserController::class);
$app->get('/forgot', '\App\Controllers\UserController:forgot');
/*
 * POST
 */
$app->post('/process', App\Controllers\ProcessController::class);
$app->post('/login', '\App\Controllers\UserController:login');
$app->post('/save', 'App\Controllers\AnalyzeController:save');
