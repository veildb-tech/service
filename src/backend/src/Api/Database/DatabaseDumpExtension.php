<?php

declare(strict_types=1);

namespace App\Api\Database;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Database\DatabaseRuleTemplate;
use App\Enums\Database\DumpStatusEnum;
use App\Enums\Database\Rule\TemplateTypeEnum;
use App\Service\Url;
use Doctrine\ORM\QueryBuilder;
use App\Service\Workspace\GetSelectedWorkspace;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class DatabaseDumpExtension implements QueryCollectionExtensionInterface
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     * @param Security $security
     */
    public function __construct(
        private GetSelectedWorkspace $getSelectedWorkspace,
        private Security $security
    ) {
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param Operation|null $operation
     * @param array $context
     * @return void
     * @throws \Exception
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        return;
        $user = $this->security->getUser();
        if (
            !empty($context['filters'])
            && $context['filters']['status'] === DumpStatusEnum::SCHEDULED->value
            && $user->getApiWorkspaceCode()
        ) {
            // expects that for API request user has identifier like <server_uuid>@<domain>
            list($serverUuid) = explode('@', $user->getUserIdentifier());

        }

        var_dump($_COOKIE);
        $token = $this->security->getToken();
        var_dump($user->getUserIdentifier());
        exit;
//        if ($resourceClass === DatabaseRuleTemplate::class) {
//            $this->addTypeAndWorkspaceFilter($queryBuilder, $resourceClass);
//        }
    }
}
