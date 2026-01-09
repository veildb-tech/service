<?php

declare(strict_types=1);

namespace App\Service\Database\Rule\FakerOptions;

use App\Service\Database\Rule\FakerOptionsInterface;

class NumberBetween implements FakerOptionsInterface
{
    /**
     * @var array|string[]
     */
    private array $allowedOption = [ 'int1', 'int2' ];

    /**
     * Remove not valid options
     *
     * @param array|null $options
     * @return array
     */
    public function process(?array $options): array
    {
        foreach ($options as $key => $value) {
            if (!in_array($key, $this->allowedOption) || !is_numeric($value)) {
                unset($options[$key]);
            }
        }

        return $options;
    }
}
