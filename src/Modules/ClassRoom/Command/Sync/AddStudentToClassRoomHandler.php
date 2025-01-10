<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\Sync;

use App\Modules\ClassRoom\Event\StudentAddedToClassRoom;
use App\Modules\ClassRoom\Exception\ClassRoomDoesNotExist;
use App\Modules\ClassRoom\Exception\ClassRoomOverflow;
use App\Modules\ClassRoom\Repository\ClassRoomRepository;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AddStudentToClassRoomHandler implements CommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ClassRoomRepository $classRoomRepository,
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

        $this->eventDispatcher->dispatch(new StudentAddedToClassRoom($command->classRoomId, $command->studentId));
    }
}
