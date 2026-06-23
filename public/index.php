<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use App\Controllers\Home;

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

$app->get("/", Home::class);

$app->run();
