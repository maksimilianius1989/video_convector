<?php

use \Psr\Container\ContainerInterface;

return [
    Swift_Mailer::class => function  (ContainerInterface $container) {
        $config = $container->get('config')['mailer'];
        $transport = (new Swift_SmtpTransport($container['host'], $config['port']))
            ->setUsername($config['username'])
            ->setPassword($container['password'])
            ->setEncryption($config['encryption']);
        return new Swift_Mailer($transport);
    },

    'config' => [
        'mailer' => [
            'host' => getenv('API_MAILER_HOST'),
            'port' => getenv('API_MAILER_PORT'),
            'username' => getenv('API_MAILER_USERNAME'),
            'password' => getenv('API_MAILER_PASSWORD'),
            'encryption' => getenv('API_MAILER_ENCRYPTION'),
        ]
    ],
];