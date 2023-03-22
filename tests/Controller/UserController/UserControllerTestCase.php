<?php

declare(strict_types=1);

namespace App\Tests\Controller\UserController;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Tests\AbstractWebTestCase;

class UserControllerTestCase extends AbstractWebTestCase
{
    /**
     * @var TaskRepository
     */
    protected $userRepository;

    protected function setUp(): void
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
