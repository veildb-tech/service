<?php

declare(strict_types=1);

namespace App\Enums\Security;

enum AuthenticationTypeEnum: string
{
    case USER = 'User';
    case TOKEN = 'Token';
}
