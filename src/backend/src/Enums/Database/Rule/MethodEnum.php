<?php

declare(strict_types=1);

namespace App\Enums\Database\Rule;

enum MethodEnum: string
{
    case TRUNCATE = 'truncate';
    case UPDATE = 'update';
    case FAKE = 'fake';
    case CUSTOM = 'custom';
}
