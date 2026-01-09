<?php

declare(strict_types=1);

namespace App\Resolver\Workspace\User;

use ApiPlatform\GraphQl\Resolver\QueryItemResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[ApiResource]
final readonly class CurrentUserResolver implements QueryItemResolverInterface
{

    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function __invoke(?object $item, array $context): object
    {
        $token = $this->tokenStorage->getToken();
        if ($token) {
            return $token->getUser();
        }

        throw new InvalidTokenException('Invalid JWT Token');
    }
}
