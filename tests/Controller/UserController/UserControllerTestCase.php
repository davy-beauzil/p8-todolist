<?php

declare(strict_types=1);

namespace App\Tests\Controller\UserController;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\AbstractWebTestCase;

class UserControllerTestCase extends AbstractWebTestCase
{
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->userRepository = $userRepository;
    }

    public function getEditableUser(): User
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.username != :username')
            ->setParameter('username', 'davy')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0];
    }
}
