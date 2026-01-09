<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Database\Database;
use App\Service\Database\GenerateSuggestedRule;
use App\Service\Workspace\GetSelectedWorkspace;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\Uid\Factory\UuidFactory;

#[AsEntityListener(event: Events::prePersist, entity: Database::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Database::class)]
#[AsEntityListener(event: Events::postPersist, entity: Database::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Database::class)]
class DatabaseEntityListener extends AbstractEntityListener
{
    /**
     * @param UuidFactory $uuidFactory
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param GenerateSuggestedRule $generateSuggestedRule
     */
    public function __construct(
        protected UuidFactory $uuidFactory,
        protected GetSelectedWorkspace $getSelectedWorkspace,
        protected readonly GenerateSuggestedRule $generateSuggestedRule
    ) {
        parent::__construct($this->uuidFactory, $this->getSelectedWorkspace);
    }

    public function prePersist(Database $database, LifecycleEventArgs $event): void
    {
        $database->setUpdatedAt($this->getCurrentTime());
        $database->setCreatedAt($this->getCurrentTime());
        $this->updateFields($database);
    }

    public function preUpdate(Database $database, LifecycleEventArgs $event): void
    {
        $database->setUpdatedAt($this->getCurrentTime());
        $this->updateFields($database);
    }

    public function postPersist(Database $database, LifecycleEventArgs $eventArgs): void
    {
        $this->generateSuggestedRule->execute($database);
    }

    public function postUpdate(Database $database, LifecycleEventArgs $eventArgs): void
    {
        $this->generateSuggestedRule->execute($database);
    }

    protected function updateFields(Database $database): void
    {
        $this->assignWorkspaceToEntity($database);
        $this->assignUuidToEntity($database);
    }
}
