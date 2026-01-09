<?php

declare(strict_types=1);

namespace App\Service\Database\Rule\FakerOptions;

use App\Service\Database\Rule\FakerOptionsInterface;

class DefaultFaker implements FakerOptionsInterface
{

    /**
     * Nothing do here
     *
     * @param array|null $options
     * @return array
     */
    public function process(?array $options): array
    {
        return $options ?? [];
    }
}
