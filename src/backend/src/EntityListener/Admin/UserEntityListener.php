<?php

declare(strict_types=1);

namespace App\EntityListener\Admin;

use App\Entity\Admin\User as AdminUser;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: AdminUser::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: AdminUser::class)]
class UserEntityListener
{
    public function __construct(
        protected UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function prePersist(AdminUser $user, PrePersistEventArgs $event): void
    {
        if ($this->userPasswordHasher->needsRehash($user)) {
            $password = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
        }
    }

    public function preUpdate(AdminUser $user, PreUpdateEventArgs $event): void
    {
        if ($this->userPasswordHasher->needsRehash($user)) {
            $password = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
        }
    }
}
