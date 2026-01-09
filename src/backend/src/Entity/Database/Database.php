<?php

declare(strict_types=1);

namespace App\Entity\Database;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\EntityWithUuidInterface;
use App\Entity\EntityWithWorkspaceInterface;
use App\Entity\Server;
use App\Entity\Webhook;
use App\Entity\Workspace\Group;
use App\Entity\Workspace\Workspace;
use App\Repository\Database\DatabaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Factory\UuidFactory;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Ignore;
#[ORM\Entity(repositoryClass: DatabaseRepository::class)]
#[ORM\Table(name: '`database`')]
#[UniqueEntity('uid')]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'database:read']),
        new GetCollection(normalizationContext: ['groups' => 'database:read']),
        new Post(
            security: "is_granted('dbm_edit', object)"
        ),
        new Patch(
            security: "is_granted('dbm_edit', object)"
        )
    ],
    paginationEnabled: false,
    graphQlOperations: [
        new Query(
            security: "is_granted('dbm_read', object)",
        ),
        new QueryCollection(
            paginationEnabled: true,
            paginationType: 'page',
//            securityPostDenormalize: "is_granted('dbm_read', object)"
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
    ]
)]
class Database implements EntityWithWorkspaceInterface, EntityWithUuidInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(type: 'text', length: 255)]
    #[Groups(['database:read'])]
    private ?string $name = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['databaseDump:read', 'database:read', "Group"])]
    private ?Uuid $uid = null;

    #[ORM\OneToMany(mappedBy: 'db', targetEntity: DatabaseDump::class, orphanRemoval: true)]
    #[Groups(['database:read'])]
    private Collection $databaseDumps;

    #[ORM\ManyToOne(inversedBy: 'databases')]
    #[Ignore]
    private ?Workspace $workspace = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['database:read'])]
    private ?string $db_schema = null;

    #[ORM\Column(type: 'text', length: 255)]
    #[Groups(['database:read'])]
    private ?string $status = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['database:read'])]
    private ?string $engine = null;

    #[ORM\ManyToOne(inversedBy: 'databases')]
    #[Groups(['database:read', 'databaseDump:read'])]
    private ?Server $server = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['database:read'])]
    private ?string $platform = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['database:read'])]
    private ?string $additional_data = null;

    #[ORM\Column]
    #[Groups(['database:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['database:read'])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'databases')]
    private Collection $groups;

    #[ORM\OneToMany(mappedBy: 'db', targetEntity: DatabaseDumpDeleteRules::class, orphanRemoval: true)]
    #[Groups(['database:read'])]
    private Collection $databaseDumpDeleteRules;

    #[ORM\OneToOne(mappedBy: 'db')]
    #[Groups(['database:read'])]
    private ?DatabaseRule $databaseRule = null;

    #[ORM\OneToMany(mappedBy: 'db', targetEntity: DatabaseRuleSuggestion::class, orphanRemoval: true)]
    #[Groups(['database:read'])]
    private Collection $databaseRuleSuggestions;

    #[ORM\OneToMany(mappedBy: 'database', targetEntity: Webhook::class, orphanRemoval: true)]
    private ?Collection $webhooks = null;

    public function __construct()
    {
        $this->databaseDumps = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->databaseDumpDeleteRules = new ArrayCollection();
        $this->databaseRuleSuggestions = new ArrayCollection();
    }

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

    /**
     * @deprecated
     *
     * @return Uuid|null
     */
    public function getUid(): ?Uuid
    {
        return $this->uid;
    }

    /**
     * @deprecated
     *
     * @param Uuid $uid
     * @return $this
     */
    public function setUid(Uuid $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function generateUid(UuidFactory $uuidFactory): void
    {
        if (!$this->getUid()) {
            $this->setUid($uuidFactory->create());
        }
    }

    public function setUuid(?Uuid $uuid): self
    {
        $this->uid = $uuid;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uid;
    }

    /**
     * @return Collection<int, DatabaseDump>
     */
    public function getDatabaseDumps(): Collection
    {
        return $this->databaseDumps;
    }

    public function addDatabaseDump(DatabaseDump $databaseDump): self
    {
        if (!$this->databaseDumps->contains($databaseDump)) {
            $this->databaseDumps->add($databaseDump);
            $databaseDump->setDb($this);
        }

        return $this;
    }

    public function removeDatabaseDump(DatabaseDump $databaseDump): self
    {
        if ($this->databaseDumps->removeElement($databaseDump)) {
            // set the owning side to null (unless already changed)
            if ($databaseDump->getDb() === $this) {
                $databaseDump->setDb(null);
            }
        }

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

    public function getDbSchema(): ?string
    {
        return $this->db_schema;
    }

    public function setDbSchema(string $db_schema): self
    {
        $this->db_schema = $db_schema;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEngine(): ?string
    {
        return $this->engine;
    }

    public function setEngine(?string $engine): self
    {
        $this->engine = $engine;

        return $this;
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function setServer(?Server $server): self
    {
        $this->server = $server;

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

    public function getAdditionalData(): ?string
    {
        return $this->additional_data;
    }

    public function setAdditionalData(?string $additional_data): self
    {
        $this->additional_data = $additional_data;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addDatabase($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeDatabase($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DatabaseDumpDeleteRules>
     */
    public function getDatabaseDumpDeleteRules(): Collection
    {
        return $this->databaseDumpDeleteRules;
    }

    public function addDatabaseDumpDeleteRule(DatabaseDumpDeleteRules $databaseDumpDeleteRule): self
    {
        if (!$this->databaseDumpDeleteRules->contains($databaseDumpDeleteRule)) {
            $this->databaseDumpDeleteRules->add($databaseDumpDeleteRule);
            $databaseDumpDeleteRule->setDbId($this);
        }

        return $this;
    }

    public function removeDatabaseDumpDeleteRule(DatabaseDumpDeleteRules $databaseDumpDeleteRules): self
    {
        if ($this->databaseDumpDeleteRules->removeElement($databaseDumpDeleteRules)) {
            // set the owning side to null (unless already changed)
            if ($databaseDumpDeleteRules->getDbId() === $this) {
                $databaseDumpDeleteRules->setDbId(null);
            }
        }

        return $this;
    }

    public function getDatabaseRule(): ?DatabaseRule
    {
        return $this->databaseRule;
    }

    public function setDatabaseRule(?DatabaseRule $databaseRule): self
    {
        // unset the owning side of the relation if necessary
        if ($databaseRule === null && $this->databaseRule !== null) {
            $this->databaseRule->setDb(null);
        }

        // set the owning side of the relation if necessary
        if ($databaseRule !== null && $databaseRule->getDb() !== $this) {
            $databaseRule->setDb($this);
        }

        $this->databaseRule = $databaseRule;

        return $this;
    }

    /**
     * @return Collection<int, DatabaseRuleSuggestion>
     */
    public function getDatabaseRuleSuggestions(): Collection
    {
        return $this->databaseRuleSuggestions;
    }

    public function addDatabaseRuleSuggestion(DatabaseRuleSuggestion $databaseRuleSuggestion): self
    {
        if (!$this->databaseRuleSuggestions->contains($databaseRuleSuggestion)) {
            $this->databaseRuleSuggestions->add($databaseRuleSuggestion);
            $databaseRuleSuggestion->setDb($this);
        }

        return $this;
    }

    public function removeDatabaseRuleSuggestion(DatabaseRuleSuggestion $databaseRuleSuggestion): self
    {
        if ($this->databaseRuleSuggestions->removeElement($databaseRuleSuggestion)) {
            // set the owning side to null (unless already changed)
            if ($databaseRuleSuggestion->getDb() === $this) {
                $databaseRuleSuggestion->setDb(null);
            }
        }

        return $this;
    }
}
