<?php

declare(strict_types=1);

namespace App\Enums\Database\Rule;

use App\Enums\ConfigurableEnumInterface;
use App\Enums\ConfigurableEnumTrait;

enum OperatorEnum: string implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case EQUAL = 'eq';
    case NOT_EQUAL = 'neq';
    case LIKE = 'like';
    case NOT_LIKE = 'not-like';
    case IN = 'in';
    case NOT_IN = 'nin';
    case GREATER_THAN = 'gt';
    case GREATER_THAN_EQUAL = 'gte';
    case LESS_THAN = 'lt';
    case LESS_THAN_EQUAL = 'lte';
    case IS_NULL = 'null';
    case IS_NOT_NULL = 'not-null';

    public function label(): string
    {
        return match($this) {
            self::EQUAL => 'Equal',
            self::NOT_EQUAL => 'Not Equal',
            self::LIKE => 'LIKE',
            self::NOT_LIKE => 'NOT_LIKE',
            self::IN => 'IN',
            self::NOT_IN => 'NOT IN',
            self::GREATER_THAN => 'Greater Than',
            self::GREATER_THAN_EQUAL => 'Greater Than or Equal',
            self::LESS_THAN => 'Less Than',
            self::LESS_THAN_EQUAL => 'Less Than or Equal',
            self::IS_NULL => 'IS NULL',
            self::IS_NOT_NULL => 'IS NOT NULL',
        };
    }

    /**
     * @param string $field
     * @param string|null $value
     * @return string
     */
    public function getSqlCondition(string $field, string $value = null): string
    {
        return match($this) {
            self::EQUAL => sprintf("%s = '%s'", $field, $value),
            self::NOT_EQUAL => sprintf("%s != '%s'", $field, $value),
            self::LIKE => sprintf("%s LIKE '%s'", $field, $value),
            self::NOT_LIKE => sprintf("%s NOT LIKE '%s'", $field, $value),
            self::IN => sprintf("%s IN (%s)", $field, $value),
            self::NOT_IN => sprintf("%s NOT IN (%s)", $field, $value),
            self::GREATER_THAN => sprintf("%s > '%s'", $field, $value),
            self::GREATER_THAN_EQUAL => sprintf("%s >= '%s'", $field, $value),
            self::LESS_THAN => sprintf("%s < '%s'", $field, $value),
            self::LESS_THAN_EQUAL => sprintf("%s <= '%s'", $field, $value),
            self::IS_NULL => sprintf("%s IS NULL", $field),
            self::IS_NOT_NULL => sprintf("%s IS NOT NULL", $field),
        };
    }
}
