<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        /* for ($i = 0; $i < 10; $i++) {
            $task = new Task;
            $task->setTitle('Tâche '.($i + 1) );
            $task->setIsDone(false);
            $task->setCreatedAt(new DateTimeImmutable());

            $manager->persist($task);
        }
        $manager->flush(); */

        // USER 1
        $pass = '123456';
        $user = new User;
        $user->setEmail('user1@test.com');
        $hashedPassword = $this->hasher->hashPassword($user, $pass);
        $user->setPassword($hashedPassword);
        
        $manager->persist($user);

        // ADMIN
        $admin = new User;
        $admin->setEmail('admin@test.com');
        $hashedPassword = $this->hasher->hashPassword($admin, $pass);
        $admin->setPassword($hashedPassword);
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        
        $manager->flush();
    }
}
