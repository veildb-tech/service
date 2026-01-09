<?php

declare(strict_types=1);

namespace App\Service\Workspace;

use App\Entity\Workspace\Workspace;
use App\Repository\Workspace\WorkspaceRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetSelectedWorkspace
{
    const WORKSPACE_KEY = 'workspace';

    private ?Workspace $selectedWorkspace = null;

    /**
     * @param Security $security
     * @param WorkspaceRepository $workspaceRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        private readonly Security              $security,
        private readonly WorkspaceRepository   $workspaceRepository,
        private readonly TokenStorageInterface $tokenStorage
    ) {
    }

    /**
     * @return Workspace|null
     */
    public function execute(): ?Workspace
    {
        if ($this->selectedWorkspace) {
            return $this->selectedWorkspace;
        }

        $token = $this->tokenStorage->getToken();
        $user = $this->security->getUser();

        if (!$user) {
            throw new AccessDeniedException();
        }

        if ($user->getApiWorkspaceCode()) {
            $this->selectedWorkspace = $this->workspaceRepository->findOneBy(
                [
                    'code' => $user->getApiWorkspaceCode()
                ]
            );
            return $this->selectedWorkspace;
        }

        if (!$token->hasAttribute(self::WORKSPACE_KEY)) {
            throw new AccessDeniedException();
        }

        $this->selectedWorkspace = $this->workspaceRepository->findOneBy(
            [
                'code' => $token->getAttribute(self::WORKSPACE_KEY)
            ]
        );

        $isUserAssignedToWorkspace = false;
        foreach ($user->getWorkspaces() as $userWorkspace) {
            if ($userWorkspace->getCode() === $this->selectedWorkspace->getCode()) {
                $isUserAssignedToWorkspace = true;
            }
        }

        if (!$this->selectedWorkspace || !$isUserAssignedToWorkspace) {
            throw new AccessDeniedException();
        }

        return $this->selectedWorkspace;
    }
}
