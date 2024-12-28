<?php

declare(strict_types=1);

namespace App\Modules\User\Entity;

use App\Modules\User\Repository\UserVerificationTokenRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

#[Entity(repositoryClass: UserVerificationTokenRepository::class)]
class UserVerificationToken
{
    #[Id]
    #[Column(type: Types::GUID)]
    private string $id;

    #[OneToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $user;

    #[Column(type: Types::STRING)]
    private string $token;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $expiresAt;

    public function __construct(
        string $id,
        User $user,
        string $token,
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->token = $token;

        $currentTime = new DateTimeImmutable();
        $this->createdAt = $currentTime;
        $this->expiresAt = $currentTime->modify('+1 hour');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
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

    public function isExpired(): bool
    {
        return $this->expiresAt < new DateTimeImmutable();
    }
}
