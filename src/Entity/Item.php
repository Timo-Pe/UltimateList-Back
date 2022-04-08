<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item
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
     * @ORM\Column(type="text")
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $release_date;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $productor;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $autor;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $host;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $developer;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $editor;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Mode::class, inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_tags_collection"})
     */
    private $mode;

    /**
     * @ORM\ManyToMany(targetEntity=ListItem::class, inversedBy="items", cascade={"persist"})
     * @Groups({"get_platforms_collection", "get_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $list_items;

    /**
     * @ORM\ManyToMany(targetEntity=Platform::class, inversedBy="items", cascade={"persist"})
     * @Groups({"get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $platforms;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="items", cascade={"persist"})
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection"})
     */
    private $tags;

    public function __construct()
    {
        $this->list_items = new ArrayCollection();
        $this->platforms = new ArrayCollection();
        $this->tags = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->release_date;
    }

    public function setReleaseDate(\DateTimeInterface $release_date): self
    {
        $this->release_date = $release_date;

        return $this;
    }

    public function getProductor(): ?string
    {
        return $this->productor;
    }

    public function setProductor(?string $productor): self
    {
        $this->productor = $productor;

        return $this;
    }

    public function getAutor(): ?string
    {
        return $this->autor;
    }

    public function setAutor(?string $autor): self
    {
        $this->autor = $autor;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getDeveloper(): ?string
    {
        return $this->developer;
    }

    public function setDeveloper(?string $developer): self
    {
        $this->developer = $developer;

        return $this;
    }

    public function getEditor(): ?string
    {
        return $this->editor;
    }

    public function setEditor(?string $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getMode(): ?Mode
    {
        return $this->mode;
    }

    public function setMode(?Mode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return Collection<int, ListItem>
     */
    public function getListItems(): ?Collection
    {
        return $this->list_items;
    }

    public function addListItem(ListItem $listItem): self
    {
        if (!$this->list_items->contains($listItem)) {
            $this->list_items[] = $listItem;
        }

        return $this;
    }

    public function removeListItem(ListItem $listItem): self
    {
        $this->list_items->removeElement($listItem);

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

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): ?Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }


}
