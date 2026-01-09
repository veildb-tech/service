<?php
declare(strict_types=1);

namespace App\Enums\Database;

use App\Enums\ConfigurableEnumInterface;
use App\Enums\ConfigurableEnumTrait;

enum DatabaseStatusEnum: string implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case ENABLED = 'enabled';
    case DISABLED = 'disabled';
    case PENDING = 'pending';

    public function label(): string
    {
        return match($this) {
            self::ENABLED => 'Enabled',
            self::DISABLED => 'Disabled',
            self::PENDING => 'Pending'
        };
    }
}
