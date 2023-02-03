<?php

namespace Tests\AppBundle\Controller\UserController;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Repository\TaskRepository;
use Tests\AbstractWebTestCase;

class UserControllerTestCase extends AbstractWebTestCase
{
    /** @var TaskRepository */
    protected $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /**
     * @return User
     */
    public function getEditableUser()
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