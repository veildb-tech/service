<?php

declare(strict_types=1);

namespace App\EntityListener\Workspace;

use App\Entity\Workspace\UserInvitation;
use App\EntityListener\AbstractEntityListener;
use App\Enums\Workspace\UserInvitationStatusEnum;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: UserInvitation::class)]
#[AsEntityListener(event: Events::preUpdate, entity: UserInvitation::class)]
class InvitationEntityListener extends AbstractEntityListener
{

    public function prePersist(UserInvitation $invitation, LifecycleEventArgs $event): void
    {
        $invitation->setCreatedAt($this->getCurrentTime());
        $this->updateFields($invitation);
    }

    public function preUpdate(UserInvitation $invitation, LifecycleEventArgs $event): void
    {
        $this->updateFields($invitation);
    }

    protected function updateFields(UserInvitation $invitation): void
    {
        if (!$invitation->getStatus()) {
            $invitation->setStatus(UserInvitationStatusEnum::PENDING->value);
        }

        $this->assignWorkspaceToEntity($invitation);
        $this->assignUuidToEntity($invitation);
    }
}
