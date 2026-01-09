<?php

declare(strict_types=1);

namespace App\Security\Validators;

use App\Entity\Workspace\Workspace;
use App\Security\ValidatePermissions;
use App\Service\Workspace\GetSelectedWorkspace;
use App\Entity\Database\DatabaseDump;
use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\Api\IriConverterInterface;

readonly class DatabaseDumpValidator extends ValidatePermissions
{
    /**
     * @param mixed $entity
     * @return bool|Workspace
     */
    protected function getWorkspaceFromEntity(mixed $entity): bool | Workspace
    {
        if ($entity instanceof DatabaseDump) {
            return $entity->getDb()->getWorkspace();
        }

        return false;
    }
}
