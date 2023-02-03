<?php

namespace Tests\AppBundle\Controller\SecurityController;

use Tests\AbstractWebTestCase;

class LoginTest extends AbstractWebTestCase
{
    public function testShowLoginPage()
    {
        // Given

        // When
        $this->client->request('GET', '/login');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Nom d\'utilisateur :', $content);
        $this->assertContains('Mot de passe :', $content);
        $this->assertContains('Se connecter', $content);
    }
}