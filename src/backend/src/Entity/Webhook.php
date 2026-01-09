<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Entity\Database\Database;
use App\Entity\Workspace\Workspace;
use App\Enums\Webhook\WebhookStatusEnum;
use App\Repository\WebhookRepository;
use App\Validator\WebhookValidator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WebhookRepository::class)]
#[UniqueEntity('uuid')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [],
    paginationEnabled: false,
    security: "is_granted('dbm_admin', object)",
    graphQlOperations: [
        new Query(
            security: "is_granted('dbm_admin', object)",
        ),
        new QueryCollection(
            paginationEnabled: true,
            paginationType: 'page',
            security: "is_granted('dbm_admin', object)"
        ),
        // todo add check if the provided DB of the provided Workspace
        new Mutation(
            security: "is_granted('dbm_admin', object)",
            name: 'create'
        ),
        new Mutation(
            security: "is_granted('dbm_admin', object)",
            name: 'update'
        ),
        new DeleteMutation(
            security: "is_granted('dbm_admin', object)",
            name: "delete"
        )
    ]
)]
class Webhook implements EntityWithWorkspaceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\Callback([WebhookValidator::class, 'validateStatus'])]
    private ?string $status = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 255)]
    #[Assert\Callback([WebhookValidator::class, 'validateOperation'])]
    private ?string $operation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(targetEntity: Workspace::class)]
    private ?Workspace $workspace = null;

    #[ORM\ManyToOne(targetEntity: Database::class)]
    private ?Database $database = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $domains = null;

    private ?string $url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): self
    {
        $this->uuid = $uuid;

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

    public function getDatabase(): ?Database
    {
        return $this->database;
    }

    public function setDatabase(?Database $database): self
    {
        $this->database = $database;

        return $this;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at = null): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function generateUid(UuidFactory $uuidFactory): void
    {
        if (!$this->getUuid()) {
            $this->setUuid($uuidFactory->create());
        }
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

    public function getIsActive(): bool
    {
        return $this->getStatus() === WebhookStatusEnum::ENABLED->value;
    }

    public function getDomains(): ?string
    {
        return $this->domains;
    }

    public function setDomains(?string $domains): self
    {
        $this->domains = $domains;

        return $this;
    }
}
