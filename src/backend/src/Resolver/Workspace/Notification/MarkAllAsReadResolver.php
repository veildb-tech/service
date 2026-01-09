<?php
declare(strict_types=1);

namespace App\Resolver\Workspace\Notification;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Service\Workspace\Notification\MarkAllAsRead;

#[ApiResource]
final readonly class MarkAllAsReadResolver implements MutationResolverInterface
{
    /**
     * @param MarkAllAsRead $markAllAsReadService
     */
    public function __construct(private MarkAllAsRead $markAllAsReadService) {
    }

    /**
     * Automatically save selected workspace to object if it is empty
     *
     * @param object|null $item
     * @param array $context
     * @return object
     * @throws \Exception
     */
    public function __invoke(?object $item, array $context): null
    {
        $this->markAllAsReadService->execute();
        return null;
    }
}
