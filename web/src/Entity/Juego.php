<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\JuegoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Util\SlugMaker;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=JuegoRepository::class)
 */
class Juego
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $bote;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha_bote;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="juego", cascade={"persist", "remove"})
     */
    private $juego_file;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publish;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderElement;

    /**
     * @ORM\ManyToMany(targetEntity=OrderClasification::class, inversedBy="juegos")
     */
    private $orderClasification;

    /**
     * @ORM\OneToMany(targetEntity=Result::class, mappedBy="juego")
     */
    private $results;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $num_sorteo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $order_home;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_juego;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $bote_destacado;


    public function __construct()
    {
        $this->juego_file = new ArrayCollection();
        $this->orderClasification = new ArrayCollection();
        $this->results = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return SlugMaker::obtenerSlug($this->name);
    }

    public function getBote(): ?string
    {
        return $this->bote;
    }

    public function setBote(?string $bote): self
    {
        $this->bote = $bote;

        return $this;
    }

    public function getFechaBote(): ?\DateTimeInterface
    {
        return $this->fecha_bote;
    }

    public function setFechaBote(?\DateTimeInterface $fecha_bote): self
    {
        $this->fecha_bote = $fecha_bote;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getJuegoFile(): Collection
    {
        return $this->juego_file;
    }

    public function addJuegoFile(File $juegoFile): self
    {
        if (!$this->juego_file->contains($juegoFile)) {
            $this->juego_file[] = $juegoFile;
            $juegoFile->setJuego($this);
        }

        return $this;
    }

    public function removeJuegoFile(File $juegoFile): self
    {
        if ($this->juego_file->contains($juegoFile)) {
            $this->juego_file->removeElement($juegoFile);
            // set the owning side to null (unless already changed)
            if ($juegoFile->getJuego() === $this) {
                $juegoFile->setJuego(null);
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

    public function getOrderElement(): ?int
    {
        return $this->orderElement;
    }

    public function setOrderElement(?int $orderElement): self
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

    /**
     * @return Collection|Result[]
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(Result $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setJuego($this);
        }

        return $this;
    }

    public function removeResult(Result $result): self
    {
        if ($this->results->contains($result)) {
            $this->results->removeElement($result);
            // set the owning side to null (unless already changed)
            if ($result->getJuego() === $this) {
                $result->setJuego(null);
            }
        }

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getNumSorteo(): ?string
    {
        return $this->num_sorteo;
    }

    public function setNumSorteo(?string $num_sorteo): self
    {
        $this->num_sorteo = $num_sorteo;

        return $this;
    }

    public function getOrderHome(): ?int
    {
        return $this->order_home;
    }

    public function setOrderHome(?int $order_home): self
    {
        $this->order_home = $order_home;

        return $this;
    }

    public function getUrlJuego(): ?string
    {
        return $this->url_juego;
    }

    public function setUrlJuego(?string $url_juego): self
    {
        $this->url_juego = $url_juego;

        return $this;
    }

    public function getBoteDestacado(): ?bool
    {
        return $this->bote_destacado;
    }

    public function setBoteDestacado(?bool $bote_destacado): self
    {
        $this->bote_destacado = $bote_destacado;

        return $this;
    }


}
