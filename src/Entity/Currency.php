<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['currency:read']]),
        new Post(denormalizationContext: ['groups' => ['currency:write']]),
        new Get(normalizationContext: ['groups' => ['currency:read', 'currency:detail']]),
        new Put(denormalizationContext: ['groups' => ['currency:write']]),
        new Patch(denormalizationContext: ['groups' => ['currency:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['currency:read']],
    denormalizationContext: ['groups' => ['currency:write']]
)]
#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['currency:read', 'ad:detail', 'transaction:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['currency:read', 'currency:write', 'ad:detail'])]
    private ?string $code = null; // e.g. USDT, MAD

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups(['currency:read', 'currency:write'])]
    private ?string $name = null; // e.g. Tether USD, Moroccan Dirham

    #[ORM\Column(type: 'integer')]
    #[Groups(['currency:read', 'currency:write'])]
    private int $decimals = 6;

    public function getId(): ?int { return $this->id; }

    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDecimals(): int { return $this->decimals; }
    public function setDecimals(int $d): static { $this->decimals = $d; return $this; }
}
