<?php

namespace App\Entity\Workspace;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Enums\Workspace\UserRestoreStatusEnum;
use App\Repository\Workspace\UserRestoreRepository;
use App\Resolver\Workspace\RestorePassword\CheckHashResolver;
use App\Resolver\Workspace\RestorePassword\RestorePasswordResolver;
use App\Resolver\Workspace\RestorePassword\SendEmailWithHashResolver;
use Symfony\Component\Uid\Factory\UuidFactory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRestoreRepository::class)]
#[ApiResource(
    operations: [],
    graphQlOperations: [
        new Query(),
        new QueryCollection(
            paginationEnabled: true,
            paginationType: 'page'
        ),
        new Mutation(
            resolver: SendEmailWithHashResolver::class,
            args: [
                'email' => ['type' => 'String!']
            ],
            name: 'sendEmailWithHash'
        ),
        new Mutation(
            resolver: CheckHashResolver::class,
            args: [
                'hash' => ['type' => 'String!']
            ],
            name: 'checkHash'
        ),
        new Mutation(
            resolver: RestorePasswordResolver::class,
            args: [
                'newPassword' => ['type' => 'String!'],
                'confirmPassword' => ['type' => 'String!'],
                'hash' => ['type' => 'String!']
            ],
            name: 'restorePassword'
        ),
        new DeleteMutation(name: 'delete')
    ]
)]
#[ORM\HasLifecycleCallbacks]
class UserRestore
{
    const RESTORE_EXPIRED_PERIOD = 30;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expired_at = null;

    #[ORM\Column(type: 'uuid', unique: true, nullable: true)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = null;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expired_at;
    }

    public function setExpiredAt(?\DateTimeImmutable $expired_at): self
    {
        $this->expired_at = $expired_at;

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

        if (!$this->getStatus()) {
            $this->setStatus(UserRestoreStatusEnum::PENDING->value);
        }
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function setExpiredAtValue(): void
    {
        $expiredAt = new \DateTimeImmutable();
        $expiredAt = $expiredAt->add(new \DateInterval(sprintf('PT%dM', self::RESTORE_EXPIRED_PERIOD)));
        $this->expired_at = $expiredAt;
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
}
