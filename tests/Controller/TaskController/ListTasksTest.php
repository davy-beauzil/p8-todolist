<?php

declare(strict_types=1);

namespace App\Tests\Controller\TaskController;

class ListTasksTest extends TaskControllerTestCase
{
    /**
     * @test
     */
    public function listTasks()
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/tasks');
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(200, $response->getStatusCode());
        static::assertStringContainsString('Créer une tâche', $response->getContent());
    }

    /**
     * @test
     */
    public function listTasksWithoutBeLoggedIn()
    {
        // Given

        // When
        $this->client->request('GET', '/tasks');
        $response = $this->client->getResponse();

        // Then
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('https://localhost/login'));
    }
}
