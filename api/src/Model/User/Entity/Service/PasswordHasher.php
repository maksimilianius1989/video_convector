<?php


namespace Api\Model\User\Entity\Service;


interface PasswordHasher
{
    public function hash(string $password): string;

    public function validate(string $password, string $hash): bool;
}