<?php

namespace App\Tests\Controller\UserController;

use App\Tests\AbstractWebTestCase;
use App\Entity\User;
use App\Repository\TaskRepository;

class UserControllerTestCase extends AbstractWebTestCase
{
    /** @var TaskRepository */
    protected $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->entityManager->getRepository(User::class);
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