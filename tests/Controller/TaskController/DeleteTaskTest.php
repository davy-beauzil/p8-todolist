<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;

class DeleteTaskTest extends TaskControllerTestCase
{
    /**
     * @var Task
     */
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task = $this->taskRepository->findBy([], [], 1)[0];
    }

    /**
     * @test
     */
    public function deleteTask(): void
    {
        // Given
        $this->logIn();
        $id = $this->task->getId();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/delete', $id));
        $response = $this->client->getResponse();
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        // Then
        static::assertNull($updatedTask);
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/tasks'));
    }

    /**
     * @test
     */
    public function deleteTaskWithoutBeLoggedIn(): void
    {
        // Given
        $id = $this->task->getId();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/delete', $id));
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('https://localhost/login'));
    }

    /**
     * @test
     */
    public function deleteTaskWithInexistentId(): void
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/tasks/bad-id/delete');
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(404, $response->getStatusCode());
    }
}
