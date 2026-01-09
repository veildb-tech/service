<?php

declare(strict_types=1);

namespace App\Entity\Workspace;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Entity\Database\Database;
use App\Entity\Database\DatabaseRuleTemplate;
use App\Entity\Server;
use App\Repository\Workspace\WorkspaceRepository;
use App\Resolver\Workspace\CurrentWorkspace;
use App\Resolver\Workspace\UpdateWorkspace;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkspaceRepository::class)]
#[UniqueEntity(fields: ['token'], message: 'The token field must be unique!')]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('same_workspace', object)",
        )
    ],
    paginationEnabled: false,
    graphQlOperations: [
        new Query(
            security: "is_granted('dbm_read', object)",
        ),
        new QueryCollection(
            security: "is_granted('dbm_read', object)",
        ),
        new Query(
            resolver: CurrentWorkspace::class,
            args: [],
            name: 'current'
        ),
        new Mutation(
            args: [
                'code' => ['type' => 'String'],
                'name' => ['type' => 'String']
            ],
            validate: true,
            name: 'create'
        ),
        new Mutation(
            resolver: UpdateWorkspace::class,
            args: [
                'code' => ['type' => 'String!'],
                'name' => ['type' => 'String']
            ],
            security: "is_granted('dbm_owner', object)",
            name: 'update'
        ),
        new DeleteMutation(
            security: "is_granted('dbm_owner', object)",
            name: "delete",
        )
    ],
)]
class Workspace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ApiProperty(identifier: false)]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["User", "UpdateWorkspace"])]
    #[Assert\Unique]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(["User", "UpdateWorkspace"])]
    #[ApiProperty(identifier: true)]
    #[Assert\Unique]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: Server::class, orphanRemoval: true)]
    private Collection $servers;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: Database::class, orphanRemoval: true)]
    private Collection $databases;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'workspaces', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'workspace_user')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: DatabaseRuleTemplate::class, orphanRemoval: true)]
    private Collection $databaseRuleTemplates;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: Group::class)]
    #[Groups(["User", "Group"])]
    private Collection $workspace_groups;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: UserInvitation::class, orphanRemoval: true)]
    private Collection $userInvitations;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    public function __construct()
    {
        $this->servers = new ArrayCollection();
        $this->databases = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->databaseRuleTemplates = new ArrayCollection();
        $this->workspace_groups = new ArrayCollection();
        $this->userInvitations = new ArrayCollection();
        $this->notifications = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function generateToken(): void
    {
        if (!$this->getToken()) {
            $this->setToken(md5(time() . '_' . $this->getCode() . '_' . $this->getName()));
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Server>
     */
    public function getServers(): Collection
    {
        return $this->servers;
    }

    public function addServer(Server $server): self
    {
        if (!$this->servers->contains($server)) {
            $this->servers->add($server);
            $server->setWorkspace($this);
        }

        return $this;
    }

    public function removeServer(Server $server): self
    {
        if ($this->servers->removeElement($server)) {
            // set the owning side to null (unless already changed)
            if ($server->getWorkspace() === $this) {
                $server->setWorkspace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Server>
     */
    public function getDatabases(): Collection
    {
        return $this->databases;
    }

    public function addDatabase(Database $database): self
    {
        if (!$this->databases->contains($database)) {
            $this->databases->add($database);
            $database->setWorkspace($this);
        }

        return $this;
    }

    public function removeDatabase(Server $database): self
    {
        if ($this->databases->removeElement($database)) {
            // set the owning side to null (unless already changed)
            if ($database->getWorkspace() === $this) {
                $database->setWorkspace(null);
            }
        }

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

    /**
     * @return Collection<int, DatabaseRuleTemplate>
     */
    public function getDatabaseRuleTemplates(): Collection
    {
        return $this->databaseRuleTemplates;
    }

    public function addDatabaseRuleTemplate(DatabaseRuleTemplate $databaseRuleTemplate): self
    {
        if (!$this->databaseRuleTemplates->contains($databaseRuleTemplate)) {
            $this->databaseRuleTemplates->add($databaseRuleTemplate);
            $databaseRuleTemplate->setWorkspace($this);
        }

        return $this;
    }

    public function removeDatabaseRuleTemplate(DatabaseRuleTemplate $databaseRuleTemplate): self
    {
        if ($this->databaseRuleTemplates->removeElement($databaseRuleTemplate)) {
            // set the owning side to null (unless already changed)
            if ($databaseRuleTemplate->getWorkspace() === $this) {
                $databaseRuleTemplate->setWorkspace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getWorkspaceGroups(): Collection
    {
        return $this->workspace_groups;
    }

    public function addWorkspaceGroup(Group $workspaceGroup): self
    {
        if (!$this->workspace_groups->contains($workspaceGroup)) {
            $this->workspace_groups->add($workspaceGroup);
            $workspaceGroup->setWorkspace($this);
        }

        return $this;
    }

    public function removeWorkspaceGroup(Group $workspaceGroup): self
    {
        if ($this->workspace_groups->removeElement($workspaceGroup)) {
            // set the owning side to null (unless already changed)
            if ($workspaceGroup->getWorkspace() === $this) {
                $workspaceGroup->setWorkspace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserInvitation>
     */
    public function getUserInvitations(): Collection
    {
        return $this->userInvitations;
    }

    public function addUserInvitation(UserInvitation $userInvitation): self
    {
        if (!$this->userInvitations->contains($userInvitation)) {
            $this->userInvitations->add($userInvitation);
            $userInvitation->setWorkspace($this);
        }

        return $this;
    }

    public function removeUserInvitation(UserInvitation $userInvitation): self
    {
        if ($this->userInvitations->removeElement($userInvitation)) {
            // set the owning side to null (unless already changed)
            if ($userInvitation->getWorkspace() === $this) {
                $userInvitation->setWorkspace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setWorkspace($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getWorkspace() === $this) {
                $notification->setWorkspace(null);
            }
        }

        return $this;
    }
}
