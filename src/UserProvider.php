<?php

namespace App;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $registry;
    public function __construct(
        RegistryInterface $registry
    ){
        $this->registry = $registry;
    }

    public function loadUserByUsername($username)
    {
        $userRepository = $this->registry->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => $username]);
        if(null === $user){
            throw new UsernameNotFoundException();
        }
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === User::class;
    }
}