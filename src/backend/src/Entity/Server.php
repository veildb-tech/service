<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\Database\GetDumpsForDeletingController;
use App\Entity\Database\Database;
use App\Entity\Workspace\Workspace;
use App\Repository\ServerRepository;
use App\Validator\ServerValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Ignore;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;

#[ORM\Entity(repositoryClass: ServerRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Get(
            uriTemplate: '/servers/{uuid}/get_dump_delete_list',
            controller: GetDumpsForDeletingController::class,
            normalizationContext: ['groups' => 'database:read'],
            name: 'get_dump_delete_list'
        ),
        new GetCollection(),
        new Post(),
        new Patch()
    ],
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
class Server implements EntityWithWorkspaceInterface, EntityWithUuidInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'uuid')]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 50)]
    #[Assert\Callback([ServerValidator::class, 'validateStatus'])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'servers')]
    #[Ignore]
    private ?Workspace $workspace = null;

    #[ORM\OneToMany(mappedBy: 'server', targetEntity: Database::class, orphanRemoval: true)]
    private Collection $databases;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secret_key = "";

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ip_address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ping_date = null;

    public function __construct()
    {
        $this->databases = new ArrayCollection();
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

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function generateSecretKey(): void
    {
        if (!$this->getSecretKey()) {
            try {
                $this->setSecretKey(bin2hex(random_bytes(16)));
            } catch (Exception $e) {
                $this->setSecretKey(md5($this->getName() . '-' . $this->getId()));
            }
        }
    }

    /**
     * @return Collection<int, Database>
     */
    public function getDatabases(): Collection
    {
        return $this->databases;
    }

    public function addDatabase(Database $database): self
    {
        if (!$this->databases->contains($database)) {
            $this->databases->add($database);
            $database->setServer($this);
        }

        return $this;
    }

    public function removeDatabase(Database $database): self
    {
        if ($this->databases->removeElement($database)) {
            // set the owning side to null (unless already changed)
            if ($database->getServer() === $this) {
                $database->setServer(null);
            }
        }

        return $this;
    }

    public function getSecretKey(): ?string
    {
        return $this->secret_key;
    }

    public function setSecretKey(?string $secret_key): self
    {
        $this->secret_key = $secret_key;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(?string $ip_address): self
    {
        $this->ip_address = $ip_address;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
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

    public function getPingDate(): ?\DateTimeInterface
    {
        return $this->ping_date;
    }

    public function setPingDate(?\DateTimeInterface $ping_date): self
    {
        $this->ping_date = $ping_date;

        return $this;
    }
}
