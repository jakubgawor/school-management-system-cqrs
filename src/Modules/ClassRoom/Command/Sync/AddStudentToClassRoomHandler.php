<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\Sync;

use App\Modules\ClassRoom\Command\ASync\StudentAddedToClassRoom;
use App\Modules\ClassRoom\Exception\ClassRoomDoesNotExist;
use App\Modules\ClassRoom\Exception\ClassRoomOverflow;
use App\Modules\ClassRoom\Repository\ClassRoomRepository;
use App\Shared\Command\Async\CommandBus as ASyncCommandBus;
use App\Shared\Command\Sync\CommandHandler;

final class AddStudentToClassRoomHandler implements CommandHandler
{
    public function __construct(
        private ClassRoomRepository $classRoomRepository,
        private ASyncCommandBus $asyncCommandBus,
    ) {
    }

    public function __invoke(AddStudentToClassRoom $command): void
    {
        $classRoom = $this->classRoomRepository->findById($command->classRoomId);
        if (! $classRoom) {
            throw new ClassRoomDoesNotExist();
        }

        if ($this->classRoomRepository->countStudentsInClassRoom($command->classRoomId) >= 30) {
            throw new ClassRoomOverflow();
        }

        $this->asyncCommandBus->dispatch(new StudentAddedToClassRoom($command->classRoomId, $command->studentId));
    }
}
