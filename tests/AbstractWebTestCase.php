<?php

namespace Tests;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class AbstractWebTestCase extends WebTestCase
{
    /** @var Client */
    protected $client = null;

    public function setUp()
    {
        $this->client = static::createClient([], [
            'HTTPS' => 'on'
        ]);
    }

    protected function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $user = new User();
        $user->setUsername('davy');
        $user->setEmail('davy@test.fr');
        $user->setPassword('test@1234');
        $token = new UsernamePasswordToken('davy', null, $firewallName, ['ROLE_ADMIN']);
        $token->setUser($user);
        $session->set('_security_'.$firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}