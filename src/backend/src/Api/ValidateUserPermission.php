<?php

declare(strict_types=1);

namespace App\Api;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use App\Security\ValidatePermissions;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Database\Database;
use App\Entity\Database\DatabaseRule;
use App\Entity\Database\DatabaseDump;
use App\Entity\Database\DatabaseDumpLogs;
use App\Service\Workspace\GetSelectedWorkspace;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class ValidateUserPermission implements QueryCollectionExtensionInterface
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param ValidatePermissions $validatePermissions
     * @param Security $security
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace,
        private ValidatePermissions $validatePermissions,
        private Security $security
    ) {
    }

    /**
     * Filter entities which related to database according to permission rules
     *
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
        if (in_array($resourceClass, [
            Database::class,
            DatabaseRule::class,
            DatabaseDump::class,
            DatabaseDumpLogs::class,
        ])) {
            $this->validatePermissions($queryBuilder, $resourceClass);
        }
    }

    /**
     * Add filtering by ID's
     *
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     *
     * @return void
     */
    private function validatePermissions(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $user = $this->security->getUser();
        $workspace = $this->getSelectedWorkspace->execute();
        $dbsToFilter = $this->validatePermissions->getPermittedDatabases($user, $workspace);
        if (is_array($dbsToFilter) && count($dbsToFilter)) {
            $idField = $resourceClass === Database::class ? 'id' : 'db';
            $queryBuilder->andWhere(
                sprintf(
                    "%s.%s IN (%s)",
                    $queryBuilder->getRootAliases()[0],
                    $idField,
                    implode(',', $dbsToFilter)
                )
            );
        } elseif (is_array($dbsToFilter) && count($dbsToFilter) === 0) {
            throw new AccessDeniedException();
        }
    }
}
