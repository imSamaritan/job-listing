<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use App\Middleware\JsonResponseHeaderMiddleware;
use App\Controllers\Home\HomeController;
use App\Controllers\Users\UsersController;
use App\Controllers\Users\UsersDashboardController;
use App\Controllers\Users\UsersResetPasswordController;
use App\Middleware\Validation\UserValidationMiddleware;
use App\Middleware\Validation\UserEmailValidationMiddleware;
use App\Middleware\Auth\AuthMiddleware;

require_once dirname(__DIR__) . "/config/constants.php";
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
$app->get("/login", UsersController::class . ":loginIndex");
$app->get("/register", UsersController::class . ":registerIndex");

$app->get("/dashboard", UsersDashboardController::class . ":index")
    ->add(AuthMiddleware::class);

$app->get("/reset/password", UsersResetPasswordController::class . ":index");

// ----API----

$app->post("/api/create", UsersController::class . ":register")
    ->add(UserValidationMiddleware::class)
    ->add(JsonResponseHeaderMiddleware::class);

$app->post("/api/auth", UsersController::class . ":login")
    ->add(UserValidationMiddleware::class)
    ->add(JsonResponseHeaderMiddleware::class);

$app->post("/api/reset/password", UsersResetPasswordController::class . ":request")
    ->add(UserEmailValidationMiddleware::class)
    ->add(JsonResponseHeaderMiddleware::class);

$app->run();
