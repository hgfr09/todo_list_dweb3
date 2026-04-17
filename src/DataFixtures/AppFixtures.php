<?php

namespace App\DataFixtures;

use App\Entity\Task;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $task = new Task;
            $task->setTitle('Tâche '.($i + 1) );
            $task->setIsDone(false);
            $task->setCreatedAt(new DateTimeImmutable());

            $manager->persist($task);
        }

        $manager->flush();
    }
}
