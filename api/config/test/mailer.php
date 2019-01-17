<?php

use Geekdevs\SwiftMailer\Transport\FileTransport;
use Psr\Container\ContainerInterface;

return [
    Swift_Mailer::class => function  (ContainerInterface $container) {
        $config = $container->get('config')['mailer'];
        $transport = new FileTransport(
            new Swift_Events_SimpleEventDispatcher(),
            $config['local_path']
        );
        return new Swift_Mailer($transport);
    },

    'config' => [
        'mailer' => [
            'local_path' => 'var/mail',
        ],
    ],
];