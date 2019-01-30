<?php

declare(strict_types=1);

namespace Api\Infrastructure\Doctrine\Type\OAuth;


use Api\Model\OAuth\Entity\ScopeEntity;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class ScopesType extends JsonType
{
    public const NAME = 'oauth_scopes';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $data = array_map(function (ScopeEntity $entity) {
            return $entity->getIdentifier();
        }, $value);

        return parent::convertToDatabaseValue($data, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        if ($value) {
            return array_map(function ($value) {
                return new ScopeEntity($value);
            }, $value);
        }

        return [];
    }

    public function getName(): string
    {
        return self::NAME;
    }
}