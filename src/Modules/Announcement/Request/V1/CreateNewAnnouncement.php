<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Request\V1;

use App\Modules\Announcement\Command\Sync\CreateNewAnnouncement as Command;
use App\Shared\Request\RequestInterface;

final readonly class CreateNewAnnouncement implements RequestInterface
{
    public function __construct(
        public mixed $title,
        public mixed $message,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->title,
            $this->message,
        );
    }
}
