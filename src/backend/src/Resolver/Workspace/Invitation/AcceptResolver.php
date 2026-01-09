<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\Invitation;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Workspace\UserInvitation;
use App\Enums\Workspace\UserInvitationStatusEnum;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use App\Service\Workspace\GetSelectedWorkspace;
use App\Service\Workspace\AcceptInvitation;
use App\Exception\ValidationException;

#[ApiResource]
final readonly class AcceptResolver implements MutationResolverInterface
{

    public function __construct(private AcceptInvitation $acceptInvitationService) {

    }

    /**
     * Automatically save selected workspace to object if it is empty
     *
     * @param object|null $item
     * @param array $context
     * @return object
     * @throws Exception
     */
    public function __invoke(?object $item, array $context): object
    {
        /** @var UserInvitation $item */
        if ($item->getStatus() !== UserInvitationStatusEnum::PENDING->value) {
            $this->validateStatus($item);
        }
        $this->acceptInvitationService->execute($item);
        return $item;
    }

    /**
     * @param UserInvitation $invitation
     * @return never
     */
    public function validateStatus(UserInvitation $invitation): never
    {
        if ($invitation->getStatus() === UserInvitationStatusEnum::ACCEPTED->value) {
            throw new ValidationException("This invitation is accepted");
        }

        if ($invitation->getStatus() === UserInvitationStatusEnum::EXPIRED->value) {
            throw new ValidationException("This invitation is expired");
        }

        if ($invitation->getStatus() === UserInvitationStatusEnum::DECLINED->value) {
            throw new ValidationException("This invitation is canceled");
        }
        throw new ValidationException("Something went wrong");
    }
}
