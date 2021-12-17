<?php

namespace App\Entity\Core;

use App\Entity\Post;
use App\Repository\Core\ViewCounterRepository;
use Doctrine\ORM\Mapping as ORM;

use Tchoulom\ViewCounterBundle\Entity\ViewCounter as BaseViewCounter;
use Tchoulom\ViewCounterBundle\Entity\ViewCounterInterface;
use Tchoulom\ViewCounterBundle\Model\ViewCountable;

/**
 * @ORM\Entity(repositoryClass=ViewCounterRepository::class)
 */
class ViewCounter extends BaseViewCounter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, cascade={"persist"}, inversedBy="viewCounters")
     */
    private $post;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * Gets the ViewCountable entity.
     *
     * @return ViewCountable The ViewCountable entity.
     */
    public function getPage(): ViewCountable
    {
        return $this->post;
    }

    /**
     * Sets the ViewCountable entity.
     *
     * @param ViewCountable $page The ViewCountable entity.
     *
     * @return self
     */
    public function setPage(ViewCountable $post): ViewCounterInterface
    {
        $this->post = $post;

        return $this;
    }
}
