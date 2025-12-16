<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;   
use App\Repository\UserBankDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['user_bank_detail:read']]),
        new Post(denormalizationContext: ['groups' => ['user_bank_detail:write']]),
        new Get(normalizationContext: ['groups' => ['user_bank_detail:read', 'user_bank_detail:detail']]),
        new Put(denormalizationContext: ['groups' => ['user_bank_detail:write']]),
        new Patch(denormalizationContext: ['groups' => ['user_bank_detail:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['user_bank_detail:read']],
    denormalizationContext: ['groups' => ['user_bank_detail:write']]
)]
#[ORM\Entity(repositoryClass: UserBankDetailRepository::class)]
class UserBankDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_bank_detail:read', 'user:detail', 'ad:detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bankDetails')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write'])]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Choice(['CIH', 'Attijariwafabank', 'Saham Bank', 'BMCE', 'BMCI', 'Crédit du Maroc', 'Banque Populaire'])]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'ad:detail', 'user:detail'])]
    private string $bankName = 'CIH';

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'user:detail'])]
    private string $accountHolder;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 50)]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'user:detail'])]
    private string $accountNumber;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'user:detail'])]
    private ?string $swiftCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'user:detail'])]
    private ?string $branchName = null;

    #[ORM\Column]
    #[Groups(['user_bank_detail:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['user_bank_detail:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write'])]
    private bool $isActive = true;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Choice(['current', 'savings', 'professional'])]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write'])]
    private ?string $accountType = 'current';

    #[ORM\ManyToMany(targetEntity: Ad::class, mappedBy: 'acceptedBankDetails')]
    #[Groups(['user_bank_detail:read'])]
    private Collection $adsUsingThisDetail;

    public function __construct()
    {
        $this->adsUsingThisDetail = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters et setters...

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static {
        $this->user = $user;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getBankName(): string { return $this->bankName; }
    public function setBankName(string $bankName): static {
        $this->bankName = $bankName;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getAccountHolder(): string { return $this->accountHolder; }
    public function setAccountHolder(string $accountHolder): static {
        $this->accountHolder = $accountHolder;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getAccountNumber(): string { return $this->accountNumber; }
    public function setAccountNumber(string $accountNumber): static {
        $this->accountNumber = $accountNumber;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getSwiftCode(): ?string { return $this->swiftCode; }
    public function setSwiftCode(?string $swiftCode): static {
        $this->swiftCode = $swiftCode;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getBranchName(): ?string { return $this->branchName; }
    public function setBranchName(?string $branchName): static {
        $this->branchName = $branchName;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getAccountType(): ?string { return $this->accountType; }
    public function setAccountType(?string $accountType): static {
        $this->accountType = $accountType;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /** @return Collection<int, Ad> */
    public function getAdsUsingThisDetail(): Collection { return $this->adsUsingThisDetail; }
    public function addAdsUsingThisDetail(Ad $ad): static {
        if (!$this->adsUsingThisDetail->contains($ad)) {
            $this->adsUsingThisDetail->add($ad);
        }
        return $this;
    }
    public function removeAdsUsingThisDetail(Ad $ad): static {
        $this->adsUsingThisDetail->removeElement($ad);
        return $this;
    }

    public function getMaskedAccountNumber(): string {
        $length = strlen($this->accountNumber);
        if ($length <= 4) {
            return $this->accountNumber;
        }
        
        $visible = 4;
        $mask = str_repeat('*', $length - $visible);
        $lastDigits = substr($this->accountNumber, -$visible);
        
        return $mask . $lastDigits;
    }

    public function __toString(): string {
        return $this->bankName . ' - ' . $this->accountHolder . ' (' . $this->getMaskedAccountNumber() . ')';
    }

    public function validateUser(): void {
        if ($this->user === null) {
            throw new \RuntimeException('Un UserBankDetail doit être associé à un utilisateur.');
        }
    }
}