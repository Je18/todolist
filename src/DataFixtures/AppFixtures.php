<?php

namespace App\DataFixtures;

use App\Entity\Todo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $todo = new Todo();
            $rand = random_int(10, 500);
            $todo->setName('Todo n°'.$rand);
            $todo->setDescription('Desc n°'.$i);
            $manager->persist($todo);
        }

        $manager->flush();
    }
}
