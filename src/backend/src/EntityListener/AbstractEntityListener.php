<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\EntityWithUuidInterface;
use App\Entity\EntityWithWorkspaceInterface;
use Symfony\Component\Uid\Factory\UuidFactory;
use App\Service\Workspace\GetSelectedWorkspace;
use DateTimeImmutable;

abstract class AbstractEntityListener
{
    /**
     * @param UuidFactory $uuidFactory
     * @param GetSelectedWorkspace $getSelectedWorkspace
     */
    public function __construct(
        protected UuidFactory $uuidFactory,
        protected GetSelectedWorkspace $getSelectedWorkspace
    ) {
    }

    /**
     * @param EntityWithWorkspaceInterface $entity
     * @return void
     */
    protected function assignWorkspaceToEntity(EntityWithWorkspaceInterface $entity): void
    {
        if (!$entity->getWorkspace()) {
            $entity->setWorkspace($this->getSelectedWorkspace->execute());
        }
    }

    /**
     * @param EntityWithUuidInterface $entity
     * @return void
     */
    protected function assignUuidToEntity(EntityWithUuidInterface $entity): void
    {
        if (!$entity->getUuid()) {
            $entity->setUuid($this->uuidFactory->create());
        }
    }

    /**
     * @return DateTimeImmutable
     */
    protected function getCurrentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
