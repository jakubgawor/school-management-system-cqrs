<?php

declare(strict_types=1);

namespace App\Shared\Command\Sync;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
