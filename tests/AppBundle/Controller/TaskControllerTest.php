<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Tests\AbstractWebTestCase;

class TaskControllerTest extends AbstractWebTestCase
{

    public function testCreateTask()
    {
        // Given
        $this->logIn();

        // When
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $crawler = $this->client->submit($form, ['task' => ['title' => 'test_title', 'content' => 'test_content']]);
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('/tasks'));
    }

    public function testCreateTaskWithoutBeLoggedIn()
    {
        // Given
        $this->logIn();

        // When
        $crawler = $this->client->request('GET', '/tasks/create');

        // Suppression de token de connexion
        $this->client->getCookieJar()->clear();

        $form = $crawler->selectButton('Ajouter')->form();
        $crawler = $this->client->submit($form, ['task' => ['title' => 'test_title', 'content' => 'test_content']]);
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://localhost/login'));
    }

    /**
     * @var string $title
     * @var string $content
     * @dataProvider testCreateTaskNotValid_dataProvider
     */
    public function testCreateTaskNotValid($title, $content)
    {
        // Given
        $this->logIn();

        // When
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $crawler = $this->client->submit($form, ['task' => ['title' => $title, 'content' => $content]]);
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Ajouter', $response->getContent());
        $this->assertContains('Retour à la liste des tâches', $response->getContent());
    }

    public function testCreateTaskNotValid_dataProvider()
    {
        return [
            ['test_title', ''],
            ['', 'test_content'],
            ['', ''],
        ];
    }

    /**
     * @dataProvider testShowPages_dataProvider
     *
     * @param string $route
     * @param bool $isLoggedIn
     * @param string[] $content
     * @return void
     */
    public function testShowPages($route, $isLoggedIn, $content = [])
    {
        // Given
        if($isLoggedIn){
            $this->logIn();
        }

        // When
        $this->client->request('GET', $route);
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(200, $response->getStatusCode());
        foreach ($content as $sentence){
            $this->assertContains($sentence, $response->getContent());
        }
    }

    /**
     * @return array[]
     */
    public function testShowPages_dataProvider()
    {
        return [
            'La page qui liste les tâches doit s’afficher si on est connecté' => ['/tasks', true, ['Créer une tâche']],
            'La page pour créer une tâche doit s’afficher si on est connecté' => ['/tasks/create', true, ['Ajouter', 'Retour à la liste des tâches']],
        ];
    }

    /**
     * @dataProvider testRedirectPage_dataProvider
     *
     * @param string $route
     * @param bool $isLoggedIn
     * @param string $redirectUrl
     * @return void
     */
    public function testRedirectPage($route, $isLoggedIn, $redirectUrl)
    {
        // Given
        if($isLoggedIn){
            $this->logIn();
        }

        // When
        $this->client->request('GET', $route);
        $response = $this->client->getResponse();

        // Then
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect($redirectUrl));
    }

    /**
     * @return array[]
     */
    public function testRedirectPage_dataProvider()
    {
        return [
            ['/tasks', false, 'https://localhost/login'],
            ['/tasks/create', false, 'https://localhost/login'],
            ['/tasks/test/edit', false, 'https://localhost/login'],
        ];
    }
}