<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\Sync;

use App\Modules\ClassRoom\Exception\ClassRoomAlreadyExists;
use App\Modules\ClassRoom\Exception\ClassRoomDoesNotExist;
use App\Modules\ClassRoom\Repository\ClassRoomRepository;
use App\Shared\Command\Sync\CommandHandler;
use DateTimeImmutable;

final class EditClassRoomHandler implements CommandHandler
{
    public function __construct(
        private ClassRoomRepository $classRoomRepository,
    ) {
    }

    public function __invoke(EditClassRoom $command): void
    {
        $classRoom = $this->classRoomRepository->findById($command->id);

        if (! $classRoom) {
            throw new ClassRoomDoesNotExist();
        }

        if ($this->classRoomRepository->findByName($command->name)) {
            throw new ClassRoomAlreadyExists();
        }

        $classRoom->setName($command->name);
        $classRoom->setUpdatedAt(new DateTimeImmutable());

        $this->classRoomRepository->save($classRoom);
    }
}
