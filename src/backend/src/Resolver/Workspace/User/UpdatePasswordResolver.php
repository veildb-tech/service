<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\User;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\Workspace\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Exception\ValidationException;

#[ApiResource]
final readonly class UpdatePasswordResolver implements MutationResolverInterface
{
    /**
     * @param TokenStorageInterface $tokenStorage
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher
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
        $currentUserEmail = $this->tokenStorage->getToken()->getUser()->getUserIdentifier();
        if (!$currentUserEmail) {
            throw new AccessDeniedException();
        }
        $user = $this->userRepository->findOneBy(['email' => $currentUserEmail]);

        $newPassword = $context['args']['input']['newPassword'];
        $confirmPassword = $context['args']['input']['confirmPassword'];
        $oldPassword = $context['args']['input']['oldPassword'];

        if ($newPassword !== $confirmPassword) {
            throw new ValidationException("Passwords don't match");
        }

        if (!$this->userPasswordHasher->isPasswordValid($user, $oldPassword)) {
            throw new ValidationException("Password is not valid");
        }

        $user->setPassword($newPassword);
        $this->userRepository->save($user);

        return $user;
    }
}
