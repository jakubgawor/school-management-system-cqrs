<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\Sync;

use App\Modules\ClassRoom\Event\ClassRoomRemoved;
use App\Modules\ClassRoom\Exception\ClassRoomDoesNotExist;
use App\Modules\ClassRoom\Repository\ClassRoomRepository;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RemoveClassRoomHandler implements CommandHandler
{
    public function __construct(
        private ClassRoomRepository $classRoomRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(RemoveClassRoom $command): void
    {
        $classRoom = $this->classRoomRepository->findById($command->id);

        if (! $classRoom) {
            throw new ClassRoomDoesNotExist();
        }

        $this->classRoomRepository->remove($classRoom);

        $this->eventDispatcher->dispatch(new ClassRoomRemoved($command->id));
    }
}
