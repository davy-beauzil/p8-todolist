<?php

namespace Tests\AppBundle\Controller\DefaultController;

use Tests\AbstractWebTestCase;

class HomepageTest extends AbstractWebTestCase
{
    public function testShowHomePage()
    {
        // Given
        $this->logIn();

        // When
        $this->client->request('GET', '/');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', $content);
        $this->assertContains('Créer une nouvelle tâche', $content);
        $this->assertContains('Consulter la liste des tâches à faire', $content);
        $this->assertContains('Consulter la liste des tâches terminées', $content);
    }
}