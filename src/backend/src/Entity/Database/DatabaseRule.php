<?php

namespace App\Entity\Database;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use App\Repository\Database\DatabaseRuleRepository;
use Cron\CronExpression;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DatabaseRuleRepository::class)]
#[ORM\Table(name: '`database_rule`')]
#[UniqueEntity('uuid')]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => 'database:read',
            ]
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'database:read'],
        )
    ],
    paginationEnabled: false,
    security: "is_granted('dbm_edit', object)",
    graphQlOperations: [
        new Query(
            security: "is_granted('dbm_admin', object)",
        ),
        new QueryCollection(
            paginationEnabled: true,
            paginationType: 'page',
            security: "is_granted('dbm_edit', object)"
        ),
        new Mutation(
            security: "is_granted('dbm_edit', object)",
            name: 'create'
        ),
        new Mutation(
            security: "is_granted('dbm_edit', object)",
            name: 'update'
        ),
        new DeleteMutation(
            security: "is_granted('dbm_edit', object)",
            name: "delete"
        )
    ],
)]
class DatabaseRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['databaseDump:read'])]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['database:read'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['database:read'])]
    private array $rule = [];

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['database:read'])]
    private ?string $schedule = null;

    #[ORM\OneToOne(inversedBy: 'databaseRule')]
    #[Groups(['database:read'])]
    private ?Database $db = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['database:read'])]
    private ?array $addition = [];

    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['databaseDump:read', 'database:read', "Group"])]
    private ?Uuid $uuid = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['database:read'])]
    private ?int $schedule_type = null;

    #[ORM\OneToOne()]
    #[Groups(['databaseDump:read', 'database:read', "Group"])]
    private ?DatabaseRuleTemplate $template = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function __toString(): string
    {
        return $this->name;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function getScheduleExpression(): ?CronExpression
    {
        if (!$this->getSchedule()) {
            return null;
        }

        return new CronExpression($this->getSchedule());
    }

    public function setSchedule(string $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getDb(): ?Database
    {
        return $this->db;
    }

    public function setDb(?Database $db): self
    {
        $this->db = $db;

        return $this;
    }

    public function getAddition(): ?array
    {
        return $this->addition;
    }

    public function setAddition(?array $addition): self
    {
        $this->addition = $addition;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(?Uuid $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function generateUuid(UuidFactory $uuidFactory): void
    {
        if (!$this->getUuid()) {
            $this->setUuid($uuidFactory->create());
        }
    }

    public function getScheduleType(): ?int
    {
        return $this->schedule_type;
    }

    public function setScheduleType(?int $schedule_type): self
    {
        $this->schedule_type = $schedule_type;

        return $this;
    }

    public function getTemplate(): ?DatabaseRuleTemplate
    {
        return $this->template;
    }

    public function setTemplate(?DatabaseRuleTemplate $template): self
    {
        $this->template = $template;

        return $this;
    }
}
