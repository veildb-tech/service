<?php

declare(strict_types=1);

namespace App\Enums\Database\Rule;

enum SuggestionStatusEnum: int
{
    case DISABLED = 0;
    case ACTIVE = 1;
    case UPDATED = 2;

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::DISABLED => 'Disabled',
            self::UPDATED => 'Updated'
        };
    }
}
