<?php

ob_start();

use CoffeeCode\Router\Router;
use Dotenv\Dotenv;
use MelquesPaiva\RestResponse\HttpResponse\Error;
use MelquesPaiva\RestResponse\Response;

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->safeLoad();

try {
    $dotenv->required(['environment']);
} catch (Exception $e) {
    $error = (new Error())
        ->setStatusCode(500)
        ->setMessage("The enviroment variables are not configured correctly")
        ->setType("enviroment_error");
    echo (new Response())->errorResponse($error);

    ob_end_flush();

    return;
}

$route = new Router(url());
$route->namespace("Source\Api");

$route->group(null);

$route->post("/login", "Sign:login");
$route->post("/register", "Sign:register");

// ALBUM ROUTES
$route->group("/album");

$route->get("/", "Album:getAll");
$route->post("/", "Album:save");
$route->get("/{id}", "Album:getById");
$route->put("/{id}", "Album:update");
$route->delete("/{id}", "Album:delete");

// ARTIST ROUTES
$route->group("/artist");
$route->get("/", "Artist:getAll");
$route->get("/{id}", "Artist:getById");

$route->dispatch();

if ($route->error()) {
    $error = (new Error())
        ->setStatusCode($route->error())
        ->setMessage("A error occured while trying to send the request")
        ->setType((string) $route->error());
    echo (new Response())->errorResponse($error);
}

ob_end_flush();
