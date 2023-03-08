<?php

namespace App\Tests\Controller\UserController;

use App\Entity\User;

class EditUserTest extends UserControllerTestCase
{
    /** @var User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->getEditableUser();
    }

    public function testShowEditUserPage(): void
    {
        // Given

        // When
        $this->client->request('GET', sprintf('/users/%s/edit', $this->user->getId()));
        $response = $this->client->getResponse();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Modifier', $response->getContent());
        $this->assertStringContainsString($this->user->getUsername(), $response->getContent());
    }

    public function testShowEditUserPageWithInexistentId(): void
    {
        // Given

        // When
        $this->client->request('GET', '/users/bad-id/edit');
        $response = $this->client->getResponse();

        // Then
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testEditUser(): void
    {
        // Given
        $this->logIn();
        $randomString = uniqid('', true);

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->getId()), 'Modifier', [
            'user' => [
                'username' => $randomString,
                'password' => [
                    'first' => $randomString,
                    'second' => $randomString,
                ],
                'email' => sprintf('%s@test.fr', $randomString)
            ]
        ]);
        /** @var User $updatedUser */
        $updatedUser = $this->userRepository->findOneBy(['username' => $randomString]);

        // Then
        $this->assertSame($randomString, $updatedUser->getUsername());
        $this->assertSame(sprintf('%s@test.fr', $randomString), $updatedUser->getEmail());
        $this->assertSame(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('/users'));
    }

    public function testEditUserTooLongUsername(): void
    {
        // Given

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->getId()), 'Modifier', [
            'user' => [
                'username' => 'un-très-très-très-long-username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'email@test.fr'
            ]
        ]);
        $user = $this->userRepository->findOneBy(['username' => 'un-très-très-très-long-username', 'email' => 'email@test.fr']);

        // Then
        $this->assertNull($user);
        $this->assertSame(500, $response->getStatusCode());
        $this->assertStringContainsString("SQLSTATE[22001]: String data, right truncated: 1406 Data too long for column 'username'", $response->getContent());
    }

    public function testEditUserWithDifferentPasswords(): void
    {
        // Given

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->getId()), 'Modifier', [
            'user' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password-1',
                    'second' => 'password-2',
                ],
                'email' => 'email@test.fr'
            ]
        ]);
        $user = $this->userRepository->findOneBy(['username' => 'un-très-très-très-long-username', 'email' => 'email@test.fr']);

        // Then
        $this->assertNull($user);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Les deux mots de passe doivent correspondre.', $response->getContent());
    }

    public function testEditUserWithBadFormatEmail(): void
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
        $this->assertStringContainsString('Le format de l&#039;adresse n&#039;est pas correcte.', $response->getContent());
    }

    public function testEditUserWithTooLongEmail(): void
    {
        // Given

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->getId()), 'Modifier', [
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
        $this->assertStringContainsString('Le format de l&#039;adresse n&#039;est pas correcte.', $response->getContent());
    }
}