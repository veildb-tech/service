<?php

declare(strict_types=1);

namespace App\Enums\Database;

use App\Enums\ConfigurableEnumInterface;
use App\Enums\ConfigurableEnumTrait;

enum DatabasePlatformEnum: string implements ConfigurableEnumInterface
{
    use ConfigurableEnumTrait;

    case MAGENTO = 'magento';
    case WORDPRESS = 'wordpress';
    case SHOPWARE = 'shopware';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match($this) {
            self::MAGENTO => 'Magento',
            self::WORDPRESS => 'WordPress',
            self::SHOPWARE => 'Shopware',
            self::CUSTOM => 'Custom'
        };
    }
}
