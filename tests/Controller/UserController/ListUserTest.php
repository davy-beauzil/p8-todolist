<?php

declare(strict_types=1);

namespace App\Tests\Controller\UserController;

class ListUserTest extends UserControllerTestCase
{
    /**
     * @test
     */
    public function listUsers(): void
    {
        // Given

        // When
        $this->client->request('GET', '/users');
        $response = $this->client->getResponse();

        // Then
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Liste des utilisateurs', $response->getContent());
    }
}
