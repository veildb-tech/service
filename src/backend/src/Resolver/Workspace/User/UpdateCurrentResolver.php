<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\User;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\Workspace\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[ApiResource]
final readonly class UpdateCurrentResolver implements MutationResolverInterface
{
    /**
     * @param TokenStorageInterface $tokenStorage
     * @param UserRepository $userRepository
     */
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @param object|null $item
     * @param array $context
     * @return object
     */
    public function __invoke(?object $item, array $context): object
    {
        $currentUserEmail = $this->tokenStorage->getToken()->getUser()->getUserIdentifier();
        if (!$currentUserEmail) {
            throw new AccessDeniedException();
        }

        $user = $this->userRepository->findOneBy(['email' => $currentUserEmail]);

        foreach ($context['args']['input'] as $field => $value) {
            $method = 'set' . ucfirst($field);
            $user->{$method}($value);
        }

        $this->userRepository->save($user);

        return $user;
    }
}
