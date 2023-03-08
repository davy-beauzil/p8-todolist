<?php

namespace App\Tests\Controller\SecurityController;

use App\Tests\AbstractWebTestCase;

class LoginTest extends AbstractWebTestCase
{
    public function testShowLoginPage(): void
    {
        // Given

        // When
        $this->client->request('GET', '/login');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Nom d\'utilisateur :', $content);
        $this->assertStringContainsString('Mot de passe :', $content);
        $this->assertStringContainsString('Se connecter', $content);
    }
}