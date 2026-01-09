<?php

declare(strict_types=1);

namespace App\Service\Group;

use App\Entity\Workspace\Group;
use App\Entity\Workspace\Workspace;
use App\Enums\Workspace\UserGroupPermissionEnum;
use App\Repository\Workspace\GroupRepository;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CreateOwnerGroup
{
    /**
     * @param Security $security
     * @param GroupRepository $groupRepository
     */
    public function __construct(
        private Security        $security,
        private GroupRepository $groupRepository
    ) {
    }

    /**
     * Create owner group and assign current user to this group
     *
     * @param Workspace $workspace
     * @return void
     * @throws \Exception
     */
    public function execute(Workspace $workspace): void
    {
        $user = $this->security->getUser();
        if (!$user && $workspace->getUsers()->count() === 1) {
            // In case if workspace has already created we could use first user
            $user = $workspace->getUsers()[0];
        }

        if (!$user) {
            throw new \Exception("Something went wrong. Please try again or contact support");
        }

        $group = new Group();
        $group->setWorkspace($workspace)
            ->setName('Default Group')
            ->setPermission(UserGroupPermissionEnum::OWNER->value);
        $group->addUser($user);

        $this->groupRepository->save($group, true);
    }
}
