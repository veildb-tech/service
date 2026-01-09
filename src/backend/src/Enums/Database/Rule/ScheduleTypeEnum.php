<?php

declare(strict_types=1);

namespace App\Enums\Database\Rule;

use App\Enums\ConfigurableEnumTrait;
enum ScheduleTypeEnum: int
{
    use ConfigurableEnumTrait;

    case MANUALLY = 1;
    case SCHEDULE = 2;

    public function label(): string
    {
        return match ($this) {
            self::MANUALLY => "Manually",
            self::SCHEDULE => "Schedule"
        };
    }
}
