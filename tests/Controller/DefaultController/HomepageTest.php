<?php

declare(strict_types=1);

namespace App\Tests\Controller\DefaultController;

use App\Tests\AbstractWebTestCase;

class HomepageTest extends AbstractWebTestCase
{
    /**
     * @test
     */
    public function showHomePage(): void
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
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString(
            'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !',
            $content
        );
        static::assertStringContainsString('Créer une nouvelle tâche', $content);
        static::assertStringContainsString('Consulter la liste des tâches à faire', $content);
        static::assertStringContainsString('Consulter la liste des tâches terminées', $content);
    }
}
