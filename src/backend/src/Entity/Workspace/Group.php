<?php

namespace App\Entity\Workspace;

use AllowDynamicProperties;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Entity\Database\Database;
use App\Entity\EntityWithUuidInterface;
use App\Entity\EntityWithWorkspaceInterface;
use App\Repository\Workspace\GroupRepository;
use App\Resolver\Workspace\Group\SaveGroupResolver;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`workspace_group`')]
#[ApiResource(
    operations: [],
    security: "is_granted('dbm_admin', object)",
    graphQlOperations: [
        new Query(),
        new QueryCollection(
            paginationEnabled: true,
            paginationType: 'page',
            security: "is_granted('dbm_admin', object)",
        ),
        new Mutation(
            security: "is_granted('dbm_admin', object)",
            name: 'create'
        ),
        new Mutation(
            resolver: SaveGroupResolver::class,
            security: "is_granted('dbm_admin', object)",
            name: 'update'
        ),
        new DeleteMutation(name: 'delete')
    ],
)]
class Group implements EntityWithWorkspaceInterface, EntityWithUuidInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ApiProperty(identifier: false)]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workspace_groups')]
    #[Ignore]
    private ?Workspace $workspace = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $permission = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'groups')]
    #[ORM\JoinTable(name: 'user_group')]
    private Collection $users;

    #[ORM\ManyToMany(targetEntity: Database::class, inversedBy: 'groups')]
    #[ORM\JoinTable(name: 'user_group_database')]
    private Collection $databases;

    #[ORM\Column(type: 'uuid', unique: true, nullable: true)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    private ?bool $forceRemoveFlag = false;

    public function __construct(
    ) {
        $this->users = new ArrayCollection();
        $this->databases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPermission(): ?int
    {
        return $this->permission;
    }

    public function setPermission(int $permission): self
    {
        $this->permission = $permission;

        return $this;
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

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(?Uuid $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }
        return $this;
    }

    public function getDatabases(): Collection
    {
        return $this->databases;
    }

    public function addDatabase(Database $database): self
    {
        if (!$this->databases->contains($database)) {
            $this->databases[] = $database;
        }
        return $this;
    }

    public function removeDatabase(Database $database): self
    {
        if ($this->databases->contains($database)) {
            $this->databases->removeElement($database);
        }
        return $this;
    }

    /**
     * @param bool|null $forceRemoveFlag
     * @return $this
     */
    public function setForceRemoveFlag(?bool $forceRemoveFlag): self
    {
        $this->forceRemoveFlag = $forceRemoveFlag;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getForceRemoveFlag(): ?bool
    {
        return $this->forceRemoveFlag;
    }

    public function __toString(): string
    {
        return sprintf("%s (%s)", $this->name, $this->workspace->getName());
    }
}
