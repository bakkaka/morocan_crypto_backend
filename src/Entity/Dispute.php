<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\DisputeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['dispute:read']]),
        new Post(denormalizationContext: ['groups' => ['dispute:write']]),
        new Get(normalizationContext: ['groups' => ['dispute:read', 'dispute:detail']]),
        new Put(denormalizationContext: ['groups' => ['dispute:write']]),
        new Patch(denormalizationContext: ['groups' => ['dispute:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['dispute:read']],
    denormalizationContext: ['groups' => ['dispute:write']]
)]
#[ORM\Entity(repositoryClass: DisputeRepository::class)]
class Dispute
{
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_RESOLVED = 'resolved';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['dispute:read', 'transaction:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Transaction::class)]
    #[Assert\NotNull]
    #[Groups(['dispute:read', 'dispute:write'])]
    private ?Transaction $transaction = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Assert\NotNull]
    #[Groups(['dispute:read', 'dispute:write'])]
    private ?User $openedBy = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['dispute:read', 'dispute:write'])]
    private ?string $reason = null;

    #[ORM\Column(length: 20)]
    #[Groups(['dispute:read', 'dispute:write'])]
    private string $status = self::STATUS_OPEN;

    #[ORM\Column]
    #[Groups(['dispute:read'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTransaction(): ?Transaction { return $this->transaction; }
    public function setTransaction(?Transaction $t): static { $this->transaction = $t; return $this; }

    public function getOpenedBy(): ?User { return $this->openedBy; }
    public function setOpenedBy(?User $u): static { $this->openedBy = $u; return $this; }

    public function getReason(): ?string { return $this->reason; }
    public function setReason(string $r): static { $this->reason = $r; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): static { $this->status = $s; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
