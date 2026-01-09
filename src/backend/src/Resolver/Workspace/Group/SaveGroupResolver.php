<?php

namespace App\Resolver\Workspace\Group;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Service\Workspace\GetSelectedWorkspace;

#[ApiResource]
final readonly class SaveGroupResolver implements MutationResolverInterface
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace
    ) {}

    /**
     * Automatically save selected workspace to object if it is empty
     *
     * @param object|null $item
     * @param array $context
     * @return object
     */
    public function __invoke(?object $item, array $context): object
    {
        /**
         * 1. Need to add validation by workspace. Also validate if user change permission he should have access to do that
         * 2. add databases to group
         */
        if (empty($context['args']['input']['workspace']) && !$item->getWorkspace()) {
            $workspace = $this->getSelectedWorkspace->execute();
            $item->setWorkspace($workspace);
        }

        return $item;
    }
}
