<?php

declare(strict_types=1);

namespace App\Enums\Webhook;

use App\Enums\ConfigurableEnumInterface;
use App\Enums\ConfigurableEnumTrait;

enum WebhookOperationEnum: string implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case CREATE_DB_DUMP = 'create_db_dump';

    public function label(): string
    {
        return match($this) {
            self::CREATE_DB_DUMP => 'Create Database Dump',
        };
    }
}
