<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\RestorePassword;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Enums\Workspace\UserRestoreStatusEnum;
use App\Repository\Workspace\UserRepository;
use App\Repository\Workspace\UserRestoreRepository;
use App\Exception\ValidationException;

#[ApiResource]
final readonly class RestorePasswordResolver implements MutationResolverInterface
{
    /**
     * @param UserRestoreRepository $userRestoreRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        private UserRestoreRepository $userRestoreRepository,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @param object|null $item
     * @param array $context
     * @return mixed
     * @throws \Exception
     */
    public function __invoke(?object $item, array $context): object
    {
        $newPassword = $context['args']['input']['newPassword'];
        $confirmPassword = $context['args']['input']['confirmPassword'];
        $hash = $context['args']['input']['hash'];

        if ($newPassword !== $confirmPassword) {
            throw new ValidationException("Passwords don't match");
        }

        $pendingRestore = $this->userRestoreRepository->findOneBy([
            'uuid' => $hash,
            'status' => UserRestoreStatusEnum::PENDING->value
        ]);
        if (!$pendingRestore) {
            throw new ValidationException('Hash not found');
        }

        $user = $this->userRepository->findOneBy(['email' => $pendingRestore->getEmail()]);
        if (!$user) {
            throw new ValidationException('User not found');
        }

        if(new \DateTimeImmutable() > $pendingRestore->getExpiredAt()) {
            throw new ValidationException('Hash already expired');
        }

        $user->setPassword($newPassword);
        $this->userRepository->save($user);
        $pendingRestore->setStatus(UserRestoreStatusEnum::USED->value);
        $this->userRestoreRepository->save($pendingRestore);

        return $pendingRestore;
    }
}
