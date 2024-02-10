<?php

namespace App\Entity;

use App\Repository\QuoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: QuoteRepository::class)]
class Quote
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(nullable: true,type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable:true,type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $expiryAt = null;

    #[ORM\Column(nullable:true,length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?float $totalAmount = null;

    #[ORM\Column]
    private ?float $tva = null;
    #[ORM\Column]
    private ?float $totalTva = null;

    #[ORM\ManyToOne(inversedBy: 'quotes')]
    private ?Client $client = null;

    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: QuoteLine::class , cascade: ['persist'])]
    private Collection $quoteLines;

    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\Column(length: 50)]
    private ?string $Description = null;

    #[ORM\ManyToOne(inversedBy: 'quotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userQuote = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->expiryAt = (new \DateTimeImmutable())->modify('+1 month');
        $this->quoteLines = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getExpiryAt(): ?\DateTimeImmutable
    {
        return $this->expiryAt;
    }

    public function setExpiryAt(\DateTimeImmutable $expiryAt): static
    {
        $this->expiryAt = $expiryAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }
    public function setTva(float $tva): static
    {
        $this->tva = $tva;

        return $this;
    }
    public function getTva(): ?float
    {
        return $this->tva;
    }
     public function setTotalTva(float $totalTva): static
    {
        $this->totalTva = $totalTva;

        return $this;
    }
    public function getTotalTva(): ?float
    {
        return $this->tva;
    }


    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, QuoteLine>
     */
    public function getQuoteLines(): Collection
    {
        return $this->quoteLines;
    }

    public function addQuoteLine(QuoteLine $quoteLine): static
    {
        if (!$this->quoteLines->contains($quoteLine)) {
            $this->quoteLines->add($quoteLine);
            $quoteLine->setQuote($this);
        }

        return $this;
    }

    public function removeQuoteLine(QuoteLine $quoteLine): static
    {
        if ($this->quoteLines->removeElement($quoteLine)) {
            // set the owning side to null (unless already changed)
            if ($quoteLine->getQuote() === $this) {
                $quoteLine->setQuote(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setQuote($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getQuote() === $this) {
                $invoice->setQuote(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getUserQuote(): ?User
    {
        return $this->userQuote;
    }

    public function setUserQuote(?User $userQuote): static
    {
        $this->userQuote = $userQuote;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
    public function generateName(): String
    {
     if ($this->getCreatedAt() === null || $this->getId() === null) {
            // Handle the case where necessary properties are not set
            throw new \RuntimeException('Cannot generate Quote name. Missing required properties.');
        }

        // Format the date part of the name using the creation date
        $datePart = $this->getCreatedAt()->format('Y_m_d');

        // Get the ID of the associated quote and take the first four digits
        $quoteIdPart = substr((string) $this->getId(), -4);

        // Combine the date and quote ID to create the invoice name
        $quoteName = "Devis n° {$datePart}_{$quoteIdPart}";

        return $quoteName;
    }
}

