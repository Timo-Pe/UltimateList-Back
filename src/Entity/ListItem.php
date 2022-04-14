<?php

namespace App\Entity;

use App\Repository\ListItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ListItemRepository::class)
 */
class ListItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_platforms_collection", "get_items_collection", "get_users_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups("get_list_items_collection")
     */
    private $item_added_at;

    /**
     * @ORM\Column(type="integer")
     * @Groups("get_list_items_collection")
     */
    private $item_status;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("get_list_items_collection")
     */
    private $item_comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("get_list_items_collection")
     */
    private $item_rating;

    /**
     * @ORM\ManyToMany(targetEntity=Item::class, mappedBy="list_items", cascade={"persist"})
     * @Groups("get_list_items_collection")
     */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity=Mode::class, inversedBy="listItems", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups("get_list_items_collection")
     */
    private $mode;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="listItems", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups("get_list_items_collection")
     */
    private $user;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemAddedAt(): ?\DateTimeImmutable
    {
        return $this->item_added_at;
    }

    public function setItemAddedAt(\DateTimeImmutable $item_added_at): self
    {
        $this->item_added_at = $item_added_at;

        return $this;
    }

    public function getItemStatus(): ?int
    {
        return $this->item_status;
    }

    public function setItemStatus(int $item_status): self
    {
        $this->item_status = $item_status;

        return $this;
    }

    public function getItemComment(): ?string
    {
        return $this->item_comment;
    }

    public function setItemComment(?string $item_comment): self
    {
        $this->item_comment = $item_comment;

        return $this;
    }

    public function getItemRating(): ?int
    {
        return $this->item_rating;
    }

    public function setItemRating(?int $item_rating): self
    {
        $this->item_rating = $item_rating;

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
            $item->addListItem($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            $item->removeListItem($this);
        }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
