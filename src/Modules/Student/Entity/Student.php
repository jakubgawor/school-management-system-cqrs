<?php

declare(strict_types=1);

namespace App\Modules\Student\Entity;

use App\Modules\Student\Repository\StudentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[Id]
    #[Column(type: Types::STRING)]
    private string $id;

    #[Column(type: Types::STRING)]
    private string $userId;

    #[Column(type: Types::STRING)]
    private ?string $classRoomId;

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

    public function getClassRoomId(): ?string
    {
        return $this->classRoomId;
    }

    public function setClassRoomId(?string $classRoomId): void
    {
        $this->classRoomId = $classRoomId;
    }
}
