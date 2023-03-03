<?php

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;

class DeleteTaskTest extends TaskControllerTestCase
{
    /** @var Task */
    protected $task;

    public function setUp(): void
    {
        parent::setUp();
        $this->task = $this->taskRepository->findBy([], [], 1)[0];
    }

    public function testDeleteTask(): void
    {
        // Given
        $this->logIn();
        $id = $this->task->getId();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/delete', $id));
        $response = $this->client->getResponse();
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy(['id' => $id]);

        // Then
        $this->assertNull($updatedTask);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('/tasks'));
    }

    public function testDeleteTaskWithoutBeLoggedIn(): void
    {
        // Given
        $id = $this->task->getId();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/delete', $id));
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://localhost/login'));
    }

    public function testDeleteTaskWithInexistentId(): void
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/tasks/bad-id/delete');
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(404, $response->getStatusCode());
    }

}