<?php

namespace App\Entity\Core;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Security\User;
use App\Repository\Core\NotificationsRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Translatable\Translatable;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=NotificationsRepository::class)
 * @Gedmo\TranslationEntity(class="App\Entity\Core\NotificationsTranslation")
 */
class Notifications implements Translatable
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

    const NOTIFICATIONS_TYPE_INFO = 'INFO';
    const NOTIFICATIONS_TYPE_SUCCESS = 'SUCCESS';
    const NOTIFICATIONS_TYPE_WAITING = 'WAITING';
    const NOTIFICATIONS_TYPE_WARNING = 'WARNING';
    const NOTIFICATIONS_TYPE_ERROR = 'ERROR';

    const MESSAGE_TYPE_REQUEST = 'REQUEST';
    const MESSAGE_TYPE_CONTRACT = 'CONTRACT';
    const MESSAGE_TYPE_GENERAL = 'GENERAL';
    const MESSAGE_TYPE_LOGISTIC = 'LOGISTIC';

    const NOTIFICATIONS_STATUS_READ = 'READ';
    const NOTIFICATIONS_STATUS_UNREAD = 'UNREAD';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Translatable
     * @Groups({"organizer:read",
     *     "artist:read",
     *     "agency:read",
     *     "agent:read",
     *     "notification-read"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"organizer:read",
     *     "artist:read",
     *     "agency:read",
     *     "agent:read",
     *     "notification-read"})
     */
    private $messageType;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"organizer:read",
     *     "artist:read",
     *     "agency:read",
     *     "agent:read",
     *     "notification-read"})
     */
    private $notificationsType;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"organizer:read",
     *     "artist:read",
     *     "agency:read",
     *     "agent:read",
     *     "notification-read"})
     */
    private $status;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notifications")
     * @Groups({"notification-read"})
     */
    private $user;

    public function __construct()
    {
        $this->status = Notifications::NOTIFICATIONS_STATUS_UNREAD;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMessageType(): ?string
    {
        return $this->messageType;
    }

    public function setMessageType(string $messageType): self
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function getNotificationsType(): ?string
    {
        return $this->notificationsType;
    }

    public function setNotificationsType(string $notificationsType): self
    {
        $this->notificationsType = $notificationsType;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
