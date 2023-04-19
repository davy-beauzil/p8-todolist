<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('username', message: 'Ce nom d\'utilisateur est déjà utilisé.')]
#[ORM\Table('user')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: Types::STRING, length: 25, unique: true)]
    #[Assert\Length(max: 25, maxMessage: "Le nom d'utilisateur peut faire jusqu'à 25 caractères")]
    #[Assert\NotBlank(message: "Vous devez saisir un nom d'utilisateur.")]
    public string $username;

    #[ORM\Column(type: Types::STRING)]
    public string $password;

    #[ORM\Column(type: Types::STRING, length: 60, unique: true)]
    #[Assert\Length(max: 60)]
    #[Assert\NotBlank(message: 'Vous devez saisir une adresse email.')]
    #[Assert\Email(message: "Le format de l'adresse n'est pas correcte.")]
    public string $email;

    #[Assert\NotNull(message: 'Vous devez saisir un rôle pour cet utilisateur.')]
    #[ORM\Column(type: Types::JSON)]
    public array $roles;

    #[ORM\Column(type: Types::STRING)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $id;

    public function getId(): string
    {
        return $this->id;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->id = bin2hex(random_bytes(64));
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles ?? [];
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function eraseCredentials(): void
    {
        unset($this->password);
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
