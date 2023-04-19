<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;

class ToggleTaskTest extends TaskControllerTestCase
{
    /**
     * @test
     */
    public function toggleTask(): void
    {
        // Given
        $this->loginAsUser();
        $id = $this->userTask->getId();
        $isDone = $this->userTask->isDone;

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
        $id = $this->userTask->getId();

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
        $this->loginAsUser();

        // When
        $this->client->request('GET', '/tasks/bad-id/toggle');
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function adminCanToggleTaskWithoutAuthor(): void
    {
        // Given
        $this->loginAsAdmin();
        $id = $this->adminTask->getId();
        $isDone = $this->adminTask->isDone;

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
    public function userCannotToggleTaskWithoutAuthor(): void
    {
        // Given
        $this->loginAsUser();
        $id = $this->adminTask->getId();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/toggle', $id));
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(403, $response->getStatusCode());
    }
}
