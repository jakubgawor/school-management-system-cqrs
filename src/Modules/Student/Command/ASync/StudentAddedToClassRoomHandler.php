<?php

declare(strict_types=1);

namespace App\Modules\Student\Command\ASync;

use App\Modules\ClassRoom\Command\ASync\StudentAddedToClassRoom;
use App\Modules\Student\Exception\StudentDoesNotExist;
use App\Modules\Student\Repository\StudentRepository;
use App\Shared\Command\Async\CommandHandler;

final class StudentAddedToClassRoomHandler implements CommandHandler
{
    public function __construct(
        private StudentRepository $studentRepository,
    ) {
    }

    public function __invoke(StudentAddedToClassRoom $command): void
    {
        $student = $this->studentRepository->findStudentById($command->studentId);
        if (! $student) {
            throw new StudentDoesNotExist();
        }

        $student->setClassRoomId($command->classRoomId);
        $this->studentRepository->save($student);
    }
}
