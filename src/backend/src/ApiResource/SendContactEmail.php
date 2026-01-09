<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Mutation;
use App\Resolver\SendContactEmailResolver;

#[ApiResource(
    graphQlOperations: [
        new Mutation(
            resolver: SendContactEmailResolver::class,
            args: [
                'subject' => ['type' => 'String!'],
                'message' => ['type' => 'String!']
            ],
            security: "is_granted('dbm_admin', object)",
            name: 'sendContactEmail'
        )
    ]
)]
class SendContactEmail
{
}
