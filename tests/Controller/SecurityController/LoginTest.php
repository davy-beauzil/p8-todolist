<?php

declare(strict_types=1);

namespace App\Tests\Controller\SecurityController;

use App\Tests\AbstractWebTestCase;

class LoginTest extends AbstractWebTestCase
{
    /**
     * @test
     */
    public function showLoginPage(): void
    {
        // Given

        // When
        $this->client->request('GET', '/login');
        $response = $this->client->getResponse();
        $content = $response->getContent();

        // Then
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Nom d\'utilisateur :', $content);
        static::assertStringContainsString('Mot de passe :', $content);
        static::assertStringContainsString('Se connecter', $content);
    }
}
