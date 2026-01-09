<?php

declare(strict_types=1);

namespace App\Enums;

trait ConfigurableEnumTrait
{
    /**
     * @return array
     */
    public static function getValues(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * @return array
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[] = [
                'value' => $case->value,
                'label' => $case->label()
            ];
        }

        return $options;
    }
}
