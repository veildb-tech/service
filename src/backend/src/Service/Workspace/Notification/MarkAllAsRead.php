<?php

declare(strict_types=1);

namespace App\Service\Workspace\Notification;

use App\Entity\Workspace\Notification;
use App\Repository\Workspace\NotificationRepository;
use App\Service\Workspace\GetSelectedWorkspace;

readonly class MarkAllAsRead
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace,
        private NotificationRepository $notificationRepository
    ) {
    }

    /**
     * Mark all notification as read
     *
     * @return void
     * @throws \Exception
     */
    public function execute(): void
    {
        $workspace = $this->getSelectedWorkspace->execute();
        if (!$workspace) {
            throw new \Exception("Can't allocate workspace");
        }

        $notifications = $this->notificationRepository->findBy(
            [
                'status' => Notification::STATUS_NEW,
                'workspace' => $workspace->getId()
            ]
        );
        foreach ($notifications as $notification) {
            $notification->setStatus(Notification::STATUS_READ);
            $this->notificationRepository->save($notification, true);
        }
    }
}
