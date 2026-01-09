<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\User;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\Workspace\UserRepository;
use App\Service\Workspace\GetSelectedWorkspace;

#[ApiResource]
final readonly class DeleteResolver implements MutationResolverInterface
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param UserRepository $userRepository
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace,
        private UserRepository $userRepository
    ) {
    }

    /**
     * In case if user has another workspaces need to unsign from current one
     *
     * @param object|null $item
     * @param array $context
     * @return object|null
     */
    public function __invoke(?object $item, array $context): ?object
    {
        /** @var \App\Entity\Workspace\User $item */
        if (count($item->getWorkspaces()) <= 1) {
            $this->userRepository->remove($item, true);
        } else  {
            $item->removeWorkspace($this->getSelectedWorkspace->execute());
            $this->userRepository->save($item, true);
        }
        return null;
    }
}
