<?php

declare(strict_types=1);

namespace App\Service\Database;

use App\Entity\Workspace\Notification;
use App\Enums\Database\DatabaseStatusEnum;
use App\Repository\Database\DatabaseRepository;
use App\Repository\Workspace\NotificationRepository;

readonly class ProcessDamaged
{
    /**
     * @param DatabaseRepository $databaseRepository
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        private DatabaseRepository     $databaseRepository,
        private NotificationRepository $notificationRepository
    ) {
    }

    /**
     * Disabled database and send notification to user
     *
     * @param int $databaseId
     * @return void
     */
    public function execute(int $databaseId): void
    {
        // Disable database
        $database = $this->databaseRepository->find($databaseId);
        $database->setStatus(DatabaseStatusEnum::DISABLED->value);
        $this->databaseRepository->save($database, true);

        // Send notification to user
        $notification = new Notification();
        $notification->setLevel('error')
            ->setWorkspace($database->getWorkspace())
            ->setExternalUrl('https://dbvisor.gitbook.io/')
            ->setMessage(
                sprintf(
                    "Database \"%s\" is corrupted: 3 previous attempts to create a dump failed. Database was disabled.",
                    $database->getName()
                )
            );

        $this->notificationRepository->save($notification, true);
    }
}
