<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Workspace\Workspace;
use App\Repository\Workspace\GroupRepository;
use App\Repository\Workspace\UserRepository;
use App\Service\Group\CreateOwnerGroup;
use App\Util\Workspace\CodeTransformerTrait;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Workspace::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Workspace::class)]
#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Workspace::class)]
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Workspace::class)]
readonly class WorkspaceEntityListener
{
    use CodeTransformerTrait;

    /**
     * @param CreateOwnerGroup $createOwnerGroup
     * @param UserRepository $userRepository
     * @param GroupRepository $groupRepository
     */
    public function __construct(
        private CreateOwnerGroup $createOwnerGroup,
        private UserRepository $userRepository,
        private GroupRepository $groupRepository,
    ) {
    }

    /**
     * @param Workspace $workspace
     * @param PrePersistEventArgs $event
     * @return void
     */
    public function prePersist(Workspace $workspace, PrePersistEventArgs $event): void
    {
        if ($workspace->getCode() === null) {
            $workspace->setCode($this->getTransformedCode($workspace->getName()));
        }
        $workspace->generateToken();
    }

    /**
     * @param Workspace $workspace
     * @param PreUpdateEventArgs $event
     * @return void
     */
    public function preUpdate(Workspace $workspace, PreUpdateEventArgs $event): void
    {
        if ($workspace->getCode() === null) {
            $workspace->setCode($this->getTransformedCode($workspace->getName()));
        }
        $workspace->generateToken();
    }

    /**
     * Create owner group for current user and assign this user to this group
     *
     * @param Workspace $workspace
     * @param PostPersistEventArgs $event
     * @return void
     * @throws \Exception
     */
    public function postPersist(Workspace $workspace, PostPersistEventArgs $event): void
    {
        $this->createOwnerGroup->execute($workspace);
    }

    /**
     * Delete user if he has one workspace. If user has more than one workspace just unassign it from that workspace
     *
     * @param Workspace $workspace
     * @param PreRemoveEventArgs $event
     * @return void
     */
    public function preRemove(Workspace $workspace, PreRemoveEventArgs $event): void
    {
        $users = $workspace->getUsers();
        foreach ($users as $user) {
            if (count($user->getWorkspaces()) > 1) {
                $user->removeWorkspace($workspace);
            } else {
                $this->userRepository->remove($user);
            }
        }

        $groups = $workspace->getWorkspaceGroups();
        foreach ($groups as $group) {
            $group->setForceRemoveFlag(true);
            $this->groupRepository->remove($group);
        }
    }
}
