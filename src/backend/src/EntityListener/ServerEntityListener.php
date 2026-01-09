<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Server;
use App\Enums\Database\DatabaseStatusEnum;
use App\Enums\ServerStatusEnum;
use App\Repository\Database\DatabaseRepository;
use App\Service\Workspace\GetSelectedWorkspace;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Uid\Factory\UuidFactory;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Server::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Server::class)]
#[AsEntityListener(event: Events::postUpdate, entity: Server::class)]
class ServerEntityListener extends AbstractEntityListener
{
    /**
     * @param UuidFactory $uuidFactory
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param DatabaseRepository $databaseRepository
     */
    public function __construct(
        UuidFactory $uuidFactory,
        GetSelectedWorkspace $getSelectedWorkspace,
        private readonly DatabaseRepository $databaseRepository
    ) {
        parent::__construct($uuidFactory, $getSelectedWorkspace);
    }

    public function prePersist(Server $server, LifecycleEventArgs $event): void
    {
        $this->assignWorkspaceToEntity($server);
        $this->assignUuidToEntity($server);
        $server->generateSecretKey();
        $server->setPingDate(new \DateTimeImmutable());
    }

    public function preUpdate(Server $server, LifecycleEventArgs $event): void
    {
        $this->assignUuidToEntity($server);
        $this->assignWorkspaceToEntity($server);
        $server->generateSecretKey();
    }

    public function postUpdate(Server $server, LifecycleEventArgs $event): void
    {
        if ($server->getStatus() !== ServerStatusEnum::ENABLED->value) {
            $this->disableServerDatabases($server);
        }
    }

    /**
     * Disable all databases that are related to server
     *
     * @param Server $server
     * @return void
     */
    private function disableServerDatabases(Server $server): void
    {
        $databases = $server->getDatabases()->getValues();
        foreach ($databases as $database) {
            $updateDatabase = $this->databaseRepository->find($database->getId());
            $updateDatabase->setStatus(DatabaseStatusEnum::DISABLED->value);
            $this->databaseRepository->save($updateDatabase, true);
        }
    }
}
