<?php

declare(strict_types=1);

namespace App\Api;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Database\Database;
use Doctrine\ORM\QueryBuilder;
use App\Service\Workspace\GetSelectedWorkspace;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class FilterByWorkspaceExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * $entityToFilter - entity which has direct access (property) to workspace.
     * For example Database has property workspace
     *
     * $subEntityToFilter - entity which has access to workspace from parent entity.
     * For example entity DatabaseRule has Database property which has property Workspace
     *
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param Security $security
     * @param array $entityToFilter
     * @param array $subEntityToFilter
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace,
        private Security $security,
        private array $entityToFilter = [],
        private array $subEntityToFilter = [],
    ) {
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->addWorkspaceFilter($queryBuilder, $resourceClass);
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->addWorkspaceFilter($queryBuilder, $resourceClass);
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
        if (in_array($resourceClass, $this->entityToFilter) || in_array($resourceClass, $this->subEntityToFilter)) {
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
            if (count($workspacesToFilter)) {
                if (in_array($resourceClass, $this->subEntityToFilter)) {
                    $queryBuilder->join(
                        Database::class,
                        'd',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'd.id = o.db'
                    )
                        ->andWhere(
                            sprintf("d.workspace IN (%s)", implode(',', $workspacesToFilter))
                        );
                } else {
                    $queryBuilder->andWhere(
                        sprintf("%s.workspace IN (%s)", $queryBuilder->getRootAliases()[0], implode(',', $workspacesToFilter))
                    );
                }
            } else {
                throw new \Exception("Something went wrong. User is not assigned to any workspace");
            }
        }
    }
}
