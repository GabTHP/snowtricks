<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $username = 'gabriel';
        $roles = ['admin'];
        $password = 'azerty';
        $email = 'gabriel.bouakira@hotmail.fr';
        $avatar = 'https://i.pravatar.cc/300';
        $role = 'admin';

        $user = new User();

        $user->setUsername($username);
        $user->setRoles($roles);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setAvatar($avatar);
        $user->setRole($role);

        $manager->persist($user);
        $manager->flush();

        $faker = Faker\Factory::create('fr_FR');

        $manager->flush();
    }
}
