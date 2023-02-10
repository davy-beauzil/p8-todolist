<?php

namespace Tests\AppBundle\Controller\SecurityController;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
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

    public function testLoginWithUserNotFound()
    {
        // Given
        $this->logIn('not-found-username', 'not-found-email', 'not-found-password');

        // When
        $this->client->request('GET', '/');
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://localhost/login'));
    }