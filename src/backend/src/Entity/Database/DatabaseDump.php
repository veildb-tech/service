<?php

declare(strict_types=1);

namespace App\Entity\Database;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Delete;
use App\Api\Filter\UuidFilter;
use App\Repository\Database\DatabaseDumpRepository;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DatabaseDumpRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'databaseDump:read']),
        new GetCollection(normalizationContext: ['groups' => 'databaseDump:read']),
        new Post(),
        new Patch(),
        new Delete()
    ],
    order: ['updated_at' => 'DESC'],
    paginationEnabled: true,
)]
#[ApiFilter(SearchFilter::class,
    properties: [
        'status' => SearchFilterInterface::STRATEGY_EXACT,
        'db.server' => SearchFilterInterface::STRATEGY_EXACT
    ]
)]
class DatabaseDump
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    #[Groups(['databaseDump:read'])]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    #[ORM\ManyToOne(targetEntity: Database::class, inversedBy: 'databaseDumps')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['databaseDump:read'])]
    #[ApiFilter(UuidFilter::class, properties: ['db.uid' => SearchFilterInterface::STRATEGY_EXACT])]
    private ?Database $db = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['databaseDump:read'])]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['databaseDump:read'])]
    private ?string $filename = null;

    #[ORM\OneToMany(mappedBy: 'dump_id', targetEntity: DatabaseDumpLogs::class, cascade: ["remove"])]
    #[Groups(['databaseDump:read'])]
    private Collection $databaseDumpLogs;

    #[ORM\Column]
    #[Groups(['databaseDump:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['databaseDump:read'])]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $this->databaseDumpLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDb(): ?Database
    {
        return $this->db;
    }

    public function setDb(?Database $db): self
    {
        $this->db = $db;

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

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id . '-' . $this->status;
    }

    /**
     * @return Collection<int, DatabaseDumpLogs>
     */
    public function getDatabaseDumpLogs(): Collection
    {
        return $this->databaseDumpLogs;
    }

    public function addDatabaseDumpLog(DatabaseDumpLogs $databaseDumpLog): self
    {
        if (!$this->databaseDumpLogs->contains($databaseDumpLog)) {
            $this->databaseDumpLogs->add($databaseDumpLog);
            $databaseDumpLog->setDumpId($this);
        }

        return $this;
    }

    public function removeDatabaseDumpLog(DatabaseDumpLogs $databaseDumpLog): self
    {
        if ($this->databaseDumpLogs->removeElement($databaseDumpLog)) {
            // set the owning side to null (unless already changed)
            if ($databaseDumpLog->getDumpId() === $this) {
                $databaseDumpLog->setDumpId(null);
            }
        }

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function generateUuid(): void
    {
        if (!$this->getUuid()) {
            $uuidFactory = new UuidFactory();
            $this->setUuid($uuidFactory->create());
        }
    }
}
