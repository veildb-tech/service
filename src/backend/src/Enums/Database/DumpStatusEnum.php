<?php

declare(strict_types=1);

namespace App\Enums\Database;

use App\Enums\ConfigurableEnumTrait;
use App\Enums\ConfigurableEnumInterface;

enum DumpStatusEnum: string implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case SCHEDULED = 'scheduled';
    case PROCESSING = 'processing';
    case ERROR = 'error';
    case READY = 'ready';
    case READY_WITH_ERROR = 'ready_with_error';

    public function label(): string
    {
        return match($this) {
            self::SCHEDULED => 'Scheduled',
            self::PROCESSING => 'Processing',
            self::ERROR => 'Error',
            self::READY => 'Ready',
            self::READY_WITH_ERROR => 'Ready with errors'
        };
    }
}
