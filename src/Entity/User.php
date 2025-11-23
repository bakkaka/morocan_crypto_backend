<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\UserPasswordHasherProcessor;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("PUBLIC_ACCESS")',
            normalizationContext: ['groups' => ['user:read']]
        ),
        new Post(
            security: 'is_granted("PUBLIC_ACCESS")',
            processor: UserPasswordHasherProcessor::class,
            validationContext: ['groups' => ['Default', 'user:create']],
            denormalizationContext: ['groups' => ['user:create']],
            normalizationContext: ['groups' => ['user:read']]
        ),
        new Get(
            security: 'is_granted("PUBLIC_ACCESS")',
            normalizationContext: ['groups' => ['user:read', 'user:detail']]
        ),
        new Put(
            security: 'is_granted("PUBLIC_ACCESS")',
            processor: UserPasswordHasherProcessor::class,
            denormalizationContext: ['groups' => ['user:update']],
            normalizationContext: ['groups' => ['user:read']]
        ),
        new Patch(
            security: 'is_granted("PUBLIC_ACCESS")',
            processor: UserPasswordHasherProcessor::class,
            denormalizationContext: ['groups' => ['user:update']],
            normalizationContext: ['groups' => ['user:read']]
        ),
        new Delete(
            security: 'is_granted("PUBLIC_ACCESS")'
        )
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', message: 'Cet email est déjà utilisé.')]
#[UniqueEntity('phone', message: 'Ce numéro de téléphone est déjà utilisé.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'ad:read', 'transaction:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['user:read', 'user:create', 'user:update', 'user:detail', 'ad:read', 'transaction:read'])]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Assert\Length(min: 6, groups: ['user:create'])]
    #[Groups(['user:create', 'user:update'])]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['user:read', 'user:create', 'user:update', 'user:detail', 'ad:read', 'transaction:read'])]
    private ?string $fullName = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^212[5-7][0-9]{8}$/', message: 'Le numéro doit être au format marocain (212XXXXXXXXX)')]
    #[Groups(['user:read', 'user:create', 'user:update', 'user:detail'])]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:detail'])]
    private bool $isVerified = false;

    #[ORM\Column(type: 'float')]
    #[Groups(['user:read', 'user:detail', 'ad:read'])]
    private float $reputation = 5.0;

    #[ORM\Column]
    #[Groups(['user:read', 'user:detail'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:detail'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:detail'])]
    private ?string $walletAddress = null;

    // Relations
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

    #[ORM\Column(type: 'integer')]
private int $loginAttempts = 0;

#[ORM\Column(type: 'datetime', nullable: true)]
private ?\DateTimeInterface $lockedUntil = null;

#[ORM\Column(type: 'boolean')]
private bool $isActive = true;


    public function __construct()
    {
        $this->ads = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->isVerified = false;
        $this->reputation = 5.0;
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getReputation(): float
    {
        return $this->reputation;
    }

    public function setReputation(float $reputation): static
    {
        $this->reputation = $reputation;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getWalletAddress(): ?string
    {
        return $this->walletAddress;
    }

    public function setWalletAddress(?string $walletAddress): static
    {
        $this->walletAddress = $walletAddress;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @return Collection<int, Ad>
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): static
    {
        if (!$this->ads->contains($ad)) {
            $this->ads->add($ad);
            $ad->setUser($this);
        }
        return $this;
    }

    public function removeAd(Ad $ad): static
    {
        if ($this->ads->removeElement($ad)) {
            if ($ad->getUser() === $this) {
                $ad->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Transaction $sale): static
    {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
            $sale->setSeller($this);
        }
        return $this;
    }

    public function removeSale(Transaction $sale): static
    {
        if ($this->sales->removeElement($sale)) {
            if ($sale->getSeller() === $this) {
                $sale->setSeller(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Transaction $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setBuyer($this);
        }
        return $this;
    }

    public function removePurchase(Transaction $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            if ($purchase->getBuyer() === $this) {
                $purchase->setBuyer(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, PaymentMethod>
     */
    public function getPaymentMethods(): Collection
    {
        return $this->paymentMethods;
    }

    public function addPaymentMethod(PaymentMethod $paymentMethod): static
    {
        if (!$this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods->add($paymentMethod);
            $paymentMethod->setUser($this);
        }
        return $this;
    }

    public function removePaymentMethod(PaymentMethod $paymentMethod): static
    {
        if ($this->paymentMethods->removeElement($paymentMethod)) {
            if ($paymentMethod->getUser() === $this) {
                $paymentMethod->setUser(null);
            }
        }
        return $this;
    }

    #[Groups(['user:read', 'user:detail'])]
    public function getTotalTransactions(): int
    {
        return $this->sales->count() + $this->purchases->count();
    }

    #[Groups(['user:read', 'user:detail'])]
    public function getSuccessfulTransactions(): int
    {
        $successful = 0;
        foreach ($this->sales as $transaction) {
            if ($transaction->getStatus() === Transaction::STATUS_COMPLETED) {
                $successful++;
            }
        }
        foreach ($this->purchases as $transaction) {
            if ($transaction->getStatus() === Transaction::STATUS_COMPLETED) {
                $successful++;
            }
        }
        return $successful;
    }

    #[Groups(['user:read', 'user:detail'])]
    public function getSuccessRate(): float
    {
        $total = $this->getTotalTransactions();
        if ($total === 0) {
            return 0.0;
        }
        return round(($this->getSuccessfulTransactions() / $total) * 100, 2);
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->fullName . ' (' . $this->email . ')';
    }

    public function getLockedUntil(): ?\DateTimeInterface { return $this->lockedUntil; }
public function setLockedUntil(?\DateTimeInterface $lockedUntil): static { 
    $this->lockedUntil = $lockedUntil; 
    return $this; 
}

public function isActive(): bool { return $this->isActive; }
public function setIsActive(bool $isActive): static { 
    $this->isActive = $isActive; 
    return $this; 
}

public function isAccountLocked(): bool
{
    if (!$this->lockedUntil) {
        return false;
    }
    return new \DateTime() < $this->lockedUntil;
}

public function incrementLoginAttempts(): void
{
    $this->loginAttempts++;
    if ($this->loginAttempts >= 5) {
        $this->lockedUntil = (new \DateTime())->modify('+30 minutes');
    }
}

public function resetLoginAttempts(): void
{
    $this->loginAttempts = 0;
    $this->lockedUntil = null;
}
}