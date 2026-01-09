<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\Workspace\User;
use App\Service\Workspace\GetUserVisibleServers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @param GetUserVisibleServers $getUserVisibleServers
     */
    public function __construct(
        private readonly GetUserVisibleServers $getUserVisibleServers
    ) {
    }

    #[Route('/api/profile', name: 'app_profile')]
    public function getProfile(Security $security): JsonResponse
    {
        /** @var User $user */
        $user = $security->getUser();
        $workspaces = array_map(fn ($workspace) => $workspace->getCode(), $user->getWorkspaces()->toArray());

        $userData = [
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'identifier' => $user->getUserIdentifier(),
            'workspaces' => $workspaces
        ];
        $selectedWorkspace = $security->getToken()->getAttribute('workspace');

        if ($selectedWorkspace !== null) {
            $selectedWorkspaceResource = array_filter(
                $user->getWorkspaces()->toArray(),
                fn($workspace) => $workspace->getCode() === $selectedWorkspace
            );
            $selectedWorkspaceResource = array_shift($selectedWorkspaceResource);

            if ($selectedWorkspaceResource) {
                $userData['servers'] = $this->getUserVisibleServers->execute($user, $selectedWorkspaceResource);
            }
        }
        return new JsonResponse($userData);
    }
}
