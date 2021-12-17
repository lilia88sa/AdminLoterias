<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use App\Util\SlugMaker;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
 */
class Section
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="section", cascade={"persist", "remove"})
     */
    private $section_files;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publish;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sumary;

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
    private $sumaryEs;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderElement;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="section", cascade={"persist", "remove"})
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity=OrderClasification::class, inversedBy="sections")
     */
    private $orderClasification;

    /**
     * @ORM\ManyToOne(targetEntity=Cliente::class, inversedBy="sections")
     */
    private $cliente;



    private $slug;

    public function __construct()
    {
        $this->section_files = new ArrayCollection();
        $this->publish = false;
        $this->categories = new ArrayCollection();
        $this->orderElement = 0;
        $this->orderClasification = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getSectionFiles(): Collection
    {
        return $this->section_files;
    }

    public function addSectionFile(File $sectionFile): self
    {
        if (!$this->section_files->contains($sectionFile)) {
            $this->section_files[] = $sectionFile;
            $sectionFile->setSection($this);
        }

        return $this;
    }

    public function removeSectionFile(File $sectionFile): self
    {
        if ($this->section_files->contains($sectionFile)) {
            $this->section_files->removeElement($sectionFile);
            // set the owning side to null (unless already changed)
            if ($sectionFile->getSection() === $this) {
                $sectionFile->setSection(null);
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

    public function getSumary(): ?string
    {
        return $this->sumary;
    }

    public function setSumary(?string $sumary): self
    {
        $this->sumary = $sumary;

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
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setSection($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getSection() === $this) {
                $category->setSection(null);
            }
        }

        return $this;
    }

    public function getOrderElement(): ?int
    {
        return $this->orderElement;
    }

    public function setOrderElement(int $orderElement): self
    {
        $this->orderElement = $orderElement;

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

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }
}
