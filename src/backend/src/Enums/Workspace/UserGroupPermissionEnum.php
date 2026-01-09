<?php

declare(strict_types=1);

namespace App\Enums\Workspace;

use App\Enums\ConfigurableEnumInterface;
use App\Enums\ConfigurableEnumTrait;

enum UserGroupPermissionEnum: int
{
    use ConfigurableEnumTrait;

    case OWNER = 1;
    case ADMIN = 2;
    case READ = 3;
    case EDIT = 4;
    case READ_ALL = 5;
    case EDIT_ALL = 6;

    public function label(): string
    {
        return match($this) {
            self::OWNER => 'Owner',
            self::ADMIN => 'Admin',
            self::READ => 'Read',
            self::EDIT => 'Edit',
            self::READ_ALL => 'Read all',
            self::EDIT_ALL => 'Edit all'
        };
    }
}
