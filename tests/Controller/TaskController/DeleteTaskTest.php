<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;

class DeleteTaskTest extends TaskControllerTestCase
{
    /**
     * @test
     */
    public function deleteTask(): void
    {
        // Given
        $this->loginAsUser();
        $id = $this->userTask->getId();

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
        $id = $this->userTask->getId();

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
        $this->loginAsUser();

        // When
        $this->client->request('GET', '/tasks/bad-id/delete');
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function adminCanDeleteTaskWithoutAuthor(): void
    {
        // Given
        $this->loginAsAdmin();
        $id = $this->adminTask->getId();

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
    public function userCannotDeleteTaskWithoutAuthor(): void
    {
        // Given
        $this->loginAsUser();
        $id = $this->adminTask->getId();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/delete', $id));
        $response = $this->client->getResponse();
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        // Then
        static::assertNotNull($updatedTask);
        static::assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function adminCannotDeleteTaskOfOtherUser(): void
    {
        // Given
        $this->loginAsAdmin();
        $id = $this->userTask->getId();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/delete', $id));
        $response = $this->client->getResponse();
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        // Then
        static::assertNotNull($updatedTask);
        static::assertEquals(403, $response->getStatusCode());
    }
}
