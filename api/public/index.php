<?php

declare(strict_types=1);

use Api\Http\Action;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

if (file_exists('.env')) {
    (new \Symfony\Component\Dotenv\Dotenv())->load('.env');
}

(function () {
    $container = require 'config/container.php';
    $app = new \Slim\App($container);
    (require 'config/routes.php')($app);
    $app->run();
})();