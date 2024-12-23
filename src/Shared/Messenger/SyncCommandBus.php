<?php

declare(strict_types=1);

namespace App\Shared\Messenger;

use App\Shared\Command\Sync\Command;
use App\Shared\Command\Sync\CommandBus;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class SyncCommandBus implements CommandBus
{
    public function __construct(
        private MessagebusInterface $commandSyncBus,
    ) {
    }

    public function dispatch(Command $command): void
    {
        try {
            $this->commandSyncBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious() ?? $exception;
        }
    }
}
