<?php

namespace Tests\AppBundle\Controller\TaskController;

use AppBundle\Entity\Task;

class ToggleTaskTest extends TaskControllerTestCase
{
    /** @var Task */
    protected $task;

    public function setUp()
    {
        parent::setUp();
        $this->task = $this->taskRepository->findBy([], [], 1)[0];
    }

    public function testToggleTask()
    {
        // Given
        $this->logIn();
        $id = $this->task->getId();
        $isDone = $this->task->isDone();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/toggle', $id));
        $response = $this->client->getResponse();
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy(['id' => $id]);

        // Then
        $this->assertSame(!$isDone, $updatedTask->isDone());
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('/tasks'));
    }

    public function testToggleTaskWithoutBeLoggedIn()
    {
        // Given
        $id = $this->task->getId();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/toggle', $id));
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://localhost/login'));
    }

    public function testToggleTaskWithInexistentId()
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/tasks/bad-id/toggle');
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(404, $response->getStatusCode());
    }

}