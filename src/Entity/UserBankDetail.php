<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\ArrayCollection; // ⬅️ IMPORTANT
use Doctrine\Common\Collections\Collection;   
use App\Repository\UserBankDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['user_bank_detail:read']],
            security: "is_granted('ROLE_USER') and object.getUser() == user"
        ),
        new Post(
            denormalizationContext: ['groups' => ['user_bank_detail:write']],
            security: "is_granted('ROLE_USER')"
        ),
        new Get(
            normalizationContext: ['groups' => ['user_bank_detail:read', 'user_bank_detail:detail']],
            security: "is_granted('ROLE_USER') and object.getUser() == user"
        ),
        new Put(
            denormalizationContext: ['groups' => ['user_bank_detail:write']],
            security: "is_granted('ROLE_USER') and object.getUser() == user"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['user_bank_detail:write']],
            security: "is_granted('ROLE_USER') and object.getUser() == user"
        ),
        new Delete(
            security: "is_granted('ROLE_USER') and object.getUser() == user"
        )
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
    #[Groups(['user_bank_detail:read', 'user:detail'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bankDetails')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    #[Groups(['user_bank_detail:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Choice(['CIH', 'Attijariwafabank', 'Saham Bank', 'BMCE', 'BMCI', 'Crédit du Maroc', 'Banque Populaire'])]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'ad:detail', 'user:detail'])]
    private string $bankName = 'CIH'; // Banques marocaines principales

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'user:detail'])]
    private string $accountHolder;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 50)]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'user:detail'])]
    private string $accountNumber; // RIB (24 chiffres) ou CCP (10 chiffres)

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'user:detail'])]
    private ?string $swiftCode = null; // Code SWIFT/BIC

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write', 'user:detail'])]
    private ?string $branchName = null; // Nom de l'agence

    #[ORM\Column]
    #[Groups(['user_bank_detail:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['user_bank_detail:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    #[Groups(['user_bank_detail:read'])]
    private bool $isActive = true;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Choice(['current', 'savings', 'professional'])]
    #[Groups(['user_bank_detail:read', 'user_bank_detail:write'])]
    private ?string $accountType = 'current'; // Type de compte

    #[ORM\ManyToMany(targetEntity: Ad::class, mappedBy: 'acceptedBankDetails')]
#[Groups(['user_bank_detail:read'])]
private Collection $adsUsingThisDetail;

    public function __construct()
    {
         $this->adsUsingThisDetail = new ArrayCollection(); // ⬅️ AJOUTER
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): static
    {
        $this->bankName = $bankName;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getAccountHolder(): string
    {
        return $this->accountHolder;
    }

    public function setAccountHolder(string $accountHolder): static
    {
        $this->accountHolder = $accountHolder;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getSwiftCode(): ?string
    {
        return $this->swiftCode;
    }

    public function setSwiftCode(?string $swiftCode): static
    {
        $this->swiftCode = $swiftCode;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getBranchName(): ?string
    {
        return $this->branchName;
    }

    public function setBranchName(?string $branchName): static
    {
        $this->branchName = $branchName;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(?string $accountType): static
    {
        $this->accountType = $accountType;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    // Méthode utilitaire pour afficher un résumé masqué
    public function getMaskedAccountNumber(): string
    {
        $length = strlen($this->accountNumber);
        if ($length <= 4) {
            return $this->accountNumber;
        }
        
        $visible = 4; // Montrer les 4 derniers chiffres
        $mask = str_repeat('*', $length - $visible);
        $lastDigits = substr($this->accountNumber, -$visible);
        
        return $mask . $lastDigits;
    }

    // Validation personnalisée pour les RIB marocains
    public function isValidMoroccanRIB(): bool
    {
        // Format RIB marocain : 24 chiffres
        if (strlen($this->accountNumber) !== 24 || !ctype_digit($this->accountNumber)) {
            return false;
        }

        // Les 3 premiers chiffres indiquent la banque
        $bankCode = substr($this->accountNumber, 0, 3);
        
        // Codes bancaires marocains connus
        $moroccanBankCodes = [
            '002' => 'Attijariwafabank',
            '003' => 'BMCE Bank',
            '007' => 'BMCI',
            '011' => 'Banque Populaire',
            '014' => 'CIH Bank',
            '022' => 'Crédit du Maroc',
            // Ajoutez d'autres codes selon besoin
        ];

        return isset($moroccanBankCodes[$bankCode]);
    }

    /** @return Collection<int, Ad> */
public function getAdsUsingThisDetail(): Collection
{
    return $this->adsUsingThisDetail;
}
}