<?php

declare(strict_types=1);

namespace App\Modules\Subject\Entity;

use App\Modules\Subject\Repository\SubjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[Id]
    #[Column(type: Types::STRING)]
    private string $id;

    #[Column(type: Types::STRING)]
    private string $teacherId;

    #[Column(type: Types::STRING, length: 255)]
    private string $name;

    #[Column(type: Types::STRING, length: 4096, nullable: true)]
    private ?string $description;

    public function __construct(
        string $id,
        string $teacherId,
        string $name,
        ?string $description = null,
    ) {
        $this->id = $id;
        $this->teacherId = $teacherId;
        $this->name = $name;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTeacherId(): string
    {
        return $this->teacherId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
