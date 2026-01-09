<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\RestorePassword;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Enums\Workspace\UserRestoreStatusEnum;
use App\Repository\Workspace\UserRestoreRepository;
use ApiPlatform\Validator\Exception\ValidationException;

#[ApiResource]
final readonly class CheckHashResolver implements MutationResolverInterface
{
    /**
     * @param UserRestoreRepository $userRestoreRepository
     */
    public function __construct(
        private UserRestoreRepository $userRestoreRepository
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
        $hash = $context['args']['input']['hash'];
        try {
            $pendingRestore = $this->userRestoreRepository->findOneBy([
                'uuid' => $hash,
                'status' => UserRestoreStatusEnum::PENDING->value
            ]);
        } catch (\Exception $exception) {
            throw new ValidationException('Hash was not found');
        }

        if (!$pendingRestore) {
            throw new ValidationException('Hash was not found');
        }

        if(new \DateTimeImmutable() > $pendingRestore->getExpiredAt()) {
            throw new ValidationException('Hash already expired');
        }

        return $pendingRestore;
    }
}
