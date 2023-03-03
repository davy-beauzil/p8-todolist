<?php

namespace App\Tests\Controller\UserController;

use App\Entity\User;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class CreateUserTest extends UserControllerTestCase
{
    public function testShowCreateUserPage(): void
    {
        // Given

        // When
        $this->client->request('GET', '/users/create');
        $response = $this->client->getResponse();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Créer un utilisateur', $response->getContent());
    }

    public function testCreateUser(): void
    {
        // Given
        $randomString = uniqid('', true);

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user' => [
                'username' => $randomString,
                'password' => [
                    'first' => $randomString,
                    'second' => $randomString,
                ],
                'email' => sprintf('%s@test.fr', $randomString)
            ]
        ]);
        $newUser = $this->userRepository->findOneBy(['username' => $randomString]);

        // Then
        $this->assertInstanceOf(User::class, $newUser);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('/users'));
    }

    public function testCreateUserWithAlreadyExistedUsername(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user' => [
                'username' => 'davy',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'email@create.fr'
            ]
        ]);
        $user = $this->userRepository->findOneBy(['username' => 'davy', 'email' => 'email@create.fr']);

        // Then
        $this->assertNull($user);
        $this->assertSame(500, $response->getStatusCode());
    }

    public function testCreateUserWithTooLongUsername(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user' => [
                'username' => 'un-très-très-très-long-username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'email@create.fr'
            ]
        ]);
        $user = $this->userRepository->findOneBy(['username' => 'un-très-très-très-long-username', 'email' => 'email@create.fr']);

        // Then
        $this->assertNull($user);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertContains("SQLSTATE[22001]: String data, right truncated: 1406 Data too long for column 'username'", $response->getContent());
    }

    public function testCreateUserWithDifferentPasswords(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password-1',
                    'second' => 'password-2',
                ],
                'email' => 'email@create.fr'
            ]
        ]);
        $user = $this->userRepository->findOneBy(['username' => 'username', 'email' => 'email@create.fr']);

        // Then
        $this->assertNull($user);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Les deux mots de passe doivent correspondre.', $response->getContent());
    }

    public function testCreateUserWithBadFormatEmail(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'bad-email'
            ]
        ]);
        $user = $this->userRepository->findOneBy(['username' => 'username', 'email' => 'bad-email']);

        // Then
        $this->assertNull($user);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Le format de l&#039;adresse n&#039;est pas correcte.', $response->getContent());
    }

    public function testCreateUserWithTooLongEmail(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'un-très-très-très-très-très-très-très-très-très-long-email@test.fr'
            ]
        ]);
        $user = $this->userRepository->findOneBy(['username' => 'username', 'email' => 'un-très-très-très-très-très-très-très-très-très-long-email@test.fr']);

        // Then
        $this->assertNull($user);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Le format de l&#039;adresse n&#039;est pas correcte.', $response->getContent());
    }
}