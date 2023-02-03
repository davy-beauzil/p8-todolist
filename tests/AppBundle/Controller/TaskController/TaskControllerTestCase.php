<?php

namespace Tests\AppBundle\Controller\TaskController;

use AppBundle\Entity\Task;
use AppBundle\Repository\TaskRepository;
use Tests\AbstractWebTestCase;

class TaskControllerTestCase extends AbstractWebTestCase
{
    /** @var TaskRepository */
    protected $taskRepository;

    public function setUp()
    {
        parent::setUp();
        $this->taskRepository = $this->entityManager->getRepository(Task::class);
    }
}