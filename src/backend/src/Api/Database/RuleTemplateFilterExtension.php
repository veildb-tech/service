<?php

declare(strict_types=1);

namespace App\Api\Database;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Database\DatabaseRuleTemplate;
use App\Enums\Database\Rule\TemplateTypeEnum;
use Doctrine\ORM\QueryBuilder;
use App\Service\Workspace\GetSelectedWorkspace;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class RuleTemplateFilterExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
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
        if ($resourceClass === DatabaseRuleTemplate::class) {
            $this->addTypeAndWorkspaceFilter($queryBuilder, $resourceClass);
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
        if ($resourceClass === DatabaseRuleTemplate::class) {
            $this->addTypeAndWorkspaceFilter($queryBuilder, $resourceClass);
        }
    }

    /**
     * Add filtering by Workspace ID's and type
     *
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     *
     * @return void
     * @throws \Exception
     */
    private function addTypeAndWorkspaceFilter(QueryBuilder $queryBuilder, string $resourceClass): void
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
        if (count($workspacesToFilter)) {
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq(sprintf('%s.type', $alias), TemplateTypeEnum::SYSTEM->value),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq(sprintf('%s.type', $alias), TemplateTypeEnum::CUSTOM->value),
                        $queryBuilder->expr()->in(sprintf('%s.workspace', $alias), $workspacesToFilter)
                    )
                )
            );
        } else {
            throw new \Exception("Something went wrong. User is not assigned to any workspace");
        }
    }
}
