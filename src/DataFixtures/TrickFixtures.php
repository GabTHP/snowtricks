<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\Media;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        $cat1 = new Category();
        $cat1->setname('Spin');
        $manager->persist($cat1);

        $cat2 = new Category();
        $cat2->setname('Straight Airs');
        $manager->persist($cat2);

        $cat3 = new Category();
        $cat3->setname('Grab');
        $manager->persist($cat3);

        $cat4 = new Category();
        $cat4->setname('Slides');
        $manager->persist($cat4);

        $cat5 = new Category();
        $cat5->setname('Stall');
        $manager->persist($cat5);

        $cat_list = array($cat1, $cat2, $cat3, $cat4, $cat5);



        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setRoles(['user']);
            $user->setPassword('azerty');
            $user->setEmail($faker->email);
            $user->setAvatar('https://i.pravatar.cc/300');
            $manager->persist($user);


            for ($j = 1; $j <= mt_rand(1, 3); $j++) {
                $trick = new Trick();
                $name = $faker->country;
                $trick->setName($name);
                $trick->setDescription($faker->paragraph);
                $trick->setUser($user);
                $trick->setCategory($cat_list[array_rand($cat_list, 1)]);
                $manager->persist($trick);

                for ($count = 0; $count < 3; $count++) {
                    $media = new Media();
                    $media->setName($faker->word);
                    $media->setUrl("https://fakeimg.pl/250x100/");
                    $media->setTrick($trick);
                    $manager->persist($media);
                }
                for ($count = 0; $count < 3; $count++) {
                    $video = new Video();
                    $video->setName($faker->word);
                    $video->setUrl("https://fakeimg.pl/250x100/");
                    $video->setTrick($trick);
                    $manager->persist($video);
                }
            }


            for ($k = 1; $k <= mt_rand(4, 6); $k++) {
                $message = new Message();
                $message->setContent($faker->sentence);
                $message->setUser($user);
                $message->setTrick($trick);
                $manager->persist($message);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
