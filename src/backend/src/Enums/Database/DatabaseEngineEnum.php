<?php

declare(strict_types=1);

namespace App\Enums\Database;

use App\Enums\ConfigurableEnumInterface;
use App\Enums\ConfigurableEnumTrait;

enum DatabaseEngineEnum: string implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case MYSQL = 'mysql';
    case MARIADB = 'mariadb';
    case POSTGRES = 'pgsql';
    case SQL_LITE = 'sql_lite';

    public function label(): string
    {
        return match($this) {
            self::MYSQL => 'MySQL',
            self::MARIADB => 'MariaDB',
            self::POSTGRES => 'PostgreSQL',
            self::SQL_LITE => 'SQLite'
        };
    }
}
