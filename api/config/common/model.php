<?php

declare(strict_types=1);

use Api\Infrastructure\Model\Service\DoctrineFlusher;
use Api\Infrastructure\Model\User\Entity\DoctrineUserRepository;
use Api\Infrastructure\Model\User\Service\BCryptPasswordHasher;
use Api\Infrastructure\Model\User\Service\RandConfirmTokenizer;
use Api\Model\Flusher;
use Api\Model\User\Entity\User\UserRepository;
use Api\Model\User\Service\ConfirmTokenizer;
use Api\Model\User\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    Flusher::class => function (ContainerInterface $container) {
        return new DoctrineFlusher(
            $container->get(EntityManagerInterface::class)
        );
    },

    PasswordHasher::class => function  () {
        return new BCryptPasswordHasher();
    },

    UserRepository::class => function  (ContainerInterface $container) {
        return new DoctrineUserRepository(
            $container->get(EntityManagerInterface::class)
        );
    },

    ConfirmTokenizer::class => function  (ContainerInterface $container) {
        $interval = $container->get('config')['auth']['signup_confirm_interval'];
        return new RandConfirmTokenizer(new DateInterval($interval));
    },

    'config' => [
        'auth' => [
            'signup_confirm_interval' => 'PT5M',
        ],
    ],
];