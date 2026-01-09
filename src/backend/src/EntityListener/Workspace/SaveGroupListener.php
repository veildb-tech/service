<?php

declare(strict_types=1);

namespace App\EntityListener\Workspace;

use App\Entity\Admin\User as AdminUser;
use App\Entity\Workspace\Group;
use App\EntityListener\AbstractEntityListener;
use App\Exception\OperationDeniedException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Factory\UuidFactory;
use App\Service\Workspace\GetSelectedWorkspace;
use App\Enums\Workspace\UserGroupPermissionEnum;
use App\Exception\ValidationException;

#[AsEntityListener(event: Events::preUpdate, entity: Group::class)]
#[AsEntityListener(event: Events::prePersist, entity: Group::class)]
#[AsEntityListener(event: Events::preRemove, entity: Group::class)]
class SaveGroupListener extends AbstractEntityListener
{

    /**
     * @param UuidFactory $uuidFactory
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param Security $security
     */
    public function __construct(
        protected UuidFactory $uuidFactory,
        protected GetSelectedWorkspace $getSelectedWorkspace,
        private Security $security
    ) {
        parent::__construct($this->uuidFactory, $this->getSelectedWorkspace);
    }

    /**
     * @param Group $group
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function prePersist(Group $group, LifecycleEventArgs $event): void
    {
        $this->assignWorkspaceToEntity($group);
        $this->assignUuidToEntity($group);
    }

    /**
     * @param Group $group
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function preUpdate(Group $group, LifecycleEventArgs $event): void
    {
        $changes = $event->getEntityChangeSet();

        if (!empty($changes['permission']) && $changes['permission'][0] === UserGroupPermissionEnum::OWNER->value) {
            throw new ValidationException("You can't change owner group permissions");
        }
        $this->assignWorkspaceToEntity($group);
        $this->assignUuidToEntity($group);
    }

    /**
     * @param Group $group
     * @param LifecycleEventArgs $event
     * @return void
     * @throws OperationDeniedException
     */
    public function preRemove(Group $group, LifecycleEventArgs $event): void
    {
        $user = $this->security->getUser();
        // Allow only for admin users remove groups or in case if whole workspace is removing
        if (
            !$group->getForceRemoveFlag()
            && $group->getPermission() === UserGroupPermissionEnum::OWNER->value
            && !($user instanceof AdminUser)
        ) {
            throw new OperationDeniedException("Owner group couldn't be removed");
        }
    }
}
