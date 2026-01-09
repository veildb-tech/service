<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Workspace\Workspace;
use App\Entity\Workspace\Notification;
use App\Repository\Workspace\NotificationRepository;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

readonly class Logger
{
    /**
     * @param LoggerInterface $logger
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        private LoggerInterface        $logger,
        private NotificationRepository $notificationRepository
    ) {
    }

    /**
     * Log data. If workspace is passed then save to workspace notifications
     *
     * @param string $message
     * @param mixed $level
     * @param Workspace|null $workspace
     * @return void
     */
    public function log(string $message, mixed $level = LogLevel::INFO, ?Workspace $workspace = null): void
    {
        $this->logger->log($level, $message);
        if ($workspace) {
            $notification = new Notification();
            $notification->setLevel($level)
                ->setMessage($message)
                ->setWorkspace($workspace);
            $this->notificationRepository->save($notification, true);
        }
    }
}
