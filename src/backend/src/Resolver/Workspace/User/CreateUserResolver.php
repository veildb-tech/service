<?php

declare(strict_types=1);

namespace App\Resolver\Workspace\User;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Workspace\UserInvitation;
use App\Exception\ValidationException;
use App\Entity\Workspace\Group;
use App\Repository\Workspace\WorkspaceRepository;
use App\Enums\Workspace\UserInvitationStatusEnum;
use App\Repository\Workspace\UserInvitationRepository;
use App\Util\Workspace\CodeTransformerTrait;
use ApiPlatform\Api\IriConverterInterface;

#[ApiResource]
final readonly class CreateUserResolver implements MutationResolverInterface
{
    use CodeTransformerTrait;

    /**
     * @param UserInvitationRepository $invitationRepository
     * @param IriConverterInterface $iriConverter
     * @param WorkspaceRepository $workspaceRepository
     */
    public function __construct(
        private UserInvitationRepository $invitationRepository,
        private IriConverterInterface $iriConverter,
        private WorkspaceRepository $workspaceRepository
    ) {
    }

    /**
     * @param object|null $item
     * @param array $context
     * @return object|null
     * @throws \Exception
     */
    public function __invoke(?object $item, array $context): object
    {
        if (!empty($context['args']['input']['invitation'])) {
            $invitationId = $context['args']['input']['invitation'];
            $invitation = $this->invitationRepository->findOneBy(['uuid' => $invitationId]);
            if (!$invitation) {
                throw new \Exception(sprintf("Invitation with ID %s not found", $invitationId));
            }

            if ($invitation->getStatus() !== UserInvitationStatusEnum::PENDING->value) {
                $this->validateInvitationStatus($invitation);
            }

            $invitationGroups = $invitation->getInvitationGroups();
            foreach ($invitationGroups as $invitationGroup) {
                /** @var Group $group */
                try {
                    // added try-catch to avoid case when group was removed after invitation had sent
                    $group = $this->iriConverter->getResourceFromIri($invitationGroup);
                    $item->addGroup($group);
                } catch (\Exception $exception) {
                }
            }

            $item->addWorkspace($invitation->getWorkspace());

            $invitation->setStatus(UserInvitationStatusEnum::ACCEPTED->value);
            $this->invitationRepository->save($invitation);
        } else {
            $this->validate($item);
        }

        return $item;
    }

    /**
     * @param object $item
     * @return void
     */
    protected function validate(object $item): void
    {
        $workspaces = $item->getWorkspaces();
        if ($workspaces && !empty($workspaces[0])) {
            $workspaceName = $workspaces[0]->getName();
            $workspaceCode = $this->getTransformedCode($workspaceName);

            $loadedWorkspace = $this->workspaceRepository->findOneBy(['code' => $workspaceCode]);

            if ($loadedWorkspace) {
                throw new ValidationException("Workspace with such name already exists");
            }
        } else {
            throw new ValidationException("Workspace / Company is required field");
        }
    }

    /**
     * @param UserInvitation $invitation
     * @return never
     */
    public function validateInvitationStatus(UserInvitation $invitation): never
    {
        if ($invitation->getStatus() === UserInvitationStatusEnum::ACCEPTED->value) {
            throw new ValidationException("This invitation is accepted");
        }

        if ($invitation->getStatus() === UserInvitationStatusEnum::EXPIRED->value) {
            throw new ValidationException("This invitation is expired");
        }

        if ($invitation->getStatus() === UserInvitationStatusEnum::DECLINED->value) {
            throw new ValidationException("This invitation is canceled");
        }
        throw new ValidationException("Something went wrong");
    }
}
