<?php

declare(strict_types=1);

namespace App\Tests\Controller\UserController;

use App\Entity\User;

class EditUserTest extends UserControllerTestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->getEditableUser();
    }

    /**
     * @test
     */
    public function showEditUserPage(): void
    {
        // Given
        $this->loginAsAdmin();

        // When
        $this->client->request('GET', sprintf('/users/%s/edit', $this->user->id));
        $response = $this->client->getResponse();

        // Then
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Modifier', $response->getContent());
        static::assertStringContainsString($this->user->username, $response->getContent());
    }

    /**
     * @test
     */
    public function showEditUserPageWithInexistentId(): void
    {
        // Given
        $this->loginAsAdmin();

        // When
        $this->client->request('GET', '/users/bad-id/edit');
        $response = $this->client->getResponse();

        // Then
        static::assertSame(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function editUser(): void
    {
        // Given
        $this->loginAsAdmin();
        $randomString = uniqid('', true);

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->id), 'Modifier', [
            'user_form' => [
                'username' => $randomString,
                'password' => [
                    'first' => $randomString,
                    'second' => $randomString,
                ],
                'email' => sprintf('%s@test.fr', $randomString),
                'roles' => ['ROLE_USER'],
            ],
        ]);
        /** @var User $updatedUser */
        $updatedUser = $this->userRepository->findOneBy([
            'username' => $randomString,
        ]);

        // Then
        static::assertSame(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/users'));
        static::assertSame($randomString, $updatedUser->username);
        static::assertSame(sprintf('%s@test.fr', $randomString), $updatedUser->email);
    }

    /**
     * @test
     */
    public function editUserTooLongUsername(): void
    {
        // Given
        $this->loginAsAdmin();

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->id), 'Modifier', [
            'user_form' => [
                'username' => 'un-très-très-très-long-username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'email@test.fr',
                'roles' => ['ROLE_USER'],
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'un-très-très-très-long-username',
            'email' => 'email@test.fr',
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
    public function editUserWithDifferentPasswords(): void
    {
        // Given
        $this->loginAsAdmin();

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->id), 'Modifier', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password-1',
                    'second' => 'password-2',
                ],
                'email' => 'email@test.fr',
                'roles' => ['ROLE_USER'],
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'un-très-très-très-long-username',
            'email' => 'email@test.fr',
        ]);

        // Then
        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Les deux mots de passe doivent correspondre.', $response->getContent());
    }

    /**
     * @test
     */
    public function editUserWithBadFormatEmail(): void
    {
        // Given
        $this->loginAsAdmin();

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'bad-email',
                'roles' => ['ROLE_USER'],
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
    public function editUserWithTooLongEmail(): void
    {
        // Given
        $this->loginAsAdmin();

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->id), 'Modifier', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'un-très-très-très-très-très-très-très-très-très-long-email@test.fr',
                'roles' => ['ROLE_USER'],
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
