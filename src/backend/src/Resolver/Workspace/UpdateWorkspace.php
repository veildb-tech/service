<?php

namespace App\Resolver\Workspace;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Workspace\Workspace;
use App\Repository\Workspace\WorkspaceRepository;

#[ApiResource]
final readonly class UpdateWorkspace implements MutationResolverInterface
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository
    ) {
    }

    /**
     * Save workspace using code (please remove it if there is another way to do that just by code field)
     *
     * @param object|null $item
     * @param array $context
     * @return Workspace
     */
    public function __invoke(?object $item, array $context): Workspace
    {
        $newItem = $this->workspaceRepository->findOneBy([
            'code' => $item->getCode()
        ]);

        if (!empty($context['args']['input']['name'])) {
            $newItem->setName($context['args']['input']['name']);
        }

        $this->workspaceRepository->save($newItem, true);

        return $newItem;
    }
}
