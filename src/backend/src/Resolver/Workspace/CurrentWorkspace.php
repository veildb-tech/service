<?php

namespace App\Resolver\Workspace;

use ApiPlatform\GraphQl\Resolver\QueryItemResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use App\Service\Workspace\GetSelectedWorkspace;

#[ApiResource]
final readonly class CurrentWorkspace implements QueryItemResolverInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private GetSelectedWorkspace $getSelectedWorkspace
    ) {
    }

    /**
     * Retrieve current (selected) user workspace
     *
     * @param object|null $item
     * @param array $context
     * @return object|\App\Entity\Workspace\Workspace
     */
    public function __invoke(?object $item, array $context): object
    {
        $token = $this->tokenStorage->getToken();
        if ($token) {
            return $this->getSelectedWorkspace->execute();
        }

        throw new InvalidTokenException('Invalid JWT Token');
    }
}
