<?php

declare(strict_types=1);

namespace App\Shared\Command\Async;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
