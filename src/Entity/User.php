<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *      fields={"username"},
 *      groups={"registration"},
 *      message="Ce nom d'utilisateur est déjà utilisé"
 * )
 * @UniqueEntity(
 *      fields={"email"},
 *      groups={"registration"},
 *      message="Cet email est déjà utilisé"
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_list_items_collection", "get_users_collection"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"get_platforms_collection", "get_items_collection", "get_list_items_collection", "get_modes_collection", "get_tags_collection", "get_users_collection"})
     * @Assert\NotBlank (
     *      groups={"registration"},
     *      message = "Vous devez renseigner un nom d'utilisateur"
     * )
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "Votre nom d'utilisateur doit contenir au moins {{ limit }} caractères",
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="json")     
     * @Groups("get_users_collection")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(
     *     groups={"api_patch"},
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
     *     match=true,
     *     message="Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
     * )
     * @Groups("get_users_collection")
     */
    private $password;

    /**
     * @Assert\NotBlank (
     *      groups={"registration"},
     *      message = "Vous devez renseigner un mot de passe"
     * )
     * @Assert\Regex(
     *     groups={"registration"},
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
     *     match=true,
     *     message="Le mot de passe doit contenir au minimum 8 caractères, une majuscule, un chiffre et un caractère spécial"
     * )
     */
    private $plainPassword;


    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("get_users_collection")
     * @Assert\NotBlank (
     *      message = "Vous devez renseigner un email"
     * )
     * @Assert\Email(
     *     message = "La valeur '{{ value }}' n'est pas un email valide."
     * )
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=ListItem::class, mappedBy="user", cascade={"persist"})
     * @Groups("get_users_collection")
     * 
     */
    private $listItems;

    public function __construct()
    {
        $this->listItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getBestRole() :string
    {
        $bestRole = 'Anonymous';
        if (in_array('ROLE_ADMIN', $this->roles))
        {
            return 'Admin';
        }

        if (in_array('ROLE_USER', $this->roles))
        {
            return 'Util.';
        }
        return $bestRole;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, ListItem>
     */
    public function getListItems(): Collection
    {
        return $this->listItems;
    }

    public function addListItem(ListItem $listItem): self
    {
        if (!$this->listItems->contains($listItem)) {
            $this->listItems[] = $listItem;
            $listItem->setUser($this);
        }

        return $this;
    }

    public function removeListItem(ListItem $listItem): self
    {
        if ($this->listItems->removeElement($listItem)) {
            // set the owning side to null (unless already changed)
            if ($listItem->getUser() === $this) {
                $listItem->setUser(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->username;
    }

    /**
     * Get pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
     */ 
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
     *
     * @return  self
     */ 
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function isEqualTo(UserInterface $user)
    {
    //if ($this->password !== $user->getPassword()) {
    //    return false;
    //}

    //if ($this->salt !== $user->getSalt()) {
    //    return false;
    //}

    if ($this->username !== $user->getUsername()) {
        return false;
    }

    return true;
    }
}
