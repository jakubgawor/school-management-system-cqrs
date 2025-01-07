<?php

declare(strict_types=1);

namespace App\Modules\Student\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class Student
{
    #[Id]
    #[Column(type: Types::STRING)]
    private string $id;

    #[Column(type: Types::STRING)]
    private string $userId;

    public function __construct(
        string $id,
        string $userId,
    ) {
        $this->id = $id;
        $this->userId = $userId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}