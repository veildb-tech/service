<?php

declare(strict_types=1);

namespace App\Service\Workspace;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class WorkspaceProcessor
{
    private ?string $workspace = null;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * Try to allocate workspace
     *
     * @param UserInterface $user
     * @return string
     */
    public function getWorkspaceToLogin(UserInterface $user): string
    {
        if (!$this->workspace) {
            $this->workspace = $this->getWorkspaceFromRequest();
            if (!$this->workspace) {
                $workspaces = $user->getWorkspaces();
                if (!$workspaces) {
                    throw new AccessDeniedException();
                }

                // For now use first workspace. TODO: assign workspace where user is owner if such exists.
                $this->workspace = $workspaces[0]->getCode();
            }

            if (!$this->validateWorkspace($user, $this->workspace)) {
                throw new AccessDeniedException();
            }
        }

        return $this->workspace;
    }

    /**
     * @return string|null
     */
    private function getWorkspaceFromRequest(): ?string
    {
        $request = $this->requestStack->getMainRequest();
        if ($request->get('workspace')) {
            $selectedWorkspace = $request->get('workspace');
        } else {
            $content = json_decode($request->getContent(), true);
            $selectedWorkspace = !empty($content['workspace']) ? $content['workspace'] : null;
        }

        return $selectedWorkspace;
    }

    /**
     * @param UserInterface $user
     * @param string $workspace
     * @return bool
     */
    private function validateWorkspace(UserInterface $user, string $workspace): bool
    {
        $valid = false;
        foreach ($user->getWorkspaces() as $userWorkspace) {
            if ($userWorkspace->getCode() === $workspace) {
                $valid = true;
            }
        }

        return $valid;
    }
}
