<?php

declare(strict_types=1);

namespace App\Modules\Student\Command\ASync;

use App\Modules\ClassRoom\Command\ASync\ClassRoomRemoved;
use App\Modules\Student\Entity\Student;
use App\Modules\Student\Repository\StudentRepository;
use App\Shared\Command\Async\CommandHandler;

final class ClassRoomRemovedHandler implements CommandHandler
{
    public function __construct(
        private StudentRepository $studentRepository,
    ) {
    }

    public function __invoke(ClassRoomRemoved $command): void
    {
        $students = $this->studentRepository->findStudentAssignedToClassRoom($command->classRoomId);

        /** @var Student $student */
        foreach ($students as $student) {
            $student->setClassRoomId(null);
            $this->studentRepository->save($student);
        }
    }
}
