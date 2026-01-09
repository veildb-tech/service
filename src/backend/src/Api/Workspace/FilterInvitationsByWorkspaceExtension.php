<?php

declare(strict_types=1);

namespace App\Api\Workspace;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Workspace\UserInvitation;
use App\Service\Workspace\GetSelectedWorkspace;
use Doctrine\ORM\QueryBuilder;

final readonly class FilterInvitationsByWorkspaceExtension implements QueryCollectionExtensionInterface
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace
    ) {
    }

    /**
     * Workaround to filter groups by workspace. When user on edit uses page groups should retrieve individually
     * To selected workspace. However, collection for groups should works also in case when user registers
     *
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
        $workspace = $this->getSelectedWorkspace->execute();
        if ($resourceClass === UserInvitation::class && $workspace) {
            $queryBuilder->andWhere(
                sprintf("%s.workspace = %s", $queryBuilder->getRootAliases()[0], $workspace->getId())
            );
        }
    }
}
