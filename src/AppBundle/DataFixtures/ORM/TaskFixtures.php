<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager)
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