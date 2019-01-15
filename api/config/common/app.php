<?php

declare(strict_types=1);

use Api\Http\Action;
use Api\Http\Action\Auth\SignUp\RequestAction;
use Api\Model\User\UseCase\SignUp\Request\Handler;
use Psr\Container\ContainerInterface;

return [
    Action\HomeAction::class => function () {
        return new Action\HomeAction();
    },

    RequestAction::class => function  (ContainerInterface $container) {
        return new RequestAction(
            $container->get(Handler::class)
        );
    }
];