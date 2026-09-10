<?php

declare(strict_types=1);

require_once __DIR__ . "/constants.php";

use App\Database;
use Dotenv\Dotenv;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Container\ContainerInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserResetPasswordRepositoryInterface;
use App\Repositories\Users\UserRepository;
use App\Repositories\Users\UserPasswordResetRepository;
use App\Middleware\Auth\AuthMiddleware;
use App\Controllers\Users\UsersController;
use App\Controllers\Users\UsersResetPasswordController;
use App\Utilities\AuthTokenUtils;
use App\Utilities\ResetPasswordTokenUtils;
use App\Services\MailService;
use PHPMailer\PHPMailer\PHPMailer;

$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

$cookie_name = $_ENV["USER_COOKIE_NAME"];
$reset_url = $_ENV["RESET_PASSWORD_URL"];
$mail_host = $_ENV["MAIL_HOST"];
$mail_port = (int) $_ENV["MAIL_PORT"];
$mail_username = $_ENV["MAIL_USERNAME"];
$mail_password = $_ENV["MAIL_PASSWORD"];


return [
    ResponseFactoryInterface::class => DI\get(ResponseFactory::class),

    UserRepositoryInterface::class => DI\get(UserRepository::class),

    UserResetPasswordRepositoryInterface::class => DI\get(
        UserPasswordResetRepository::class,
    ),

    MailService::class => function(ContainerInterface $container) use ($mail_host, $mail_password, $mail_port, $mail_username) {
        return new MailService(
            new PHPMailer(true),
            host: $mail_host,
            port: $mail_port,
            username: $mail_username,
            password: $mail_password
        );
    },

    UsersController::class => DI\autowire()->constructorParameter(
        "cookie_name",
        $cookie_name,
    ),

    UsersResetPasswordController::class => DI\autowire()->constructorParameter(
        "reset_password_url",
        $reset_url,
    ),

    AuthMiddleware::class => DI\autowire()->constructorParameter(
        "cookie_name",
        $cookie_name,
    ),

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

    ResetPasswordTokenUtils::class => function () {
        return new ResetPasswordTokenUtils(
            algorithm: $_ENV["RESET_PASSWORD_HASH_ALGORITHM"],
        );
    },
];
