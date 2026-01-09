<?php

declare(strict_types=1);

namespace App\Service\Workspace;

use App\Entity\Workspace\UserInvitation;
use App\Service\Logger;
use App\Service\Mailer;
use App\Service\Url;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

readonly class SendInvitationEmail
{
    /**
     * @param Mailer $mailer
     * @param Security $security
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param Url $urlService
     * @param Logger $logger
     */
    public function __construct(
        private Mailer $mailer,
        private Security $security,
        private GetSelectedWorkspace $getSelectedWorkspace,
        private Url $urlService,
        private Logger $logger
    ) {
    }

    /**
     * @param UserInvitation $invitation
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendEmail(UserInvitation $invitation): void
    {
        $user = $this->security->getUser();
        $workspace = $this->getSelectedWorkspace->execute();
        $url = $this->urlService->getUrl('auth/invitation', ['invitation' => $invitation->getUuid()]);

        try {
            $email = $this->mailer->getMailer()
                ->to($invitation->getEmail())
                ->htmlTemplate('emails/user/invitation.html.twig')
                ->subject(sprintf('%s invited you to join DB Manager service', $user->getFullName()))
                ->context([
                    'workspace' => $workspace,
                    'user' => $user,
                    'url' => $url
                ]);

            $this->mailer->send($email);
        } catch (\Exception $exception) {
            $this->logger->log(
                sprintf("Something went wrong sending email. Error: %s", $exception->getMessage()),
                'error'
            );
        }
    }
}
