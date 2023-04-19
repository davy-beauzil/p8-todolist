<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Tests\AbstractWebTestCase;

class TaskControllerTestCase extends AbstractWebTestCase
{
    protected TaskRepository $taskRepository;

    protected ?Task $adminTask = null;

    protected ?Task $userTask = null;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $this->taskRepository = $taskRepository;
        $this->adminTask = $this->taskRepository->findOneBy([
            'author' => $this->userRepository->findOneBy([
                'username' => 'davy',
            ]),
        ]);
        $this->userTask = $this->taskRepository->findOneBy([
            'author' => $this->userRepository->findOneBy([
                'username' => 'john',
            ]),
        ]);
    }
}
