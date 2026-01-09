<?php

declare(strict_types=1);

namespace App\Api;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Workspace\Workspace;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class FilterWorkspaceExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * @param Security $security
     */
    public function __construct(
        private Security $security,
    ) {
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param array $identifiers
     * @param Operation|null $operation
     * @param array $context
     * @return void
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        if ($resourceClass === Workspace::class) {
            $this->filterByWorkspace($queryBuilder);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array $context
     * @return void
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        if ($resourceClass === Workspace::class) {
            $this->filterByWorkspace($queryBuilder);
        }
    }

    /**
     * Filter only by available workspaces for user
     *
     * @param $queryBuilder
     * @return void
     */
    private function filterByWorkspace($queryBuilder): void
    {
        $user = $this->security->getUser();
        $userWorkspaces = $user->getWorkspaces();
        $ids = [];
        foreach ($userWorkspaces as $userWorkspace) {
            $ids[] = $userWorkspace->getId();
        }

        $queryBuilder->andWhere(
            sprintf("%s.id IN (%s)", $queryBuilder->getRootAliases()[0], implode(',', $ids))
        );
    }
}
