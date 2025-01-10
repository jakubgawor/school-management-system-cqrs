<?php

declare(strict_types=1);

namespace App\Modules\ClassRoom\Command\Sync;

use App\Modules\ClassRoom\Event\StudentAddedToClassRoom;
use App\Shared\Command\Sync\CommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AddStudentToClassRoomHandler implements CommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(AddStudentToClassRoom $command): void
    {
        $this->eventDispatcher->dispatch(new StudentAddedToClassRoom($command->classRoomId, $command->studentId));
    }
}
