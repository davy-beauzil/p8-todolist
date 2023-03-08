<?php

namespace App\Tests\Controller\DefaultController;

use App\Tests\AbstractWebTestCase;

class HomepageTest extends AbstractWebTestCase
{
    public function testShowHomePage(): void
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/', [], [], [
            'PHP_AUTH_USER' => 'davy',
            'PHP_AUTH_PW' => 'test@1234',
        ]);
        $response = $this->client->getResponse();
        $content = $response->getContent();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', $content);
        $this->assertStringContainsString('Créer une nouvelle tâche', $content);
        $this->assertStringContainsString('Consulter la liste des tâches à faire', $content);
        $this->assertStringContainsString('Consulter la liste des tâches terminées', $content);
    }
}