<?php

namespace App\Entity;

use App\Repository\KeyWordsRepository;
use App\Util\SlugMaker;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass=KeyWordsRepository::class)
 */
class KeyWords
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titleES;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionES;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isGlossary;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="keyWords", cascade={"remove"})
     */
    private $keyWordsFiles;

    private $slug;

    public function __construct()
    {
        $this->keyWordsFiles = new ArrayCollection();
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

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitleES(): ?string
    {
        return $this->titleES;
    }

    public function setTitleES(?string $titleES): self
    {
        $this->titleES = $titleES;

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

    public function getDescriptionES(): ?string
    {
        return $this->descriptionES;
    }

    public function setDescriptionES(?string $descriptionES): self
    {
        $this->descriptionES = $descriptionES;

        return $this;
    }

    public function getIsGlossary(): ?bool
    {
        return $this->isGlossary;
    }

    public function setIsGlossary(bool $isGlossary): self
    {
        $this->isGlossary = $isGlossary;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getKeyWordsFiles(): Collection
    {
        return $this->keyWordsFiles;
    }

    public function addKeyWordsFile(File $keyWordsFile): self
    {
        if (!$this->keyWordsFiles->contains($keyWordsFile)) {
            $this->keyWordsFiles[] = $keyWordsFile;
            $keyWordsFile->setKeyWords($this);
        }

        return $this;
    }

    public function removeKeyWordsFile(File $keyWordsFile): self
    {
        if ($this->keyWordsFiles->contains($keyWordsFile)) {
            $this->keyWordsFiles->removeElement($keyWordsFile);
            // set the owning side to null (unless already changed)
            if ($keyWordsFile->getKeyWords() === $this) {
                $keyWordsFile->setKeyWords(null);
            }
        }

        return $this;
    }
}
