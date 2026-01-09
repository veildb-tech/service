<?php

namespace App\Resolver\Rule;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Database\DatabaseRuleTemplate;
use App\Enums\Database\Rule\TemplateTypeEnum;
use App\Service\Workspace\GetSelectedWorkspace;

#[ApiResource]
final readonly class TemplateResolver implements MutationResolverInterface
{
    /**
     * @param GetSelectedWorkspace $getSelectedWorkspace
     */
    public function __construct(private GetSelectedWorkspace $getSelectedWorkspace)
    {}

    /**
     * @param DatabaseRuleTemplate|null $item
     * @param array $context
     * @return object|null
     */
    public function __invoke(?object $item, array $context): ?object
    {
        // By mutation only custom type is available
        $workspace = $this->getSelectedWorkspace->execute();
        if ($workspace) {
            $item->setWorkspace($workspace);
        }
        $item->setType(TemplateTypeEnum::CUSTOM->value);
        return $item;
    }
}
