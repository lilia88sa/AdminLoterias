<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Security\User;
use App\Repository\EntitiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use App\Util\SlugMaker;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=EntitiesRepository::class)
 */
class Entities
{
    const ENTITY_TYPE_ESTATAL = 'ESTATAL';
    const ENTITY_TYPE_PARTICULAR = 'PARTICULAR';

    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * Hook blameable behavior
     * updates createdBy, updatedBy fields
     */
    use BlameableEntity;

    /**
     * Hook SoftDeleteable behavior
     * updates deletedAt field
     */
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comercialName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $socialReason;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $schedule;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $schedulePublic;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $serviceDescription;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publish;

    protected $captcha;

    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="entities")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=OrderClasification::class, inversedBy="entities")
     */
    private $orderClasification;

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="entities", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $entityType;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="entities")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="entities", cascade={"remove"})
     */
    private $entitiesFiles;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->orderClasification = new ArrayCollection();
        $this->rating = new ArrayCollection();
        $this->publish = false;
        $this->entitiesFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCaptcha()
    {
        return $this->captcha;
    }

    public function setCaptcha($captcha)
    {
        $this->captcha = $captcha;
    }

    public function getSlug(): ?string
    {
        return SlugMaker::obtenerSlug($this->comercialName);
    }

    public function getComercialName(): ?string
    {
        return $this->comercialName;
    }

    public function setComercialName(?string $comercialName): self
    {
        $this->comercialName = $comercialName;

        return $this;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSocialReason(): ?string
    {
        return $this->socialReason;
    }

    public function setSocialReason(?string $socialReason): self
    {
        $this->socialReason = $socialReason;

        return $this;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getSchedulePublic(): ?string
    {
        return $this->schedulePublic;
    }

    public function setSchedulePublic(?string $schedulePublic): self
    {
        $this->schedulePublic = $schedulePublic;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getServiceDescription(): ?string
    {
        return $this->serviceDescription;
    }

    public function setServiceDescription(?string $serviceDescription): self
    {
        $this->serviceDescription = $serviceDescription;

        return $this;
    }

    public function getPublish(): ?bool
    {
        return $this->publish;
    }

    public function setPublish(bool $publish): self
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setEntities($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getEntities() === $this) {
                $comment->setEntities(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrderClasification[]
     */
    public function getOrderClasification(): Collection
    {
        return $this->orderClasification;
    }

    public function addOrderClasification(OrderClasification $orderClasification): self
    {
        if (!$this->orderClasification->contains($orderClasification)) {
            $this->orderClasification[] = $orderClasification;
        }

        return $this;
    }

    public function removeOrderClasification(OrderClasification $orderClasification): self
    {
        if ($this->orderClasification->contains($orderClasification)) {
            $this->orderClasification->removeElement($orderClasification);
        }

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRating(): Collection
    {
        return $this->rating;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->rating->contains($rating)) {
            $this->rating[] = $rating;
            $rating->setEntities($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->rating->contains($rating)) {
            $this->rating->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getEntities() === $this) {
                $rating->setEntities(null);
            }
        }

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

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): self
    {
        $this->entityType = $entityType;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getEntitiesFiles(): Collection
    {
        return $this->entitiesFiles;
    }

    public function addEntitiesFile(File $entitiesFile): self
    {
        if (!$this->entitiesFiles->contains($entitiesFile)) {
            $this->entitiesFiles[] = $entitiesFile;
            $entitiesFile->setEntities($this);
        }

        return $this;
    }

    public function removeEntitiesFile(File $entitiesFile): self
    {
        if ($this->entitiesFiles->contains($entitiesFile)) {
            $this->entitiesFiles->removeElement($entitiesFile);
            // set the owning side to null (unless already changed)
            if ($entitiesFile->getEntities() === $this) {
                $entitiesFile->setEntities(null);
            }
        }

        return $this;
    }

}
