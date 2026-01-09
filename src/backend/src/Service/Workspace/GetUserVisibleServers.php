<?php

declare(strict_types=1);

namespace App\Service\Workspace;

use App\Entity\Workspace\User;
use App\Entity\Workspace\Workspace;
use App\Repository\Database\DatabaseRepository;
use App\Security\ValidatePermissions;

readonly class GetUserVisibleServers
{
    /**
     * @param ValidatePermissions $validatePermissions
     * @param DatabaseRepository $databaseRepository
     */
    public function __construct(
        private ValidatePermissions $validatePermissions,
        private DatabaseRepository $databaseRepository
    ) {
    }

    /**
     * Retrieve user available servers
     * For now it returns only names for servers because it is forbidden to retrieve servers for regular user
     *
     * @param User $user
     * @param Workspace $workspace
     * @return array
     */
    public function execute(User $user, Workspace $workspace): array
    {
        $servers = [];
        $databaseIds = $this->validatePermissions->getPermittedDatabases($user, $workspace);

        if (!$databaseIds) {
            $databases = $workspace->getDatabases();
        } else {
            $databases = $this->databaseRepository->findBy([
                'id' => $databaseIds
            ]);
        }

        foreach ($databases as $database) {
            $servers[$database->getServer()->getUuid()->__toString()] = $database->getServer()->getName();
        }
        return $servers;
    }
}
