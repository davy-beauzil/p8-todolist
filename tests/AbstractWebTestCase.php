<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AbstractWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected EntityManager $entityManager;

    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient([], [
            'HTTPS' => 'on',
        ]);
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    protected function logIn(string $username = 'davy'): void
    {
        $this->client->loginUser($this->userRepository->findOneBy([
            'username' => $username,
        ]));
    }

    /**
     * This allow to perform request with form to submit
     * @param string $route Route to the form
     * @param string $button Content in button to find it
     * @param array $data Data to submit
     */
    protected function submitForm(string $route, string $button, array $data, bool $loggedIn = true): Response
    {
        $crawler = $this->client->request('GET', $route);

        if (! $loggedIn) {
            $this->client->getCookieJar()
                ->clear();
        }

        $form = $crawler->selectButton($button)
            ->form();
        $this->client->submit($form, $data);

        return $this->client->getResponse();
    }
}
