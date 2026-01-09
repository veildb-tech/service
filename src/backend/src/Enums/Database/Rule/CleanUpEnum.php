<?php

declare(strict_types=1);

namespace App\Enums\Database\Rule;

use App\Enums\ConfigurableEnumInterface;
use App\Enums\ConfigurableEnumTrait;

enum CleanUpEnum: string implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case EVERY_24 = 'PT24H';
    case EVERY_3_DAYS = 'P3D';

    public function label(): string
    {
        return match($this) {
            self::EVERY_24 => 'Older than 24hrs',
            self::EVERY_3_DAYS => 'Older than 3 days',
        };
    }
}
