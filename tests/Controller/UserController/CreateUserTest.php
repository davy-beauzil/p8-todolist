<?php

declare(strict_types=1);

namespace App\Tests\Controller\UserController;

use App\Entity\User;

class CreateUserTest extends UserControllerTestCase
{
    /**
     * @test
     */
    public function showCreateUserPage(): void
    {
        // Given

        // When
        $this->client->request('GET', '/users/create');
        $response = $this->client->getResponse();

        // Then
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Créer un utilisateur', $response->getContent());
    }

    /**
     * @test
     */
    public function createUser(): void
    {
        // Given
        $randomString = uniqid('', true);

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => $randomString,
                'password' => [
                    'first' => $randomString,
                    'second' => $randomString,
                ],
                'email' => sprintf('%s@test.fr', $randomString),
            ],
        ]);
        $newUser = $this->userRepository->findOneBy([
            'username' => $randomString,
        ]);

        // Then
        static::assertInstanceOf(User::class, $newUser);
        static::assertSame(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/users'));
    }

    /**
     * @test
     */
    public function createUserWithAlreadyExistedUsername(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'davy',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'email@create.fr',
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'davy',
            'email' => 'email@create.fr',
        ]);

        // Then
        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function createUserWithTooLongUsername(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'un-très-très-très-long-username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'email@create.fr',
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'un-très-très-très-long-username',
            'email' => 'email@create.fr',
        ]);

        // Then
        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString(
            'Le nom d&#039;utilisateur peut faire jusqu&#039;à 25 caractères',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function createUserWithDifferentPasswords(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password-1',
                    'second' => 'password-2',
                ],
                'email' => 'email@create.fr',
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'username',
            'email' => 'email@create.fr',
        ]);

        // Then
        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Les deux mots de passe doivent correspondre.', $response->getContent());
    }

    /**
     * @test
     */
    public function createUserWithBadFormatEmail(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'bad-email',
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'username',
            'email' => 'bad-email',
        ]);

        // Then
        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString(
            'Le format de l&#039;adresse n&#039;est pas correcte.',
            $response->getContent()
        );
    }

    /**
     * @test
     */
    public function createUserWithTooLongEmail(): void
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'un-très-très-très-très-très-très-très-très-très-long-email@test.fr',
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'username',
            'email' => 'un-très-très-très-très-très-très-très-très-très-long-email@test.fr',
        ]);

        // Then
        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString(
            'Le format de l&#039;adresse n&#039;est pas correcte.',
            $response->getContent()
        );
    }
}
