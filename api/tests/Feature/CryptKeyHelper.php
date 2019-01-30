<?php


namespace Api\Test\Feature;


use League\OAuth2\Server\CryptKey;

class CryptKeyHelper
{
    public static function get(): CryptKey
    {
        return new CryptKey(\dir(__DIR__, 2) . '/' . getenv('API_OAUTH_PRIVATE_KEY_PATH'), null, false);
    }
}