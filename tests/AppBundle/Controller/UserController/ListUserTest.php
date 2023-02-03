<?php

namespace Tests\AppBundle\Controller\UserController;

use Tests\AbstractWebTestCase;

class ListUserTest extends UserControllerTestCase
{
    public function testListUsers()
    {
        // Given

        // When
        $this->client->request('GET', '/users');
        $response = $this->client->getResponse();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Liste des utilisateurs', $response->getContent());
    }
}