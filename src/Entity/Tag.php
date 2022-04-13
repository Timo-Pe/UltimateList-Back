<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
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
     * @ORM\ManyToMany(targetEntity=Item::class, mappedBy="tags", cascade={"persist"})
     * @Groups("get_tags_collection")
     */
    private $items;

    /**
     * @ORM\Column(type="string", length=64, options={"default" : "#7068F4"})
     * @Groups({"get_tags_collection", "get_items_collection"})
     */
    private $color;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->addTag($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            $item->removeTag($this);
        }

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

}
