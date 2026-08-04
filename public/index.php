<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use App\Middleware\JsonResponseHeaderMiddleware;
use App\Controllers\HomeController;
use App\Controllers\UsersController;
use App\Middleware\Validation\UserValidationMiddleware;

require_once dirname(__DIR__) . "/helper/constant-variables-helper.php";
require_once ROOT_PATH . "/vendor/autoload.php";

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder
    ->addDefinitions(ROOT_PATH . "/config/definitions.php")
    ->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$errorMiddlerware = $app->addErrorMiddleware(true, true, true);
$handler = $errorMiddlerware->getDefaultErrorHandler();
$handler->forceContentType("application/json");

$app->addBodyParsingMiddleware();

$app->get("/", HomeController::class);
$app->post("/api/create", UsersController::class . ":create")
    ->add(UserValidationMiddleware::class)
    ->add(JsonResponseHeaderMiddleware::class);

$app->post("/api/auth", UsersController::class . ":auth")
    ->add(UserValidationMiddleware::class)
    ->add(JsonResponseHeaderMiddleware::class);

$app->run();
