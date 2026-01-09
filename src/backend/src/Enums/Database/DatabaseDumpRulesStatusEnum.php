<?php

declare(strict_types=1);

namespace App\Enums\Database;

use App\Enums\ConfigurableEnumInterface;
use App\Enums\ConfigurableEnumTrait;

enum DatabaseDumpRulesStatusEnum: int implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case ENABLED = 1;
    case DISABLED = 0;

    public function label(): string
    {
        return match($this) {
            self::ENABLED => 'Enabled',
            self::DISABLED => 'Disabled'
        };
    }
}
