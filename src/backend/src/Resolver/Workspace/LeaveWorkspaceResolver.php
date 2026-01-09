<?php

declare(strict_types=1);

namespace App\Resolver\Workspace;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Workspace\Workspace;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[ApiResource]
final readonly class LeaveWorkspaceResolver implements MutationResolverInterface
{

    /**
     * @param Security $security
     * @param IriConverterInterface $iriConverter
     */
    public function __construct(
        private Security $security,
        private IriConverterInterface $iriConverter
    ) {
    }

    /**
     * @param object|null $item
     * @param array $context
     * @return object|null
     */
    public function __invoke(?object $item, array $context): ?object
    {
        $user = $this->security->getUser();
        $userWorkspaces = $user->getWorkspaces();
        $workspace = $this->iriConverter->getResourceFromIri($context['args']['input']['workspace']);
        if (!$workspace) {
            throw new NotFoundHttpException(sprintf('Workspace "%s" not found.', $context['input']['workspace']));
        }

        $canProceed = false;
        foreach ($userWorkspaces as $userWorkspace) {
            if ($userWorkspace->getId() === $workspace->getId()) {
                $canProceed = true;
            }
        }

        /** @var $item Workspace */
        if ($canProceed) {
            $workspace->removeUser($user);
        }

        return $user;
    }
}
