<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Database\DatabaseDump;
use App\Repository\Database\DatabaseDumpRepository;
use App\Enums\Database\DumpStatusEnum;
use App\Exception\ItemExistsException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use App\Entity\Workspace\Notification;
use App\Repository\Workspace\NotificationRepository;

#[AsEntityListener(event: Events::prePersist, entity: DatabaseDump::class)]
#[AsEntityListener(event: Events::preUpdate, entity: DatabaseDump::class)]
#[AsEntityListener(event: Events::postUpdate, entity: DatabaseDump::class)]
#[AsEntityListener(event: Events::postPersist, entity: DatabaseDump::class)]
readonly class DatabaseDumpEntityListener
{
    /**
     * @param DatabaseDumpRepository $databaseDumpRepository
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        private DatabaseDumpRepository $databaseDumpRepository,
        private NotificationRepository $notificationRepository
    ) {
    }

    /**
     * @param DatabaseDump $databaseDump
     * @param LifecycleEventArgs $event
     * @return void
     * @throws ItemExistsException
     */
    public function prePersist(DatabaseDump $databaseDump, LifecycleEventArgs $event): void
    {
        $this->validate($databaseDump);
        $databaseDump->setUpdatedAt(new \DateTimeImmutable());
        $databaseDump->setCreatedAt(new \DateTimeImmutable());
        $databaseDump->generateUuid();
    }

    /**
     * @param DatabaseDump $databaseDump
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function preUpdate(DatabaseDump $databaseDump, LifecycleEventArgs $event): void
    {
        $databaseDump->setUpdatedAt(new \DateTimeImmutable());
        $databaseDump->generateUuid();

    }

    /**
     * @param DatabaseDump $databaseDump
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postUpdate(DatabaseDump $databaseDump, LifecycleEventArgs $event): void
    {
        if ($databaseDump->getStatus() === DumpStatusEnum::READY_WITH_ERROR->value) {
            $this->addNotification($databaseDump);
        }
    }

    /**
     * @param DatabaseDump $databaseDump
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postPersist(DatabaseDump $databaseDump, LifecycleEventArgs $event): void
    {
        if ($databaseDump->getStatus() === DumpStatusEnum::READY_WITH_ERROR->value) {
            $this->addNotification($databaseDump);
        }
    }

    /**
     * Check is there already scheduled dump
     *
     * @param DatabaseDump $databaseDump
     * @return void
     * @throws ItemExistsException
     */
    private function validate(DatabaseDump $databaseDump): void
    {
        if ($databaseDump->getStatus() === DumpStatusEnum::SCHEDULED->value) {
            $dump = $this->databaseDumpRepository->findByStatus(
                DumpStatusEnum::SCHEDULED->value,
                $databaseDump->getDb()
            );
            if (count($dump)) {
                throw new ItemExistsException("Database Dump has been already schedule");
            }
        }
    }

    /**
     * For dumps with status READY_WITH_ERROR add notification to workspace
     *
     * @param DatabaseDump $databaseDump
     * @return void
     */
    private function addNotification(DatabaseDump $databaseDump): void
    {
        $notification = new Notification();
        $database = $databaseDump->getDb();

        $notification->setLevel('warning')
            ->setWorkspace($database->getWorkspace())
            ->setExternalUrl('https://docs.dbvisor.pro/s/wiki/doc/processing-error-SRFnAjACrC')
            ->setMessage(
                sprintf(
                    "
                        There are errors with processing during processing of \"%s\" database.
                        It might be serious issue and some rule doesn't work properly.
                        Please check errors and contact our support
                        ",
                    $database->getName()
                )
            );

        $this->notificationRepository->save($notification, true);
    }
}
