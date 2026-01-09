<?php

namespace App\Entity\Workspace;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Entity\EntityWithWorkspaceInterface;
use App\Entity\EntityWithUuidInterface;
use App\Repository\Workspace\NotificationRepository;
use App\Resolver\Workspace\Notification\MarkAllAsReadResolver;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: '`workspace_notification`')]
#[ApiResource(
    operations: [],
    graphQlOperations: [
        new QueryCollection(
            paginationEnabled: true,
            paginationType: 'page',
            security: "is_granted('dbm_admin', object)",
        ),
        new Mutation(
            security: "is_granted('dbm_admin', object)",
            name: 'update'
        ),
        new Mutation(
            resolver: MarkAllAsReadResolver::class,
            args: [],
            security: "is_granted('dbm_admin', object)",
            name: 'allRead'
        ),
    ]
)]
#[ApiFilter(SearchFilter::class,
    properties: [
        'status' => SearchFilterInterface::STRATEGY_EXACT
    ]
)]
class Notification implements EntityWithWorkspaceInterface, EntityWithUuidInterface
{
    const STATUS_READ = 0;
    const STATUS_NEW = 1;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[Ignore]
    private ?Workspace $workspace = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $level = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    private int $status = 1;

    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $external_url = null;

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

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * value '1' - new
     * value '0' - read
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

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

    public function getExternalUrl(): ?string
    {
        return $this->external_url;
    }

    public function setExternalUrl(?string $external_url): self
    {
        $this->external_url = $external_url;

        return $this;
    }
}
