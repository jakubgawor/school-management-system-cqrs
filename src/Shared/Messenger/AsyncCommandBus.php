<?php

declare(strict_types=1);

namespace App\Shared\Messenger;

use App\Shared\Command\Async\Command;
use App\Shared\Command\Async\CommandBus;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class AsyncCommandBus implements CommandBus
{
    public function __construct(
        private MessagebusInterface $commandAsyncBus,
    ) {
    }

    public function dispatch(Command $command): void
    {
        try {
            $this->commandAsyncBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious() ?? $exception;
        }
    }
}
