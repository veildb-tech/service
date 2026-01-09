<?php

declare(strict_types=1);

namespace App\Enums\Workspace;

enum UserInvitationStatusEnum: string
{
    case PENDING = 'pending';
    case EXPIRED = 'expired';
    case ACCEPTED = 'accepted';
    case DECLINED = 'canceled';
}
