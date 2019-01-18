<?php

use Api\Infrastructure\Model\EventDispatcher\SyncEventDispatcher;
use Api\Model\EventDispatcher;
use Psr\Container\ContainerInterface;

return [
    EventDispatcher::class => function  (ContainerInterface $container) {
        return new SyncEventDispatcher(
            $container,
            [
                \Api\Model\User\Entity\User\Event\UserCreated::class => [
                    \Api\Infrastructure\Model\EventDispatcher\Listener\User\CreatedListener::class,
                ]
            ]
        );
    },

    \Api\Infrastructure\Model\EventDispatcher\Listener\User\CreatedListener::class => function  (ContainerInterface $container) {
        return new \Api\Infrastructure\Model\EventDispatcher\Listener\User\CreatedListener(
            $container->get(Swift_Mailer::class),
            $container->get('config')['mailer']['from']
        );
    }
];