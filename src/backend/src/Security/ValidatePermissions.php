<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Database\DatabaseDump;
use App\Entity\Database\DatabaseDumpLogs;
use App\Entity\Server;
use App\Entity\Workspace\Workspace;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Enums\Workspace\UserRoleEnum;
use App\Service\Workspace\GetSelectedWorkspace;
use App\Enums\Workspace\UserGroupPermissionEnum;
use App\Security\Validators\ValidatorInterface;

readonly class ValidatePermissions implements ValidatorInterface
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace
    ) {}

    /**
     * Validate user permissions
     *
     * @param UserInterface $user
     * @param mixed $entity
     * @param string $action
     * @return bool
     * @throws \Exception
     */
    public function validate(UserInterface $user, mixed $entity, string $action): bool
    {
        $userPermissions = [];

        if ($user->getApiWorkspaceCode()) {
            $entityWorkspace = $this->getWorkspaceFromEntity($entity);
            return $entityWorkspace->getCode() === $user->getApiWorkspaceCode();
        } else {
            $workspace = $this->getSelectedWorkspace->execute();
            if (!$workspace) {
                $workspace = $this->getWorkspaceFromEntity($entity);
            }
        }

        if (!$workspace) {
            throw new \Exception("Workspace in not specified");
        }

        if (!$this->validateUserWorkspace($user, $workspace)) {
            return false;
        }

        $groups = $user->getGroups();
        foreach ($groups as $group) {
            if ($group->getWorkspace()->getId() === $workspace->getId()) {
                $permission = $group->getPermission();
                $userPermissions[] = $permission;
            }
        }

        if (in_array(UserGroupPermissionEnum::OWNER->value, $userPermissions)) {
            return true;
        }

        $result = false;
        switch (strtolower($action)) {
            case UserRoleEnum::DBM_OWNER->value:
                break;
            case UserRoleEnum::DBM_ADMIN->value:
                $result = in_array(UserGroupPermissionEnum::ADMIN->value, $userPermissions);
                break;
            case UserRoleEnum::DBM_EDIT->value:
                $result = !!array_intersect(
                    [UserGroupPermissionEnum::EDIT_ALL->value, UserGroupPermissionEnum::ADMIN->value],
                    $userPermissions
                );
                if (!$result && in_array(UserGroupPermissionEnum::EDIT->value, $userPermissions)) {
                    $result = $this->validateEntityAccess($entity, $user, $workspace);
                }

                break;
            case UserRoleEnum::DBM_READ->value:
                $result = !!array_intersect(
                    [
                        UserGroupPermissionEnum::EDIT_ALL->value,
                        UserGroupPermissionEnum::ADMIN->value,
                        UserGroupPermissionEnum::READ_ALL->value
                    ],
                    $userPermissions
                );
                if (!$result
                    && !!array_intersect([
                        UserGroupPermissionEnum::READ->value,
                        UserGroupPermissionEnum::EDIT->value],
                        $userPermissions
                    )) {
                    $result = $this->validateEntityAccess($entity, $user, $workspace);
                }

                break;

        }

        return $result;
    }

    /**
     * This method returns array with database ids if need to filter.
     * It returns false if no need to filter (basically in that case user has access to all databases under his workspace)
     *
     * @param UserInterface $user
     * @param Workspace $workspace
     * @return bool|array
     */
    public function getPermittedDatabases(UserInterface $user, Workspace $workspace): bool | array
    {
        $allowedDatabases = [];

        /**
         * If at least one permission is assigned to user group then skip database filtration
         */
        $skipPermissions = [
            UserGroupPermissionEnum::EDIT_ALL->value,
            UserGroupPermissionEnum::READ_ALL->value,
            UserGroupPermissionEnum::ADMIN->value,
            UserGroupPermissionEnum::OWNER->value
        ];

        $filterPermissions = [
            UserGroupPermissionEnum::EDIT->value,
            UserGroupPermissionEnum::READ->value
        ];

        if ($user->getApiWorkspaceCode()) {
            return false;
        } else {
            $groups = $user->getGroups()->filter(function ($group) use ($workspace) {
                return $group->getWorkspace()->getCode() === $workspace->getCode();
            });
            foreach ($groups as $group) {
                $permission = $group->getPermission();
                if (in_array($permission, $skipPermissions)) {
                    return false;
                }

                if (in_array($permission, $filterPermissions)) {
                    $databases = $group->getDatabases();
                    foreach ($databases as $database) {
                        $allowedDatabases[] = $database->getId();
                    }
                }
            }
        }

        return $allowedDatabases;
    }

    /**
     * Retrieve workspace from provided entity
     *
     * @param mixed $entity
     * @return bool|Workspace
     */
    protected function getWorkspaceFromEntity(mixed $entity): bool | Workspace
    {
        if ($entity instanceof Server) {
            return $entity->getWorkspace();
        }

        return false;
    }

    /**
     * Validate if provided user has assigned to same workspace as provided
     *
     * @param UserInterface $user
     * @param Workspace $workspace
     * @return bool
     */
    protected function validateUserWorkspace(UserInterface $user, Workspace $workspace): bool
    {
        foreach ($user->getWorkspaces() as $userWorkspace) {
            if ($userWorkspace->getId() === $workspace->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compare permitted databases with current entities. If there are different IDs or count then decline access
     * Filtering of entities is done on extension level (see App\Api\ValidateUserPermission class)
     *
     * @param $entity
     * @param $user
     * @param Workspace $workspace
     * @return bool
     */
    protected function validateEntityAccess($entity, $user, Workspace $workspace): bool
    {
        $permittedDatabases = $this->getPermittedDatabases($user, $workspace);
        if (is_array($entity)) {
            foreach ($entity as $item) {
                $index = array_search($item->getId(), $permittedDatabases);
                if ($index !== false) {
                    unset($permittedDatabases[$index]);
                } else {
                    return false;
                }
            }

            return count($permittedDatabases) === 0;
        }

        return in_array($entity->getId(), $permittedDatabases);
    }
}
