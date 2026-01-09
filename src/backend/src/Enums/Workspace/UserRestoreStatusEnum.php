<?php

declare(strict_types=1);

namespace App\Enums\Workspace;

enum UserRestoreStatusEnum: string
{
    case PENDING = 'pending';
    case USED = 'used';
}
