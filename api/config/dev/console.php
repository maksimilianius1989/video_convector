<?php

use Api\Console\Command\FixtureCommand;

return [
    FixtureCommand::class => function  (\Psr\Container\ContainerInterface $container) {
        return new FixtureCommand(
            $container->get(\Doctrine\ORM\EntityManagerInterface::class),
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