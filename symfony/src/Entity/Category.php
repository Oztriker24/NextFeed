<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[UniqueEntity('name')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(nullable: true, type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $UpdatedAt = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: FluxRss::class)]
    private Collection $fluxRsses;
    public function __construct()
    {
        $this->CreatedAt = new \DateTimeImmutable();
        $this->fluxRsses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $UpdatedAt): self
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    /**
     * @return Collection<int, FluxRss>
     */
    public function getFluxRsses(): Collection
    {
        return $this->fluxRsses;
    }

    public function addFluxRss(FluxRss $fluxRss): self
    {
        if (!$this->fluxRsses->contains($fluxRss)) {
            $this->fluxRsses->add($fluxRss);
            $fluxRss->setCategory($this);
        }

        return $this;
    }

    public function removeFluxRss(FluxRss $fluxRss): self
    {
        if ($this->fluxRsses->removeElement($fluxRss)) {
            // set the owning side to null (unless already changed)
            if ($fluxRss->getCategory() === $this) {
                $fluxRss->setCategory(null);
            }
        }

        return $this;
    }



}
