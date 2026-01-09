<?php

namespace App\Entity;

use App\Repository\AccessBackupTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessBackupTokenRepository::class)]
class AccessBackupToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $user_identifier = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expiration_date = null;

    #[ORM\Column(length: 255)]
    private ?string $dump_uid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->user_identifier;
    }

    public function setUserIdentifier(string $user_identifier): self
    {
        $this->user_identifier = $user_identifier;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(\DateTimeInterface $expiration_date): self
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function isValid(): bool
    {
        if (new \DateTime("now") < $this->getExpirationDate()) {
            return true;
        }
        return false;
    }

    public function getDumpUid(): ?string
    {
        return $this->dump_uid;
    }

    public function setDumpUid(string $dump_uid): self
    {
        $this->dump_uid = $dump_uid;

        return $this;
    }
}
