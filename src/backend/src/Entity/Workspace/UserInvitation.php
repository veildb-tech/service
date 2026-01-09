<?php

namespace App\Entity\Workspace;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Entity\EntityWithUuidInterface;
use App\Entity\EntityWithWorkspaceInterface;
use App\Repository\Workspace\UserInvitationRepository;
use App\Resolver\Workspace\Invitation\AcceptResolver;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserInvitationRepository::class)]
#[ApiResource(
    operations: [],
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
            security: "is_granted('dbm_admin', object)",
            name: 'update'
        ),
        new Mutation(
            resolver: AcceptResolver::class,
            args: [
                'id' => ['type' => 'ID!']
            ],
            name: 'accept'
        )
    ]
)]
#[ApiFilter(SearchFilter::class,
    properties: [
        'status' => SearchFilterInterface::STRATEGY_EXACT
    ]
)]
#[ApiFilter(OrderFilter::class,
    properties: [
        'created_at' => 'DESC'
    ]
)]
class UserInvitation implements EntityWithWorkspaceInterface, EntityWithUuidInterface
{
    // 3 days
    const EXPIRATION_PERIOD = 259200;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'userInvitations')]
    private ?Workspace $workspace = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private array $invitation_groups = [];

    #[ORM\Column(type: 'uuid', unique: true, nullable: true)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = null;

    private ?string $url = null;
    private ?\DateTimeImmutable $expiration_date = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(?Workspace $workspace): self
    {
        $this->workspace = $workspace;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getInvitationGroups(): array
    {
        return $this->invitation_groups;
    }

    public function setInvitationGroups(?array $invitation_groups): self
    {
        $this->invitation_groups = $invitation_groups;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(?\DateTimeImmutable $expirationDate): self
    {
        $this->expiration_date = $expirationDate;
        return $this;
    }
}
