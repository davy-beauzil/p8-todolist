<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

class CreateTaskTest extends TaskControllerTestCase
{
    /**
     * @test
     */
    public function showCreateTaskPage(): void
    {
        // Given
        $this->loginAsUser();

        // When
        $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(200, $response->getStatusCode());
        static::assertStringContainsString('Ajouter', $response->getContent());
        static::assertStringContainsString('Retour à la liste des tâches', $response->getContent());
    }

    /**
     * @test
     */
    public function showCreateTaskPageWithoutBeLoggedIn(): void
    {
        // Given

        // When
        $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('https://localhost/login'));
    }

    /**
     * @test
     */
    public function createTask(): void
    {
        // Given
        $this->loginAsUser();
        $randomString = uniqid(__FUNCTION__, true);

        // When
        $response = $this->submitForm('/tasks/create', 'Ajouter', [
            'task_form' => [
                'title' => $randomString,
                'content' => $randomString,
            ],
        ]);
        $task = $this->taskRepository->findOneBy([
            'title' => $randomString,
            'content' => $randomString,
        ]);

        // Then
        static::assertNotNull($task);
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/tasks'));
    }

    /**
     * @test
     */
    public function createTaskWithoutBeLoggedIn(): void
    {
        // Given
        $this->loginAsUser();
        $randomString = uniqid(__FUNCTION__, true);

        // When
        $response = $this->submitForm('/tasks/create', 'Ajouter', [
            'task_form' => [
                'title' => $randomString,
                'content' => $randomString,
            ],
        ], false);
        $task = $this->taskRepository->findOneBy([
            'title' => $randomString,
            'content' => $randomString,
        ]);

        // Then
        static::assertNull($task);
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('https://localhost/login'));
    }

    /**
     * @dataProvider createTaskNotValid_dataProvider
     *
     * @test
     */
    public function createTaskNotValid(string $title, string $content): void
    {
        // Given
        $this->loginAsUser();

        // When
        $response = $this->submitForm('/tasks/create', 'Ajouter', [
            'task_form' => [
                'title' => $title,
                'content' => $content,
            ],
        ]);
        $task = $this->taskRepository->findOneBy([
            'title' => $title,
            'content' => $content,
        ]);

        // Then
        static::assertNull($task);
        static::assertEquals(200, $response->getStatusCode());
        static::assertStringContainsString('Ajouter', $response->getContent());
        static::assertStringContainsString('Retour à la liste des tâches', $response->getContent());
    }

    public function createTaskNotValid_dataProvider(): array
    {
        return [['test_title', ''], ['', 'test_content'], ['', '']];
    }
}
