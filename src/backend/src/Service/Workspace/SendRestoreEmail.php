<?php

declare(strict_types=1);

namespace App\Service\Workspace;

use App\Entity\Workspace\UserRestore;
use App\Service\Logger;
use App\Service\Mailer;
use App\Service\Url;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

readonly class SendRestoreEmail
{
    /**
     * @param Mailer $mailer
     * @param Url $urlService
     * @param Logger $logger
     */
    public function __construct(
        private Mailer $mailer,
        private Url $urlService,
        private Logger $logger
    ) {
    }

    /**
     * @param UserRestore $restore
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendEmail(UserRestore $restore): void
    {
        try {
            $url = $this->urlService->getUrl(sprintf("auth/restore-password/%s", $restore->getUuid()));
            $email = $this->mailer->getMailer()
                ->to($restore->getEmail())
                ->htmlTemplate('emails/user/restore.html.twig')
                ->subject('Restore password hash.')
                ->context([
                    'url' => $url,
                    'expiredPeriod' => UserRestore::RESTORE_EXPIRED_PERIOD
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
