<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Security\ValidatePermissions;
use App\Security\Validators\ValidatorFactory;

class GeneralVoter extends Voter
{
    /**
     * @param ValidatorFactory $validatorFactory
     */
    public function __construct(
        private ValidatorFactory $validatorFactory
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['dbm_edit', 'dbm_read', 'dbm_admin', 'dbm_owner']);
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (
            in_array('ROLE_ADMIN', $token->getRoleNames())
            || $user->getApiWorkspaceCode()
        ) {
            return true;
        }

        $validator = $this->validatorFactory->create($subject);

        if (is_array($subject)) {
            foreach ($subject as $item) {
                $result = $validator->validate($user, $item, $attribute);
                if (!$result) {
                    return false;
                }
            }

            return true;
        } else {
            return $validator->validate($user, $subject, $attribute);
        }
    }
}
