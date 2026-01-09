<?php

declare(strict_types=1);

namespace App\EntityListener\Workspace;

use App\Entity\Workspace\Notification;
use App\EntityListener\AbstractEntityListener;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Notification::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Notification::class)]
class NotificationEntityListener extends AbstractEntityListener
{
    public function prePersist(Notification $notification, LifecycleEventArgs $event): void
    {
        $this->assignUuidToEntity($notification);
        $this->assignWorkspaceToEntity($notification);
    }

    public function preUpdate(Notification $notification, LifecycleEventArgs $event): void
    {
        $this->assignWorkspaceToEntity($notification);
    }
}
