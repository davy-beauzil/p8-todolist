<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\TaskRepository')]
#[ORM\Table]
#[ORM\HasLifecycleCallbacks]
class Task
{
    #[ORM\Column(type: Types::STRING)]
    #[Assert\Length(
        min: 1,
        max: 255,
        minMessage: 'Vous devez saisir un titre.',
        maxMessage: 'Le titre ne peut faire plus de 255 caractères.'
    )]
    #[Assert\NotNull(message: 'Vous devez saisir un titre.')]
    public ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull(message: 'Vous devez saisir un titre.')]
    public ?string $content = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $isDone = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public DateTime $createdAt;

    #[ORM\Column(type: Types::STRING)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $id;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->id = bin2hex(random_bytes(64));
    }
}
