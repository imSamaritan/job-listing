<?php

declare(strict_types=1);

require_once __DIR__ . "/constants.php";

use App\Database;
use Dotenv\Dotenv;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
// use Psr\Container\ContainerInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserResetPasswordRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserPasswordResetRepository;
use App\Middleware\Auth\AuthMiddleware;
use App\Controllers\Users\UsersController;
use App\Utilities\AuthTokenUtils;
use App\Utilities\ResetPasswordTokenUtils;
use PHPMailer\PHPMailer\PHPMailer;

$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

$cookie_name = $_ENV["USER_COOKIE_NAME"] ?? "user_token_4500";

return [
    ResponseFactoryInterface::class => DI\get(ResponseFactory::class),

    UserRepositoryInterface::class => DI\get(UserRepository::class),

    UserResetPasswordRepositoryInterface::class => DI\get(UserPasswordResetRepository::class),
    
    PHPMailer::class => function() {
        return new PHPMailer(true);
    },
    
    UsersController::class => DI\autowire()->constructorParameter("cookie_name", $cookie_name),

    AuthMiddleware::class => DI\autowire()->constructorParameter("cookie_name", $cookie_name),
    
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

    AuthTokenUtils::class => function () {
        return new AuthTokenUtils(
            secret_key: $_ENV["JWT_SECRET_KEY"],
            algorithm: $_ENV["JWT_ALGORITHM"],
        );
    },

    ResetPasswordTokenUtils::class => function() {
      return new ResetPasswordTokenUtils(
        algorithm: $_ENV["RESET_PASSWORD_HASH_ALGORITHM"]
      ); 
    },
];
