<?php

declare(strict_types=1);

namespace App\Security\Validators;

use App\Entity\Workspace\Workspace;
use App\Security\ValidatePermissions;
use App\Service\Workspace\GetSelectedWorkspace;
use App\Entity\Database\DatabaseDump;
use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\Api\IriConverterInterface;

readonly class DatabaseDumpLogsValidator extends ValidatePermissions
{
    /**
     * @param RequestStack $requestStack
     * @param IriConverterInterface $iriConverter
     * @param GetSelectedWorkspace $getSelectedWorkspace
     */
    public function __construct(
        private RequestStack $requestStack,
        private IriConverterInterface $iriConverter,
        GetSelectedWorkspace $getSelectedWorkspace
    ) {
        parent::__construct($getSelectedWorkspace);
    }

    /**
     * @param mixed $entity
     * @return bool|Workspace
     */
    protected function getWorkspaceFromEntity(mixed $entity): bool | Workspace
    {
        if ($entity === 'create_log') {
            $request = $this->requestStack->getCurrentRequest();

            if ($content = $request->getContent()) {
                $content = json_decode($content);
                if ($content->dumpId) {
                    /** @var DatabaseDump $dump */
                    $dump = $this->iriConverter->getResourceFromIri($content->dumpId);
                    return $dump->getDb()->getWorkspace();
                }
            }
        }

        return false;
    }
}
