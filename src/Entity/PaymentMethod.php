<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PaymentMethodRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['pm:read']]),
        new Post(denormalizationContext: ['groups' => ['pm:write']]),
        new Get(normalizationContext: ['groups' => ['pm:read', 'pm:detail']]),
        new Put(denormalizationContext: ['groups' => ['pm:write']]),
        new Patch(denormalizationContext: ['groups' => ['pm:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['pm:read']],
    denormalizationContext: ['groups' => ['pm:write']]
)]
#[ORM\Entity(repositoryClass: PaymentMethodRepository::class)]
class PaymentMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['pm:read', 'ad:detail', 'user:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['pm:read', 'pm:write', 'ad:detail', 'user:detail'])]
    private ?string $name = null; // e.g. CIH Bank, Attijari, Cash

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['pm:read', 'pm:write', 'user:detail'])]
    private ?string $details = null; // e.g. account number / instructions

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'paymentMethods')]
    #[Groups(['pm:read', 'pm:write', 'user:detail'])]
    private ?User $user = null; // optional: a user-specific payment method

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDetails(): ?string { return $this->details; }
    public function setDetails(?string $d): static { $this->details = $d; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
}
