<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use App\Middleware\JsonResponseHeaderMiddleware;
use App\Controllers\Home\HomeController;
use App\Controllers\Users\RegisterController;
use App\Controllers\Users\LoginController;
use App\Controllers\Users\DashboardController;
use App\Controllers\Users\ResetPasswordController;
use App\Middleware\Validation\RegisterValidationMiddleware;
use App\Middleware\Validation\LoginValidationMiddleware;
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
$app->get("/login", LoginController::class . ":index");
$app->get("/register", RegisterController::class . ":index");

$app->get("/dashboard", DashboardController::class . ":index")
    ->add(AuthMiddleware::class);

$app->get("/reset/password", ResetPasswordController::class . ":index");
$app->get("/password/reset", ResetPasswordController::class . ":resetIndex");

// ----API----

$app->post("/api/create", RegisterController::class . ":register")
    ->add(RegisterValidationMiddleware::class)
    ->add(JsonResponseHeaderMiddleware::class);

$app->post("/api/auth", LoginController::class . ":login")
    ->add(LoginValidationMiddleware::class)
    ->add(JsonResponseHeaderMiddleware::class);

$app->post("/api/reset/password", ResetPasswordController::class . ":request")
    ->add(UserEmailValidationMiddleware::class)
    ->add(JsonResponseHeaderMiddleware::class);

$app->run();
