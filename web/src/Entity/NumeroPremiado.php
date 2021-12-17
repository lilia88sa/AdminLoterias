<?php

namespace App\Entity;

use App\Repository\NumeroPremiadoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NumeroPremiadoRepository::class)
 */
class NumeroPremiado
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $array_json;

    public function __construct(array $arg=[]){

        foreach ($arg as $key=>$value) $this->$key=$value;

    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getArrayJson(): ?string
    {
        return $this->array_json;
    }

    public function setArrayJson(?string $array_json): self
    {
        $this->array_json = $array_json;

        return $this;
    }
}
