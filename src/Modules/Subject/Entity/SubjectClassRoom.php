<?php

declare(strict_types=1);

namespace App\Modules\Subject\Entity;

use App\Modules\Subject\Repository\SubjectClassRoomRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: SubjectClassRoomRepository::class)]
class SubjectClassRoom
{
    #[Id]
    #[Column(type: Types::STRING)]
    private string $id;

    #[Column(type: Types::STRING)]
    private string $classRoomId;

    #[Column(type: Types::STRING)]
    private string $subjectId;

    public function __construct(
        string $id,
        string $classRoomId,
        string $subjectId,
    ) {
        $this->id = $id;
        $this->classRoomId = $classRoomId;
        $this->subjectId = $subjectId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClassRoomId(): string
    {
        return $this->classRoomId;
    }

    public function getSubjectId(): string
    {
        return $this->subjectId;
    }
}
