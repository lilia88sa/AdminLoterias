<?php

namespace App\Entity;

use App\Entity\Core\ViewCounter;
use App\Repository\PostRepository;
use App\Util\SlugMaker;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Tchoulom\ViewCounterBundle\Model\ViewCountable;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post implements ViewCountable
{
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

    const CLASIFICATION_RELEVANT = 'RELEVANTE';
    const CLASIFICATION_IMPACT = 'IMPACTO';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $audio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $video;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clasification;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="post", cascade={"persist", "remove"})
     */
    private $post_files;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publish;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titleEs;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionEs;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sumary;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sumaryEs;

    /**
     * @ORM\OneToMany(targetEntity=Pdf::class, mappedBy="post", cascade={"persist"}, orphanRemoval=true)
     */
    private $pdf;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="post")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="post")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=OrderClasification::class, inversedBy="posts")
     */
    private $orderClasification;

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="post", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $optionalDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views = 0;

    /**
     * @ORM\OneToMany(targetEntity=ViewCounter::class, mappedBy="post", orphanRemoval=true)
     */
    private $viewCounters;

    private $slug;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $translationUpdate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $postUpdate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastUserTranslation;

    public function __construct()
    {
        $this->post_files = new ArrayCollection();
        $this->publish = false;
        $this->pdf = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->orderClasification = new ArrayCollection();
        $this->rating = new ArrayCollection();
        $this->viewCounters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return SlugMaker::obtenerSlug($this->title);
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getAudio(): ?string
    {
        return $this->audio;
    }

    public function setAudio(string $audio): self
    {
        $this->audio = $audio;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getClasification(): ?string
    {
        return $this->clasification;
    }

    public function setClasification(?string $clasification): self
    {
        $this->clasification = $clasification;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getPostFiles(): Collection
    {
        return $this->post_files;
    }

    public function addPostFile(File $postFile): self
    {
        if (!$this->post_files->contains($postFile)) {
            $this->post_files[] = $postFile;
            $postFile->setPost($this);
        }

        return $this;
    }

    public function removePostFile(File $postFile): self
    {
        if ($this->post_files->contains($postFile)) {
            $this->post_files->removeElement($postFile);
            // set the owning side to null (unless already changed)
            if ($postFile->getPost() === $this) {
                $postFile->setPost(null);
            }
        }

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

    public function getTitleEs(): ?string
    {
        return $this->titleEs;
    }

    public function setTitleEs(?string $titleEs): self
    {
        $this->titleEs = $titleEs;

        return $this;
    }

    public function getDescriptionEs(): ?string
    {
        return $this->descriptionEs;
    }

    public function setDescriptionEs(?string $descriptionEs): self
    {
        $this->descriptionEs = $descriptionEs;

        return $this;
    }

    public function getSumary(): ?string
    {
        return $this->sumary;
    }

    public function setSumary(?string $sumary): self
    {
        $this->sumary = $sumary;

        return $this;
    }

    public function getSumaryEs(): ?string
    {
        return $this->sumaryEs;
    }

    public function setSumaryEs(?string $sumaryEs): self
    {
        $this->sumaryEs = $sumaryEs;

        return $this;
    }

    /**
     * @return Collection|Pdf[]
     */
    public function getPdf(): Collection
    {
        return $this->pdf;
    }

    public function addPdf(Pdf $pdf): self
    {
        if (!$this->pdf->contains($pdf)) {
            $this->pdf[] = $pdf;
            $pdf->setPost($this);
        }

        return $this;
    }

    public function removePdf(Pdf $pdf): self
    {
        if ($this->pdf->contains($pdf)) {
            $this->pdf->removeElement($pdf);
            // set the owning side to null (unless already changed)
            if ($pdf->getPost() === $this) {
                $pdf->setPost(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
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
            $rating->setPost($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->rating->contains($rating)) {
            $this->rating->removeElement($rating);
            // set the owning side to null (unless already changed)
            if ($rating->getPost() === $this) {
                $rating->setPost(null);
            }
        }

        return $this;
    }

    public function getOptionalDate(): ?\DateTime
    {
        return $this->optionalDate;
    }

    public function setOptionalDate(?\DateTime $optionalDate): self
    {
        $this->optionalDate = $optionalDate;

        return $this;
    }



    /**
     * Get $views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set $views
     *
     * @param integer $views
     *
     * @return $this
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return Collection|ViewCounter[]
     */
    public function getViewCounters(): Collection
    {
        return $this->viewCounters;
    }

    public function addViewCounter(ViewCounter $viewCounter): self
    {
        if (!$this->viewCounters->contains($viewCounter)) {
            $this->viewCounters[] = $viewCounter;
            $viewCounter->setPost($this);
        }

        return $this;
    }

    public function removeViewCounter(ViewCounter $viewCounter): self
    {
        if ($this->viewCounters->contains($viewCounter)) {
            $this->viewCounters->removeElement($viewCounter);
            // set the owning side to null (unless already changed)
            if ($viewCounter->getPost() === $this) {
                $viewCounter->setPost(null);
            }
        }

        return $this;
    }

    public function getTranslationUpdate(): ?\DateTimeInterface
    {
        return $this->translationUpdate;
    }

    public function setTranslationUpdate(?\DateTimeInterface $translationUpdate): self
    {
        $this->translationUpdate = $translationUpdate;

        return $this;
    }

    public function getPostUpdate(): ?\DateTimeInterface
    {
        return $this->postUpdate;
    }

    public function setPostUpdate(?\DateTimeInterface $postUpdate): self
    {
        $this->postUpdate = $postUpdate;

        return $this;
    }

    public function getLastUserTranslation(): ?string
    {
        return $this->lastUserTranslation;
    }

    public function setLastUserTranslation(?string $lastUserTranslation): self
    {
        $this->lastUserTranslation = $lastUserTranslation;

        return $this;
    }
}
