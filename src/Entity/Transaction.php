<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['transaction:read']]),
        new Post(denormalizationContext: ['groups' => ['transaction:write']]),
        new Get(normalizationContext: ['groups' => ['transaction:read', 'transaction:detail']]),
        new Put(denormalizationContext: ['groups' => ['transaction:write']]),
        new Patch(denormalizationContext: ['groups' => ['transaction:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['transaction:read']],
    denormalizationContext: ['groups' => ['transaction:write']]
)]
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_RELEASED = 'released';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['transaction:read', 'ad:detail', 'user:detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Ad::class, inversedBy: 'transactions')]
    #[Assert\NotNull]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?Ad $ad = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'purchases')]
    #[Assert\NotNull]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?User $buyer = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sales')]
    #[Assert\NotNull]
    #[Groups(['transaction:read', 'transaction:write'])]
    private ?User $seller = null;

    #[ORM\Column(type: 'float')]
    #[Assert\Positive]
    #[Groups(['transaction:read', 'transaction:write'])]
    private float $usdtAmount = 0.0;

    #[ORM\Column(type: 'float')]
    #[Assert\Positive]
    #[Groups(['transaction:read', 'transaction:write'])]
    private float $fiatAmount = 0.0;

    #[ORM\Column(length: 20)]
    #[Groups(['transaction:read', 'transaction:write'])]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column]
    #[Groups(['transaction:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['transaction:read'])]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['transaction:read'])]
    private ?\DateTimeImmutable $releasedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getAd(): ?Ad { return $this->ad; }
    public function setAd(?Ad $ad): static { $this->ad = $ad; return $this; }

    public function getBuyer(): ?User { return $this->buyer; }
    public function setBuyer(?User $buyer): static { $this->buyer = $buyer; return $this; }

    public function getSeller(): ?User { return $this->seller; }
    public function setSeller(?User $seller): static { $this->seller = $seller; return $this; }

    public function getUsdtAmount(): float { return $this->usdtAmount; }
    public function setUsdtAmount(float $amount): static { $this->usdtAmount = $amount; return $this; }

    public function getFiatAmount(): float { return $this->fiatAmount; }
    public function setFiatAmount(float $amount): static { $this->fiatAmount = $amount; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getPaidAt(): ?\DateTimeImmutable { return $this->paidAt; }
    public function setPaidAt(?\DateTimeImmutable $paidAt): static { $this->paidAt = $paidAt; return $this; }
    public function getReleasedAt(): ?\DateTimeImmutable { return $this->releasedAt; }
    public function setReleasedAt(?\DateTimeImmutable $releasedAt): static { $this->releasedAt = $releasedAt; return $this; }
}
