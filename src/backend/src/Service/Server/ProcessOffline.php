<?php

declare(strict_types=1);

namespace App\Service\Server;

use App\Entity\Server;
use App\Entity\Workspace\Notification;
use App\Enums\ServerStatusEnum;
use App\Repository\ServerRepository;
use App\Repository\Workspace\NotificationRepository;

readonly class ProcessOffline
{
    /**
     * @param ServerRepository $serverRepository
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        private ServerRepository     $serverRepository,
        private NotificationRepository $notificationRepository
    ) {
    }

    /**
     * Disabled server and send notification to user
     *
     * @param Server $server
     * @return void
     */
    public function execute(Server $server): void
    {
        $server->setStatus(ServerStatusEnum::OFFLINE->value);
        $this->serverRepository->save($server, true);

        // Send notification to user
        $notification = new Notification();
        $notification->setLevel('error')
            ->setWorkspace($server->getWorkspace())
            ->setExternalUrl('https://dbvisor.gitbook.io/')
            ->setMessage(
                sprintf(
                    "Server \"%s\" is offline for a while. The server was disabled. Please check the server status or contact support if you have questions",
                    $server->getName()
                )
            );

        $this->notificationRepository->save($notification, true);
    }
}
