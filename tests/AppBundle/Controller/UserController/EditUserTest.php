<?php

namespace Tests\AppBundle\Controller\UserController;

use AppBundle\Entity\User;
use Tests\AbstractWebTestCase;

class EditUserTest extends UserControllerTest
{
    /** @var User */
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = $this->userRepository->findBy([], [], 1)[0];
    }

    public function testShowEditUserPage()
    {
        // Given

        // When
        $this->client->request('GET', sprintf('/users/%s/edit', $this->user->getId()));
        $response = $this->client->getResponse();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Modifier', $response->getContent());
        $this->assertContains($this->user->getUsername(), $response->getContent());
    }

    public function testShowEditUserPageWithInexistentId()
    {
        // Given

        // When
        $this->client->request('GET', '/users/bad-id/edit');
        $response = $this->client->getResponse();

        // Then
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testEditUser()
    {
        // Given
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

    /**
     * @dataProvider testCreateUserNotValid_dataProvider
     * @param string $username
     * @param string $firstPassword
     * @param string $secondPassword
     * @param string $email
     */
    public function testEditUserNotValid($username, $firstPassword, $secondPassword, $email, $statusCode = 500, $message = null)
    {
        // Given

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->getId()), 'Modifier', [
            'user' => [
                'username' => $username,
                'password' => [
                    'first' => $firstPassword,
                    'second' => $secondPassword,
                ],
                'email' => $email
            ]
        ]);
        $user = $this->userRepository->findOneBy(['username' => $username, 'email' => $email]);

        // Then
        $this->assertNull($user);
        $this->assertSame($statusCode, $response->getStatusCode());
        if(200 === $statusCode){
            $this->assertContains($message, $response->getContent());
        }
    }

    public function testCreateUserNotValid_dataProvider()
    {
        return [
            '25 caractères maximum pour le username' => ['un-très-très-très-long-username','password', 'password', 'email@test.fr'],
            'les deux mots de passe doivent être identiques' => ['username','mot-de-passe-1', 'mot-de-passe-2', 'email@test.fr', 200, 'Les deux mots de passe doivent correspondre.'],
            'email au mauvais format' => ['username','password', 'password', 'bad-email', 200, 'Le format de l&#039;adresse n&#039;est pas correcte.'],
            '60 caractères maximum pour l’email' => ['username','password', 'password', 'un-très-très-très-très-très-très-très-très-très-long-email@test.fr'],
        ];
    }
}