<?php

namespace App\Security\Voter;

use App\Service\Workspace\GetSelectedWorkspace;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class WorkspaceVoter extends Voter
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     */
    public function __construct(
        private readonly GetSelectedWorkspace $getSelectedWorkspace
    ) {
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'same_workspace';
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $userWorkspace = $this->getSelectedWorkspace->execute();
        return $userWorkspace->getCode() === $subject->getCode();
    }
}
