<?php

declare(strict_types=1);

namespace App\Modules\Student\Command\Sync;

use App\Modules\Student\Exception\StudentDoesNotExist;
use App\Modules\Student\Repository\StudentRepository;
use App\Shared\Command\Sync\CommandHandler;

final class RemoveStudentClassRoomHandler implements CommandHandler
{
    public function __construct(
        private StudentRepository $studentRepository,
    ) {
    }

    public function __invoke(RemoveStudentClassRoom $command): void
    {
        $student = $this->studentRepository->findStudentById($command->id);
        if (! $student) {
            throw new StudentDoesNotExist();
        }

        $student->setClassRoomId(null);
        $this->studentRepository->save($student);
    }
}
