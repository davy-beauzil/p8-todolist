<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    #[ORM\Column(type: Types::STRING)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    public string $id;

    #[ORM\Column(type: Types::STRING, length: 25, unique: true)]
    #[Assert\Length(max: 25, maxMessage: "Le nom d'utilisateur peut faire jusqu'à 25 caractères")]
    #[Assert\NotBlank(message: "Vous devez saisir un nom d'utilisateur.")]
    public string $username;

    #[Assert\NotBlank(message: 'Vous devez saisir un mot de passe.')]
    #[Assert\NotNull(message: 'Vous devez saisir un mot de passe.')]
    #[ORM\Column(type: Types::STRING)]
    public ?string $password = null;

    #[ORM\Column(type: Types::STRING, length: 60, unique: true)]
    #[Assert\Length(max: 60)]
    #[Assert\NotBlank(message: 'Vous devez saisir une adresse email.')]
    #[Assert\Email(message: "Le format de l'adresse n'est pas correcte.")]
    public string $email;

    #[Assert\NotNull(message: 'Vous devez saisir un rôle pour cet utilisateur.')]
    #[ORM\Column(type: Types::JSON)]
    public array $roles;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\Task>|\App\Entity\Task[]
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Task::class)]
    public Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function addTask(Task $task): self
    {
        if (! $this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->author = $this;
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->author === $this) {
                $task->author = null;
            }
        }

        return $this;
    }
}
