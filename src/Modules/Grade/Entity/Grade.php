<?php

declare(strict_types=1);

namespace App\Modules\Grade\Entity;

use App\Modules\Grade\Enum\GradeValue;
use App\Modules\Grade\Repository\GradeRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity(repositoryClass: GradeRepository::class)]
class Grade
{
    #[Id]
    #[Column(type: Types::GUID)]
    private string $id;

    #[Column(type: Types::STRING)]
    private string $teacherId;

    #[Column(type: Types::STRING)]
    private string $studentId;

    #[Column(type: Types::STRING)]
    private string $subjectId;

    #[Column(enumType: GradeValue::class)]
    private GradeValue $grade;

    #[Column(type: Types::INTEGER)]
    private int $weight;

    #[Column(type: Types::STRING)]
    private string $description;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $teacherId,
        string $studentId,
        string $subjectId,
        GradeValue $grade,
        int $weight,
        string $description,
    ) {
        $this->id = $id;
        $this->teacherId = $teacherId;
        $this->studentId = $studentId;
        $this->subjectId = $subjectId;
        $this->grade = $grade;
        $this->weight = $weight;
        $this->description = $description;

        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTeacherId(): string
    {
        return $this->teacherId;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
    }

    public function getSubjectId(): string
    {
        return $this->subjectId;
    }

    public function getGrade(): GradeValue
    {
        return $this->grade;
    }

    public function setGrade(GradeValue $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
