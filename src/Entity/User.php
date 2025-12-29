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
            security: 'is_granted("ROLE_ADMIN")', // ← MODIFIÉ: Seulement pour les admins
            processor: UserPasswordHasherProcessor::class,
            denormalizationContext: ['groups' => ['user:update', 'user:admin:write']], // ← AJOUTÉ
            normalizationContext: ['groups' => ['user:read']]
        ),
        new Patch(
            security: 'is_granted("ROLE_ADMIN")', // ← MODIFIÉ: Seulement pour les admins
            processor: UserPasswordHasherProcessor::class,
            denormalizationContext: ['groups' => ['user:update', 'user:admin:write']], // ← AJOUTÉ
            normalizationContext: ['groups' => ['user:read']]
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")' // ← MODIFIÉ: Seulement pour les admins
        )
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
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
    #[Groups(['user:read', 'user:detail', 'user:admin:write'])] // ← AJOUTÉ 'user:admin:write'
    private bool $isVerified = false;

    #[ORM\Column(type: 'float')]
    #[Groups(['user:read', 'user:detail', 'user:admin:write'])] // ← AJOUTÉ 'user:admin:write'
    private float $reputation = 5.0;

    #[ORM\Column]
    #[Groups(['user:read', 'user:detail'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:detail'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['user:read', 'user:detail', 'user:admin:write'])] // ← MODIFIÉ: Ajout de 'user:admin:write'
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:detail', 'user:admin:write'])] // ← AJOUTÉ 'user:admin:write'
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
    #[Groups(['user:read', 'user:detail', 'user:admin:write'])] // ← AJOUTÉ 'user:admin:write'
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserBankDetail::class, cascade: ['persist', 'remove'])]
    #[Groups(['user:detail'])]
    private Collection $bankDetails;

    public function __construct()
    {
        $this->bankDetails = new ArrayCollection();
        $this->ads = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->isVerified = false;
        $this->reputation = 5.0;
        $this->roles = ['ROLE_USER'];
        $this->isActive = true;
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
        // Garantir que ROLE_USER est toujours présent
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        // Nettoyer et valider les rôles
        $roles = array_map('strtoupper', $roles);
        $roles = array_unique($roles);
        
        // S'assurer que ROLE_USER est toujours présent
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        
        $this->roles = $roles;
        return $this;
    }
     
    #[Groups(['user:read', 'user:detail'])]
    public function getStoredRoles(): array
    {
        return $this->roles;
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    #[Groups(['user:read', 'user:detail'])]
    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Ajoute un rôle à l'utilisateur
     */
    public function addRole(string $role): static
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    /**
     * Retire un rôle de l'utilisateur
     */
    public function removeRole(string $role): static
    {
        $role = strtoupper($role);
        $this->roles = array_filter($this->roles, function($r) use ($role) {
            return $r !== $role;
        });
        
        // Réindexer le tableau
        $this->roles = array_values($this->roles);
        
        // S'assurer qu'il reste au moins ROLE_USER
        if (!in_array('ROLE_USER', $this->roles, true)) {
            $this->roles[] = 'ROLE_USER';
        }
        
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

    public function getLockedUntil(): ?\DateTimeInterface 
    { 
        return $this->lockedUntil; 
    }
    
    public function setLockedUntil(?\DateTimeInterface $lockedUntil): static 
    { 
        $this->lockedUntil = $lockedUntil; 
        return $this; 
    }

    public function isActive(): bool 
    { 
        return $this->isActive; 
    }
    
    public function setIsActive(bool $isActive): static 
    { 
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

    public function getBankDetails(): Collection
    {
        return $this->bankDetails;
    }

    public function addBankDetail(UserBankDetail $bankDetail): static
    {
        if (!$this->bankDetails->contains($bankDetail)) {
            $this->bankDetails->add($bankDetail);
            $bankDetail->setUser($this);
        }
        return $this;
    }

    public function removeBankDetail(UserBankDetail $bankDetail): static
    {
        if ($this->bankDetails->removeElement($bankDetail)) {
            if ($bankDetail->getUser() === $this) {
                $bankDetail->setUser(null);
            }
        }
        return $this;
    }

    // ============================================
    // MÉTHODES AJOUTÉES POUR LA GESTION ADMIN
    // ============================================

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    #[Groups(['user:read', 'user:detail'])]
    public function isAdmin(): bool
    {
        return $this->hasRole('ROLE_ADMIN');
    }

    /**
     * Vérifie si l'utilisateur est super administrateur
     */
    #[Groups(['user:read', 'user:detail'])]
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('ROLE_SUPER_ADMIN');
    }

    /**
     * Méthode pour promouvoir un utilisateur en admin
     */
    public function promoteToAdmin(): static
    {
        return $this->addRole('ROLE_ADMIN');
    }

    /**
     * Méthode pour rétrograder un admin en utilisateur normal
     */
    public function demoteFromAdmin(): static
    {
        return $this->removeRole('ROLE_ADMIN');
    }

    /**
     * Active le compte utilisateur
     */
    public function activate(): static
    {
        $this->isActive = true;
        $this->resetLoginAttempts();
        return $this;
    }

    /**
     * Désactive le compte utilisateur
     */
    public function deactivate(): static
    {
        $this->isActive = false;
        return $this;
    }

    /**
     * Vérifie si l'utilisateur peut être modifié par un autre utilisateur
     */
    public function canBeModifiedBy(User $modifier): bool
    {
        // Un super admin peut modifier tout le monde
        if ($modifier->isSuperAdmin()) {
            return true;
        }
        
        // Un admin ne peut pas modifier un super admin ou un autre admin
        if ($modifier->isAdmin()) {
            return !$this->isSuperAdmin() && !$this->isAdmin();
        }
        
        // Un utilisateur normal ne peut modifier que son propre compte
        return $this->getId() === $modifier->getId();
    }
}