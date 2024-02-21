<?php

namespace App\Entity;

use App\Entity\Livre;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TagRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['tag:read']],
    denormalizationContext: ['groups' => ['tag:write']],
    operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Put(),
    //new Delete(),
    new Patch()
])]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tag:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['tag:read', 'tag:write', 'livre:read', 'livre:write'])]
    private ?string $label = null;

    #[ORM\ManyToMany(targetEntity: Livre::class, mappedBy: 'listeTags')]
    #[Groups(['tag:read'])]
    private Collection $listeLivres;

    public function __construct()
    {
        $this->listeLivres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getListeLivres(): Collection
    {
        return $this->listeLivres;
    }

    public function addListeLivre(Livre $listeLivre): static
    {
        if (!$this->listeLivres->contains($listeLivre)) {
            $this->listeLivres->add($listeLivre);
            $listeLivre->addListeTag($this);
        }

        return $this;
    }

    public function removeListeLivre(Livre $listeLivre): static
    {
        if ($this->listeLivres->removeElement($listeLivre)) {
            $listeLivre->removeListeTag($this);
        }

        return $this;
    }
}
