<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;

class ToggleTaskTest extends TaskControllerTestCase
{
    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task = $this->taskRepository->findBy([], [], 1)[0];
    }

    /**
     * @test
     */
    public function toggleTask(): void
    {
        // Given
        $this->logIn();
        $id = $this->task->getId();
        $isDone = $this->task->isDone;

        // When
        $this->client->request('GET', sprintf('/tasks/%s/toggle', $id));
        $response = $this->client->getResponse();
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        // Then
        static::assertSame(! $isDone, $updatedTask->isDone);
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/tasks'));
    }

    /**
     * @test
     */
    public function toggleTaskWithoutBeLoggedIn(): void
    {
        // Given
        $id = $this->task->getId();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/toggle', $id));
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('https://localhost/login'));
    }

    /**
     * @test
     */
    public function toggleTaskWithInexistentId(): void
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/tasks/bad-id/toggle');
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(404, $response->getStatusCode());
    }
}
