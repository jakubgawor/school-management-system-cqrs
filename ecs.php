<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSets([
        SetList::SPACES,
        SetList::ARRAY,
        SetList::PSR_12,
        SetList::CLEAN_CODE,
        SetList::COMMON,
        SetList::STRICT,
        SetList::PHPUNIT,
    ]);