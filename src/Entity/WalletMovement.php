<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\WalletMovementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['wm:read']]),
        new Post(denormalizationContext: ['groups' => ['wm:write']]),
        new Get(normalizationContext: ['groups' => ['wm:read', 'wm:detail']]),
        new Put(denormalizationContext: ['groups' => ['wm:write']]),
        new Patch(denormalizationContext: ['groups' => ['wm:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['wm:read']],
    denormalizationContext: ['groups' => ['wm:write']]
)]
#[ORM\Entity(repositoryClass: WalletMovementRepository::class)]
class WalletMovement
{
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['wm:read', 'user:detail', 'transaction:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Assert\NotNull]
    #[Groups(['wm:read', 'wm:write'])]
    private ?User $user = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotNull]
    #[Groups(['wm:read', 'wm:write'])]
    private float $amount = 0.0;

    #[ORM\Column(length: 10)]
    #[Assert\Choice(choices: [self::TYPE_CREDIT, self::TYPE_DEBIT])]
    #[Groups(['wm:read', 'wm:write'])]
    private string $type = self::TYPE_CREDIT;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['wm:read', 'wm:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['wm:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Transaction::class)]
#[Groups(['wm:read', 'wm:write'])]
private ?Transaction $transaction = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getAmount(): float { return $this->amount; }
    public function setAmount(float $amount): static { $this->amount = $amount; return $this; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getTransaction(): ?Transaction { return $this->transaction; }
public function setTransaction(?Transaction $transaction): static {
    $this->transaction = $transaction;
    return $this;
}
}
