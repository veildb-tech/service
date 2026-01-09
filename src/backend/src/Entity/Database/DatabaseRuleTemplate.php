<?php

namespace App\Entity\Database;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Workspace\Workspace;
use App\Repository\Database\DatabaseRuleTemplateRepository;
use App\Resolver\Rule\TemplateResolver;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;

#[ORM\Entity(repositoryClass: DatabaseRuleTemplateRepository::class)]
#[ApiResource(
    operations: [],
    paginationEnabled: false,
    security: "is_granted('dbm_edit', object)",
    graphQlOperations: [
        new Query(
            security: "is_granted('dbm_read', object)",
        ),
        new QueryCollection(
            security: "is_granted('dbm_read', object)",
        ),
        new Mutation(
            resolver: TemplateResolver::class,
            args: [
                'name' => ['type' => 'String!'],
                'rule' => ['type' => 'Iterable!']
            ],
            name: 'create'
        ),
        new Mutation(
            resolver: TemplateResolver::class,
            args: [
                'id' => ['type' => 'ID!'],
                'name' => ['type' => 'String!'],
                'rule' => ['type' => 'Iterable!']
            ],
            name: 'update'
        )
    ]
)]
#[ApiFilter(NumericFilter::class, properties: ['type'])]
class DatabaseRuleTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private array $rule = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\ManyToOne(inversedBy: 'databaseRuleTemplates')]
    private ?Workspace $workspace = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $platform = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRule(): array
    {
        return $this->rule;
    }

    public function setRule(?array $rule): self
    {
        $this->rule = $rule;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(?Workspace $workspace): self
    {
        $this->workspace = $workspace;

        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(?string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }
}
