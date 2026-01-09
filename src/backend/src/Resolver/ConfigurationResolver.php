<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\QueryItemResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\ApiResource\Configuration;

#[ApiResource]
final class ConfigurationResolver implements QueryItemResolverInterface
{
    public function __invoke(?object $item, array $context): object
    {
        return new Configuration();
    }
}
