<?php

declare(strict_types=1);

namespace App\Entity\Database;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\Database\DatabaseRuleSuggestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DatabaseRuleSuggestionRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => 'database:read',
            ]
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'database:read'],
        )
    ],
    paginationEnabled: false
)]
class DatabaseRuleSuggestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['database:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'databaseRuleSuggestions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['database:read'])]
    private ?Database $db = null;

    #[ORM\Column]
    #[Groups(['database:read'])]
    private ?int $status = null;

    #[ORM\Column]
    #[Groups(['database:read'])]
    private array $rule = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRule(): array
    {
        return $this->rule;
    }

    public function setRule(array $rule): self
    {
        $this->rule = $rule;

        return $this;
    }
}
