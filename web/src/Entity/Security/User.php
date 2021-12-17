<?php

namespace App\Entity\Security;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Cliente;
use App\Entity\Core\Notifications;
use App\Entity\Entities;
use App\Repository\Security\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource()
 * @Gedmo\Loggable(logEntryClass="App\Entity\Security\Logs")
 */
class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_WRITER = 'ROLE_WRITER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const DEFAULT_ROLES = [self::ROLE_USER];

    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_PENDING_FOR_APPROVAL = 'PENDING_FOR_APPROVAL';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(groups={"post","user:register"})
     * @Groups({"get",
     *          "post",
     *          "patch-update-profile",
     *          "put","user:read",
     *          "user:register",
     *          "agency:read",
     *          "artist:read",
     *          "organizer:read",
     *          "agent:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotNull(groups={"post","user:register"})
     * @Groups({"get",
     *          "post",
     *          "patch-update-profile",
     *          "get-owner","user:read",
     *          "user:register",
     *          "agency:read",
     *          "agent:write",
     *          "artist:read",
     *          "artist:write",
     *          "agency:write",
     *          "organizer:write",
     *          "organizer:read",
     *          "agent:read"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(groups={"post","user:register"})
     * @Assert\Length(
     *     min=8,
     *     groups={"post","user:register"}
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$&*])/",
     *     message="user.password.complex",
     *     groups={"post","user:register"}
     * )
     * @Groups({"put",
     *          "post",
     *          "user:write",
     *          "patch-update-profile",
     *          "agency:write",
     *          "artist:write",
     *          "organizer:write"})
     */
    private $password;

    /**
     * @Assert\NotNull(groups={"post","user:register"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRepeatPassword()",
     *     message="user.repeatPassword.mo_match",
     *     groups={"post","user:register"}
     * )
     * @Groups({"put",
     *          "post",
     *          "agency:write",
     *          "patch-update-profile",
     *          "artist:write",
     *          "organizer:write"})
     */
    private $repeatPassword;

    /**
     * @Groups({"put-reset-password","user:write"})
     * @Assert\NotBlank(groups={"put-reset-password","user:write"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="user.newPassword.complex",
     *     groups={"put-reset-password","user:write"}
     * )
     * @Assert\Expression(
     *     "this.getNewPassword() === this.getPassword()",
     *     message="user.repeatPassword.mo_match",
     *     groups={"user:write"}
     * )
     */
    private $newPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @Assert\Expression(
     *     "this.getNewPassword() === this.getNewRetypedPassword()",
     *     message="user.repeatPassword.mo_match",
     *     groups={"put-reset-password"}
     * )
     */
    private $newRetypedPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(groups={"put-reset-password"})
     * @UserPassword(groups={"put-reset-password"})
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull
     * @Assert\Email
     * @Groups({"post",
     *          "get-admin",
     *          "get-owner",
     *          "user:read",
     *          "user:register",
     *          "agency:write",
     *         "patch-update-profile",
     *          "agency:read",
     *          "artist:write",
     *          "artist:read",
     *          "organizer:write",
     *          "organizer:read",
     *          "agent:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotNull
     * @Groups({"get-admin","get-owner"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"post",
     *          "user:register",
     *          "agency:write",
     *          "artist:write",
     *         "organizer:write"})
     */
    private $privacy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotNull
     * @Groups({"get-admin"})
     * @Gedmo\Versioned
     */
    private $status;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tokenLogoutChangeDate;
    


    /**
     * @ORM\OneToMany(targetEntity=Notifications::class, mappedBy="user")
     * @Groups({"organizer:read",
     *     "artist:read",
     *     "agency:read",
     *     "agent:read"})
     */
    private $notifications;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $version;

    /**
     * @ORM\OneToMany(targetEntity=Entities::class, mappedBy="user")
     */
    private $entities;

    /**
     * @ORM\ManyToOne(targetEntity=Cliente::class, inversedBy="user")
     */
    private $client;

    public function __construct()
    {
        $this->enabled = false;
        $this->roles = self::DEFAULT_ROLES;
        $this->status = self::STATUS_PENDING_FOR_APPROVAL;
        $this->confirmationToken = null;
        $this->privacy = false;
        $this->notifications = new ArrayCollection();
        $this->entities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    function __toString()
    {
        return $this->getEmail().'-'.$this->getName();

    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
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


    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * Returns the roles granted to the user.
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
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

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRepeatPassword()
    {
        return $this->repeatPassword;
    }

    public function setRepeatPassword($repeatPassword): void
    {
        $this->repeatPassword = $repeatPassword;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }

    public function setNewRetypedPassword($newRetypedPassword): void
    {
        $this->newRetypedPassword = $newRetypedPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }

    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }


    public function getPrivacy(): ?bool
    {
        return $this->privacy;
    }

    public function setPrivacy(bool $privacy): self
    {
        $this->privacy = $privacy;

        return $this;
    }



    /**
     * @return Collection|Notifications[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notifications $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getTokenLogoutChangeDate(): ?int
    {
        return $this->tokenLogoutChangeDate;
    }

    public function setTokenLogoutChangeDate(?int $tokenLogoutChangeDate): self
    {
        $this->tokenLogoutChangeDate = $tokenLogoutChangeDate;

        return $this;
    }


    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return Collection|Entities[]
     */
    public function getEntities(): Collection
    {
        return $this->entities;
    }

    public function addEntity(Entities $entity): self
    {
        if (!$this->entities->contains($entity)) {
            $this->entities[] = $entity;
            $entity->setUser($this);
        }

        return $this;
    }

    public function removeEntity(Entities $entity): self
    {
        if ($this->entities->contains($entity)) {
            $this->entities->removeElement($entity);
            // set the owning side to null (unless already changed)
            if ($entity->getUser() === $this) {
                $entity->setUser(null);
            }
        }

        return $this;
    }

    public function getClient(): ?Cliente
    {
        return $this->client;
    }

    public function setClient(?Cliente $client): self
    {
        $this->client = $client;

        return $this;
    }

}
