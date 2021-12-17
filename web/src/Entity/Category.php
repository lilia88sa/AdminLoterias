<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Util\SlugMaker;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="category", cascade={"remove"})
     */
    private $category_files;

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
     * @ORM\Column(type="integer")
     */
    private $orderElement;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="categories")
     */
    private $section;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="category", cascade={"persist", "remove"})
     */
    private $post;

    /**
     * @ORM\ManyToMany(targetEntity=OrderClasification::class, inversedBy="categories")
     */
    private $orderClasification;

    private $slug;

    public function __construct()
    {
        $this->category_files = new ArrayCollection();
        $this->publish = false;
        $this->post = new ArrayCollection();
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
    public function getCategoryFiles(): Collection
    {
        return $this->category_files;
    }

    public function addCategoryFile(File $categoryFile): self
    {
        if (!$this->category_files->contains($categoryFile)) {
            $this->category_files[] = $categoryFile;
            $categoryFile->setCategory($this);
        }

        return $this;
    }

    public function removeCategoryFile(File $categoryFile): self
    {
        if ($this->category_files->contains($categoryFile)) {
            $this->category_files->removeElement($categoryFile);
            // set the owning side to null (unless already changed)
            if ($categoryFile->getCategory() === $this) {
                $categoryFile->setCategory(null);
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

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(Post $post): self
    {
        if (!$this->post->contains($post)) {
            $this->post[] = $post;
            $post->setCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->post->contains($post)) {
            $this->post->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }

        return $this;
    }

    function __toString()
    {
        if(!is_null($this->getTitle()) && !is_null($this->getSection()->getTitle())) {
            return $this->getTitle().'- Seccíon: '.$this->getSection()->getTitle();
        }else{
            return 'Categoria';
        }

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
}
