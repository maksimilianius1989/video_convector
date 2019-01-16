<?php

declare(strict_types=1);

use Api\Http\Action;
use Psr\Container\ContainerInterface;
use Api\Infrastructure\Framework\Middleware\CallableMiddlewareAdepter as CM;
use Slim\App;

return function (App $app, ContainerInterface $container) {

    $app->add(new CM($container, \Api\Http\Middleware\DomainExceptionMiddleware::class));

    $app->get('/', Action\HomeAction::class . ':handle');

    $app->post('/auth/signup', Action\Auth\SignUp\RequestAction::class . ':handle');

};