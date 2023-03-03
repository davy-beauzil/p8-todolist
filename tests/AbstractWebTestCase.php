<?php

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AbstractWebTestCase extends WebTestCase
{
    /** @var KernelBrowser */
    protected $client;

    /** @var EntityManager */
    protected $entityManager;

    public function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient([], [
            'HTTPS' => 'on',
        ]);
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param string $username
     * @param string $password
     * @return void
     */
    protected function logIn(string $username = 'davy', string $password = 'test@1234'): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient([], [
            'HTTPS' => 'on',
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW' => $password,
        ]);
//        $this->client->setServerParameter('PHP_AUTH_USER', $username);
//        $this->client->setServerParameter('PHP_AUTH_PW', $password);
    }

    /**
     * This allow to perform request with form to submit
     * @param string $route Route to the form
     * @param string $button Content in button to find it
     * @param array $data Data to submit
     * @return Response
     */
    protected function submitForm($route, $button, $data, $loggedIn = true)
    {
        $crawler = $this->client->request('GET', $route);

        if(!$loggedIn){
            $this->logIn('', '');
        }

        $form = $crawler->selectButton($button)->form();
        $this->client->submit($form, $data);

        return $this->client->getResponse();
    }
}