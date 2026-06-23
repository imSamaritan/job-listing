<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use App\Controllers\Home;

require_once dirname(__DIR__) . "/helper/constant-variables-helper.php";
require_once ROOT_PATH . "/vendor/autoload.php";

$app = AppFactory::create();

$app->get("/", Home::class);

$app->run();
