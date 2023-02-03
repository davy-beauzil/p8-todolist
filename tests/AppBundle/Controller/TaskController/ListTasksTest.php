<?php

namespace Tests\AppBundle\Controller\TaskController;

use Tests\AbstractWebTestCase;

class ListTasksTest extends TaskControllerTestCase
{
    public function testListTasks()
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/tasks');
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Créer une tâche', $response->getContent());
    }

    public function testListTasksWithoutBeLoggedIn()
    {
        // Given

        // When
        $this->client->request('GET', '/tasks');
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://localhost/login'));
    }
}