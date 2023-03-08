<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Tests\AbstractWebTestCase;

class TaskControllerTestCase extends AbstractWebTestCase
{
    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = $this->entityManager->getRepository(Task::class);
    }
}
