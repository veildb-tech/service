<?php

declare(strict_types=1);

namespace App\Api\Workspace;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Service\Workspace\GetSelectedWorkspace;
use App\Entity\Workspace\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class FilterUsersByWorkspaceExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param Security $security
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace,
        private Security $security
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
     * @throws \Exception
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        if ($resourceClass === User::class) {
            $this->addWorkspaceFilter($queryBuilder, $resourceClass);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        if ($resourceClass === User::class) {
            $this->addWorkspaceFilter($queryBuilder, $resourceClass);
        }
    }

    /**
     * Add filtering by Workspace ID's
     *
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     *
     * @return void
     * @throws \Exception
     */
    private function addWorkspaceFilter(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $workspace = $this->getSelectedWorkspace->execute();
        $workspacesToFilter = [];
        if ($workspace) {
            $workspacesToFilter = [$workspace->getId()];
        } else {
            $workspaces = $this->security->getUser()->getWorkspaces();
            foreach ($workspaces as $workspace) {
                $workspacesToFilter[] = $workspace->getId();
            }
        }

        if (empty($workspacesToFilter)) {
            throw new \Exception("Something went wrong. User is not assigned to any workspace");
        }

        $queryBuilder->join(sprintf("%s.workspaces", $queryBuilder->getRootAliases()[0]), 'w')
            ->andWhere($queryBuilder->expr()->in('w.id', $workspacesToFilter));

    }
}
