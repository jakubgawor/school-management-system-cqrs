<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Command\Sync;

use App\Shared\Command\Sync\Command;

final readonly class EditAnnouncement implements Command
{
    public function __construct(
        public string $id,
        public string $title,
        public string $message,
    ) {
    }
}
