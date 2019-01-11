<?php

use Api\Console\Command\FixtureCommand;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    FixtureCommand::class => function  (ContainerInterface $container) {
        return new FixtureCommand(
            $container->get(EntityManagerInterface::class),
            'src/Data/Fixture'
        );
    },

    'config' => [
        'console' => [
            'commands' => [
                FixtureCommand::class,
            ],
        ],
    ],
];