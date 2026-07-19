<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/helper/constant-variables-helper.php";

use Slim\Views\PhpRenderer;
use Dotenv\Dotenv;
use App\Database;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\Users\User;

$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

return [
    PhpRenderer::class => function () {
        $renderer = new PhpRenderer(ROOT_PATH . "/templates");
        $renderer->setLayout("layouts/layout.phtml");
        return $renderer;
    },
    Database::class => function () {
        return new Database(
            host: $_ENV["DB_HOST"],
            user: $_ENV["DB_USER"],
            password: $_ENV["DB_PASSWORD"],
            database: $_ENV["DB_NAME"],
        );
    },
    UserRepositoryInterface::class => DI\get(User::class),
];
