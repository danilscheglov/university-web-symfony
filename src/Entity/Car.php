<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[ORM\Table(name: 'cars')]
#[ORM\HasLifecycleCallbacks]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "Марка автомобиля обязательна")]
    #[Assert\Length(
        max: 50,
        maxMessage: "Марка автомобиля должна быть не длиннее {{ limit }} символов"
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9\s\-]+$/u",
        message: "Марка автомобиля может содержать только буквы, цифры, пробелы и дефисы"
    )]
    private string $brand;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "Модель автомобиля обязательна")]
    #[Assert\Length(
        max: 50,
        maxMessage: "Модель автомобиля должна быть не длиннее {{ limit }} символов"
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}0-9\s\-\.]+$/u",
        message: "Модель автомобиля может содержать только буквы, цифры, пробелы, точки и дефисы"
    )]
    private string $model;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "Год выпуска обязателен")]
    #[Assert\Range(
        min: 1900,
        max: 2026,
        notInRangeMessage: "Год выпуска должен быть между {{ min }} и {{ max }}"
    )]
    private int $year;

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\NotBlank(message: "Цвет автомобиля обязателен")]
    private string $color;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false)]
    private User $owner;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User|UserInterface $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    public static function getColorGroups(): array
    {
        return [
            'Основные цвета' => ['Белый', 'Чёрный', 'Серый', 'Красный', 'Синий', 'Зелёный'],
            'Металлики' => ['Серебристый', 'Золотистый', 'Графитовый', 'Бронзовый', 'Платиновый'],
            'Пастельные тона' => ['Голубой', 'Розовый', 'Мятный', 'Лавандовый', 'Персиковый'],
            'Эксклюзивные цвета' => ['Хамелеон', 'Карбоновый', 'Жемчужный', 'Ультрамарин', 'Коралловый'],
            'Двухцветные комбинации' => ['Чёрно-белый', 'Красно-чёрный', 'Сине-серебристый', 'Оранжево-графитовый']
        ];
    }
}
