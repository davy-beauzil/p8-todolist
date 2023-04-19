<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;

class EditTaskTest extends TaskControllerTestCase
{
    /**
     * @test
     */
    public function showEditTaskPage(): void
    {
        // Given
        $this->loginAsUser();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/edit', $this->userTask->getId()));
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(200, $response->getStatusCode());
        static::assertStringContainsString('Modifier', $response->getContent());
        static::assertStringContainsString($this->userTask->title, $response->getContent());
        static::assertStringContainsString($this->userTask->content, $response->getContent());
    }

    /**
     * @test
     */
    public function showEditTaskPageWithoutBeLoggedIn(): void
    {
        // Given

        // When
        $this->client->request('GET', sprintf('/tasks/%s/edit', $this->userTask->getId()));
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
        $this->loginAsAdmin();

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
        $id = $this->userTask->getId();

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
     * @dataProvider editTaskNotValid_dataProvider
     *
     * @test
     */
    public function editTaskNotValid(string $title, string $content): void
    {
        // Given
        $this->loginAsUser();
        $id = $this->userTask->getId();

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

    /**
     * @test
     */
    public function adminCanUpdateTaskWithoutAuthor(): void
    {
        // Given
        $this->loginAsAdmin();
        $randomString = uniqid(__FUNCTION__, true);
        $id = $this->adminTask->getId();

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
    public function userCannotShowEditTaskPageOfAnotherUser(): void
    {
        // Given
        $this->loginAsUser();

        // When
        $this->client->request('GET', sprintf('/tasks/%s/edit', $this->adminTask->getId()));
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(403, $response->getStatusCode());
    }
}
