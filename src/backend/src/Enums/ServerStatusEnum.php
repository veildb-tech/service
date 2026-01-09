<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\ConfigurableEnumTrait;
use App\Enums\ConfigurableEnumInterface;

enum ServerStatusEnum: string implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case ENABLED = 'enabled';
    case DISABLED = 'disabled';
    case PENDING = 'pending';
    case OFFLINE = 'offline';

    public function label(): string
    {
        return match($this) {
            self::ENABLED => 'Enabled',
            self::DISABLED => 'Disabled',
            self::PENDING => 'Pending',
            self::OFFLINE => 'Offline'
        };
    }
}
