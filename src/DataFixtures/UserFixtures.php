<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class UserFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $username = 'gabriel';
        $roles = ['admin'];
        $password = 'azerty';
        $email = 'gabriel.bouakira@hotmail.fr';
        $avatar = 'https://i.pravatar.cc/300';
        $role = 'admin';

        $user = new User();
        $password = $this->encoder->encodePassword($user, 'azerty');
        $user->setPassword($password);
        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setEmail($email);
        $user->setAvatar($avatar);
        $user->setIsVerified(1);

        $manager->persist($user);
        $manager->flush();

        $faker = Faker\Factory::create('fr_FR');

        $manager->flush();
    }
}
