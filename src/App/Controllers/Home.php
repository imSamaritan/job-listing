<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use App\Database;

class Home
{
    public function __construct(private PhpRenderer $renderer, private Database $database)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $sql = "SELECT * FROM users";
        $connection = $this->database->connect();
        $statement = $connection->query($sql);
        $statement->execute();
        $count = $statement->rowCount();
        $this->renderer->render($response, "Home/Index.phtml", ["title" => "Home Page!", "count" => $count]);
        return $response;
    }
}
