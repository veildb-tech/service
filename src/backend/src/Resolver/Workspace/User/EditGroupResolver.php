<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\User;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Service\Workspace\GetSelectedWorkspace;
use App\Entity\Workspace\User;
use ApiPlatform\Api\IriConverterInterface;

#[ApiResource]
final readonly class EditGroupResolver implements MutationResolverInterface
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param IriConverterInterface $iriConverter
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace,
        private IriConverterInterface $iriConverter
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
        $workspace = $this->getSelectedWorkspace->execute();
        $groupsToUpdate = $context['args']['input']['updateGroups'];

        /** @var User $item */
        foreach ($item->getGroups() as $userGroup) {
            if ($userGroup->getWorkspace()->getId() !== $workspace->getId()) continue;

            $arrayIndex = array_search($this->iriConverter->getIriFromResource($userGroup), $groupsToUpdate);
            if ($arrayIndex !== false) {
                unset($groupsToUpdate[$arrayIndex]);
            } else {
                $item->removeGroup($userGroup);
            }
        }

        if (!empty($groupsToUpdate)) {
            foreach ($groupsToUpdate as $groupToUpdate) {
                $item->addGroup($this->iriConverter->getResourceFromIri($groupToUpdate));
            }
        }

        return $item;
    }
}
