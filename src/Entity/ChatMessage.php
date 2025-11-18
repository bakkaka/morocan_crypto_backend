<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ChatMessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['chat:read']]),
        new Post(denormalizationContext: ['groups' => ['chat:write']]),
        new Get(normalizationContext: ['groups' => ['chat:read', 'chat:detail']]),
        new Put(denormalizationContext: ['groups' => ['chat:write']]),
        new Patch(denormalizationContext: ['groups' => ['chat:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['chat:read']],
    denormalizationContext: ['groups' => ['chat:write']]
)]
#[ORM\Entity(repositoryClass: ChatMessageRepository::class)]
class ChatMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['chat:read', 'transaction:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Transaction::class)]
    #[Assert\NotNull]
    #[Groups(['chat:read', 'chat:write', 'transaction:read'])]
    private ?Transaction $transaction = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Assert\NotNull]
    #[Groups(['chat:read', 'chat:write', 'transaction:read'])]
    private ?User $sender = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['chat:read', 'chat:write'])]
    private ?string $message = null;

    #[ORM\Column]
    #[Groups(['chat:read', 'transaction:read'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTransaction(): ?Transaction { return $this->transaction; }
    public function setTransaction(?Transaction $tx): static { $this->transaction = $tx; return $this; }

    public function getSender(): ?User { return $this->sender; }
    public function setSender(?User $s): static { $this->sender = $s; return $this; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $m): static { $this->message = $m; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
