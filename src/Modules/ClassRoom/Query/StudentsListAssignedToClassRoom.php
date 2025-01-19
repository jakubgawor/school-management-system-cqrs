<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Query;

use App\Modules\ClassRoom\Query\DTO\StudentAssignedToClassInfo;
use App\Modules\ClassRoom\Repository\ClassRoomRepository;

final class StudentsListAssignedToClassRoom
{
    public function __construct(
        private ClassRoomRepository $classRoomRepository,
    ) {
    }

    public function execute(string $classRoomId): array
    {
        $students = $this->classRoomRepository->getStudentsInfoAssignedToClassRoom($classRoomId);

        $data = [];
        foreach ($students as $student) {
            $data[] = new StudentAssignedToClassInfo(
                $student['id'],
                $student['firstName'],
                $student['lastName'],
                $student['email'],
            );
        }

        return $data;
    }
}
