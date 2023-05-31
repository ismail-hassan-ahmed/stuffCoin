<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Event;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liior\Faker\Prices;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugifier = new Slugify();
        for ($c = 0; $c < 5; $c++)
        {
            $category = new Category;
            $category->setName($faker->title)
                     ->setSlug($slugifier->slugify($category->getName()));

            $manager->persist($category);

            for ($e = 0; $e < mt_rand(15, 20); $e++){

                $event= new Event();
                $event->setTitle($faker->title)
                      ->setSlug($slugifier->slugify($event->getTitle()))
                      ->setPrice($faker->numberBetween(10, 200))
                      ->setContent($faker->realText(1800))
                      ->setLocation($faker->address())
                      ->setStartsAt(new \DateTimeImmutable())
                      ->setCategory($category);
                $manager->persist($event);
            }
        }

        $manager->flush();
    }
}
