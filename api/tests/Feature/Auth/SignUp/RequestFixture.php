<?php


namespace Api\Test\Feature\Auth\SignUp;


use Api\Model\User\Entity\User\ConfirmToken;
use Api\Model\User\Entity\User\Email;
use Api\Model\User\Entity\User\User;
use Api\Model\User\Entity\User\UserId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
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
            new Email('test@example.com'),
            'password_hash',
            new ConfirmToken($token = 'token', new \DateTimeImmutable('+1 day'))
        );

        $user->confirmSignup($token, $now);

        $manager->persist($user);
        $manager->flush();
    }
}