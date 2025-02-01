<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Request\V1;

use App\Modules\Announcement\Command\Sync\EditAnnouncement as Command;
use App\Shared\Request\RequestInterface;

final class EditAnnouncement implements RequestInterface
{
    public function __construct(
        public mixed $id,
        public readonly mixed $title,
        public readonly mixed $message,
    ) {
    }

    public function toCommand(): Command
    {
        return new Command(
            $this->id,
            $this->title,
            $this->message,
        );
    }
}
