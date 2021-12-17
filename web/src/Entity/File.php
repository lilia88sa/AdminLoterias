<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Blameable\Traits\BlameableEntity;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="category_files")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="section_files")
     */
    private $section;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="post_files")
     */
    private $post;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=KeyWords::class, inversedBy="keyWordsFiles")
     */
    private $keyWords;

    /**
     * @ORM\ManyToOne(targetEntity=Entities::class, inversedBy="entitiesFiles")
     */
    private $entities;

    /**
     * @ORM\ManyToOne(targetEntity=Juego::class, inversedBy="juego_file")
     */
    private $juego;

    /**
     * @ORM\ManyToOne(targetEntity=Cliente::class, inversedBy="imagen")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Cliente::class, inversedBy="carteles")
     */
    private $carteles;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

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


    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

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

    public function getKeyWords(): ?KeyWords
    {
        return $this->keyWords;
    }

    public function setKeyWords(?KeyWords $keyWords): self
    {
        $this->keyWords = $keyWords;

        return $this;
    }

    public function getEntities(): ?Entities
    {
        return $this->entities;
    }

    public function setEntities(?Entities $entities): self
    {
        $this->entities = $entities;

        return $this;
    }

    public function getJuego(): ?Juego
    {
        return $this->juego;
    }

    public function setJuego(?Juego $juego): self
    {
        $this->juego = $juego;

        return $this;
    }

    public function getImage(): ?Cliente
    {
        return $this->image;
    }

    public function setImage(?Cliente $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCarteles(): ?Cliente
    {
        return $this->carteles;
    }

    public function setCarteles(?Cliente $carteles): self
    {
        $this->carteles = $carteles;

        return $this;
    }
}
