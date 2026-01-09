<?php

declare(strict_types=1);

namespace App\EntityListener\Workspace;

use App\Entity\Workspace\UserInvitation;
use App\Service\Workspace\SendInvitationEmail;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: UserInvitation::class)]
readonly class SendInvitationEmailListener
{
    /**
     * @param SendInvitationEmail $sendInvitationEmail
     */
    public function __construct(
        private SendInvitationEmail $sendInvitationEmail,
    ) {
    }

    /**
     * @param UserInvitation $userInvitation
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postPersist(UserInvitation $userInvitation, LifecycleEventArgs $event): void
    {
        $this->sendInvitationEmail->sendEmail($userInvitation);
    }
}
