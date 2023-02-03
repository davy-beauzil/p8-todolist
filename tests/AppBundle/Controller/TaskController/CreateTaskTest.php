<?php

namespace Tests\AppBundle\Controller\TaskController;

use AppBundle\Entity\Task;
use AppBundle\Repository\TaskRepository;
use Tests\AbstractWebTestCase;

class CreateTaskTest extends TaskControllerTestCase
{
    public function testShowCreateTaskPage()
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Ajouter', $response->getContent());
        $this->assertContains('Retour à la liste des tâches', $response->getContent());
    }

    public function testShowCreateTaskPageWithoutBeLoggedIn()
    {
        // Given

        // When
        $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://localhost/login'));
    }

    public function testCreateTask()
    {
        // Given
        $this->logIn();
        $randomString = uniqid(__FUNCTION__, true);

        // When
        $response = $this->submitForm('/tasks/create', 'Ajouter', ['task' => ['title' => $randomString, 'content' => $randomString]]);
        $task = $this->taskRepository->findOneBy(['title' => $randomString, 'content' => $randomString]);

        // Then
        $this->assertNotNull($task);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('/tasks'));
    }

    public function testCreateTaskWithoutBeLoggedIn()
    {
        // Given
        $this->logIn();
        $randomString = uniqid(__FUNCTION__, true);

        // When
        $response = $this->submitForm('/tasks/create', 'Ajouter', ['task' => ['title' => $randomString, 'content' => $randomString]], false);
        $task = $this->taskRepository->findOneBy(['title' => $randomString, 'content' => $randomString]);

        // Then
        $this->assertNull($task);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://localhost/login'));
    }

    /**
     * @var string $title
     * @var string $content
     * @dataProvider testCreateTaskNotValid_dataProvider
     */
    public function testCreateTaskNotValid($title, $content)
    {
        // Given
        $this->logIn();

        // When
        $response = $this->submitForm('/tasks/create', 'Ajouter', ['task' => ['title' => $title, 'content' => $content]]);
        $task = $this->taskRepository->findOneBy(['title' => $title, 'content' => $content]);


        // Then
        $this->assertNull($task);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Ajouter', $response->getContent());
        $this->assertContains('Retour à la liste des tâches', $response->getContent());
    }

    public function testCreateTaskNotValid_dataProvider()
    {
        return [
            ['test_title', ''],
            ['', 'test_content'],
            ['', ''],
        ];
    }
}