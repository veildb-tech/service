<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\User;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Exception\OperationDeniedException;
use App\Repository\Workspace\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

#[ApiResource]
final readonly class DeleteCurrentResolver implements MutationResolverInterface
{
    /**
     * @param UserRepository $userRepository
     * @param Security $security
     */
    public function __construct(
        private UserRepository $userRepository,
        private Security $security,
    ) {
    }

    /**
     * Allow to remove current user
     *
     * @param object|null $item
     * @param array $context
     * @return object|null
     * @throws OperationDeniedException
     */
    public function __invoke(?object $item, array $context): ?object
    {
        $user = $this->security->getUser();
        if ($user->getId() === $item->getId()) {
            $this->security->logout(false);
            $this->userRepository->remove($item, true);
            return null;
        }

        throw new OperationDeniedException("Operation denied");
    }
}
