<?php

declare(strict_types=1);

namespace App\Entity\Database;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\Database\DatabaseDumpDeleteRulesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DatabaseDumpDeleteRulesRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'database:read']),
        new GetCollection(normalizationContext: ['groups' => 'database:read'])
    ],
    paginationEnabled: false,
    security: "is_granted('dbm_edit', object)",
)]
class DatabaseDumpDeleteRules
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'boolean', options: [ 'default' => 0 ])]
    private bool $status = false;

    #[ORM\Column(nullable: true)]
    private array $rule = [];

    #[ORM\ManyToOne(inversedBy: 'databaseDatabaseDumpDeleteRules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Database $db = null;

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

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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

    public function getDb(): ?Database
    {
        return $this->db;
    }

    public function setDb(?Database $db): self
    {
        $this->db = $db;

        return $this;
    }
}
