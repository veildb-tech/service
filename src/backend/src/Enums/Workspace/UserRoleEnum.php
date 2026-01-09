<?php

declare(strict_types=1);

namespace App\Enums\Workspace;

enum UserRoleEnum: string
{
    case DBM_EDIT = 'dbm_edit';
    case DBM_READ = 'dbm_read';
    case DBM_ADMIN = 'dbm_admin';
    case DBM_OWNER = 'dbm_owner';
}
