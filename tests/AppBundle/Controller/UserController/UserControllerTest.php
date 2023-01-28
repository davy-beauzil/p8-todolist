<?php

namespace Tests\AppBundle\Controller\UserController;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Repository\TaskRepository;
use Tests\AbstractWebTestCase;

class UserControllerTest extends AbstractWebTestCase
{
    /** @var TaskRepository */
    protected $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }
}