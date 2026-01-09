<?php

declare(strict_types=1);

namespace App\Service\Workspace;

use App\Entity\Workspace\UserInvitation;
use App\Enums\Workspace\UserInvitationStatusEnum;
use App\Repository\Workspace\UserInvitationRepository;
use App\Repository\Workspace\UserRepository;
use ApiPlatform\Api\IriConverterInterface;

readonly class AcceptInvitation
{
    /**
     * @param UserRepository $userRepository
     * @param IriConverterInterface $iriConverter
     * @param UserInvitationRepository $invitationRepository
     */
    public function __construct(
        private UserRepository           $userRepository,
        private IriConverterInterface $iriConverter,
        private UserInvitationRepository $invitationRepository
    ) {
    }

    /**
     * Accept invitation: try to find user by email from invitation. Added workspace to this user and change invitation
     * status to accepted
     *
     * @param UserInvitation $invitation
     * @return void
     * @throws \Exception
     */
    public function execute(UserInvitation $invitation): void
    {
        $user = $this->userRepository->findOneBy(['email' => $invitation->getEmail()]);
        if (!$user) {
            throw new \Exception(sprintf("User with %s email not found", $invitation->getEmail()));
        }
        $user->addWorkspace($invitation->getWorkspace());
        foreach ($invitation->getInvitationGroups() as $invitationGroup) {
            $group = $this->iriConverter->getResourceFromIri($invitationGroup);
            $user->addGroup($group);
        }

        $invitation->setStatus(UserInvitationStatusEnum::ACCEPTED->value);
        $this->invitationRepository->save($invitation);
    }
}
