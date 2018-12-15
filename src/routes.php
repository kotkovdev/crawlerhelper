<?php

use Slim\Http\Request;
use Slim\Http\Response;

/*
 * GET
 */
$app->get('/', App\Controllers\AnalyzeController::class);
$app->get('/save', App\Controllers\SaveController::class);
/*
 * POST
 */
$app->get('/processor', App\Controllers\ProcessorController::class);
