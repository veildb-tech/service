<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Webhook;
use App\Service\Workspace\GetSelectedWorkspace;
use DateTimeImmutable;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Uid\Factory\UuidFactory;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Webhook::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Webhook::class)]
class WebhookEntityListener
{
    public function __construct(
        private UuidFactory $uuidFactory,
        private GetSelectedWorkspace $getSelectedWorkspace,
    ) {
    }

    public function prePersist(Webhook $webhook, LifecycleEventArgs $event): void
    {
        $webhook->generateUid($this->uuidFactory);
        $webhook->setCreatedAt($this->getCurrentDate());
        $webhook->setUpdatedAt($this->getCurrentDate());
        $this->assignWorkspaceToWebhook($webhook);
    }

    public function preUpdate(Webhook $webhook, LifecycleEventArgs $event): void
    {
        $webhook->generateUid($this->uuidFactory);
        $webhook->setUpdatedAt($this->getCurrentDate());
        $this->assignWorkspaceToWebhook($webhook);
    }

    private function getCurrentDate(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }

    /**
     * If there are no workspace in request assign current
     *
     * @param Webhook $webhook
     * @return void
     */
    private function assignWorkspaceToWebhook(Webhook $webhook): void
    {
        if (!$webhook->getWorkspace()) {
            $currentWorkspace = $this->getSelectedWorkspace->execute();
            $webhook->setWorkspace($currentWorkspace);
        }
    }
}
