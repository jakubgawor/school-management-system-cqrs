<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\Sync;

use App\Modules\ClassRoom\Entity\ClassRoom;
use App\Modules\ClassRoom\Exception\ClassRoomAlreadyExists;
use App\Modules\ClassRoom\Repository\ClassRoomRepository;
use App\Shared\Command\Sync\CommandHandler;
use App\Shared\Ramsey\IdGenerator;

final class CreateClassRoomHandler implements CommandHandler
{
    public function __construct(
        private ClassRoomRepository $classRoomRepository,
    ) {
    }

    public function __invoke(CreateClassRoom $command): void
    {
        if ($this->classRoomRepository->findByName($command->name)) {
            throw new ClassRoomAlreadyExists();
        }

        $classRoom = new ClassRoom(
            IdGenerator::generate(),
            $command->name,
        );

        $this->classRoomRepository->save($classRoom);
    }
}