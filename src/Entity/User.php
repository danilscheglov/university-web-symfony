<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Этот email уже зарегистрирован')]
#[UniqueEntity(fields: ['username'], message: 'Это имя пользователя уже занято')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Имя пользователя обязательно")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Имя пользователя должно быть не короче {{ limit }} символов",
        maxMessage: "Имя пользователя должно быть не длиннее {{ limit }} символов"
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9_\-]+$/u",
        message: "Имя пользователя может содержать только буквы, цифры, дефисы и подчеркивания"
    )]
    private string $username;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank(message: "Email обязателен")]
    #[Assert\Email(message: "Некорректный формат email")]
    #[Assert\Length(max: 255, maxMessage: "Email не может быть длиннее {{ limit }} символов")]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Пароль обязателен")]
    #[Assert\Length(
        min: 8,
        max: 128,
        minMessage: "Пароль должен быть не короче {{ limit }} символов",
        maxMessage: "Пароль должен быть не длиннее {{ limit }} символов"
    )]
    private string $password;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(
        choices: ['user', 'admin'],
        message: "Недопустимая роль пользователя"
    )]
    private string $role = 'user';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return [$this->role === 'admin' ? 'ROLE_ADMIN' : 'ROLE_USER'];
    }

    public function setRole(string $role): self
    {
        if (!in_array($role, ['user', 'admin'])) {
            throw new \InvalidArgumentException("Invalid role");
        }
        $this->role = $role;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function eraseCredentials(): void {}

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }
}
