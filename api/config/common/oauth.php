<?php


use Api\Infrastructure\Model\OAuth\Entity\AccessTokenRepository;
use Api\Infrastructure\Model\OAuth\Entity\AuthCodeRepository;
use Api\Infrastructure\Model\OAuth\Entity\ClientRepository;
use Api\Infrastructure\Model\OAuth\Entity\RefreshTokenRepository;
use Api\Infrastructure\Model\OAuth\Entity\ScopeRepository;
use Api\Infrastructure\Model\OAuth\Entity\UserRepository;
use Api\Model\User\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Middleware\ResourceServerMiddleware;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerInterface;

return [
    AuthorizationServer::class => function  (ContainerInterface $container) {
        $config = $container->get('config')['oauth'];

        $clientRepository = $container->get(ClientRepositoryInterface::class);
        $scopeRepository = $container->get(ScopeRepositoryInterface::class);
        $accessTokenRepository = $container->get(AccessTokenRepositoryInterface::class);
        $authCodeRepository = $container->get(AuthCodeRepositoryInterface::class);
        $refreshTokenRepository = $container->get(RefreshTokenRepositoryInterface::class);
        $userRepository = $container->get(UserRepositoryInterface::class);

        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            new CryptKey($config['private_key_path'], null, false),
            $config['encryption_key']
        );

        $grant = new AuthCodeGrant($authCodeRepository, $refreshTokenRepository, new DateInterval('PT10M'));
        $server->enableGrantType($grant, new DateInterval('PT1H'));

        $server->enableGrantType(new ClientCredentialsGrant(), new DateInterval('PT1H'));

        $server->enableGrantType(new ImplicitGrant(new DateInterval('PT1H')));

        $grant = new PasswordGrant($userRepository, $refreshTokenRepository);
        $grant->setRefreshTokenTTL(new DateInterval('P1M'));
        $server->enableGrantType($grant, new DateInterval('PT1H'));

        $grant = new RefreshTokenGrant($refreshTokenRepository);
        $grant->setRefreshTokenTTL(new DateInterval('P1M'));
        $server->enableGrantType($grant, new DateInterval('PT1H'));

        return $server;
    },
    ResourceServer::class => function  (ContainerInterface $container) {
        $config = $container->get('config')['oauth'];

        $accessTokenRepository = $container->get(AccessTokenRepositoryInterface::class);

        return new ResourceServer(
            $accessTokenRepository,
            new CryptKey($config['public_key_path'], null, false)
        );
    },
    ResourceServerMiddleware::class => function  (ContainerInterface $container) {
        return new ResourceServerMiddleware(
            $container->get(ResourceServer::class)
        );
    },
    ClientRepositoryInterface::class => function  (ContainerInterface $container) {
        $container = $container->get('config')['oauth'];
        return new ClientRepository($container['clients']);
    },
    ScopeRepositoryInterface::class => function  () {
        return new ScopeRepository();
    },
    AuthCodeRepositoryInterface::class => function  (ContainerInterface $container) {
        return new AuthCodeRepository(
            $container->get(EntityManagerInterface::class)
        );
    },
    AccessTokenRepositoryInterface::class => function  (ContainerInterface $container) {
        return new AccessTokenRepository(
            $container->get(EntityManagerInterface::class)
        );
    },
    RefreshTokenRepositoryInterface::class => function  (ContainerInterface $container) {
        return new RefreshTokenRepository(
            $container->get(EntityManagerInterface::class)
        );
    },
    UserRepositoryInterface::class => function  (ContainerInterface $container) {
        return new UserRepository(
            $container->get(EntityManagerInterface::class),
            $container->get(PasswordHasher::class)
        );
    },

    'config' => [
        'oauth' => [
            'public_key_path' => dirname(__DIR__, 2) . '/' . getenv('API_OAUTH_PUBLIC_KEY_PATH'),
            'private_key_path' => dirname(__DIR__, 2) . '/' . getenv('API_OAUTH_PRIVATE_KEY_PATH'),
            'encryption_key' => getenv('API_OAUTH_ENCRYPTION_KEY'),
            'clients' => [
                'app' => [
                    'secret' => null,
                    'name' => 'App',
                    'redirect_uri' => null,
                    'is_confidential' => false,
                ],
            ],
        ],
    ],
];