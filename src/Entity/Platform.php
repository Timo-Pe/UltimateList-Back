<?php

namespace App\Entity;

use App\Repository\PlatformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=PlatformRepository::class)
 * @UniqueEntity(
 *      fields={"name"},
 *      groups={"platform-registration"},
 *      message="Cette plateforme existe déjà"
 * )
 */
class Platform
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
     * @ORM\ManyToMany(targetEntity=Mode::class, mappedBy="platforms", cascade={"persist"})
     * @Groups("get_platforms_collection")
     */
    private $modes;

    /**
     * @ORM\ManyToMany(targetEntity=Item::class, mappedBy="platforms", cascade={"persist"})
     * @Groups("get_platforms_collection")
     */
    private $items;

    public function __construct()
    {
        $this->modes = new ArrayCollection();
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
     * @return Collection<int, Mode>
     */
    public function getModes(): Collection
    {
        return $this->modes;
    }

    public function addMode(Mode $mode): self
    {
        if (!$this->modes->contains($mode)) {
            $this->modes[] = $mode;
            $mode->addPlatform($this);
        }

        return $this;
    }

    public function removeMode(Mode $mode): self
    {
        if ($this->modes->removeElement($mode)) {
            $mode->removePlatform($this);
        }

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
            $item->addPlatform($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            $item->removePlatform($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
