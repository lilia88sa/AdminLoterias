<?php

namespace App\Entity;

use App\Repository\OrderClasificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;

/**
 * @ORM\Entity(repositoryClass=OrderClasificationRepository::class)
 */
class OrderClasification
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
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Section::class, mappedBy="orderClasification")
     */
    private $sections;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, mappedBy="orderClasification")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, mappedBy="orderClasification")
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity=Entities::class, mappedBy="orderClasification")
     */
    private $entities;

    /**
     * @ORM\ManyToMany(targetEntity=Juego::class, mappedBy="orderClasification")
     */
    private $juegos;



    function __toString()
    {
        if(!is_null($this->getName())) {
            return $this->getName();
        }else{
            return 'Clasificación';
        }

    }

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->entities = new ArrayCollection();
        $this->juegos = new ArrayCollection();
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
     * @return Collection|Section[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
            $section->addOrderClasification($this);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->contains($section)) {
            $this->sections->removeElement($section);
            $section->removeOrderClasification($this);
        }

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
            $category->addOrderClasification($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeOrderClasification($this);
        }

        return $this;
    }


    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->addOrderClasification($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            $post->removeOrderClasification($this);
        }

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
            $entity->addOrderClasification($this);
        }

        return $this;
    }

    public function removeEntity(Entities $entity): self
    {
        if ($this->entities->contains($entity)) {
            $this->entities->removeElement($entity);
            $entity->removeOrderClasification($this);
        }

        return $this;
    }

    /**
     * @return Collection|Juego[]
     */
    public function getJuegos(): Collection
    {
        return $this->juegos;
    }

    public function addJuego(Juego $juego): self
    {
        if (!$this->juegos->contains($juego)) {
            $this->juegos[] = $juego;
            $juego->addOrderClasification($this);
        }

        return $this;
    }

    public function removeJuego(Juego $juego): self
    {
        if ($this->juegos->contains($juego)) {
            $this->juegos->removeElement($juego);
            $juego->removeOrderClasification($this);
        }

        return $this;
    }




}
