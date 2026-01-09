<?php

declare(strict_types=1);

namespace App\Enums\Database\Rule;

enum TemplateTypeEnum: int
{
    case SYSTEM = 1;
    case CUSTOM = 2;
}
