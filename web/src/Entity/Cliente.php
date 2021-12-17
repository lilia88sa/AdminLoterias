<?php

namespace App\Entity;

use App\Entity\Security\User;
use App\Util\SlugMaker;
use App\Repository\ClienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClienteRepository::class)
 */
class Cliente
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="image")
     */
    private $imagen;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="carteles")
     */
    private $carteles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about_us;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="client")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publish;

    /**
     * @ORM\OneToMany(targetEntity=Section::class, mappedBy="cliente", cascade={"persist", "remove"})
     */
    private $sections;

    private $slug;



    public function __construct()
    {
        $this->imagen = new ArrayCollection();
        $this->carteles = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->sections = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|File[]
     */
    public function getImagen(): Collection
    {
        return $this->imagen;
    }

    public function addImagen(File $imagen): self
    {
        if (!$this->imagen->contains($imagen)) {
            $this->imagen[] = $imagen;
            $imagen->setImage($this);
        }

        return $this;
    }

    public function removeImagen(File $imagen): self
    {
        if ($this->imagen->contains($imagen)) {
            $this->imagen->removeElement($imagen);
            // set the owning side to null (unless already changed)
            if ($imagen->getImage() === $this) {
                $imagen->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getCarteles(): Collection
    {
        return $this->carteles;
    }

    public function addCarteles(File $carteles): self
    {
        if (!$this->carteles->contains($carteles)) {
            $this->carteles[] = $carteles;
            $carteles->setCarteles($this);
        }

        return $this;
    }

    public function removeCarteles(File $carteles): self
    {
        if ($this->imagen->contains($carteles)) {
            $this->imagen->removeElement($carteles);
            // set the owning side to null (unless already changed)
            if ($carteles->getCarteles() === $this) {
                $carteles->setCarteles(null);
            }
        }

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getAboutUs(): ?string
    {
        return $this->about_us;
    }

    public function setAboutUs(?string $about_us): self
    {
        $this->about_us = $about_us;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setClient($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getClient() === $this) {
                $user->setClient(null);
            }
        }

        return $this;
    }

    public function __toString(){
     return $this->getNombre();
}

    public function getSlug(): ?string
    {
        return SlugMaker::obtenerSlug($this->nombre);
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
            $section->setCliente($this);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->contains($section)) {
            $this->sections->removeElement($section);
            // set the owning side to null (unless already changed)
            if ($section->getCliente() === $this) {
                $section->setCliente(null);
            }
        }

        return $this;
    }




}
