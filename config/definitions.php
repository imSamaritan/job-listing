<?php

declare(strict_types=1);

require_once __DIR__ . "/constants.php";

use App\Database;
use Dotenv\Dotenv;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
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
$dotenv->load();

$dotenv->required([
    "DB_HOST",
    "DB_USER",
    "DB_PASSWORD",
    "DB_NAME",
    "JWT_SECRET_KEY",
    "JWT_ALGORITHM",
    "USER_COOKIE_NAME",
    "RESET_PASSWORD_HASH_ALGORITHM",
    "RESET_PASSWORD_URL",
    "MAIL_HOST",
    "MAIL_PORT",
    "MAIL_USERNAME",
    "MAIL_PASSWORD",
]);

$db_host = $_ENV["DB_HOST"];
$db_user = $_ENV["DB_USER"];
$db_password = $_ENV["DB_PASSWORD"];
$db_name = $_ENV["DB_NAME"];

$jwt_secret_key = $_ENV["JWT_SECRET_KEY"];
$jwt_algorithm = $_ENV["JWT_ALGORITHM"];

$cookie_name = $_ENV["USER_COOKIE_NAME"];

$reset_password_hash_algorithm = $_ENV["RESET_PASSWORD_HASH_ALGORITHM"];
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

    MailService::class => function () use (
        $mail_host,
        $mail_password,
        $mail_port,
        $mail_username,
    ) {
        return new MailService(
            new PHPMailer(true),
            host: $mail_host,
            port: $mail_port,
            username: $mail_username,
            password: $mail_password,
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

    Database::class => function () use ($db_host, $db_user, $db_password, $db_name) {
        return new Database(
            host: $db_host,
            user: $db_user,
            password: $db_password,
            database: $db_name,
        );
    },

    AuthTokenUtils::class => function () use ($jwt_secret_key, $jwt_algorithm) {
        return new AuthTokenUtils(
            secret_key: $jwt_secret_key,
            algorithm: $jwt_algorithm,
        );
    },

    ResetPasswordTokenUtils::class => function () use ($reset_password_hash_algorithm) {
        return new ResetPasswordTokenUtils(
            algorithm: $reset_password_hash_algorithm,
        );
    },
];
