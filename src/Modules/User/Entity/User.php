<?php

declare(strict_types=1);

namespace App\Modules\User\Entity;

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

    public function __construct(
        string $id,
        string $username,
        string $password,
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
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
}
