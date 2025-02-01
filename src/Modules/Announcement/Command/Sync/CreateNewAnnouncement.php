<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Command\Sync;

use App\Shared\Command\Sync\Command;

final class CreateNewAnnouncement implements Command
{
    public function __construct(
        public string $title,
        public string $message,
    ) {
    }
}
