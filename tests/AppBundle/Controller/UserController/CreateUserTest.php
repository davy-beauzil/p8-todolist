<?php

namespace Tests\AppBundle\Controller\UserController;

use AppBundle\Entity\User;
use Tests\AbstractWebTestCase;

class CreateUserTest extends UserControllerTest
{
    public function testShowCreateUserPage()
    {
        // Given

        // When
        $this->client->request('GET', '/users/create');
        $response = $this->client->getResponse();

        // Then
        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('Créer un utilisateur', $response->getContent());
    }

    public function testCreateUser()
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

    /**
     * @dataProvider testCreateUserNotValid_dataProvider
     * @param string $username
     * @param string $firstPassword
     * @param string $secondPassword
     * @param string $email
     */
    public function testCreateUserNotValid($username, $firstPassword, $secondPassword, $email, $statusCode = 500, $message = null)
    {
        // Given

        // When
        $response = $this->submitForm('/users/create', 'Ajouter', [
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
            'unicité du username' => ['davy', 'password', 'password', 'email@create.fr'],
            '25 caractères maximum pour le username' => ['un-très-très-très-long-username','password', 'password', 'email@test.fr'],
            'les deux mots de passe doivent être identiques' => ['username','mot-de-passe-1', 'mot-de-passe-2', 'email@test.fr', 200, 'Les deux mots de passe doivent correspondre.'],
            'email au mauvais format' => ['username','password', 'password', 'bad-email', 200, 'Le format de l&#039;adresse n&#039;est pas correcte.'],
            '60 caractères maximum pour l’email' => ['username','password', 'password', 'un-très-très-très-très-très-très-très-très-très-long-email@test.fr'],
        ];
    }
}