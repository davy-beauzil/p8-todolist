<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 0 ; $i < 20 ; $i++){
            $faker = Factory::create('fr');
            $task = new Task();
            $task->setTitle($faker->word(6));
            $task->setContent($faker->paragraph(5));
            $task->setCreatedAt(new \DateTime());
            $manager->persist($task);
        }

        $manager->flush();
    }
}