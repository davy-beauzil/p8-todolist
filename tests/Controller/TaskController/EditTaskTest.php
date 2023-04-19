<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;

class EditTaskTest extends TaskControllerTestCase
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
    public function showEditTaskPage(): void
    {
        // Given
        $this->loginAsUser();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/edit', $this->task->getId()));
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(200, $response->getStatusCode());
        static::assertStringContainsString('Modifier', $response->getContent());
        static::assertStringContainsString($this->task->title, $response->getContent());
        static::assertStringContainsString($this->task->content, $response->getContent());
    }

    /**
     * @test
     */
    public function showEditTaskPageWithoutBeLoggedIn(): void
    {
        // Given

        // When
        $this->client->request('GET', sprintf('/tasks/%s/edit', $this->task->getId()));
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('https://localhost/login'));
    }

    /**
     * @test
     */
    public function showEditTaskPageWithInexistentId(): void
    {
        // Given
        $this->loginAsUser();

        // When
        $this->client->request('GET', '/tasks/bad-id/edit');
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function editTask(): void
    {
        // Given
        $this->loginAsUser();
        $randomString = uniqid(__FUNCTION__, true);
        $id = $this->task->getId();

        // When
        $response = $this->submitForm(sprintf('/tasks/%s/edit', $id), 'Modifier', [
            'task_form' => [
                'title' => $randomString,
                'content' => $randomString,
            ],
        ]);
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        // Then
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/tasks'));
        static::assertEquals($randomString, $updatedTask->title);
        static::assertEquals($randomString, $updatedTask->content);
    }

    /**
     * @test
     */
    public function editTaskWithoutBeLoggedIn(): void
    {
        // Given
        $this->loginAsUser();
        $randomString = uniqid(__FUNCTION__, true);
        $id = $this->task->getId();

        // When
        $response = $this->submitForm(sprintf('/tasks/%s/edit', $id), 'Modifier', [
            'task_form' => [
                'title' => $randomString,
                'content' => $randomString,
            ],
        ], false);
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        // Then
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('https://localhost/login'));
        static::assertNotEquals($randomString, $updatedTask->title);
        static::assertNotEquals($randomString, $updatedTask->content);
    }

    /**
     * @dataProvider editTaskNotValid_dataProvider
     *
     * @test
     */
    public function editTaskNotValid(string $title, string $content): void
    {
        // Given
        $this->loginAsUser();
        $id = $this->task->getId();

        // When
        $response = $this->submitForm(sprintf('/tasks/%s/edit', $id), 'Modifier', [
            'task_form' => [
                'title' => $title,
                'content' => $content,
            ],
        ]);
        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        // Then
        static::assertEquals(200, $response->getStatusCode());
        static::assertNotEquals($title, $updatedTask->title);
        static::assertNotEquals($content, $updatedTask->content);
    }

    public function editTaskNotValid_dataProvider(): array
    {
        return [['test_title', ''], ['', 'test_content'], ['', '']];
    }
}
