<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $userLogin = new User();
        $userLogin->setUsername('davy');
        $userLogin->setEmail('davy@test.fr');
        $userLogin->setPassword($this->encoder->hashPassword($userLogin, 'test@1234'));
        $manager->persist($userLogin);

        $faker = Factory::create('fr');
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
