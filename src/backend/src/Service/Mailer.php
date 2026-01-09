<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    /**
     * @param MailerInterface $mailer
     * @param string $from
     */
    public function __construct(
        private MailerInterface $mailer,
        private string $from = ''
    ) {
    }

    /**
     * @return TemplatedEmail
     */
    public function getMailer(): TemplatedEmail
    {
        return (new TemplatedEmail())->from($this->from);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(Email $email): void
    {
        $this->mailer->send($email);
    }
}
