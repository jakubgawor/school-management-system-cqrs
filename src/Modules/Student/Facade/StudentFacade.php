<?php

declare(strict_types=1);

namespace App\Modules\Student\Facade;

use App\Modules\Student\Entity\Student;
use App\Modules\Student\Exception\StudentDoesNotExist;
use App\Modules\Student\Repository\StudentRepository;
use JetBrains\PhpStorm\ArrayShape;

final class StudentFacade
{
    public function __construct(
        private StudentRepository $studentRepository
    ) {
    }

    #[ArrayShape([
        'userId' => 'studentId',
    ])]
    public function findStudentIdsByUserIds(array $userIds): array
    {
        $map = [];
        foreach ($this->studentRepository->findByUserIds($userIds) as $student) {
            $map[$student->getUserId()] = $student->getId();
        }

        return $map;
    }

    public function findStudentByIdOrFail(string $id): Student
    {
        $student = $this->studentRepository->findStudentById($id);

        if (! $student) {
            throw new StudentDoesNotExist();
        }

        return $student;
    }
}
