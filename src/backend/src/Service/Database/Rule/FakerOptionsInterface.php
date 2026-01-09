<?php

declare(strict_types=1);

namespace App\Service\Database\Rule;

interface FakerOptionsInterface
{
    public function process(?array $options): array;
}
