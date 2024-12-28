<?php

declare(strict_types=1);

namespace App\Modules\User\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class User
{
    #[Id]
    #[Column(type: Types::GUID)]
    private string $id;

    #[Column(type: Types::STRING, length: 64)]
    private string $username;

    #[Column(type: Types::STRING, length: 255)]
    private string $password;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[Column(type: Types::BOOLEAN)]
    private bool $isVerified;

    public function __construct(
        string $id,
        string $username,
        string $password,
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->createdAt = new DateTimeImmutable();
        $this->isVerified = false;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }
}
