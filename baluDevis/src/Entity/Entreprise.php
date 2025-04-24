<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomEntreprise = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: User::class)]
    private Collection $partenaires;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(inversedBy: 'userCreateEntreprise', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userEntreprise = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Client::class)]
    private Collection $entrepriseClients;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Quote::class)]
    private Collection $entrepriseQuotes;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Invoice::class)]
    private Collection $entrepriseInvoices;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Product::class)]
    private Collection $entrepriseProducts;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->partenaires = new ArrayCollection();
        $this->entrepriseClients = new ArrayCollection();
        $this->entrepriseQuotes = new ArrayCollection();
        $this->entrepriseInvoices = new ArrayCollection();
        $this->entrepriseProducts = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(?string $nomEntreprise): static
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
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
     * @return Collection<int, User>
     */
    public function getPartenaires(): Collection
    {
        return $this->partenaires;
    }

    public function addPartenaire(User $partenaire): static
    {
        if (!$this->partenaires->contains($partenaire)) {
            $this->partenaires->add($partenaire);
            $partenaire->setEntreprise($this);
        }

        return $this;
    }

    public function removePartenaire(User $partenaire): static
    {
        if ($this->partenaires->removeElement($partenaire)) {
            // set the owning side to null (unless already changed)
            if ($partenaire->getEntreprise() === $this) {
                $partenaire->setEntreprise(null);
            }
        }

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

    public function getUserEntreprise(): ?User
    {
        return $this->userEntreprise;
    }

    public function setUserEntreprise(User $userEntreprise): static
    {
        $this->userEntreprise = $userEntreprise;

        return $this;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getEntrepriseClients(): Collection
    {
        return $this->entrepriseClients;
    }

    public function addEntrepriseClient(Client $entrepriseClient): static
    {
        if (!$this->entrepriseClients->contains($entrepriseClient)) {
            $this->entrepriseClients->add($entrepriseClient);
            $entrepriseClient->setEntreprise($this);
        }

        return $this;
    }

    public function removeEntrepriseClient(Client $entrepriseClient): static
    {
        if ($this->entrepriseClients->removeElement($entrepriseClient)) {
            // set the owning side to null (unless already changed)
            if ($entrepriseClient->getEntreprise() === $this) {
                $entrepriseClient->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quote>
     */
    public function getEntrepriseQuotes(): Collection
    {
        return $this->entrepriseQuotes;
    }

    public function addEntrepriseQuote(Quote $entrepriseQuote): static
    {
        if (!$this->entrepriseQuotes->contains($entrepriseQuote)) {
            $this->entrepriseQuotes->add($entrepriseQuote);
            $entrepriseQuote->setEntreprise($this);
        }

        return $this;
    }

    public function removeEntrepriseQuote(Quote $entrepriseQuote): static
    {
        if ($this->entrepriseQuotes->removeElement($entrepriseQuote)) {
            // set the owning side to null (unless already changed)
            if ($entrepriseQuote->getEntreprise() === $this) {
                $entrepriseQuote->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getEntrepriseInvoices(): Collection
    {
        return $this->entrepriseInvoices;
    }

    public function addEntrepriseInvoice(Invoice $entrepriseInvoice): static
    {
        if (!$this->entrepriseInvoices->contains($entrepriseInvoice)) {
            $this->entrepriseInvoices->add($entrepriseInvoice);
            $entrepriseInvoice->setEntreprise($this);
        }

        return $this;
    }

    public function removeEntrepriseInvoice(Invoice $entrepriseInvoice): static
    {
        if ($this->entrepriseInvoices->removeElement($entrepriseInvoice)) {
            // set the owning side to null (unless already changed)
            if ($entrepriseInvoice->getEntreprise() === $this) {
                $entrepriseInvoice->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getEntrepriseProducts(): Collection
    {
        return $this->entrepriseProducts;
    }

    public function addEntrepriseProduct(Product $entrepriseProduct): static
    {
        if (!$this->entrepriseProducts->contains($entrepriseProduct)) {
            $this->entrepriseProducts->add($entrepriseProduct);
            $entrepriseProduct->setEntreprise($this);
        }

        return $this;
    }

    public function removeEntrepriseProduct(Product $entrepriseProduct): static
    {
        if ($this->entrepriseProducts->removeElement($entrepriseProduct)) {
            // set the owning side to null (unless already changed)
            if ($entrepriseProduct->getEntreprise() === $this) {
                $entrepriseProduct->setEntreprise(null);
            }
        }

        return $this;
    }
}
