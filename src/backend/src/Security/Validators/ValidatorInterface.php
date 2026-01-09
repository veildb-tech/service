<?php

declare(strict_types=1);

namespace App\Security\Validators;

use \Symfony\Component\Security\Core\User\UserInterface;

interface ValidatorInterface
{
    /**
     * @param UserInterface $user
     * @param mixed $entity Entity could be as object, as a string as well
     * @param string $action
     * @return bool
     */
    public function validate(UserInterface $user, mixed $entity, string $action): bool;

}
