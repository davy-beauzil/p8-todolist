<?php

namespace Tests;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Tests\AppBundle\Controller\TaskControllerTest;

class AbstractWebTestCase extends WebTestCase
{
    /** @var Client */
    protected $client = null;

    /** @var EntityManager */
    protected $entityManager = null;

    public function setUp()
    {
        $this->client = static::createClient([], [
            'HTTPS' => 'on'
        ]);
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    protected function logIn($username = 'davy', $email = 'davy@test.fr', $password = 'test@1234')
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $token = new UsernamePasswordToken($username, null, $firewallName, ['ROLE_ADMIN']);
        $token->setUser($user);
        $session->set('_security_'.$firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * This allow to perform request with form to submit
     * @param string $route Route to the form
     * @param string $button Content in button to find it
     * @param array $data Data to submit
     * @return Response
     */
    public function submitForm($route, $button, $data, $loggedIn = true)
    {
        $crawler = $this->client->request('GET', $route);

        if(!$loggedIn){
            $this->client->getCookieJar()->clear();
        }

        $form = $crawler->selectButton($button)->form();
        $crawler = $this->client->submit($form, $data);

        return $this->client->getResponse();
    }
}