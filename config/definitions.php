<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/helper/constant-variables-helper.php";
use Slim\Views\PhpRenderer;

return [
    PhpRenderer::class => function () {
        $renderer = new PhpRenderer(ROOT_PATH . "/templates");
        $renderer->setLayout("layouts/layout.phtml");
        return $renderer;
    },
];
