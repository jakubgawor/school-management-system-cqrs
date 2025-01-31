<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\Sync;

use App\Modules\ClassRoom\Command\ASync\ClassRoomRemoved;
use App\Modules\ClassRoom\Exception\ClassRoomDoesNotExist;
use App\Modules\ClassRoom\Repository\ClassRoomRepository;
use App\Shared\Command\Async\CommandBus as ASyncCommandBus;
use App\Shared\Command\Sync\CommandHandler;

final class RemoveClassRoomHandler implements CommandHandler
{
    public function __construct(
        private ClassRoomRepository $classRoomRepository,
        private ASyncCommandBus $asyncCommandBus,
    ) {
    }

    public function __invoke(RemoveClassRoom $command): void
    {
        $classRoom = $this->classRoomRepository->findById($command->id);

        if (! $classRoom) {
            throw new ClassRoomDoesNotExist();
        }

        $this->classRoomRepository->remove($classRoom);

        $this->asyncCommandBus->dispatch(new ClassRoomRemoved($command->id));
    }
}
