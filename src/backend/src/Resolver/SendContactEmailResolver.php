<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Exception\EmailSendException;
use App\Exception\ValidationException;
use App\Service\Mailer;
use App\Service\Workspace\GetSelectedWorkspace;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[ApiResource]
final readonly class SendContactEmailResolver implements MutationResolverInterface
{
    /**
     * TODO: replace to some configurations
     */
    const SUPPORT_EMAIL = 'support@dbvisor.pro';

    /**
     * @param Security $security
     * @param Mailer $mailer
     * @param GetSelectedWorkspace $getSelectedWorkspace
     */
    public function __construct(
        private Security $security,
        private Mailer $mailer,
        private GetSelectedWorkspace $getSelectedWorkspace
    ) {
    }

    /**
     * @param object|null $item
     * @param array $context
     * @return object
     * @throws TransportExceptionInterface
     * @throws EmailSendException
     */
    public function __invoke(?object $item, array $context): object
    {
        $user = $this->security->getUser();
        $currentWorkspace = $this->getSelectedWorkspace->execute();

        $data = $context['args']['input'];
        if (empty($data['subject']) || empty($data['message'])) {
            throw new ValidationException("Subject and message fields are required");
        }

        try {
            $email = $this->mailer->getMailer()
                ->to(self::SUPPORT_EMAIL)
                ->htmlTemplate('emails/support.html.twig')
                ->subject(sprintf(
                    "New support ticket from %s (%s)",
                    $user->getEmail(),
                    $currentWorkspace->getCode()
                ))
                ->context([
                    'workspace' => $currentWorkspace,
                    'user' => $user,
                    'subject' => $data['subject'],
                    'message' => nl2br(htmlspecialchars($data['message']))
                ]);

            $this->mailer->send($email);
        } catch (\Exception $exception) {
            throw new EmailSendException("Something went wrong");
        }

        return $item;
    }
}
