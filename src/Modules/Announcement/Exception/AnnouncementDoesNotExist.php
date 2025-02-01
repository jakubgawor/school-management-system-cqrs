<?php

declare(strict_types=1);

namespace App\Modules\Announcement\Exception;

use App\Shared\Exception\BaseException;

final class AnnouncementDoesNotExist extends BaseException
{
    public function __construct()
    {
        parent::__construct('Announcement does not exist.');
    }

    public function getValidationKey(): string
    {
        return 'VALIDATION.ANNOUNCEMENT_DOES_NOT_EXIST';
    }
}
