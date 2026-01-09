<?php

namespace App\Entity\Workspace;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Repository\Workspace\UserRepository;
use App\Resolver\Workspace\User\CreateUserResolver;
use App\Resolver\Workspace\User\CurrentUserResolver;
use App\Resolver\Workspace\User\DeleteCurrentResolver;
use App\Resolver\Workspace\User\EditGroupResolver;
use App\Resolver\Workspace\LeaveWorkspaceResolver;
use App\Resolver\Workspace\User\UpdateCurrentResolver;
use App\Resolver\Workspace\User\UpdatePasswordResolver;
use App\Resolver\Workspace\User\DeleteResolver;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    normalizationContext: ["groups" => ["User"]],
    denormalizationContext: ["groups" => ["User"]],
    paginationEnabled: false,
    // WARNING! There shouldn't be ability to edit user information except current user
    // Every single user should edit personal data by himself.
    // One exception - users could be added or removed from specific group
    graphQlOperations: [
        new Query(),
        new QueryCollection(
            paginationEnabled: true,
            paginationType: 'page',
            security: "is_granted('dbm_admin', object)",
        ),
        new Query(
            resolver: CurrentUserResolver::class,
            args: [],
            name: 'current'
        ),
        new Mutation(
            resolver: CreateUserResolver::class,
//            security: "is_granted('dbm_admin', object)",
            validate: true,
            name: 'create'
        ),
        new Mutation(
            resolver: EditGroupResolver::class,
            args: [
                'id' => ['type' => 'ID!'],
                'updateGroups' => ['type' => 'Iterable!']
            ],
            security: "is_granted('dbm_admin', object)",
            name: 'updateGroup'
        ),
        new Mutation(
            resolver: UpdateCurrentResolver::class,
            args: [
                'firstname' => ['type' => 'String'],
                'lastname' => ['type' => 'String'],
                'email' => ['type' => 'String']
            ],
            name: 'updateCurrent'
        ),
        new Mutation(
            resolver: UpdatePasswordResolver::class,
            args: [
                'newPassword' => ['type' => 'String!'],
                'confirmPassword' => ['type' => 'String!'],
                'oldPassword' => ['type' => 'String!']
            ],
            name: 'updatePassword'
        ),
        new Mutation(
            resolver: DeleteResolver::class,
            args: [
                'id' => ['type' => 'ID!']
            ],
            security: "is_granted('dbm_admin', object)",
            name: 'remove'
        ),
        new Mutation(
            resolver: DeleteCurrentResolver::class,
            args: [
                'id' => ['type' => 'ID!']
            ],
            name: 'removeCurrent'
        ),
        new Mutation(
            resolver: LeaveWorkspaceResolver::class,
            args: [
                'workspace' => ['type' => 'ID!'],
            ],
            name: 'leaveWorkspace',
        ),
    ],
)]
#[ApiFilter(
    SearchFilter::class, properties: ['id' => 'exact', 'email' => 'exact']
)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(["User"])]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Groups(["User"])]
    private ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 100)]
    #[Groups(["User", "user:login"])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(["User", "user:login"])]
    private ?string $password = null;

    #[Groups(["User"])]
    private ?string $invitation = null;

    private ?string $apiWorkspaceCode = null;

    #[ORM\ManyToMany(targetEntity: Workspace::class, mappedBy: 'users', cascade: ['persist'])]
    #[Groups(["User"])]
    private Collection $workspaces;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    #[Groups(["User"])]
    private Collection $groups;

    #[ORM\Column(type: 'uuid', nullable: true)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    public function __construct()
    {
        $this->workspaces = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->getFirstname() . ' '  . $this->getLastname();
    }

    public function setInvitation(?string $invitation): self
    {
        $this->invitation = $invitation;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // guarantee every user at least has WORKSPACE_USER
//        $roles[] = 'ROLE_USER';

        return [];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getWorkspaces(): Collection
    {
        return $this->workspaces;
    }

    public function getApiWorkspaceCode(): ?string
    {
        return $this->apiWorkspaceCode;
    }

    public function setApiWorkspaceCode(?string $apiWorkspaceCode): self
    {
        $this->apiWorkspaceCode = $apiWorkspaceCode;
        return $this;
    }

    public function addWorkspace(Workspace $workspace): self
    {
        if (!$this->workspaces->contains($workspace)) {
            $this->workspaces[] = $workspace;
            $workspace->addUser($this);
        }
        return $this;
    }

    public function removeWorkspace(Workspace $workspace): self
    {
        if ($this->workspaces->contains($workspace)) {
            $this->workspaces->removeElement($workspace);
            $workspace->removeUser($this);
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
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
            $group->addUser($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeUser($this);
        }

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

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function fillFields(): void
    {
        if (!$this->getUuid()) {
            $uuidFactory = new UuidFactory();
            $this->setUuid($uuidFactory->create());
        }
    }
}
