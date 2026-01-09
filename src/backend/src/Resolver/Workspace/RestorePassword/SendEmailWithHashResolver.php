<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\RestorePassword;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Workspace\UserRestore;
use App\Enums\Workspace\UserRestoreStatusEnum;
use App\Repository\Workspace\UserRepository;
use App\Repository\Workspace\UserRestoreRepository;
use App\Service\Workspace\SendRestoreEmail;
use App\Exception\NotFoundResourceException;

#[ApiResource]
final readonly class SendEmailWithHashResolver implements MutationResolverInterface
{
    /**
     * @param UserRepository $userRepository
     * @param UserRestoreRepository $userRestoreRepository
     * @param SendRestoreEmail $restoreEmail
     */
    public function __construct(
        private UserRepository        $userRepository,
        private UserRestoreRepository $userRestoreRepository,
        private SendRestoreEmail      $restoreEmail
    )
    {
    }

    /**
     * @param object|null $item
     * @param array $context
     * @return mixed
     * @throws \Exception
     */
    public function __invoke(?object $item, array $context): object
    {
        $userEmail = $context['args']['input']['email'];

        $user = $this->userRepository->findOneBy(['email' => $userEmail]);
        if (!$user) {
            throw new NotFoundResourceException("There is no user with such email");
        }

        $pendingRestore = $this->userRestoreRepository->findOneBy([
            'email' => $userEmail,
            'status' => UserRestoreStatusEnum::PENDING->value
        ]);
        if ($pendingRestore) {
            $this->userRestoreRepository->remove($pendingRestore);
        }

        $restore = new UserRestore();
        $restore->fillFields();
        $restore->setEmail($userEmail);

        $this->userRestoreRepository->save($restore);

        $this->restoreEmail->sendEmail($restore);
        return $restore;
    }
}
