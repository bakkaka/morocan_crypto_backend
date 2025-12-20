<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['ad:read']]),
        new Post(denormalizationContext: ['groups' => ['ad:write']]),
        new Get(normalizationContext: ['groups' => ['ad:read', 'ad:detail']]),
        new Put(denormalizationContext: ['groups' => ['ad:write']]),
        new Patch(denormalizationContext: ['groups' => ['ad:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['ad:read']],
    denormalizationContext: ['groups' => ['ad:write']]
)]
#[ORM\Entity(repositoryClass: AdRepository::class)]
class Ad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ad:read', 'user:detail', 'transaction:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['buy', 'sell'])]
    #[Groups(['ad:read', 'ad:write', 'user:detail'])]
    private ?string $type = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['ad:read', 'ad:write', 'ad:detail'])]
    private ?float $amount = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['ad:read', 'ad:write', 'ad:detail'])]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['ad:read', 'ad:write', 'ad:detail'])]
    private ?string $paymentMethod = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['active', 'paused', 'completed', 'cancelled'])]
    #[Groups(['ad:read', 'ad:write'])]
    private string $status = 'active';

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[Groups(['ad:read', 'ad:write', 'ad:detail'])]
    private ?Currency $currency = null;

    #[ORM\Column]
    #[Groups(['ad:read', 'ad:detail'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['ad:read', 'ad:detail'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ads')]
   #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')] // ← AJOUTER CETTE LIGNE
   #[Groups(['ad:read', 'ad:detail', 'ad:write'])]
   private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'ad', targetEntity: Transaction::class)]
    #[Groups(['ad:detail'])]
    private Collection $transactions;

    #[ORM\ManyToMany(targetEntity: PaymentMethod::class)]
    #[Groups(['ad:read', 'ad:write', 'ad:detail'])]
    private Collection $acceptedPaymentMethods;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\PositiveOrZero]
    #[Groups(['ad:read', 'ad:write', 'ad:detail'])]
    private ?float $minAmountPerTransaction = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\Positive]
    #[Groups(['ad:read', 'ad:write', 'ad:detail'])]
    private ?float $maxAmountPerTransaction = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive]
    #[Groups(['ad:read', 'ad:write'])]
    private int $timeLimitMinutes = 60;

    #[ORM\ManyToMany(targetEntity: UserBankDetail::class, inversedBy: 'adsUsingThisDetail')]
    #[ORM\JoinTable(name: 'ad_accepted_bank_details')]
    #[Groups(['ad:read', 'ad:detail'])]
    private Collection $acceptedBankDetails;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->acceptedPaymentMethods = new ArrayCollection();
        $this->acceptedBankDetails = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters et setters...

    public function getId(): ?int { return $this->id; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getAmount(): ?float { return $this->amount; }
    public function setAmount(float $amount): static { $this->amount = $amount; return $this; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): static { $this->price = $price; return $this; }

    public function getPaymentMethod(): ?string { return $this->paymentMethod; }
    public function setPaymentMethod(string $pm): static { $this->paymentMethod = $pm; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCurrency(): ?Currency { return $this->currency; }
    public function setCurrency(?Currency $currency): static { $this->currency = $currency; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    /** @return Collection<int, Transaction> */
    public function getTransactions(): Collection { return $this->transactions; }

    /** @return Collection<int, PaymentMethod> */
    public function getAcceptedPaymentMethods(): Collection { return $this->acceptedPaymentMethods; }
    public function addAcceptedPaymentMethod(PaymentMethod $method): static {
        if (!$this->acceptedPaymentMethods->contains($method)) {
            $this->acceptedPaymentMethods->add($method);
        }
        return $this;
    }
    public function removeAcceptedPaymentMethod(PaymentMethod $method): static {
        $this->acceptedPaymentMethods->removeElement($method);
        return $this;
    }

    public function getMinAmountPerTransaction(): ?float { return $this->minAmountPerTransaction; }
    public function setMinAmountPerTransaction(?float $minAmountPerTransaction): static {
        $this->minAmountPerTransaction = $minAmountPerTransaction;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getMaxAmountPerTransaction(): ?float { return $this->maxAmountPerTransaction; }
    public function setMaxAmountPerTransaction(?float $maxAmountPerTransaction): static {
        $this->maxAmountPerTransaction = $maxAmountPerTransaction;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getTimeLimitMinutes(): int { return $this->timeLimitMinutes; }
    public function setTimeLimitMinutes(int $timeLimitMinutes): static {
        $this->timeLimitMinutes = $timeLimitMinutes;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /** @return Collection<int, UserBankDetail> */
    public function getAcceptedBankDetails(): Collection { return $this->acceptedBankDetails; }
    public function addAcceptedBankDetail(UserBankDetail $bankDetail): static {
        if (!$this->acceptedBankDetails->contains($bankDetail)) {
            $this->acceptedBankDetails->add($bankDetail);
            $bankDetail->addAdsUsingThisDetail($this);
        }
        return $this;
    }
    public function removeAcceptedBankDetail(UserBankDetail $bankDetail): static {
        if ($this->acceptedBankDetails->removeElement($bankDetail)) {
            $bankDetail->removeAdsUsingThisDetail($this);
        }
        return $this;
    }

    public function acceptsBank(string $bankName): bool {
        foreach ($this->acceptedBankDetails as $bankDetail) {
            if ($bankDetail->getBankName() === $bankName) {
                return true;
            }
        }
        return false;
    }
}