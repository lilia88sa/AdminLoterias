<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ResultRepository::class)
 */
class Result
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Juego::class, inversedBy="results")
     * @ORM\JoinColumn(nullable=false)
     */
    private $juego;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $combinaciones;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $estrellas;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reintegros;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $complementos;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $clave;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $fraccion_serie;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $caballo;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $millon_joker;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $categoria;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $premio;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $categoria_joker;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $premio_joker;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bote;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_lectura;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pos_year;




        public function __construct($arg=array()){

            foreach ($arg as $key=>$value) $this->$key=$value;
//            $date = new \DateTime();
//
      //  return $this;
}

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCombinaciones(): ?string
    {
        return $this->combinaciones;
    }

    public function setCombinaciones(?string $combinaciones): self
    {
        $this->combinaciones = $combinaciones;

        return $this;
    }

    public function getEstrellas(): ?string
    {
        return $this->estrellas;
    }

    public function setEstrellas(?string $estrellas): self
    {
        $this->estrellas = $estrellas;

        return $this;
    }

    public function getReintegros(): ?string
    {
        return $this->reintegros;
    }

    public function setReintegros(?string $reintegros): self
    {
        $this->reintegros = $reintegros;

        return $this;
    }

    public function getComplementos(): ?string
    {
        return $this->complementos;
    }

    public function setComplementos(?string $complementos): self
    {
        $this->complementos = $complementos;

        return $this;
    }

    public function getClave(): ?string
    {
        return $this->clave;
    }

    public function setClave(?string $clave): self
    {
        $this->clave = $clave;

        return $this;
    }

    public function getFraccionSerie(): ?string
    {
        return $this->fraccion_serie;
    }

    public function setFraccionSerie(?string $fraccion_serie): self
    {
        $this->fraccion_serie = $fraccion_serie;

        return $this;
    }

    public function getCaballo(): ?string
    {
        return $this->caballo;
    }

    public function setCaballo(?string $caballo): self
    {
        $this->caballo = $caballo;

        return $this;
    }

    public function getMillonJoker(): ?string
    {
        return $this->millon_joker;
    }

    public function setMillonJoker(?string $millon_joker): self
    {
        $this->millon_joker = $millon_joker;

        return $this;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(?string $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getPremio(): ?string
    {
        return $this->premio;
    }

    public function setPremio(?string $premio): self
    {
        $this->premio = $premio;

        return $this;
    }



    public function getCategoriaJoker(): ?string
    {
        return $this->categoria_joker;
    }

    public function setCategoriaJoker(?string $categoria_joker): self
    {
        $this->categoria_joker = $categoria_joker;

        return $this;
    }

    public function getPremioJoker(): ?string
    {
        return $this->premio_joker;
    }

    public function setPremioJoker(?string $premio_joker): self
    {
        $this->premio_joker = $premio_joker;

        return $this;
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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getUrlLectura(): ?string
    {
        return $this->url_lectura;
    }

    public function setUrlLectura(?string $url_lectura): self
    {
        $this->url_lectura = $url_lectura;

        return $this;
    }

    public function getPosYear(): ?int
    {
        return $this->pos_year;
    }

    public function setPosYear(?int $pos_year): self
    {
        $this->pos_year = $pos_year;

        return $this;
    }



















}
