<?php

namespace App\Tests\Controller\UserController;

class ListUserTest extends UserControllerTestCase
{
    public function testListUsers(): void
    {
        // Given

        // When
        $this->client->request('GET', '/users');
        $response = $this->client->getResponse();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Liste des utilisateurs', $response->getContent());
    }
}