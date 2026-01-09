<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\Workspace\WorkspaceProcessor;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

readonly class JWTCreatedListener
{
    /**
     * @param WorkspaceProcessor $workspaceProcessor
     */
    public function __construct(
        private WorkspaceProcessor $workspaceProcessor
    ) {
    }

    /**
     * @param JWTCreatedEvent $event
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            new AccessDeniedException();
        }

        $workspace = $this->workspaceProcessor->getWorkspaceToLogin($user);
        if ($workspace) {
            $payload = $event->getData();
            $payload['workspace'] = $workspace;
            $event->setData($payload);
        }
    }
}
