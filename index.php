<?php

ob_start();

use CoffeeCode\Router\Router;
use Dotenv\Dotenv;

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

try {
    $dotenv->required(['environment']);
} catch (Exception $e) {
    redirect("/ops/environment");
}

$route = new Router(url());
$route->namespace("Source\App");

/**
 * ERROR ROUTES
 */
$route->group("/ops");
$route->get("/{errcode}", "Web:error");

/**
 * Login Routes
 */

$route->group(null);
$route->get('/', 'Web:login');
$route->get('/register', 'Web:register');
$route->get('/app', 'App:appPage');
$route->get('/session', 'App:session');
$route->get('/logout', 'App:logout');

/**
 * ERROR REDIRECT
 */

if (!$route->dispatch()) {
    redirect("/ops/{$route->error()}");
    exit;
}

ob_end_flush();
