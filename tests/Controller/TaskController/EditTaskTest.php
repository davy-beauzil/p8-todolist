<?php

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;

class EditTaskTest extends TaskControllerTestCase
{
    /** @var Task */
    protected $task;

    public function setUp(): void
    {
        parent::setUp();
        $this->task = $this->taskRepository->findBy([], [], 1)[0];
    }

    public function testShowEditTaskPage(): void
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/edit', $this->task->getId()));
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Modifier', $response->getContent());
        $this->assertContains($this->task->getTitle(), $response->getContent());
        $this->assertContains($this->task->getContent(), $response->getContent());
    }

    public function testShowEditTaskPageWithoutBeLoggedIn(): void
    {
        // Given

        // When
        $this->client->request('GET', sprintf('/tasks/%s/edit', $this->task->getId()));
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://localhost/login'));
    }

    public function testShowEditTaskPageWithInexistentId(): void
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/tasks/bad-id/edit');
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testEditTask(): void
    {
        // Given
        $this->logIn();
        $randomString = uniqid(__FUNCTION__, true);
        $id = $this->task->getId();

        // When
        $response = $this->submitForm(sprintf('/tasks/%s/edit', $id), 'Modifier', ['task' => ['title' => $randomString, 'content' => $randomString]]);
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy(['id' => $id]);

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('/tasks'));
        $this->assertEquals($randomString, $updatedTask->getTitle());
        $this->assertEquals($randomString, $updatedTask->getContent());
    }

    public function testEditTaskWithoutBeLoggedIn(): void
    {
        // Given
        $this->logIn();
        $randomString = uniqid(__FUNCTION__, true);
        $id = $this->task->getId();

        // When
        $response = $this->submitForm(sprintf('/tasks/%s/edit', $id), 'Modifier', ['task' => ['title' => $randomString, 'content' => $randomString]], false);
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy(['id' => $id]);

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://localhost/login'));
        $this->assertNotEquals($randomString, $updatedTask->getTitle());
        $this->assertNotEquals($randomString, $updatedTask->getContent());
    }

    /**
     * @dataProvider testEditTaskNotValid_dataProvider
     */
    public function testEditTaskNotValid(string $title, string $content): void
    {
        // Given
        $this->logIn();
        $id = $this->task->getId();

        // When
        $response = $this->submitForm(sprintf('/tasks/%s/edit', $id), 'Modifier', ['task' => ['title' => $title, 'content' => $content]]);
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy(['id' => $id]);

        // Then
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEquals($title, $updatedTask->getTitle());
        $this->assertNotEquals($content, $updatedTask->getContent());
    }

    public function testEditTaskNotValid_dataProvider(): array
    {
        return [
            ['test_title', ''],
            ['', 'test_content'],
            ['', ''],
        ];
    }
}