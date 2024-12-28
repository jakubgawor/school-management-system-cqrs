<?php

declare(strict_types=1);

namespace App\Modules\User\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class UserVerificationToken
{
    #[Id]
    #[Column(type: Types::GUID)]
    private string $id;

    #[Column(type: Types::GUID)]
    private string $userId;

    #[Column(type: Types::STRING)]
    private string $token;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $expiresAt;

    public function __construct(
        string $id,
        string $userId,
        string $token,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->token = $token;

        $currentTime = new DateTimeImmutable();
        $this->createdAt = $currentTime;
        $this->expiresAt = $currentTime->modify('+1 hour');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
