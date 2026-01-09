<?php

declare(strict_types=1);

namespace App\Enums\Database;

use App\Enums\ConfigurableEnumInterface;
use App\Enums\ConfigurableEnumTrait;
enum DumpLogsStatusEnum: string implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case SUCCESS = 'success';
    case ERROR = 'error';
    case PROCESSING = 'processing';

    public function label(): string
    {
        return match($this) {
            self::SUCCESS => 'Success',
            self::ERROR => 'Error',
            self::PROCESSING => 'Processing'
        };
    }
}
