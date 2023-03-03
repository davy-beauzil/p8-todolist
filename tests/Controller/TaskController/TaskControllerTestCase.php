<?php

namespace App\Tests\Controller\TaskController;

use App\Tests\AbstractWebTestCase;
use App\Entity\Task;
use App\Repository\TaskRepository;

class TaskControllerTestCase extends AbstractWebTestCase
{
    /** @var TaskRepository */
    protected $taskRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = $this->entityManager->getRepository(Task::class);
    }
}