<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\Workspace\WorkspaceProcessor;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

readonly class JWTSuccessListener
{
    /**
     * @param WorkspaceProcessor $workspaceProcessor
     */
    public function __construct(private WorkspaceProcessor $workspaceProcessor)
    {
    }

    /**
     * @param AuthenticationSuccessEvent $event
     * @return void
     */
    public function onJWTSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $data['workspace'] = $this->workspaceProcessor->getWorkspaceToLogin($event->getUser());
        $event->setData($data);
    }
}
