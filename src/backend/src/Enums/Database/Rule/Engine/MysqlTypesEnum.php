<?php

declare(strict_types=1);

namespace App\Enums\Database\Rule\Engine;

enum MysqlTypesEnum: string
{
    case VARCHAR = 'varchar';
    case LONGTEXT = 'longtext';
    case TINYTEXT = 'tinytext';
    case STRING = 'string';
    case TEXT = 'text';
    case INTEGER = 'integer';

    case DECIMAL = 'decimal';
    case FLOAT = 'float';
    case SMALLINT = 'smallint';
    case BIGINT = 'bigint';
    case DATETIME = 'datetime';
    case DATE = 'date';

    /**
     * @return array
     */
    public static function getStringTypes():array
    {
        return [
            self::STRING->value,
            self::TEXT->value,
            self::VARCHAR->value,
            self::LONGTEXT->value,
            self::TINYTEXT->value
        ];
    }

    /**
     * Retrieves an array of numeric types.
     *
     * @return array An array containing the numeric types.
     */
    public static function getNumericTypes(): array
    {
        return [
            self::INTEGER->value,
            self::DECIMAL->value,
            self::BIGINT->value,
            self::SMALLINT->value,
            self::FLOAT->value
        ];
    }

    /**
     * Retrieves an array of available date types.
     *
     * The method returns an array containing the available date types.
     * The date types include DATETIME and DATE.
     *
     * @return array An array of available date types.
     */
    public static function getDateTypes(): array
    {
        return [self::DATETIME->value, self::DATE->value];
    }
}
