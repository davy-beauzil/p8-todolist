<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Tests\AbstractWebTestCase;

class TaskControllerTestCase extends AbstractWebTestCase
{
    protected TaskRepository $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $this->taskRepository = $taskRepository;
    }
}
