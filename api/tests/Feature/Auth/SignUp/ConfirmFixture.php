<?php


namespace Api\Test\Feature\Auth\SignUp;


use Api\Model\User\Entity\User\ConfirmToken;
use Api\Model\User\Entity\User\Email;
use Api\Model\User\Entity\User\User;
use Api\Model\User\Entity\User\UserId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class ConfirmFixture extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User(
            UserId::next(),
            $now = new \DateTimeImmutable(),
            new Email('confirm@example.com'),
            'password_hash',
            new ConfirmToken('token', new \DateTimeImmutable('+1 day'))
        );

        $manager->persist($user);

        $expired = new User(
            UserId::next(),
            $now = new \DateTimeImmutable(),
            new Email('expired@example.com'),
            'password_hash',
            new ConfirmToken('token', new \DateTimeImmutable('-1 day'))
        );

        $manager->persist($expired);

        $manager->flush();
    }
}