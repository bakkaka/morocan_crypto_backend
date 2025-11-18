<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['user:read']]),
        new Post(denormalizationContext: ['groups' => ['user:write']]),
        new Get(normalizationContext: ['groups' => ['user:read', 'user:detail']]),
        new Put(denormalizationContext: ['groups' => ['user:write']]),
        new Patch(denormalizationContext: ['groups' => ['user:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', message: 'Cet email est déjà utilisé.')]
#[UniqueEntity('phone', message: 'Ce numéro de téléphone est déjà utilisé.')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'ad:read', 'transaction:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['user:read', 'user:write', 'user:detail'])]
    private ?string $email = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 6, max: 20)]
    #[Groups(['user:read', 'user:write', 'user:detail'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['user:write'])]
    #[Assert\Length(min: 6, groups: ['user:write'])]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['user:read', 'user:write', 'user:detail', 'ad:read', 'transaction:read'])]
    private ?string $fullName = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:detail'])]
    private bool $isVerified = false;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['user:read', 'user:detail', 'ad:read'])]
    private ?float $reputation = 5.0;

    #[ORM\Column]
    #[Groups(['user:read', 'user:detail'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:detail'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ad::class, cascade: ['persist', 'remove'])]
    #[Groups(['user:detail'])]
    private Collection $ads;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Transaction::class)]
    #[Groups(['user:detail'])]
    private Collection $sales;

    #[ORM\OneToMany(mappedBy: 'buyer', targetEntity: Transaction::class)]
    #[Groups(['user:detail'])]
    private Collection $purchases;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PaymentMethod::class, cascade: ['persist', 'remove'])]
    #[Groups(['user:detail'])]
    private Collection $paymentMethods;

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['user:read', 'user:detail'])]
    private ?string $walletAddress = null;

    public function __construct()
    {
        $this->ads = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(string $phone): static { $this->phone = $phone; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getFullName(): ?string { return $this->fullName; }
    public function setFullName(string $fullName): static { $this->fullName = $fullName; return $this; }

    public function isVerified(): bool { return $this->isVerified; }
    public function setIsVerified(bool $isVerified): static { $this->isVerified = $isVerified; return $this; }

    public function getReputation(): ?float { return $this->reputation; }
    public function setReputation(?float $reputation): static { $this->reputation = $reputation; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    /** @return Collection<int, Ad> */
    public function getAds(): Collection { return $this->ads; }

    public function addAd(Ad $ad): static {
        if (!$this->ads->contains($ad)) {
            $this->ads->add($ad);
            $ad->setUser($this);
        }
        return $this;
    }

    public function removeAd(Ad $ad): static {
        if ($this->ads->removeElement($ad)) {
            if ($ad->getUser() === $this) {
                $ad->setUser(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Transaction> */
    public function getSales(): Collection { return $this->sales; }

    public function addSale(Transaction $sale): static {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
            $sale->setSeller($this);
        }
        return $this;
    }

    public function removeSale(Transaction $sale): static {
        if ($this->sales->removeElement($sale)) {
            if ($sale->getSeller() === $this) {
                $sale->setSeller(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Transaction> */
    public function getPurchases(): Collection { return $this->purchases; }

    public function addPurchase(Transaction $purchase): static {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setBuyer($this);
        }
        return $this;
    }

    public function removePurchase(Transaction $purchase): static {
        if ($this->purchases->removeElement($purchase)) {
            if ($purchase->getBuyer() === $this) {
                $purchase->setBuyer(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, PaymentMethod> */
    public function getPaymentMethods(): Collection { return $this->paymentMethods; }

    public function addPaymentMethod(PaymentMethod $method): static {
        if (!$this->paymentMethods->contains($method)) {
            $this->paymentMethods->add($method);
            $method->setUser($this);
        }
        return $this;
    }

    public function removePaymentMethod(PaymentMethod $method): static {
        if ($this->paymentMethods->removeElement($method)) {
            if ($method->getUser() === $this) {
                $method->setUser(null);
            }
        }
        return $this;
    }

    // --- Security Interface -----
    public function getRoles(): array { return $this->roles; }
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function eraseCredentials(): void {}
    public function getUserIdentifier(): string { return (string) $this->email; }
    public function getUsername(): string { return (string) $this->email; }

    #[Groups(['user:read'])]
    public function getTotalTransactions(): int {
        return $this->sales->count() + $this->purchases->count();
    }

    #[Groups(['user:read', 'user:detail'])]
    public function getSuccessfulTransactions(): int {
        $successful = 0;
        foreach ($this->sales as $transaction) {
            if ($transaction->getStatus() === Transaction::STATUS_COMPLETED) $successful++;
        }
        foreach ($this->purchases as $transaction) {
            if ($transaction->getStatus() === Transaction::STATUS_COMPLETED) $successful++;
        }
        return $successful;
    }

    public function getWalletAddress(): ?string { return $this->walletAddress; }
    public function setWalletAddress(?string $addr): static { $this->walletAddress = $addr; return $this; }
}
