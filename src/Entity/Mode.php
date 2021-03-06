<?php

namespace App\Entity;

use App\Repository\ModeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ModeRepository::class)
 * @UniqueEntity(
 *      fields={"name"},
 *      groups={"mode-registration"},
 *      message="Ce mode existe déjà"
 * )
 */
class Mode
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Item::class, mappedBy="mode", cascade={"persist"})
     * @Groups("get_modes_collection")
     */
    private $items;

    /**
     * @ORM\OneToMany(targetEntity=ListItem::class, mappedBy="mode", cascade={"persist"})
     * @Groups("get_modes_collection")
     */
    private $listItems;

    /**
     * @ORM\ManyToMany(targetEntity=Platform::class, inversedBy="modes", cascade={"persist"})
     * @Groups("get_modes_collection")
     */
    private $platforms;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups("get_modes_collection")
     */
    private $color;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->listItems = new ArrayCollection();
        $this->platforms = new ArrayCollection();
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

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): ?Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setMode($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getMode() === $this) {
                $item->setMode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ListItem>
     */
    public function getListItems(): ?Collection
    {
        return $this->listItems;
    }

    public function addListItem(ListItem $listItem): self
    {
        if (!$this->listItems->contains($listItem)) {
            $this->listItems[] = $listItem;
            $listItem->setMode($this);
        }

        return $this;
    }

    public function removeListItem(ListItem $listItem): self
    {
        if ($this->listItems->removeElement($listItem)) {
            // set the owning side to null (unless already changed)
            if ($listItem->getMode() === $this) {
                $listItem->setMode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Platform>
     */
    public function getPlatforms(): ?Collection
    {
        return $this->platforms;
    }

    public function addPlatform(Platform $platform): self
    {
        if (!$this->platforms->contains($platform)) {
            $this->platforms[] = $platform;
        }

        return $this;
    }

    public function removePlatform(Platform $platform): self
    {
        $this->platforms->removeElement($platform);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

}
